<?php

declare(strict_types=1);

namespace Gitlab\Model;

use Gitlab\Client;

/**
 * @property-read int $id
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
final class Schedule extends Model
{
    /** @var mixed[] */
    protected static $properties = [
        'id',
        'project',
        'project_id',
        'description',
        'ref',
        'cron',
        'cron_timezone',
        'next_run_at',
        'active',
        'created_at',
        'updated_at',
    ];

    /**
     * @param mixed[] $data
     */
    public static function fromArray(?Client $client, Project $project, array $data): Schedule
    {
        $schedule = new static($project, $data['id'], $client);

        return $schedule->hydrate($data);
    }

    public function __construct(Project $project, ?int $id = null, ?Client $client = null)
    {
        $this->setClient($client);
        $this->setData('project', $project);
        $this->setData('id', $id);
    }

    public function show(): Schedule
    {
        $data = $this->client->schedules()->show($this->project->id, $this->id);

        return static::fromArray($this->getClient(), $this->project, $data);
    }

    /**
     * @param mixed[] $params
     */
    public function update(array $params): Schedule
    {
        $data = $this->client->schedules()->update($this->project->id, $this->id, $params);

        return static::fromArray($this->getClient(), $this->project, $data);
    }
}
