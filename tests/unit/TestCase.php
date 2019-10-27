<?php

declare(strict_types=1);

namespace Gitlab\Tests;

use Gitlab\Client;
use Gitlab\HttpClient\Builder;
use Http\Mock\Client as MockClient;
use PHPUnit\Framework\TestCase as UnitTestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use function is_array;
use function json_encode;

abstract class TestCase extends UnitTestCase
{
    /** @var Client */
    protected $client;

    /** @var MockClient */
    protected $httpClient;

    /** @var Builder */
    protected $builder;

    public function setUp(): void
    {
        $this->httpClient = new MockClient();
        $this->builder    = new Builder($this->httpClient);
        $this->client     = new Client($this->builder);
    }

    protected function setResponse(ResponseInterface $response): void
    {
        $this->httpClient->setDefaultResponse($response);
    }

    /**
     * @param mixed[]|string|int|bool|float $responseBodyContents
     */
    protected function setResponseBody($responseBodyContents): void
    {
        $this->httpClient->setDefaultResponse($this->createResponse($responseBodyContents));
    }

    /**
     * @param mixed[]|string|int|bool|float $responseBodyContents
     */
    protected function createResponse($responseBodyContents): ResponseInterface
    {
        $responseBody = $this->createMock(StreamInterface::class);
        $responseBody
            ->expects($this->any())
            ->method('__toString')
            ->willReturn(is_array($responseBodyContents) ? json_encode($responseBodyContents) : $responseBodyContents);

        $response = $this->createMock(ResponseInterface::class);
        $response
            ->expects($this->any())
            ->method('getBody')
            ->willReturn($responseBody);

        $response
            ->expects($this->any())
            ->method('getHeaderLine')
            ->willReturn(is_array($responseBodyContents) ? 'application/json' : 'txt/txt');

        return $response;
    }
}
