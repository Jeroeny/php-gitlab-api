<?php

declare(strict_types=1);

namespace Gitlab\HttpClient\Plugin;

use Http\Client\Common\Plugin;
use Http\Promise\Promise;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use function substr;

/**
 * Prefix requests path with /api/v4/ if required.
 */
final class ApiVersion implements Plugin
{
    /** @var bool */
    private $redirected = false;

    /**
     * {@inheritdoc}
     */
    public function handleRequest(RequestInterface $request, callable $next, callable $first): Promise
    {
        $uri = $request->getUri();

        if (substr($uri->getPath(), 0, 8) !== '/api/v4/' && ! $this->redirected) {
            $request = $request->withUri($uri->withPath('/api/v4/' . $uri->getPath()));
        }

        return $next($request)->then(function (ResponseInterface $response) {
            $this->redirected = $response->getStatusCode() === 302;

            return $response;
        });
    }
}
