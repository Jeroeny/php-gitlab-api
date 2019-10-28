<?php

declare(strict_types=1);

namespace Gitlab\Model;

use Gitlab\Api\ApiBase;
use Gitlab\Client;
use Gitlab\Exception\RuntimeException;
use function in_array;
use function sprintf;

abstract class Model
{
    /** @var string[] */
    protected static $properties;

    /** @var mixed[] */
    protected $data = [];

    /** @var Client|null */
    protected $client;

    public function getClient(): ?Client
    {
        return $this->client;
    }

    /**
     * @return $this
     */
    public function setClient(?Client $client = null): self
    {
        if ($client !== null) {
            $this->client = $client;
        }

        return $this;
    }

    public function api(string $api): ApiBase
    {
        return $this->getClient()->api($api);
    }

    /**
     * @param array<string, mixed> $data
     */
    protected function hydrate(array $data = []): self
    {
        if ($data !== []) {
            foreach ($data as $field => $value) {
                $this->setData($field, $value);
            }
        }

        return $this;
    }

    /**
     * @param mixed $value
     */
    protected function setData(string $field, $value): self
    {
        if (in_array($field, static::$properties, true)) {
            $this->data[$field] = $value;
        }

        return $this;
    }

    /**
     * @return mixed[]
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @param mixed $value
     *
     * @throws RuntimeException
     */
    public function __set(string $property, $value): void
    {
        throw new RuntimeException('Model properties are immutable');
    }

    /**
     * @return mixed
     */
    public function __get(string $property)
    {
        if (! in_array($property, static::$properties, true)) {
            throw new RuntimeException(sprintf(
                'Property "%s" does not exist for %s object',
                $property,
                static::class
            ));
        }

        return $this->data[$property] ?? null;
    }

    public function __isset(string $property): bool
    {
        return isset($this->data[$property]);
    }
}
