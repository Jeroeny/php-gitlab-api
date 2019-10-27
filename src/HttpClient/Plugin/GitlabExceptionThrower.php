<?php

declare(strict_types=1);

namespace Gitlab\HttpClient\Plugin;

use Gitlab\Exception\RuntimeException;
use Gitlab\HttpClient\Message\ResponseMediator;
use Http\Client\Common\Plugin;
use Http\Promise\Promise;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use function array_unique;
use function implode;
use function is_array;
use function is_int;
use function sprintf;

/**
 * A plugin to remember the last response.
 */
final class GitlabExceptionThrower implements Plugin
{
    /**
     * {@inheritdoc}
     */
    public function handleRequest(RequestInterface $request, callable $next, callable $first): Promise
    {
        return $next($request)->then(function (ResponseInterface $response) {
            if ($response->getStatusCode() >= 400 && $response->getStatusCode() < 600) {
                $content = ResponseMediator::getContent($response);
                if (is_array($content) && isset($content['message'])) {
                    if ($response->getStatusCode() === 400) {
                        $message = $this->parseMessage($content['message']);

                        throw new RuntimeException($message, 400);
                    }
                }

                $errorMessage = null;
                if (isset($content['error'])) {
                    $errorMessage = is_array($content['error']) ? implode("\n", $content['error']) : $content['error'];
                } elseif (isset($content['message'])) {
                    $errorMessage = $this->parseMessage($content['message']);
                } else {
                    $errorMessage = $content;
                }

                throw new RuntimeException($errorMessage, $response->getStatusCode());
            }

            return $response;
        });
    }

    /**
     * @param mixed $message
     */
    private function parseMessage($message): string
    {
        $string = $message;

        if (is_array($message)) {
            $format = '"%s" %s';
            $errors = [];

            foreach ($message as $field => $messages) {
                if (is_array($messages)) {
                    $messages = array_unique($messages);
                    foreach ($messages as $error) {
                        $errors[] = sprintf($format, $field, $error);
                    }
                } elseif (is_int($field)) {
                    $errors[] = $messages;
                } else {
                    $errors[] = sprintf($format, $field, $messages);
                }
            }

            $string = implode(', ', $errors);
        }

        return $string;
    }
}
