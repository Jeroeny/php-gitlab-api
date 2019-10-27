<?php

declare(strict_types=1);

namespace Gitlab\HttpClient\Message;

use Psr\Http\Message\ResponseInterface;
use const JSON_ERROR_NONE;
use function array_shift;
use function count;
use function explode;
use function json_decode;
use function json_last_error;
use function preg_match;
use function strpos;
use function trim;

/**
 * Utilities to parse response headers and content.
 */
final class ResponseMediator
{
    /**
     * Return the response body as a string or json array if content type is application/json.
     * .
     *
     * @return mixed
     */
    public static function getContent(ResponseInterface $response)
    {
        $body = $response->getBody()->__toString();
        if (strpos($response->getHeaderLine('Content-Type'), 'application/json') === 0) {
            $content = json_decode($body, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                return $content;
            }
        }

        return $body;
    }

    /**
     * Extract pagination URIs from Link header.
     *
     * @return mixed[]|null
     */
    public static function getPagination(ResponseInterface $response): ?array
    {
        if (! $response->hasHeader('Link')) {
            return null;
        }

        $header     = self::getHeader($response, 'Link');
        $pagination = [];
        foreach (explode(',', $header ?? '') as $link) {
            preg_match('/<(.*)>; rel="(.*)"/i', trim($link, ','), $match);

            if (count($match) !== 3) {
                continue;
            }

            $pagination[$match[2]] = $match[1];
        }

        return $pagination;
    }

    /**
     * Get the value for a single header.
     */
    private static function getHeader(ResponseInterface $response, string $name): ?string
    {
        $headers = $response->getHeader($name);

        return array_shift($headers);
    }
}
