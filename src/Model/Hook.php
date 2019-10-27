<?php

declare(strict_types=1);

namespace Gitlab\Model;

use Gitlab\Client;

/**
 * @property-read int $id
 * @property-read string $url
 * @property-read string $created_at
 */
final class Hook extends Model
{
    /** @var mixed[] */
    protected static $properties = [
        'id',
        'url',
        'created_at',
    ];

    /**
     * @param mixed[] $data
     */
    public static function fromArray(?Client $client, array $data): Hook
    {
        $hook = new static($data['id'], $client);

        return $hook->hydrate($data);
    }

    public static function create(Client $client, string $url): Hook
    {
        $data = $client->systemHooks()->create($url);

        return static::fromArray($client, $data);
    }

    public function __construct(int $id, ?Client $client = null)
    {
        $this->setClient($client);
        $this->setData('id', $id);
    }

    public function test(): bool
    {
        $this->client->systemHooks()->test($this->id);

        return true;
    }

    public function delete(): bool
    {
        $this->client->systemHooks()->remove($this->id);

        return true;
    }
}
