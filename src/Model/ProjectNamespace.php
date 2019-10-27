<?php

declare(strict_types=1);

namespace Gitlab\Model;

use Gitlab\Client;

/**
 * @property-read int $id
 * @property-read string $name
 * @property-read string $path
 * @property-read string $kind
 * @property-read int $owner_id
 * @property-read string $created_at
 * @property-read string $updated_at
 * @property-read string $description
 */
final class ProjectNamespace extends Model
{
    /** @var mixed[] */
    protected static $properties = [
        'id',
        'name',
        'path',
        'kind',
        'owner_id',
        'created_at',
        'updated_at',
        'description',
    ];

    /**
     * @param mixed[] $data
     */
    public static function fromArray(?Client $client, array $data): ProjectNamespace
    {
        $project = new static($data['id']);
        $project->setClient($client);

        return $project->hydrate($data);
    }

    public function __construct(?int $id = null, ?Client $client = null)
    {
        $this->setClient($client);
        $this->setData('id', $id);
    }
}
