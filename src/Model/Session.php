<?php

declare(strict_types=1);

namespace Gitlab\Model;

use Gitlab\Client;

/**
 * @property-read int $id
 * @property-read string $email
 * @property-read string $name
 * @property-read string $private_token
 * @property-read string $created_at
 * @property-read bool $blocked
 */
final class Session extends Model
{
    /** @var mixed[] */
    protected static $properties = [
        'id',
        'email',
        'name',
        'private_token',
        'created_at',
        'blocked',
    ];

    /**
     * @param mixed[] $data
     */
    public static function fromArray(?Client $client, array $data): Session
    {
        $session = new static($client);

        return $session->hydrate($data);
    }

    public function __construct(?Client $client = null)
    {
        $this->setClient($client);
    }

    public function me(): User
    {
        $data = $this->client->users()->user();

        return User::fromArray($this->getClient(), $data);
    }

    public function login(string $email, string $password): Session
    {
        $data = $this->client->users()->session($email, $password);

        return $this->hydrate($data);
    }
}
