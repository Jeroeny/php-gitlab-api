<?php

declare(strict_types=1);

namespace Gitlab\Model;

use Gitlab\Client;

/**
 * @property-read int $id
 * @property-read string $url
 * @property-read int $project_id
 * @property-read bool $push_events
 * @property-read bool $issues_events
 * @property-read bool $merge_requests_events
 * @property-read bool $job_events
 * @property-read bool $tag_push_events
 * @property-read bool $pipeline_events
 * @property-read string $created_at
 * @property-read Project $project
 */
final class ProjectHook extends Model
{
    /** @var mixed[] */
    protected static $properties = [
        'id',
        'project',
        'url',
        'project_id',
        'push_events',
        'issues_events',
        'merge_requests_events',
        'job_events',
        'tag_push_events',
        'pipeline_events',
        'created_at',
    ];

    /**
     * @param mixed[] $data
     */
    public static function fromArray(?Client $client, Project $project, array $data): ProjectHook
    {
        $hook = new static($project, $data['id'], $client);

        return $hook->hydrate($data);
    }

    public function __construct(Project $project, int $id, ?Client $client = null)
    {
        $this->setClient($client);
        $this->setData('project', $project);
        $this->setData('id', $id);
    }

    public function show(): ProjectHook
    {
        $data = $this->client->projects()->hook($this->project->id, $this->id);

        return static::fromArray($this->getClient(), $this->project, $data);
    }

    public function delete(): bool
    {
        $this->client->projects()->removeHook($this->project->id, $this->id);

        return true;
    }

    public function remove(): bool
    {
        return $this->delete();
    }

    /**
     * @param mixed[] $params
     */
    public function update(array $params): ProjectHook
    {
        $data = $this->client->projects()->updateHook($this->project->id, $this->id, $params);

        return static::fromArray($this->getClient(), $this->project, $data);
    }
}
