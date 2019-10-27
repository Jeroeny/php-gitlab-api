<?php

declare(strict_types=1);

namespace Gitlab\Api;

final class Tags extends ApiBase
{
    /**
     * @return mixed
     */
    public function all(int $project_id)
    {
        return $this->get($this->getProjectPath($project_id, 'repository/tags'));
    }

    /**
     * @return mixed
     */
    public function show(int $project_id, string $tag_name)
    {
        return $this->get($this->getProjectPath($project_id, 'repository/tags/' . $this->encodePath($tag_name)));
    }

    /**
     * @param mixed[] $params
     *
     * @return mixed
     */
    public function create(int $project_id, array $params = [])
    {
        return $this->post($this->getProjectPath($project_id, 'repository/tags'), $params);
    }

    /**
     * @return mixed
     */
    public function remove(int $project_id, string $tag_name)
    {
        return $this->delete($this->getProjectPath($project_id, 'repository/tags/' . $this->encodePath($tag_name)));
    }

    /**
     * @param mixed[] $params
     *
     * @return mixed
     */
    public function createRelease(int $project_id, string $tag_name, array $params = [])
    {
        return $this->post($this->getProjectPath($project_id, 'repository/tags/' . $this->encodePath($tag_name) . '/release'), $params);
    }

    /**
     * @param mixed[] $params
     *
     * @return mixed
     */
    public function updateRelease(int $project_id, string $tag_name, array $params = [])
    {
        return $this->put($this->getProjectPath($project_id, 'repository/tags/' . $this->encodePath($tag_name) . '/release'), $params);
    }
}
