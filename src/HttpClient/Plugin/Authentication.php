<?php

declare(strict_types=1);

namespace Gitlab\HttpClient\Plugin;

use Gitlab\Client;
use Http\Client\Common\Plugin;
use Http\Promise\Promise;
use Psr\Http\Message\RequestInterface;
use function http_build_query;
use function mb_convert_encoding;

/**
 * Add authentication to the request.
 */
final class Authentication implements Plugin
{
    /** @var string */
    private $method;

    /** @var string */
    private $token;

    /** @var string|null */
    private $sudo;

    public function __construct(string $method, string $token, ?string $sudo = null)
    {
        $this->method = $method;
        $this->token  = $token;
        $this->sudo   = $sudo;
    }

    /**
     * {@inheritdoc}
     */
    public function handleRequest(RequestInterface $request, callable $next, callable $first): Promise
    {
        switch ($this->method) {
            case Client::AUTH_HTTP_TOKEN:
                $request = $request->withHeader('PRIVATE-TOKEN', $this->token);
                if ($this->sudo !== null) {
                    $request = $request->withHeader('SUDO', $this->sudo);
                }
                break;

            case Client::AUTH_URL_TOKEN:
                $uri   = $request->getUri();
                $query = $uri->getQuery();

                $parameters = [
                    'private_token' => $this->token,
                ];

                if ($this->sudo !== null) {
                    $parameters['sudo'] = $this->sudo;
                }

                $query .= empty($query) ? '' : '&';
                $query .= mb_convert_encoding(http_build_query($parameters, '', '&'), 'UTF-8');

                $uri     = $uri->withQuery($query);
                $request = $request->withUri($uri);
                break;

            case Client::AUTH_OAUTH_TOKEN:
                $request = $request->withHeader('Authorization', 'Bearer ' . $this->token);
                if ($this->sudo !== null) {
                    $request = $request->withHeader('SUDO', $this->sudo);
                }
                break;
        }

        return $next($request);
    }
}
