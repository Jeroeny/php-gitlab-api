<?php

declare(strict_types=1);

namespace Gitlab\Api;

final class Schedules extends ApiBase
{
    /**
     * @param mixed[] $params
     *
     * @return mixed
     */
    public function create(int $project_id, array $params)
    {
        return $this->post($this->getProjectPath($project_id, 'pipeline_schedules'), $params);
    }

    /**
     * @return mixed
     */
    public function show(int $project_id, int $schedule_id)
    {
        return $this->get($this->getProjectPath($project_id, 'pipeline_schedules/' . $this->encodePath((string)$schedule_id)));
    }

    /**
     * @return mixed
     */
    public function showAll(int $project_id)
    {
        return $this->get($this->getProjectPath($project_id, 'pipeline_schedules'));
    }

    /**
     * @param mixed[] $params
     *
     * @return mixed
     */
    public function update(int $project_id, int $schedule_id, array $params)
    {
        return $this->put($this->getProjectPath($project_id, 'pipeline_schedules/' . $this->encodePath((string)$schedule_id)), $params);
    }

    /**
     * @return mixed
     */
    public function remove(int $project_id, int $schedule_id)
    {
        return $this->delete($this->getProjectPath($project_id, 'pipeline_schedules/' . $this->encodePath((string)$schedule_id)));
    }
}
