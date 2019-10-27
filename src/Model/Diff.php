<?php

declare(strict_types=1);

namespace Gitlab\Model;

use Gitlab\Client;

/**
 * @property-read string $old_path
 * @property-read string $new_path
 * @property-read string $a_mode
 * @property-read string $b_mode
 * @property-read string $diff
 * @property-read bool $new_file
 * @property-read bool $renamed_file
 * @property-read bool $deleted_file
 * @property-read Project $project
 */
final class Diff extends Model
{
    /** @var mixed[] */
    protected static $properties = [
        'old_path',
        'new_path',
        'a_mode',
        'b_mode',
        'diff',
        'new_file',
        'renamed_file',
        'deleted_file',
        'project',
    ];

    /**
     * @param mixed[] $data
     */
    public static function fromArray(?Client $client, Project $project, array $data): Diff
    {
        $diff = new static($project, $client);

        return $diff->hydrate($data);
    }

    public function __construct(Project $project, ?Client $client = null)
    {
        $this->setClient($client);
        $this->setData('project', $project);
    }

    public function __toString(): string
    {
        return $this->diff;
    }
}
