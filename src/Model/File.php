<?php

declare(strict_types=1);

namespace Gitlab\Model;

use Gitlab\Client;

/**
 * @property-read string $file_path
 * @property-read string $branch_name
 * @property-read Project $project
 */
final class File extends Model
{
    /** @var mixed[] */
    protected static $properties = [
        'project',
        'file_path',
        'branch_name',
    ];

    /**
     * @param mixed[] $data
     */
    public static function fromArray(?Client $client, Project $project, array $data): File
    {
        $file = new static($project, $data['file_path'], $client);

        return $file->hydrate($data);
    }

    public function __construct(Project $project, ?string $file_path = null, ?Client $client = null)
    {
        $this->setClient($client);
        $this->setData('project', $project);
        $this->setData('file_path', $file_path);
    }
}
