<?php

declare(strict_types=1);

namespace Gitlab\Model;

use Gitlab\Client;
use function in_array;

/**
 * @property-read int $id
 * @property-read int $iid
 * @property-read string $target_branch
 * @property-read string $source_branch
 * @property-read int $project_id
 * @property-read string $title
 * @property-read string $description
 * @property-read bool $closed
 * @property-read bool $merged
 * @property-read string $state
 * @property-read int $source_project_id
 * @property-read int $target_project_id
 * @property-read int $upvotes
 * @property-read int $downvotes
 * @property-read array $labels
 * @property-read User $author
 * @property-read User $assignee
 * @property-read Project $project
 * @property-read Milestone $milestone
 * @property-read File[] $files
 */
final class MergeRequest extends Model implements Noteable
{
    /** @var mixed[] */
    protected static $properties = [
        'id',
        'iid',
        'target_branch',
        'source_branch',
        'project_id',
        'title',
        'description',
        'closed',
        'merged',
        'author',
        'assignee',
        'project',
        'state',
        'source_project_id',
        'target_project_id',
        'upvotes',
        'downvotes',
        'labels',
        'milestone',
        'files',
    ];

    /**
     * @param mixed[] $data
     */
    public static function fromArray(?Client $client, Project $project, array $data): MergeRequest
    {
        $mr = new static($project, $data['id'], $client);

        if (isset($data['author'])) {
            $data['author'] = User::fromArray($client, $data['author']);
        }

        if (isset($data['assignee'])) {
            $data['assignee'] = User::fromArray($client, $data['assignee']);
        }

        if (isset($data['milestone'])) {
            $data['milestone'] = Milestone::fromArray($client, $project, $data['milestone']);
        }

        if (isset($data['files'])) {
            $files = [];
            foreach ($data['files'] as $file) {
                $files[] = File::fromArray($client, $project, $file);
            }

            $data['files'] = $files;
        }

        return $mr->hydrate($data);
    }

    public function __construct(Project $project, ?int $iid = null, ?Client $client = null)
    {
        $this->setClient($client);
        $this->setData('project', $project);
        $this->setData('iid', $iid);
    }

    public function show(): MergeRequest
    {
        $data = $this->client->mergeRequests()->show($this->project->id, $this->iid);

        return static::fromArray($this->getClient(), $this->project, $data);
    }

    /**
     * @param mixed[] $params
     */
    public function update(array $params): MergeRequest
    {
        $data = $this->client->mergeRequests()->update($this->project->id, $this->iid, $params);

        return static::fromArray($this->getClient(), $this->project, $data);
    }

    public function close(?string $comment = null): MergeRequest
    {
        if ($comment) {
            $this->addComment($comment);
        }

        return $this->update(['state_event' => 'close']);
    }

    public function reopen(): MergeRequest
    {
        return $this->update(['state_event' => 'reopen']);
    }

    public function open(): MergeRequest
    {
        return $this->reopen();
    }

    public function merge(?string $message = null): MergeRequest
    {
        $data = $this->client->mergeRequests()->merge($this->project->id, $this->iid, ['merge_commit_message' => $message]);

        return static::fromArray($this->getClient(), $this->project, $data);
    }

    public function merged(): MergeRequest
    {
        return $this->update(['state_event' => 'merge']);
    }

    public function addComment(string $comment): Note
    {
        $data = $this->client->mergeRequests()->addComment($this->project->id, $this->iid, $comment);

        return Note::fromArray($this->getClient(), $this, $data);
    }

    /**
     * @return Note[]
     */
    public function showComments(): array
    {
        $notes = [];
        $data  = $this->client->mergeRequests()->showComments($this->project->id, $this->iid);

        foreach ($data as $note) {
            $notes[] = Note::fromArray($this->getClient(), $this, $note);
        }

        return $notes;
    }

    public function isClosed(): bool
    {
        return in_array($this->state, ['closed', 'merged'], true);
    }

    public function changes(): MergeRequest
    {
        $data = $this->client->mergeRequests()->changes($this->project->id, $this->iid);

        return static::fromArray($this->getClient(), $this->project, $data);
    }
}
