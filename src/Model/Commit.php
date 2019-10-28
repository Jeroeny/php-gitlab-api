<?php

declare(strict_types=1);

namespace Gitlab\Model;

use Gitlab\Client;

/**
 * @property-read string $id
 * @property-read string $short_id
 * @property-read string $title
 * @property-read string $message
 * @property-read string $author_name
 * @property-read string $author_email
 * @property-read string $authored_date
 * @property-read string $committed_date
 * @property-read string $created_at
 * @property-read Commit[] $parents
 * @property-read Node[] $tree
 * @property-read User $committer
 * @property-read User $author
 * @property-read Project $project
 */
final class Commit extends Model
{
    /** @var mixed[] */
    protected static $properties = [
        'id',
        'short_id',
        'parents',
        'tree',
        'title',
        'message',
        'author',
        'author_name',
        'author_email',
        'committer',
        'authored_date',
        'committed_date',
        'created_at',
        'project',
    ];

    /**
     * @param mixed[] $data
     */
    public static function fromArray(?Client $client, Project $project, array $data): Commit
    {
        $commit = new static($project, $data['id'], $client);

        if (isset($data['parents'])) {
            $parents = [];
            foreach ($data['parents'] as $parent) {
                $parents[] = static::fromArray($client, $project, $parent);
            }

            $data['parents'] = $parents;
        }

        if (isset($data['author'])) {
            $data['author'] = User::fromArray($client, $data['author']);
        }

        if (isset($data['committer'])) {
            $data['committer'] = User::fromArray($client, $data['committer']);
        }

        return $commit->hydrate($data);
    }

    public function __construct(Project $project, ?string $id = null, ?Client $client = null)
    {
        $this->setClient($client);
        $this->setData('project', $project);
        $this->setData('id', $id);
    }
}
