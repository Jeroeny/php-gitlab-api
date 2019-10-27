<?php

declare(strict_types=1);

namespace Gitlab\Api;

use function array_filter;
use function count;

final class GroupsMilestones extends ApiBase
{
    /**
     * @param mixed[] $parameters (
     *
     *     @var int[]  $iids   Return only the milestones having the given iids.
     *     @var string $state  Return only active or closed milestones.
     *     @var string $search Return only milestones with a title or description matching the provided string.
     * )
     *
     * @return mixed
     */
    public function all(int $group_id, array $parameters = [])
    {
        $resolver = $this->createOptionsResolver();
        $resolver->setDefined('iids')
            ->setAllowedTypes('iids', 'array')
            ->setAllowedValues('iids', static function (array $value) {
                return count($value) === count(array_filter($value, 'is_int'));
            });
        $resolver->setDefined('state')
            ->setAllowedValues('state', ['active', 'closed']);
        $resolver->setDefined('search');

        return $this->get($this->getGroupPath($group_id, 'milestones'), $resolver->resolve($parameters));
    }

    /**
     * @return mixed
     */
    public function show(int $group_id, int $milestone_id)
    {
        return $this->get($this->getGroupPath($group_id, 'milestones/' . $this->encodePath((string)$milestone_id)));
    }

    /**
     * @param mixed[] $params
     *
     * @return mixed
     */
    public function create(int $group_id, array $params)
    {
        return $this->post($this->getGroupPath($group_id, 'milestones'), $params);
    }

    /**
     * @param mixed[] $params
     *
     * @return mixed
     */
    public function update(int $group_id, int $milestone_id, array $params)
    {
        return $this->put($this->getGroupPath($group_id, 'milestones/' . $this->encodePath((string)$milestone_id)), $params);
    }

    /**
     * @return mixed
     */
    public function remove(int $group_id, int $milestone_id)
    {
        return $this->delete($this->getGroupPath($group_id, 'milestones/' . $this->encodePath((string)$milestone_id)));
    }

    /**
     * @return mixed
     */
    public function issues(int $group_id, int $milestone_id)
    {
        return $this->get($this->getGroupPath($group_id, 'milestones/' . $this->encodePath((string)$milestone_id) . '/issues'));
    }

    /**
     * @return mixed
     */
    public function mergeRequests(int $group_id, int $milestone_id)
    {
        return $this->get($this->getGroupPath($group_id, 'milestones/' . $this->encodePath((string)$milestone_id) . '/merge_requests'));
    }
}
