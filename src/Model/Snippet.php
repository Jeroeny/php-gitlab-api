<?php

declare(strict_types=1);

namespace Gitlab\Model;

use Gitlab\Client;

/**
 * @property-read int $id
 * @property-read string $title
 * @property-read string $file_name
 * @property-read string $updated_at
 * @property-read string $created_at
 * @property-read Project $project
 * @property-read User $author
 */
final class Snippet extends Model
{
    /** @var mixed[] */
    protected static $properties = [
        'id',
        'title',
        'file_name',
        'author',
        'updated_at',
        'created_at',
        'project',
    ];

    /**
     * @param mixed[] $data
     */
    public static function fromArray(?Client $client, Project $project, array $data): Snippet
    {
        $snippet = new static($project, $data['id'], $client);

        if (isset($data['author'])) {
            $data['author'] = User::fromArray($client, $data['author']);
        }

        return $snippet->hydrate($data);
    }

    public function __construct(Project $project, ?int $id = null, ?Client $client = null)
    {
        $this->setClient($client);
        $this->setData('project', $project);
        $this->setData('id', $id);
    }

    public function show(): Snippet
    {
        $data = $this->client->snippets()->show($this->project->id, $this->id);

        return static::fromArray($this->getClient(), $this->project, $data);
    }

    /**
     * @param mixed[] $params
     */
    public function update(array $params): Snippet
    {
        $data = $this->client->snippets()->update($this->project->id, $this->id, $params);

        return static::fromArray($this->getClient(), $this->project, $data);
    }

    public function content(): string
    {
        return $this->client->snippets()->content($this->project->id, $this->id);
    }

    public function remove(): bool
    {
        $this->client->snippets()->remove($this->project->id, $this->id);

        return true;
    }
}
