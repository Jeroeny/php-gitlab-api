<?php

declare(strict_types=1);

namespace Gitlab\Api;

final class GroupsBoards extends ApiBase
{
    /**
     * @param mixed[] $parameters
     *
     * @return mixed
     */
    public function all(?int $group_id = null, array $parameters = [])
    {
        $resolver = $this->createOptionsResolver();

        $path = $group_id === null ? 'boards' : $this->getGroupPath($group_id, 'boards');

        return $this->get($path, $resolver->resolve($parameters));
    }

    /**
     * @return mixed
     */
    public function show(int $group_id, int $board_id)
    {
        return $this->get($this->getGroupPath($group_id, 'boards/' . $this->encodePath((string)$board_id)));
    }

    /**
     * @param mixed[] $params
     *
     * @return mixed
     */
    public function create(int $group_id, array $params)
    {
        return $this->post($this->getGroupPath($group_id, 'boards'), $params);
    }

    /**
     * @param mixed[] $params
     *
     * @return mixed
     */
    public function update(int $group_id, int $board_id, array $params)
    {
        return $this->put($this->getGroupPath($group_id, 'boards/' . $this->encodePath((string)$board_id)), $params);
    }

    /**
     * @return mixed
     */
    public function remove(int $group_id, int $board_id)
    {
        return $this->delete($this->getGroupPath($group_id, 'boards/' . $this->encodePath((string)$board_id)));
    }

    /**
     * @return mixed
     */
    public function allLists(int $group_id, int $board_id)
    {
        return $this->get($this->getGroupPath($group_id, 'boards/' . $this->encodePath((string)$board_id) . '/lists'));
    }

    /**
     * @return mixed
     */
    public function showList(int $group_id, int $board_id, int $list_id)
    {
        return $this->get($this->getGroupPath($group_id, 'boards/' . $this->encodePath((string)$board_id) . '/lists/' . $this->encodePath((string)$list_id)));
    }

    /**
     * @return mixed
     */
    public function createList(int $group_id, int $board_id, int $label_id)
    {
        $params = ['label_id' => $label_id];

        return $this->post($this->getGroupPath($group_id, 'boards/' . $this->encodePath((string)$board_id) . '/lists'), $params);
    }

    /**
     * @return mixed
     */
    public function updateList(int $group_id, int $board_id, int $list_id, int $position)
    {
        $params = ['position' => $position];

        return $this->put($this->getGroupPath($group_id, 'boards/' . $this->encodePath((string)$board_id) . '/lists/' . $this->encodePath((string)$list_id)), $params);
    }

    /**
     * @return mixed
     */
    public function deleteList(int $group_id, int $board_id, int $list_id)
    {
        return $this->delete($this->getGroupPath($group_id, 'boards/' . $this->encodePath((string)$board_id) . '/lists/' . $this->encodePath((string)$list_id)));
    }
}
