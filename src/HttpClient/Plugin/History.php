<?php

declare(strict_types=1);

namespace Gitlab\HttpClient\Plugin;

use Http\Client\Common\Plugin\Journal;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * A plugin to remember the last response.
 */
final class History implements Journal
{
    /** @var ResponseInterface */
    private $lastResponse;

    public function getLastResponse(): ?ResponseInterface
    {
        return $this->lastResponse;
    }

    /**
     * {@inheritdoc}
     */
    public function addSuccess(RequestInterface $request, ResponseInterface $response): void
    {
        $this->lastResponse = $response;
    }

    /**
     * {@inheritdoc}
     */
    public function addFailure(RequestInterface $request, ClientExceptionInterface $exception): void
    {
    }
}
