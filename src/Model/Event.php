<?php

declare(strict_types=1);

namespace Gitlab\Model;

use Gitlab\Client;

/**
 * @property-read string $title
 * @property-read int $id
 * @property-read string $action_name
 * @property-read string $data
 * @property-read int $target_id
 * @property-read string $target_type
 * @property-read string $target_title
 * @property-read int $author_id
 * @property-read string $author_username
 * @property-read User $author
 * @property-read Project $project
 */
final class Event extends Model
{
    /** @var mixed[] */
    protected static $properties = [
        'title',
        'project_id',
        'action_name',
        'target_id',
        'target_type',
        'author_id',
        'author_username',
        'data',
        'target_title',
        'author',
        'project',
    ];

    /**
     * @param mixed[] $data
     */
    public static function fromArray(?Client $client, Project $project, array $data): Event
    {
        $event = new static($project, $client);

        if (isset($data['author_id'])) {
            $data['author'] = new User($data['author_id'], $client);
        }

        return $event->hydrate($data);
    }

    public function __construct(Project $project, ?Client $client = null)
    {
        $this->setClient($client);
        $this->setData('project', $project);
    }
}
