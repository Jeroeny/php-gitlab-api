<?php

declare(strict_types=1);

namespace Gitlab;

use Gitlab\Api\Api;
use Gitlab\HttpClient\Message\ResponseMediator;
use function array_merge;
use function call_user_func_array;

/**
 * Pager class for supporting pagination in Gitlab classes
 */
final class SimpleResultPager implements ResultPager
{
    /** @var Client client */
    protected $client;

    /**
     * The Gitlab client to use for pagination. This must be the same
     * instance that you got the Api instance from, i.e.:
     *
     * $client = new \Gitlab\Client();
     * $api = $client->api('someApi');
     * $pager = new \Gitlab\ResultPager($client);
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * {@inheritdoc}
     */
    public function fetch(Api $api, $method, array $parameters = []): array
    {
        return call_user_func_array([$api, $method], $parameters);
    }

    /**
     * {@inheritdoc}
     */
    public function fetchAll(Api $api, $method, array $parameters = []): array
    {
        $result = call_user_func_array([$api, $method], $parameters);
        while ($this->hasNext()) {
            $result = array_merge($result, $this->fetchNext());
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function hasNext(): bool
    {
        return $this->has('next');
    }

    /**
     * {@inheritdoc}
     */
    public function fetchNext(): array
    {
        return $this->get('next');
    }

    /**
     * {@inheritdoc}
     */
    public function hasPrevious(): bool
    {
        return $this->has('prev');
    }

    /**
     * {@inheritdoc}
     */
    public function fetchPrevious(): array
    {
        return $this->get('prev');
    }

    /**
     * {@inheritdoc}
     */
    public function fetchFirst(): array
    {
        return $this->get('first');
    }

    /**
     * {@inheritdoc}
     */
    public function fetchLast(): array
    {
        return $this->get('last');
    }

    /**
     * @param string|int $key
     */
    protected function has($key): bool
    {
        $lastResponse = $this->client->getResponseHistory()->getLastResponse();
        if ($lastResponse === null) {
            return false;
        }

        $pagination = ResponseMediator::getPagination($lastResponse);
        if ($pagination === null) {
            return false;
        }

        return isset($pagination[$key]);
    }

    /**
     * @param string|int $key
     *
     * @return mixed
     */
    protected function get($key)
    {
        if (! $this->has($key)) {
            return [];
        }

        $pagination = ResponseMediator::getPagination($this->client->getResponseHistory()->getLastResponse()) ?? [];

        return ResponseMediator::getContent($this->client->getHttpClient()->get($pagination[$key]));
    }
}
