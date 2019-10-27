<?php

declare(strict_types=1);

namespace Gitlab\Api;

use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use function array_filter;
use function count;

final class Groups extends ApiBase
{
    /**
     * @param mixed[] $parameters all_available Show all the groups you have access to.
     *                            search        Return list of authorized groups matching the search criteria.
     *                            order_by      Order groups by name or path. Default is name.
     *                            sort          Order groups in asc or desc order. Default is asc.
     *                            statistics    Include group statistics (admins only).
     *                            owned         Limit by groups owned by the current user.
     *                            skip_groups   Skip the group IDs passes.
     *
     * @return mixed
     */
    public function all(array $parameters = [])
    {
        $resolver = $this->getGroupSearchResolver();

        return $this->get('groups', $resolver->resolve($parameters));
    }

    /**
     * @return mixed
     */
    public function show(int $id)
    {
        return $this->get('groups/' . $this->encodePath((string)(string)$id));
    }

    /**
     * @return mixed
     */
    public function create(
        string $name,
        string $path,
        ?string $description = null,
        string $visibility = 'private',
        ?bool $lfs_enabled = null,
        ?bool $request_access_enabled = null,
        ?int $parent_id = null,
        ?int $shared_runners_minutes_limit = null
    ) {
        $params = [
            'name' => $name,
            'path' => $path,
            'description' => $description,
            'visibility' => $visibility,
            'lfs_enabled' => $lfs_enabled,
            'request_access_enabled' => $request_access_enabled,
            'parent_id' => $parent_id,
            'shared_runners_minutes_limit' => $shared_runners_minutes_limit,
        ];

        return $this->post('groups', array_filter($params, 'strlen'));
    }

    /**
     * @param mixed[] $params
     *
     * @return mixed
     */
    public function update(int $id, array $params)
    {
        return $this->put('groups/' . $this->encodePath((string)$id), $params);
    }

    /**
     * @return mixed
     */
    public function remove(int $group_id)
    {
        return $this->delete('groups/' . $this->encodePath((string)$group_id));
    }

    /**
     * @return mixed
     */
    public function transfer(int $group_id, int $project_id)
    {
        return $this->post('groups/' . $this->encodePath((string)$group_id) . '/projects/' . $this->encodePath((string)$project_id));
    }

    /**
     * @param string[] $parameters
     *
     * @return mixed
     */
    public function allMembers(int $id, array $parameters = [])
    {
        $resolver = $this->createOptionsResolver();
        $resolver->setDefined('query');

        return $this->get('groups/' . $this->encodePath((string)$id) . '/members/all', $resolver->resolve($parameters));
    }

    /**
     * @param string[] $parameters
     *
     * @return mixed
     */
    public function members(int $id, array $parameters = [])
    {
        $resolver = $this->createOptionsResolver();
        $resolver->setDefined('query');

        return $this->get('groups/' . $this->encodePath((string)$id) . '/members', $resolver->resolve($parameters));
    }

    /**
     * @return mixed
     */
    public function addMember(int $group_id, int $user_id, int $access_level)
    {
        return $this->post('groups/' . $this->encodePath((string)$group_id) . '/members', [
            'user_id' => $user_id,
            'access_level' => $access_level,
        ]);
    }

    /**
     * @return mixed
     */
    public function saveMember(int $group_id, int $user_id, int $access_level)
    {
        return $this->put('groups/' . $this->encodePath((string)$group_id) . '/members/' . $this->encodePath((string)$user_id), ['access_level' => $access_level]);
    }

    /**
     * @return mixed
     */
    public function removeMember(int $group_id, int $user_id)
    {
        return $this->delete('groups/' . $this->encodePath((string)$group_id) . '/members/' . $this->encodePath((string)$user_id));
    }

    /**
     * @param mixed[] $parameters
     * archived                    Limit by archived status.
     * visibility                  Limit by visibility public, internal, or private.
     * order_by                    Return projects ordered by id, name, path, created_at, updated_at, or last_activity_at fields.
     * Default is created_at.
     * sort                        Return projects sorted in asc or desc order. Default is desc.
     * search                      Return list of authorized projects matching the search criteria.
     * simple                      Return only the ID, URL, name, and path of each project.
     * owned                       Limit by projects owned by the current user.
     * starred                     Limit by projects starred by the current user.
     * with_issues_enabled         Limit by projects with issues feature enabled. Default is false.
     * with_merge_requests_enabled Limit by projects with merge requests feature enabled. Default is false.
     * with_shared                 Include projects shared to this group. Default is true.
     * include_subgroups           Include projects in subgroups of this group. Default is false.
     * with_custom_attributes      Include custom attributes in response (admins only).
     *
     * @return mixed
     */
    public function projects(int $id, array $parameters = [])
    {
        $resolver          = $this->createOptionsResolver();
        $booleanNormalizer = static function (Options $resolver, $value): string {
            return $value ? 'true' : 'false';
        };

        $resolver
            ->setDefined('archived')
            ->setAllowedTypes('archived', 'bool')
            ->setNormalizer('archived', $booleanNormalizer);
        $resolver
            ->setDefined('visibility')
            ->setAllowedValues('visibility', ['public', 'internal', 'private']);
        $resolver
            ->setDefined('order_by')
            ->setAllowedValues('order_by', ['id', 'name', 'path', 'created_at', 'updated_at', 'last_activity_at']);
        $resolver
            ->setDefined('sort')
            ->setAllowedValues('sort', ['asc', 'desc']);
        $resolver->setDefined('search');
        $resolver
            ->setDefined('simple')
            ->setAllowedTypes('simple', 'bool')
            ->setNormalizer('simple', $booleanNormalizer);
        $resolver
            ->setDefined('owned')
            ->setAllowedTypes('owned', 'bool')
            ->setNormalizer('owned', $booleanNormalizer);
        $resolver
            ->setDefined('starred')
            ->setAllowedTypes('starred', 'bool')
            ->setNormalizer('starred', $booleanNormalizer);
        $resolver
            ->setDefined('with_issues_enabled')
            ->setAllowedTypes('with_issues_enabled', 'bool')
            ->setNormalizer('with_issues_enabled', $booleanNormalizer);
        $resolver
            ->setDefined('with_merge_requests_enabled')
            ->setAllowedTypes('with_merge_requests_enabled', 'bool')
            ->setNormalizer('with_merge_requests_enabled', $booleanNormalizer);
        $resolver
            ->setDefined('with_shared')
            ->setAllowedTypes('with_shared', 'bool')
            ->setNormalizer('with_shared', $booleanNormalizer);
        $resolver
            ->setDefined('include_subgroups')
            ->setAllowedTypes('include_subgroups', 'bool')
            ->setNormalizer('include_subgroups', $booleanNormalizer);
        $resolver
            ->setDefined('with_custom_attributes')
            ->setAllowedTypes('with_custom_attributes', 'bool')
            ->setNormalizer('with_custom_attributes', $booleanNormalizer);

        return $this->get('groups/' . $this->encodePath((string)$id) . '/projects', $resolver->resolve($parameters));
    }

    /**
     * @param mixed[] $parameters all_available Show all the groups you have access to.
     *                            search        Return list of authorized groups matching the search criteria.
     *                            order_by      Order groups by name or path. Default is name.
     *                            sort          Order groups in asc or desc order. Default is asc.
     *                            statistics    Include group statistics (admins only).
     *                            owned         Limit by groups owned by the current user.
     *                            skip_groups   Skip the group IDs passes.
     *
     * @return mixed
     */
    public function subgroups(int $group_id, array $parameters = [])
    {
        $resolver = $this->getGroupSearchResolver();

        return $this->get('groups/' . $this->encodePath((string)$group_id) . '/subgroups', $resolver->resolve($parameters));
    }

    /**
     * @param mixed[] $parameters
     *
     * @return mixed
     */
    public function labels(int $group_id, array $parameters = [])
    {
        $resolver = $this->createOptionsResolver();

        return $this->get('groups/' . $this->encodePath((string)$group_id) . '/labels', $resolver->resolve($parameters));
    }

    /**
     * @param mixed[] $params
     *
     * @return mixed
     */
    public function addLabel(int $group_id, array $params)
    {
        return $this->post('groups/' . $this->encodePath((string)$group_id) . '/labels', $params);
    }

    /**
     * @param mixed[] $params
     *
     * @return mixed
     */
    public function updateLabel(int $group_id, array $params)
    {
        return $this->put('groups/' . $this->encodePath((string)$group_id) . '/labels', $params);
    }

    /**
     * @return mixed
     */
    public function removeLabel(int $group_id, string $name)
    {
        return $this->delete('groups/' . $this->encodePath((string)$group_id) . '/labels', ['name' => $name]);
    }

    /**
     * @param mixed[] $parameters
     *
     * @return mixed
     */
    public function variables(int $group_id, array $parameters = [])
    {
        $resolver = $this->createOptionsResolver();

        return $this->get($this->getGroupPath($group_id, 'variables'), $resolver->resolve($parameters));
    }

    /**
     * @return mixed
     */
    public function variable(int $group_id, string $key)
    {
        return $this->get($this->getGroupPath($group_id, 'variables/' . $this->encodePath((string)$key)));
    }

    /**
     * @return mixed
     */
    public function addVariable(int $group_id, string $key, string $value, ?bool $protected = null)
    {
        $payload = [
            'key' => $key,
            'value' => $value,
        ];

        if ($protected) {
            $payload['protected'] = $protected;
        }

        return $this->post($this->getGroupPath($group_id, 'variables'), $payload);
    }

    /**
     * @return mixed
     */
    public function updateVariable(int $group_id, string $key, string $value, ?bool $protected = null)
    {
        $payload = ['value' => $value];

        if ($protected) {
            $payload['protected'] = $protected;
        }

        return $this->put($this->getGroupPath($group_id, 'variables/' . $this->encodePath((string)$key)), $payload);
    }

    /**
     * @return mixed
     */
    public function removeVariable(int $group_id, string $key)
    {
        return $this->delete($this->getGroupPath($group_id, 'variables/' . $this->encodePath((string)$key)));
    }

    private function getGroupSearchResolver(): OptionsResolver
    {
        $resolver          = $this->createOptionsResolver();
        $booleanNormalizer = static function (Options $resolver, $value) {
            return $value ? 'true' : 'false';
        };

        $resolver->setDefined('skip_groups')
            ->setAllowedTypes('skip_groups', 'array')
            ->setAllowedValues('skip_groups', static function (array $value) {
                return count($value) === count(array_filter($value, 'is_int'));
            });
        $resolver->setDefined('all_available')
            ->setAllowedTypes('all_available', 'bool')
            ->setNormalizer('all_available', $booleanNormalizer);
        $resolver->setDefined('search');
        $resolver->setDefined('order_by')
            ->setAllowedValues('order_by', ['name', 'path']);
        $resolver->setDefined('sort')
            ->setAllowedValues('sort', ['asc', 'desc']);
        $resolver->setDefined('statistics')
            ->setAllowedTypes('statistics', 'bool')
            ->setNormalizer('statistics', $booleanNormalizer);
        $resolver->setDefined('owned')
            ->setAllowedTypes('owned', 'bool')
            ->setNormalizer('owned', $booleanNormalizer);

        return $resolver;
    }
}
