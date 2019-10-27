<?php

declare(strict_types=1);

namespace Gitlab\Model;

use Gitlab\Client;

/**
 * @property-read int $id
 * @property-read string $title
 * @property-read string $key
 * @property-read string $created_at
 */
final class Key extends Model
{
    /** @var mixed[] */
    protected static $properties = [
        'id',
        'title',
        'key',
        'created_at',
    ];

    /**
     * @param mixed[] $data
     */
    public static function fromArray(?Client $client, array $data): Key
    {
        $key = new static($client);

        return $key->hydrate($data);
    }

    public function __construct(?Client $client = null)
    {
        $this->setClient($client);
    }
}
