<?php

declare(strict_types=1);

namespace Gitlab\Tests\HttpClient\Message;

use Gitlab\HttpClient\Message\ResponseMediator;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use function GuzzleHttp\Psr7\stream_for;
use function json_encode;

final class ResponseMediatorTest extends TestCase
{
    public function testGetContent(): void
    {
        $body     = ['foo' => 'bar'];
        $response = new Response(
            200,
            ['Content-Type' => 'application/json'],
            stream_for(json_encode($body))
        );

        $this->assertEquals($body, ResponseMediator::getContent($response));
    }

    /**
     * If content-type is not json we should get the raw body.
     */
    public function testGetContentNotJson(): void
    {
        $body     = 'foobar';
        $response = new Response(
            200,
            [],
            stream_for($body)
        );

        $this->assertEquals($body, ResponseMediator::getContent($response));
    }

    /**
     * Make sure we return the body if we have invalid json
     */
    public function testGetContentInvalidJson(): void
    {
        $body     = 'foobar';
        $response = new Response(
            200,
            ['Content-Type' => 'application/json'],
            stream_for($body)
        );

        $this->assertEquals($body, ResponseMediator::getContent($response));
    }

    public function testGetPagination(): void
    {
        $header = <<<TEXT
<https://example.gitlab.com>; rel="first",
<https://example.gitlab.com>; rel="next",
<https://example.gitlab.com>; rel="prev",
<https://example.gitlab.com>; rel="last",
TEXT;

        $pagination = [
            'first' => 'https://example.gitlab.com',
            'next' => 'https://example.gitlab.com',
            'prev' => 'https://example.gitlab.com',
            'last' => 'https://example.gitlab.com',
        ];

        // response mock
        $response = new Response(200, ['link' => $header]);
        $result   = ResponseMediator::getPagination($response);

        $this->assertEquals($pagination, $result);
    }
}
