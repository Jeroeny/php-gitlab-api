<?php

declare(strict_types=1);

namespace Gitlab\Api;

final class IssueLinks extends ApiBase
{
    /**
     * @return mixed
     */
    public function all(int $project_id, int $issue_iid)
    {
        return $this->get($this->getProjectPath($project_id, 'issues/' . $this->encodePath((string)$issue_iid)) . '/links');
    }

    /**
     * @return mixed
     */
    public function create(int $source_project_id, int $source_issue_iid, int $target_project_id, int $target_issue_iid)
    {
        return $this->post($this->getProjectPath($source_project_id, 'issues/' . $this->encodePath((string)$source_issue_iid) . '/links'), [
            'target_project_id' => $target_project_id,
            'target_issue_iid' => $target_issue_iid,
        ]);
    }

    /**
     * @return mixed
     */
    public function remove(int $project_id, int $issue_iid, int $issue_link_id)
    {
        return $this->delete($this->getProjectPath($project_id, 'issues/' . $this->encodePath((string)$issue_iid)) . '/links/' . $this->encodePath((string)$issue_link_id));
    }
}
