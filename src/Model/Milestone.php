<?php

declare(strict_types=1);

namespace Gitlab\Model;

use Gitlab\Client;

/**
 * @property-read int $id
 * @property-read int $iid
 * @property-read int $project_id
 * @property-read string $title
 * @property-read string $description
 * @property-read string $due_date
 * @property-read string $start_date
 * @property-read string $state
 * @property-read bool $closed
 * @property-read string $updated_at
 * @property-read string $created_at
 * @property-read Project $project
 */
final class Milestone extends Model
{
    /** @var mixed[] */
    protected static $properties = [
        'id',
        'iid',
        'project',
        'project_id',
        'title',
        'description',
        'due_date',
        'start_date',
        'state',
        'closed',
        'updated_at',
        'created_at',
    ];

    /**
     * @param mixed[] $data
     */
    public static function fromArray(?Client $client, Project $project, array $data): Milestone
    {
        $milestone = new static($project, $data['id'], $client);

        return $milestone->hydrate($data);
    }

    public function __construct(Project $project, int $id, ?Client $client = null)
    {
        $this->setClient($client);
        $this->setData('id', $id);
        $this->setData('project', $project);
    }

    public function show(): Milestone
    {
        $data = $this->client->milestones()->show($this->project->id, $this->id);

        return static::fromArray($this->getClient(), $this->project, $data);
    }

    /**
     * @param mixed[] $params
     */
    public function update(array $params): Milestone
    {
        $data = $this->client->milestones()->update($this->project->id, $this->id, $params);

        return static::fromArray($this->getClient(), $this->project, $data);
    }

    public function complete(): Milestone
    {
        return $this->update(['closed' => true]);
    }

    public function incomplete(): Milestone
    {
        return $this->update(['closed' => false]);
    }

    /**
     * @return Issue[]
     */
    public function issues(): array
    {
        $data = $this->client->milestones()->issues($this->project->id, $this->id);

        $issues = [];
        foreach ($data as $issue) {
            $issues[] = Issue::fromArray($this->getClient(), $this->project, $issue);
        }

        return $issues;
    }
}
