<?php

declare(strict_types=1);

namespace Gitlab\Model;

use Gitlab\Client;

/**
 * @property-read string $name
 * @property-read string $email
 * @property-read int $commits
 * @property-read int $additions
 * @property-read int $deletions
 * @property-read Project $project
 */
final class Contributor extends Model
{
    /** @var mixed[] */
    protected static $properties = [
        'name',
        'email',
        'commits',
        'additions',
        'deletions',
        'project',
    ];

    /**
     * @param mixed[] $data
     */
    public static function fromArray(?Client $client, Project $project, array $data): Contributor
    {
        $contributor = new static($project, $client);

        return $contributor->hydrate($data);
    }

    public function __construct(Project $project, ?Client $client = null)
    {
        $this->setClient($client);
        $this->setData('project', $project);
    }
}
