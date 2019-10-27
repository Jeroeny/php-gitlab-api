<?php

declare(strict_types=1);

namespace Gitlab\Model;

use Gitlab\Client;

/**
 * @property-read Commit $commit
 * @property-read int $id
 * @property-read string $coverage
 * @property-read string $created_at
 * @property-read string $artifacts_file
 * @property-read string $finished_at
 * @property-read string $name
 * @property-read Pipeline $pipeline
 * @property-read string $ref
 * @property-read string $runner
 * @property-read string $stage
 * @property-read string $started_at
 * @property-read string $status
 * @property-read string|bool $tag
 * @property-read User $user
 */
final class Job extends Model
{
    /** @var mixed[] */
    protected static $properties = [
        'id',
        'commit',
        'coverage',
        'created_at',
        'artifacts_file',
        'finished_at',
        'name',
        'pipeline',
        'ref',
        'runner',
        'stage',
        'started_at',
        'status',
        'tag',
        'user',
    ];

    /**
     * @param mixed[] $data
     */
    public static function fromArray(?Client $client, Project $project, array $data): Job
    {
        $job = new static($project, $data['id'], $client);

        if (isset($data['user'])) {
            $data['user'] = User::fromArray($client, $data['user']);
        }

        if (isset($data['commit'])) {
            $data['commit'] = Commit::fromArray($client, $project, $data['commit']);
        }

        if (isset($data['pipeline'])) {
            $data['pipeline'] = Pipeline::fromArray($client, $project, $data['pipeline']);
        }

        return $job->hydrate($data);
    }

    public function __construct(Project $project, ?int $id = null, ?Client $client = null)
    {
        $this->setClient($client);
        $this->setData('project', $project);
        $this->setData('id', $id);
    }
}
