<?php

declare(strict_types=1);

namespace Gitlab\Model;

use Gitlab\Client;

/**
 * @property-read string $name
 * @property-read string $type
 * @property-read string $mode
 * @property-read int $id
 * @property-read Project $project
 */
final class Node extends Model
{
    /** @var mixed[] */
    protected static $properties = [
        'name',
        'type',
        'mode',
        'id',
        'path',
        'project',
    ];

    /**
     * @param mixed[] $data
     */
    public static function fromArray(?Client $client, Project $project, array $data): Node
    {
        $node = new static($project, $data['id'], $client);

        return $node->hydrate($data);
    }

    public function __construct(Project $project, ?int $id = null, ?Client $client = null)
    {
        $this->setClient($client);
        $this->setData('project', $project);
        $this->setData('id', $id);
    }
}
