<?php

declare(strict_types=1);

namespace Gitlab\Model;

use Gitlab\Client;

/**
 * @property-read int $id
 * @property-read string $ref
 * @property-read string $sha
 * @property-read string $status
 */
final class Pipeline extends Model
{
    /** @var mixed[] */
    protected static $properties = [
        'id',
        'ref',
        'sha',
        'status',
    ];

    /**
     * @param mixed[] $data
     */
    public static function fromArray(?Client $client, Project $project, array $data): Pipeline
    {
        $pipeline = new static($project, $data['id'], $client);

        return $pipeline->hydrate($data);
    }

    public function __construct(Project $project, ?int $id = null, ?Client $client = null)
    {
        $this->setClient($client);
        $this->setData('project', $project);
        $this->setData('id', $id);
    }
}
