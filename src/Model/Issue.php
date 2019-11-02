<?php

declare(strict_types=1);

namespace Gitlab\Model;

use Gitlab\Client;
use function array_map;
use function in_array;
use function is_array;

/**
 * @property-read int $id
 * @property-read int $iid
 * @property-read int $project_id,
 * @property-read string $title
 * @property-read string $description
 * @property-read array $labels
 * @property-read bool $closed
 * @property-read string $updated_at
 * @property-read string $created_at
 * @property-read string $state
 * @property-read User $assignee
 * @property-read User $author
 * @property-read Milestone $milestone
 * @property-read Project $project
 */
final class Issue extends Model implements Noteable
{
    /** @var mixed[] */
    protected static $properties = [
        'id',
        'iid',
        'project_id',
        'title',
        'description',
        'labels',
        'milestone',
        'assignee',
        'author',
        'closed',
        'updated_at',
        'created_at',
        'project',
        'state',
    ];

    /**
     * @param mixed[] $data
     */
    public static function fromArray(?Client $client, Project $project, array $data): Issue
    {
        $issue = new static($project, $data['iid'], $client);

        if (isset($data['author'])) {
            $data['author'] = User::fromArray($client, $data['author']);
        }

        if (isset($data['assignee'])) {
            $data['assignee'] = User::fromArray($client, $data['assignee']);
        }

        return $issue->hydrate($data);
    }

    public function __construct(Project $project, ?int $iid = null, ?Client $client = null)
    {
        $this->setClient($client);
        $this->setData('project', $project);
        $this->setData('iid', $iid);
    }

    public function show(): Issue
    {
        $data = $this->client->issues()->show($this->project->id, $this->iid);

        return static::fromArray($this->getClient(), $this->project, $data);
    }

    /**
     * @param mixed[] $params
     */
    public function update(array $params): Issue
    {
        $data = $this->client->issues()->update($this->project->id, $this->iid, $params);

        return static::fromArray($this->getClient(), $this->project, $data);
    }

    public function move(Project $toProject): Issue
    {
        $data = $this->client->issues()->move($this->project->id, $this->iid, $toProject->id);

        return static::fromArray($this->getClient(), $toProject, $data);
    }

    public function close(?string $comment = null): Issue
    {
        if ($comment) {
            $this->addNote($comment);
        }

        return $this->update(['state_event' => 'close']);
    }

    public function open(): Issue
    {
        return $this->update(['state_event' => 'reopen']);
    }

    public function reopen(): Issue
    {
        return $this->open();
    }

    public function addNote(string $comment): Note
    {
        $data = $this->client->issues()->addComment($this->project->id, $this->iid, ['body' => $comment]);

        return Note::fromArray($this->getClient(), $this, $data);
    }

    /**
     * @return Note[]
     */
    public function showNotes(): array
    {
        $notes = [];
        $data  = $this->client->issues()->showComments($this->project->id, $this->iid);

        foreach ($data as $note) {
            $notes[] = Note::fromArray($this->getClient(), $this, $note);
        }

        return $notes;
    }

    public function isClosed(): bool
    {
        return $this->state === 'closed';
    }

    public function hasLabel(string $label): bool
    {
        return in_array($label, $this->labels, true);
    }

    /**
     * @return IssueLink[]
     */
    public function links(): array
    {
        $data = $this->client->issueLinks()->all($this->project->id, $this->iid);
        if (! is_array($data)) {
            return [];
        }

        $projects = $this->client->projects();

        return array_map(function ($data) use ($projects) {
            return IssueLink::fromArray(
                $this->client,
                Project::fromArray($this->client, $projects->show($data['project_id'])),
                $data
            );
        }, $data);
    }

    /**
     * @return Issue[]
     */
    public function addLink(Issue $target): array
    {
        $data = $this->client->issueLinks()->create($this->project->id, $this->iid, $target->project->id, $target->iid);
        if (! is_array($data)) {
            return [];
        }

        return [
            'source_issue' => static::fromArray($this->client, $this->project, $data['source_issue']),
            'target_issue' => static::fromArray($this->client, $target->project, $data['target_issue']),
        ];
    }

    /**
     * @return Issue[]
     */
    public function removeLink(int $issue_link_id): array
    {
        // The two related issues have the same link ID.
        $data = $this->client->issueLinks()->remove($this->project->id, $this->iid, $issue_link_id);
        if (! is_array($data)) {
            return [];
        }

        $targetProject = Project::fromArray(
            $this->client,
            $this->client->projects()->show($data['target_issue']['project_id'])
        );

        return [
            'source_issue' => static::fromArray($this->client, $this->project, $data['source_issue']),
            'target_issue' => static::fromArray($this->client, $targetProject, $data['target_issue']),
        ];
    }
}
