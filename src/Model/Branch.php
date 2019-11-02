<?php

declare(strict_types=1);

namespace Gitlab\Model;

use Gitlab\Api\Projects;
use Gitlab\Client;

/**
 * @property-read string  $name
 * @property-read bool    $protected
 * @property-read Commit  $commit
 * @property-read Project $project
 */
final class Branch extends Model
{
    /** @var mixed[] */
    protected static $properties = [
        'name',
        'commit',
        'project',
        'protected',
    ];

    /**
     * @param mixed[] $data
     */
    public static function fromArray(?Client $client, Project $project, array $data): Branch
    {
        $branch = new static($project, $data['name'], $client);

        if (isset($data['commit'])) {
            $data['commit'] = Commit::fromArray($client, $project, $data['commit']);
        }

        return $branch->hydrate($data);
    }

    public function __construct(Project $project, ?string $name = null, ?Client $client = null)
    {
        $this->setClient($client);
        $this->setData('project', $project);
        $this->setData('name', $name);
    }

    public function show(): Branch
    {
        $data = $this->client->repositories()->branch($this->project->id, $this->name);

        return static::fromArray($this->getClient(), $this->project, $data);
    }

    public function protect(bool $devPush = false, bool $devMerge = false): Branch
    {
        $data = $this->client->repositories()->protectBranch($this->project->id, $this->name, $devPush, $devMerge);

        return static::fromArray($this->getClient(), $this->project, $data);
    }

    public function unprotect(): Branch
    {
        $data = $this->client->repositories()->unprotectBranch($this->project->id, $this->name);

        return static::fromArray($this->getClient(), $this->project, $data);
    }

    public function delete(): bool
    {
        $this->client->repositories()->deleteBranch($this->project->id, $this->name);

        return true;
    }

    /**
     * @see Projects::commits for available parameters.
     *
     * @param mixed[] $parameters
     *
     * @return Commit[]
     */
    public function commits(array $parameters = []): array
    {
        return $this->project->commits($parameters);
    }

    public function createFile(string $file_path, string $content, string $commit_message): File
    {
        $data = $this->client->repositoryFiles()->createFile(
            $this->project->id,
            [
                'branch' => $this->name,
                'file_path' => $file_path,
                'content' => $content,
                'commit_message' => $commit_message,
            ]
        );

        return File::fromArray($this->getClient(), $this->project, $data);
    }

    public function updateFile(string $file_path, string $content, string $commit_message): File
    {
        $data = $this->client->repositoryFiles()->updateFile(
            $this->project->id,
            [

                'branch' => $this->name,
                'file_path' => $file_path,
                'content' => $content,
                'commit_message' => $commit_message,
            ]
        );

        return File::fromArray($this->getClient(), $this->project, $data);
    }

    public function deleteFile(string $file_path, string $commit_message): bool
    {
        $this->client->repositoryFiles()->deleteFile(
            $this->project->id,
            [

                'branch' => $this->name,
                'file_path' => $file_path,
                'commit_message' => $commit_message,
            ]
        );

        return true;
    }
}
