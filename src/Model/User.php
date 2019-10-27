<?php

declare(strict_types=1);

namespace Gitlab\Model;

use Gitlab\Client;

/**
 * @property-read int $id
 * @property-read string $email
 * @property-read string $password
 * @property-read string $username
 * @property-read string $name
 * @property-read string $bio
 * @property-read string $skype
 * @property-read string $linkedin
 * @property-read string $twitter
 * @property-read bool $dark_scheme
 * @property-read int $theme_id
 * @property-read int $color_scheme_id
 * @property-read bool $blocked
 * @property-read int $access_level
 * @property-read string $created_at
 * @property-read string $extern_uid
 * @property-read string $provider
 * @property-read string $state
 * @property-read bool $is_admin
 * @property-read bool $can_create_group
 * @property-read bool $can_create_project
 * @property-read string $avatar_url
 * @property-read string $current_sign_in_at
 * @property-read bool $two_factor_enabled
 */
final class User extends Model
{
    /** @var mixed[] */
    protected static $properties = [
        'id',
        'email',
        'password',
        'username',
        'name',
        'bio',
        'skype',
        'linkedin',
        'twitter',
        'dark_scheme',
        'theme_id',
        'color_scheme_id',
        'blocked',
        'projects_limit',
        'access_level',
        'created_at',
        'extern_uid',
        'provider',
        'state',
        'is_admin',
        'can_create_group',
        'can_create_project',
        'avatar_url',
        'current_sign_in_at',
        'two_factor_enabled',
    ];

    /**
     * @param mixed[] $data
     */
    public static function fromArray(?Client $client, array $data): User
    {
        $id = $data['id'] ?? 0;

        $user = new static($id, $client);

        return $user->hydrate($data);
    }

    /**
     * @param mixed[] $params
     */
    public static function create(Client $client, string $email, string $password, array $params = []): User
    {
        $data = $client->users()->create($email, $password, $params);

        return static::fromArray($client, $data);
    }

    public function __construct(?int $id = null, ?Client $client = null)
    {
        $this->setClient($client);
        $this->setData('id', $id);
    }

    public function show(): User
    {
        $data = $this->client->users()->show($this->id);

        return static::fromArray($this->getClient(), $data);
    }

    /**
     * @param mixed[] $params
     */
    public function update(array $params): User
    {
        $data = $this->client->users()->update($this->id, $params);

        return static::fromArray($this->getClient(), $data);
    }

    public function remove(): bool
    {
        $this->client->users()->remove($this->id);

        return true;
    }

    public function block(): bool
    {
        $this->client->users()->block($this->id);

        return true;
    }

    public function unblock(): bool
    {
        $this->client->users()->unblock($this->id);

        return true;
    }

    /**
     * @return Key[]
     */
    public function keys(): array
    {
        $data = $this->client->users()->keys();

        $keys = [];
        foreach ($data as $key) {
            $keys[] = Key::fromArray($this->getClient(), $key);
        }

        return $keys;
    }

    public function createKey(string $title, string $key): Key
    {
        $data = $this->client->users()->createKey($title, $key);

        return Key::fromArray($this->getClient(), $data);
    }

    public function createKeyForUser(int $user_id, string $title, string $key): Key
    {
        $data = $this->client->users()->createKeyForUser($user_id, $title, $key);

        return Key::fromArray($this->getClient(), $data);
    }

    public function removeKey(int $id): bool
    {
        $this->client->users()->removeKey($id);

        return true;
    }

    public function addToGroup(int $group_id, int $access_level): User
    {
        $group = new Group($group_id, $this->getClient());

        return $group->addMember($this->id, $access_level);
    }

    public function removeFromGroup(int $group_id): bool
    {
        $group = new Group($group_id, $this->getClient());

        return $group->removeMember($this->id);
    }
}
