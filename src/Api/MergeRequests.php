<?php

declare(strict_types=1);

namespace Gitlab\Api;

use DateTimeInterface;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Exception\UndefinedOptionsException;
use Symfony\Component\OptionsResolver\Options;
use const E_USER_DEPRECATED;
use function array_filter;
use function count;
use function is_array;
use function sprintf;
use function trigger_error;

final class MergeRequests extends ApiBase
{
    public const STATE_ALL    = 'all';
    public const STATE_MERGED = 'merged';
    public const STATE_OPENED = 'opened';
    public const STATE_CLOSED = 'closed';

    /**
     * @param mixed[] $parameters {
     *
     * @return mixed
     *
     * @throws UndefinedOptionsException If an option name is undefined.
     * @throws InvalidOptionsException   If an option doesn't fulfill the specified validation rules.
     *
     * @var int[]              $iids           Return the request having the given iid.
     * @var string             $state          Return all merge requests or just those that are opened, closed, or
     *                                             merged.
     * @var string             $order_by       Return requests ordered by created_at or updated_at fields. Default
     *                                             is created_at.
     * @var string             $sort           Return requests sorted in asc or desc order. Default is desc.
     * @var string             $milestone      Return merge requests for a specific milestone.
     * @var string             $view           If simple, returns the iid, URL, title, description, and basic state
     *                                             of merge request.
     * @var string             $labels         Return merge requests matching a comma separated list of labels.
     * @var DateTimeInterface $created_after Return merge requests created after the given time (inclusive).
     * @var DateTimeInterface $created_before Return merge requests created before the given time (inclusive).
     * }
     */
    public function all(int $project_id, array $parameters = [])
    {
        $resolver           = $this->createOptionsResolver();
        $datetimeNormalizer = static function (Options $resolver, DateTimeInterface $value) {
            return $value->format('c');
        };
        $resolver->setDefined('iids')
            ->setAllowedTypes('iids', 'array')
            ->setAllowedValues('iids', static function (array $value) {
                return count($value) === count(array_filter($value, 'is_int'));
            });
        $resolver->setDefined('state')
            ->setAllowedValues('state', ['all', 'opened', 'merged', 'closed']);
        $resolver->setDefined('order_by')
            ->setAllowedValues('order_by', ['created_at', 'updated_at']);
        $resolver->setDefined('sort')
            ->setAllowedValues('sort', ['asc', 'desc']);
        $resolver->setDefined('milestone');
        $resolver->setDefined('view')
            ->setAllowedValues('view', ['simple']);
        $resolver->setDefined('labels');
        $resolver->setDefined('created_after')
            ->setAllowedTypes('created_after', DateTimeInterface::class)
            ->setNormalizer('created_after', $datetimeNormalizer);
        $resolver->setDefined('created_before')
            ->setAllowedTypes('created_before', DateTimeInterface::class)
            ->setNormalizer('created_before', $datetimeNormalizer);

        $resolver->setDefined('updated_after')
            ->setAllowedTypes('updated_after', DateTimeInterface::class)
            ->setNormalizer('updated_after', $datetimeNormalizer);
        $resolver->setDefined('updated_before')
            ->setAllowedTypes('updated_before', DateTimeInterface::class)
            ->setNormalizer('updated_before', $datetimeNormalizer);

        $resolver->setDefined('scope')
            ->setAllowedValues('scope', ['created_by_me', 'assigned_to_me', 'all']);
        $resolver->setDefined('author_id')
            ->setAllowedTypes('author_id', 'integer');

        $resolver->setDefined('assignee_id')
            ->setAllowedTypes('assignee_id', 'integer');

        $resolver->setDefined('search');
        $resolver->setDefined('source_branch');
        $resolver->setDefined('target_branch');

        return $this->get($this->getProjectPath($project_id, 'merge_requests'), $resolver->resolve($parameters));
    }

    /**
     * @return mixed
     */
    public function show(int $project_id, int $mr_id)
    {
        return $this->get($this->getProjectPath($project_id, 'merge_requests/' . $this->encodePath((string)$mr_id)));
    }

    /**
     * @return mixed
     */
    public function create(int $project_id, string $source, string $target, string $title, ?int $assignee = null, ?int $target_project_id = null, ?string $description = null)
    {
        return $this->post($this->getProjectPath($project_id, 'merge_requests'), [
            'source_branch' => $source,
            'target_branch' => $target,
            'title' => $title,
            'assignee_id' => $assignee,
            'target_project_id' => $target_project_id,
            'description' => $description,
        ]);
    }

    /**
     * @param mixed[] $params
     *
     * @return mixed
     */
    public function update(int $project_id, int $mr_id, array $params)
    {
        return $this->put($this->getProjectPath($project_id, 'merge_requests/' . $this->encodePath((string)$mr_id)), $params);
    }

    /**
     * @param string|mixed[]|null $message
     *
     * @return mixed
     */
    public function merge(int $project_id, int $mr_id, $message = null)
    {
        if (is_array($message)) {
            $params = $message;
        } else {
            $params = ['merge_commit_message' => $message];
        }

        return $this->put($this->getProjectPath($project_id, 'merge_requests/' . $this->encodePath((string)$mr_id) . '/merge'), $params);
    }

    /**
     * @return mixed
     */
    public function showNotes(int $project_id, int $mr_id)
    {
        return $this->get($this->getProjectPath($project_id, 'merge_requests/' . $this->encodePath((string)$mr_id) . '/notes'));
    }

    /**
     * @return mixed
     */
    public function addNote(int $project_id, int $mr_id, string $note)
    {
        return $this->post($this->getProjectPath($project_id, 'merge_requests/' . $this->encodePath((string)$mr_id) . '/notes'), ['body' => $note]);
    }

    /**
     * @return mixed
     */
    public function removeNote(int $projectId, int $mrId, int $noteId)
    {
        return $this->delete($this->getProjectPath($projectId, 'merge_requests/' . $this->encodePath((string)$mrId) . '/notes/' . $this->encodePath((string)$noteId)));
    }

    /**
     * @return mixed
     */
    public function showComments(int $project_id, int $mr_id)
    {
        @trigger_error(sprintf('The %s() method is deprecated since version 9.1 and will be removed in 10.0. Use the showNotes() method instead.', __METHOD__), E_USER_DEPRECATED);

        return $this->showNotes($project_id, $mr_id);
    }

    /**
     * @return mixed
     */
    public function addComment(int $project_id, int $mr_id, string $note)
    {
        @trigger_error(sprintf('The %s() method is deprecated since version 9.1 and will be removed in 10.0. Use the addNote() method instead.', __METHOD__), E_USER_DEPRECATED);

        return $this->addNote($project_id, $mr_id, $note);
    }

    /**
     * @return mixed
     */
    public function showDiscussions(int $project_id, int $mr_iid)
    {
        return $this->get($this->getProjectPath($project_id, 'merge_requests/' . $this->encodePath((string)$mr_iid)) . '/discussions');
    }

    /**
     * @return mixed
     */
    public function showDiscussion(int $project_id, int $mr_iid, string $discussion_id)
    {
        return $this->get($this->getProjectPath($project_id, 'merge_requests/' . $this->encodePath((string)$mr_iid)) . '/discussions/' . $this->encodePath((string)$discussion_id));
    }

    /**
     * @param mixed[] $params
     *
     * @return mixed
     */
    public function addDiscussion(int $project_id, int $mr_iid, array $params)
    {
        return $this->post($this->getProjectPath($project_id, 'merge_requests/' . $this->encodePath((string)$mr_iid) . '/discussions'), $params);
    }

    /**
     * @return mixed
     */
    public function resolveDiscussion(int $project_id, int $mr_iid, string $discussion_id, bool $resolved = true)
    {
        return $this->put($this->getProjectPath($project_id, 'merge_requests/' . $this->encodePath((string)$mr_iid) . '/discussions/' . $this->encodePath((string)$discussion_id)), ['resolved' => $resolved]);
    }

    /**
     * @param string|string[] $body
     *
     * @return mixed
     */
    public function addDiscussionNote(int $project_id, int $mr_iid, string $discussion_id, $body)
    {
        // backwards compatibility
        if (is_array($body)) {
            $params = $body;
        } else {
            $params = ['body' => $body];
        }

        return $this->post($this->getProjectPath($project_id, 'merge_requests/' . $this->encodePath((string)$mr_iid) . '/discussions/' . $this->encodePath((string)$discussion_id) . '/notes'), $params);
    }

    /**
     * @param mixed[] $params
     *
     * @return mixed
     */
    public function updateDiscussionNote(int $project_id, int $mr_iid, string $discussion_id, int $note_id, array $params)
    {
        return $this->put($this->getProjectPath($project_id, 'merge_requests/' . $this->encodePath((string)$mr_iid) . '/discussions/' . $this->encodePath((string)$discussion_id) . '/notes/' . $this->encodePath((string)$note_id)), $params);
    }

    /**
     * @return mixed
     */
    public function removeDiscussionNote(int $project_id, int $mr_iid, string $discussion_id, int $note_id)
    {
        return $this->delete($this->getProjectPath($project_id, 'merge_requests/' . $this->encodePath((string)$mr_iid) . '/discussions/' . $this->encodePath((string)$discussion_id) . '/notes/' . $this->encodePath((string)$note_id)));
    }

    /**
     * @return mixed
     */
    public function changes(int $project_id, int $mr_id)
    {
        return $this->get($this->getProjectPath($project_id, 'merge_requests/' . $this->encodePath((string)$mr_id) . '/changes'));
    }

    /**
     * @return mixed
     */
    public function commits(int $project_id, int $mr_id)
    {
        return $this->get($this->getProjectPath($project_id, 'merge_requests/' . $this->encodePath((string)$mr_id) . '/commits'));
    }

    /**
     * @return mixed
     */
    public function closesIssues(int $project_id, int $mr_id)
    {
        return $this->get($this->getProjectPath($project_id, 'merge_requests/' . $this->encodePath((string)$mr_id) . '/closes_issues'));
    }

    /**
     * @return mixed
     */
    public function approvals(int $project_id, int $mr_iid)
    {
        return $this->get($this->getProjectPath($project_id, 'merge_requests/' . $this->encodePath((string)$mr_iid) . '/approvals'));
    }

    /**
     * @return mixed
     */
    public function approve(int $project_id, int $mr_iid)
    {
        return $this->post($this->getProjectPath($project_id, 'merge_requests/' . $this->encodePath((string)$mr_iid) . '/approve'));
    }

    /**
     * @return mixed
     */
    public function unapprove(int $project_id, int $mr_iid)
    {
        return $this->post($this->getProjectPath($project_id, 'merge_requests/' . $this->encodePath((string)$mr_iid) . '/unapprove'));
    }

    /**
     * @return mixed
     */
    public function awardEmoji(int $project_id, int $mr_iid)
    {
        return $this->get($this->getProjectPath($project_id, 'merge_requests/' . $this->encodePath((string)$mr_iid) . '/award_emoji'));
    }
}
