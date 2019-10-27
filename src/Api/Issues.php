<?php

declare(strict_types=1);

namespace Gitlab\Api;

use function array_filter;
use function count;
use function is_array;

final class Issues extends ApiBase
{
    /**
     * @param mixed[] $parameters (
     *
     *     @var string $state     Return all issues or just those that are opened or closed.
     *     @var string $labels    Comma-separated list of label names, issues must have all labels to be returned.
     *                            No+Label lists all issues with no labels.
     *     @var string $milestone The milestone title.
     *     @var string scope      Return issues for the given scope: created-by-me, assigned-to-me or all. Defaults to created-by-me
     *     @var int[]  $iids      Return only the issues having the given iid.
     *     @var string $order_by  Return requests ordered by created_at or updated_at fields. Default is created_at.
     *     @var string $sort      Return requests sorted in asc or desc order. Default is desc.
     *     @var string $search    Search issues against their title and description.
     * )
     *
     * @return mixed
     */
    public function all(?int $project_id = null, array $parameters = [])
    {
        $resolver = $this->createOptionsResolver();

        $resolver->setDefined('state')
            ->setAllowedValues('state', ['opened', 'closed']);
        $resolver->setDefined('labels');
        $resolver->setDefined('milestone');
        $resolver->setDefined('iids')
            ->setAllowedTypes('iids', 'array')
            ->setAllowedValues('iids', static function (array $value) {
                return count($value) === count(array_filter($value, 'is_int'));
            });
        $resolver->setDefined('scope')
            ->setAllowedValues('scope', ['created-by-me', 'assigned-to-me', 'all']);
        $resolver->setDefined('order_by')
            ->setAllowedValues('order_by', ['created_at', 'updated_at']);
        $resolver->setDefined('sort')
            ->setAllowedValues('sort', ['asc', 'desc']);
        $resolver->setDefined('search');
        $resolver->setDefined('assignee_id')
            ->setAllowedTypes('assignee_id', 'integer');

        $path = $project_id === null ? 'issues' : $this->getProjectPath($project_id, 'issues');

        return $this->get($path, $resolver->resolve($parameters));
    }

    /**
     * @return mixed
     */
    public function show(int $project_id, int $issue_iid)
    {
        return $this->get($this->getProjectPath($project_id, 'issues/' . $this->encodePath((string)$issue_iid)));
    }

    /**
     * @param mixed[] $params
     *
     * @return mixed
     */
    public function create(int $project_id, array $params)
    {
        return $this->post($this->getProjectPath($project_id, 'issues'), $params);
    }

    /**
     * @param mixed[] $params
     *
     * @return mixed
     */
    public function update(int $project_id, int $issue_iid, array $params)
    {
        return $this->put($this->getProjectPath($project_id, 'issues/' . $this->encodePath((string)$issue_iid)), $params);
    }

    /**
     * @return mixed
     */
    public function move(int $project_id, int $issue_iid, int $to_project_id)
    {
        return $this->post($this->getProjectPath($project_id, 'issues/' . $this->encodePath((string)$issue_iid)) . '/move', ['to_project_id' => $to_project_id]);
    }

    /**
     * @return mixed
     */
    public function remove(int $project_id, int $issue_iid)
    {
        return $this->delete($this->getProjectPath($project_id, 'issues/' . $this->encodePath((string)$issue_iid)));
    }

    /**
     * @return mixed
     */
    public function showComments(int $project_id, int $issue_iid)
    {
        return $this->get($this->getProjectPath($project_id, 'issues/' . $this->encodePath((string)$issue_iid)) . '/notes');
    }

    /**
     * @return mixed
     */
    public function showComment(int $project_id, int $issue_iid, int $note_id)
    {
        return $this->get($this->getProjectPath($project_id, 'issues/' . $this->encodePath((string)$issue_iid)) . '/notes/' . $this->encodePath((string)$note_id));
    }

    /**
     * @param string|mixed[] $body
     *
     * @return mixed
     */
    public function addComment(int $project_id, int $issue_iid, $body)
    {
        // backwards compatibility
        if (is_array($body)) {
            $params = $body;
        } else {
            $params = ['body' => $body];
        }

        return $this->post($this->getProjectPath($project_id, 'issues/' . $this->encodePath((string)$issue_iid) . '/notes'), $params);
    }

    /**
     * @return mixed
     */
    public function updateComment(int $project_id, int $issue_iid, int $note_id, string $body)
    {
        return $this->put($this->getProjectPath($project_id, 'issues/' . $this->encodePath((string)$issue_iid) . '/notes/' . $this->encodePath((string)$note_id)), ['body' => $body]);
    }

    /**
     * @return mixed
     */
    public function removeComment(int $project_id, int $issue_iid, int $note_id)
    {
        return $this->delete($this->getProjectPath($project_id, 'issues/' . $this->encodePath((string)$issue_iid) . '/notes/' . $this->encodePath((string)$note_id)));
    }

    /**
     * @return mixed
     */
    public function showDiscussions(int $project_id, int $issue_iid)
    {
        return $this->get($this->getProjectPath($project_id, 'issues/' . $this->encodePath((string)$issue_iid)) . '/discussions');
    }

    /**
     * @return mixed
     */
    public function showDiscussion(int $project_id, int $issue_iid, string $discussion_id)
    {
        return $this->get($this->getProjectPath($project_id, 'issues/' . $this->encodePath((string)$issue_iid)) . '/discussions/' . $this->encodePath((string)$discussion_id));
    }

    /**
     * @param string|mixed[] $body
     *
     * @return mixed
     */
    public function addDiscussion(int $project_id, int $issue_iid, $body)
    {
        // backwards compatibility
        if (is_array($body)) {
            $params = $body;
        } else {
            $params = ['body' => $body];
        }

        return $this->post($this->getProjectPath($project_id, 'issues/' . $this->encodePath((string)$issue_iid) . '/discussions'), $params);
    }

    /**
     * @param string|mixed[] $body
     *
     * @return mixed
     */
    public function addDiscussionNote(int $project_id, int $issue_iid, string $discussion_id, $body)
    {
        // backwards compatibility
        if (is_array($body)) {
            $params = $body;
        } else {
            $params = ['body' => $body];
        }

        return $this->post($this->getProjectPath($project_id, 'issues/' . $this->encodePath((string)$issue_iid) . '/discussions/' . $this->encodePath((string)$discussion_id) . '/notes'), $params);
    }

    /**
     * @return mixed
     */
    public function updateDiscussionNote(int $project_id, int $issue_iid, string $discussion_id, int $note_id, string $body)
    {
        return $this->put($this->getProjectPath($project_id, 'issues/' . $this->encodePath((string)$issue_iid) . '/discussions/' . $this->encodePath((string)$discussion_id) . '/notes/' . $this->encodePath((string)$note_id)), ['body' => $body]);
    }

    /**
     * @return mixed
     */
    public function removeDiscussionNote(int $project_id, int $issue_iid, string $discussion_id, int $note_id)
    {
        return $this->delete($this->getProjectPath($project_id, 'issues/' . $this->encodePath((string)$issue_iid) . '/discussions/' . $this->encodePath((string)$discussion_id) . '/notes/' . $this->encodePath((string)$note_id)));
    }

    /**
     * @return mixed
     */
    public function setTimeEstimate(int $project_id, int $issue_iid, string $duration)
    {
        return $this->post($this->getProjectPath($project_id, 'issues/' . $this->encodePath((string)$issue_iid) . '/time_estimate'), ['duration' => $duration]);
    }

    /**
     * @return mixed
     */
    public function resetTimeEstimate(int $project_id, int $issue_iid)
    {
        return $this->post($this->getProjectPath($project_id, 'issues/' . $this->encodePath((string)$issue_iid) . '/reset_time_estimate'));
    }

    /**
     * @return mixed
     */
    public function addSpentTime(int $project_id, int $issue_iid, string $duration)
    {
        return $this->post($this->getProjectPath($project_id, 'issues/' . $this->encodePath((string)$issue_iid) . '/add_spent_time'), ['duration' => $duration]);
    }

    /**
     * @return mixed
     */
    public function resetSpentTime(int $project_id, int $issue_iid)
    {
        return $this->post($this->getProjectPath($project_id, 'issues/' . $this->encodePath((string)$issue_iid) . '/reset_spent_time'));
    }

    /**
     * @return mixed
     */
    public function getTimeStats(int $project_id, int $issue_iid)
    {
        return $this->get($this->getProjectPath($project_id, 'issues/' . $this->encodePath((string)$issue_iid) . '/time_stats'));
    }

    /**
     * @return mixed
     */
    public function awardEmoji(int $project_id, int $issue_iid)
    {
        return $this->get($this->getProjectPath($project_id, 'issues/' . $this->encodePath((string)$issue_iid) . '/award_emoji'));
    }

    /**
     * @return mixed
     */
    public function closedByMergeRequests(int $project_id, int $issue_iid)
    {
        return $this->get($this->getProjectPath($project_id, 'issues/' . $this->encodePath((string)$issue_iid)) . '/closed_by');
    }
}
