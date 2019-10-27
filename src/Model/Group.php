<?php

declare(strict_types=1);

namespace Gitlab\Model;

use Gitlab\Client;

/**
 * @property-read int $id
 * @property-read string $name
 * @property-read string $path
 * @property-read string $description
 * @property-read string $visibility
 * @property-read bool $lfs_enabled
 * @property-read string $avatar_url
 * @property-read string $web_url
 * @property-read bool $request_access_enabled
 * @property-read string $full_name
 * @property-read string $full_path
 * @property-read int $file_template_project_id
 * @property-read int|null $parent_id
 * @property-read Project[] $projects
 * @property-read Project[] $shared_projects
 */
final class Group extends Model
{
    /** @var mixed[] */
    protected static $properties = [
        'id',
        'name',
        'path',
        'description',
        'visibility',
        'lfs_enabled',
        'avatar_url',
        'web_url',
        'request_access_enabled',
        'full_name',
        'full_path',
        'file_template_project_id',
        'parent_id',
        'projects',
        'shared_projects',
    ];

    /**
     * @param mixed[] $data
     */
    public static function fromArray(?Client $client, array $data): Group
    {
        $group = new static($data['id'], $client);

        if (isset($data['projects'])) {
            $projects = [];
            foreach ($data['projects'] as $project) {
                $projects[] = Project::fromArray($client, $project);
            }
            $data['projects'] = $projects;
        }

        if (isset($data['shared_projects'])) {
            $projects = [];
            foreach ($data['shared_projects'] as $project) {
                $projects[] = Project::fromArray($client, $project);
            }
            $data['shared_projects'] = $projects;
        }

        return $group->hydrate($data);
    }

    public static function create(Client $client, string $name, string $path): Group
    {
        $data = $client->groups()->create($name, $path);

        return static::fromArray($client, $data);
    }

    public function __construct(int $id, ?Client $client = null)
    {
        $this->setClient($client);
        $this->setData('id', $id);
    }

    public function show(): Group
    {
        $data = $this->client->groups()->show($this->id);

        return self::fromArray($this->getClient(), $data);
    }

    public function transfer(int $project_id): Group
    {
        $data = $this->client->groups()->transfer($this->id, $project_id);

        return self::fromArray($this->getClient(), $data);
    }

    /**
     * @return User[]
     */
    public function members(): array
    {
        $data = $this->client->groups()->members($this->id);

        $members = [];
        foreach ($data as $member) {
            $members[] = User::fromArray($this->getClient(), $member);
        }

        return $members;
    }

    public function addMember(int $user_id, int $access_level): User
    {
        $data = $this->client->groups()->addMember($this->id, $user_id, $access_level);

        return User::fromArray($this->getClient(), $data);
    }

    public function removeMember(int $user_id): bool
    {
        $this->client->groups()->removeMember($this->id, $user_id);

        return true;
    }

    /**
     * @return Project[]
     */
    public function projects(): array
    {
        $data = $this->client->groups()->projects($this->id);

        $projects = [];
        foreach ($data as $project) {
            $projects[] = Project::fromArray($this->getClient(), $project);
        }

        return $projects;
    }

    /**
     * @return Group[]
     */
    public function subgroups(): array
    {
        $data = $this->client->groups()->subgroups($this->id);

        $groups = [];
        foreach ($data as $group) {
            $groups[] = self::fromArray($this->getClient(), $group);
        }

        return $groups;
    }
}
