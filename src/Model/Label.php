<?php

declare(strict_types=1);

namespace Gitlab\Model;

use Gitlab\Client;

/**
 * @property-read int $id
 * @property-read string $name
 * @property-read string $color
 */
final class Label extends Model
{
    /** @var mixed[] */
    protected static $properties = [
        'id',
        'name',
        'color',
    ];

    /**
     * @param mixed[] $data
     */
    public static function fromArray(?Client $client, Project $project, array $data): Label
    {
        $label = new static($project, $client);

        return $label->hydrate($data);
    }

    public function __construct(Project $project, ?Client $client = null)
    {
        $this->setClient($client);
        $this->setData('project', $project);
    }
}
