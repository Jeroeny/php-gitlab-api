<?php

declare(strict_types=1);

namespace Gitlab\Api;

final class IssueBoards extends ApiBase
{
    /**
     * @param mixed[] $parameters
     *
     * @return mixed
     */
    public function all(?int $project_id = null, array $parameters = [])
    {
        $resolver = $this->createOptionsResolver();

        $path = $project_id === null ? 'boards' : $this->getProjectPath($project_id, 'boards');

        return $this->get($path, $resolver->resolve($parameters));
    }

    /**
     * @return mixed
     */
    public function show(int $project_id, int $board_id)
    {
        return $this->get($this->getProjectPath($project_id, 'boards/' . $this->encodePath((string)$board_id)));
    }

    /**
     * @param mixed[] $params
     *
     * @return mixed
     */
    public function create(int $project_id, array $params)
    {
        return $this->post($this->getProjectPath($project_id, 'boards'), $params);
    }

    /**
     * @param mixed[] $params
     *
     * @return mixed
     */
    public function update(int $project_id, int $board_id, array $params)
    {
        return $this->put($this->getProjectPath($project_id, 'boards/' . $this->encodePath((string)$board_id)), $params);
    }

    /**
     * @return mixed
     */
    public function remove(int $project_id, int $board_id)
    {
        return $this->delete($this->getProjectPath($project_id, 'boards/' . $this->encodePath((string)$board_id)));
    }

    /**
     * @return mixed
     */
    public function allLists(int $project_id, int $board_id)
    {
        return $this->get($this->getProjectPath($project_id, 'boards/' . $this->encodePath((string)$board_id) . '/lists'));
    }

    /**
     * @return mixed
     */
    public function showList(int $project_id, int $board_id, int $list_id)
    {
        return $this->get($this->getProjectPath($project_id, 'boards/' . $this->encodePath((string)$board_id) . '/lists/' . $this->encodePath((string)$list_id)));
    }

    /**
     * @return mixed
     */
    public function createList(int $project_id, int $board_id, int $label_id)
    {
        $params = ['label_id' => $label_id];

        return $this->post($this->getProjectPath($project_id, 'boards/' . $this->encodePath((string)$board_id) . '/lists'), $params);
    }

    /**
     * @return mixed
     */
    public function updateList(int $project_id, int $board_id, int $list_id, int $position)
    {
        $params = ['position' => $position];

        return $this->put($this->getProjectPath($project_id, 'boards/' . $this->encodePath((string)$board_id) . '/lists/' . $this->encodePath((string)$list_id)), $params);
    }

    /**
     * @return mixed
     */
    public function deleteList(int $project_id, int $board_id, int $list_id)
    {
        return $this->delete($this->getProjectPath($project_id, 'boards/' . $this->encodePath((string)$board_id) . '/lists/' . $this->encodePath((string)$list_id)));
    }
}
