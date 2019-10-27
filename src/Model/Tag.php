<?php

declare(strict_types=1);

namespace Gitlab\Model;

use Gitlab\Client;

/**
 * @property-read string $name
 * @property-read bool $protected
 * @property-read Commit $commit
 * @property-read Project $project
 */
final class Tag extends Model
{
    /** @var mixed[] */
    protected static $properties = [
        'name',
        'message',
        'commit',
        'release',
        'project',
        'protected',
    ];

    /**
     * @param mixed[] $data
     */
    public static function fromArray(?Client $client, Project $project, array $data): Tag
    {
        $branch = new static($project, $data['name'], $client);

        if (isset($data['commit'])) {
            $data['commit'] = Commit::fromArray($client, $project, $data['commit']);
        }

        if (isset($data['release'])) {
            $data['release'] = Release::fromArray($client, $data['release']);
        }

        return $branch->hydrate($data);
    }

    public function __construct(Project $project, ?string $name = null, ?Client $client = null)
    {
        $this->setClient($client);
        $this->setData('project', $project);
        $this->setData('name', $name);
    }
}
