<?php

declare(strict_types=1);

namespace Gitlab\Api;

use DateTimeInterface;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Exception\UndefinedOptionsException;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use function urldecode;

final class Projects extends ApiBase
{
    /**
     * @param mixed[] $parameters {
     *
     * @return mixed
     *
     * @throws UndefinedOptionsException If an option name is undefined.
     * @throws InvalidOptionsException   If an option doesn't fulfill the specified validation rules.
     *
     * @var bool      $archived                    Limit by archived status.
     * @var string    $visibility                  Limit by visibility public, internal, or private.
     * @var string    $order_by                    Return projects ordered by id, name, path, created_at, updated_at,
     *                                              or last_activity_at fields. Default is created_at.
     * @var string    $sort                        Return projects sorted in asc or desc order. Default is desc.
     * @var string    $search                      Return list of projects matching the search criteria.
     * @var bool      $simple                      Return only the ID, URL, name, and path of each project.
     * @var bool      $owned                       Limit by projects owned by the current user.
     * @var bool      $membership                  Limit by projects that the current user is a member of.
     * @var bool      $starred                     Limit by projects starred by the current user.
     * @var bool      $statistics                  Include project statistics.
     * @var bool      $with_issues_enabled         Limit by enabled issues feature.
     * @var bool      $with_merge_requests_enabled Limit by enabled merge requests feature.
     * @var int       $min_access_level            Limit by current user minimal access level
     * }
     */
    public function all(array $parameters = [])
    {
        $resolver          = $this->createOptionsResolver();
        $booleanNormalizer = static function (Options $resolver, $value) {
            return $value ? 'true' : 'false';
        };
        $resolver->setDefined('archived')
            ->setAllowedTypes('archived', 'bool')
            ->setNormalizer('archived', $booleanNormalizer);
        $resolver->setDefined('visibility')
            ->setAllowedValues('visibility', ['public', 'internal', 'private']);
        $resolver->setDefined('order_by')
            ->setAllowedValues('order_by', ['id', 'name', 'path', 'created_at', 'updated_at', 'last_activity_at']);
        $resolver->setDefined('sort')
            ->setAllowedValues('sort', ['asc', 'desc']);
        $resolver->setDefined('search');
        $resolver->setDefined('simple')
            ->setAllowedTypes('simple', 'bool')
            ->setNormalizer('simple', $booleanNormalizer);
        $resolver->setDefined('owned')
            ->setAllowedTypes('owned', 'bool')
            ->setNormalizer('owned', $booleanNormalizer);
        $resolver->setDefined('membership')
            ->setAllowedTypes('membership', 'bool')
            ->setNormalizer('membership', $booleanNormalizer);
        $resolver->setDefined('starred')
            ->setAllowedTypes('starred', 'bool')
            ->setNormalizer('starred', $booleanNormalizer);
        $resolver->setDefined('statistics')
            ->setAllowedTypes('statistics', 'bool')
            ->setNormalizer('statistics', $booleanNormalizer);
        $resolver->setDefined('with_issues_enabled')
            ->setAllowedTypes('with_issues_enabled', 'bool')
            ->setNormalizer('with_issues_enabled', $booleanNormalizer);
        $resolver->setDefined('with_merge_requests_enabled')
            ->setAllowedTypes('with_merge_requests_enabled', 'bool')
            ->setNormalizer('with_merge_requests_enabled', $booleanNormalizer);
        $resolver->setDefined('min_access_level')
            ->setAllowedValues('min_access_level', [null, 10, 20, 30, 40, 50]);

        return $this->get('projects', $resolver->resolve($parameters));
    }

    /**
     * @param int|string $project_id
     * @param mixed[]    $parameters {
     *
     * @return mixed
     *
     * @var bool         $statistics             Include project statistics.
     * @var bool         $with_custom_attributes Include project custom attributes.
     * }
     */
    public function show($project_id, array $parameters = [])
    {
        $resolver          = $this->createOptionsResolver();
        $booleanNormalizer = static function (Options $resolver, $value): bool {
            return (bool)$value;
        };
        $resolver
            ->setDefined('statistics')
            ->setAllowedTypes('statistics', 'bool')
            ->setNormalizer('statistics', $booleanNormalizer);
        $resolver
            ->setDefined('with_custom_attributes')
            ->setAllowedTypes('with_custom_attributes', 'bool')
            ->setNormalizer('with_custom_attributes', $booleanNormalizer);

        return $this->get('projects/' . $this->encodePath((string)$project_id), $resolver->resolve($parameters));
    }

    /**
     * @param mixed[] $params
     *
     * @return mixed
     */
    public function create(string $name, array $params = [])
    {
        $params['name'] = $name;

        return $this->post('projects', $params);
    }

    /**
     * @param mixed[] $params
     *
     * @return mixed
     */
    public function createForUser(int $user_id, string $name, array $params = [])
    {
        $params['name'] = $name;

        return $this->post('projects/user/' . $this->encodePath((string)$user_id), $params);
    }

    /**
     * @param mixed[] $params
     *
     * @return mixed
     */
    public function update(int $project_id, array $params)
    {
        return $this->put('projects/' . $this->encodePath((string)$project_id), $params);
    }

    /**
     * @return mixed
     */
    public function remove(int $project_id)
    {
        return $this->delete('projects/' . $this->encodePath((string)$project_id));
    }

    /**
     * @return mixed
     */
    public function archive(int $project_id)
    {
        return $this->post('projects/' . $this->encodePath((string)$project_id) . '/archive');
    }

    /**
     * @return mixed
     */
    public function unarchive(int $project_id)
    {
        return $this->post('projects/' . $this->encodePath((string)$project_id) . '/unarchive');
    }

    /**
     * @param mixed[] $parameters
     *                           scope       The scope of pipelines, one of: running, pending, finished, branches, tags.
     *                           status      The status of pipelines, one of: running, pending, success, failed, canceled, skipped.
     *                           ref         The ref of pipelines.
     *                           sha         The sha of pipelines.
     *                           yaml_errors Returns pipelines with invalid configurations.
     *                           name        The name of the user who triggered pipelines.
     *                           username    The username of the user who triggered pipelines.
     *                           order_by    Order pipelines by id, status, ref, or user_id (default: id).
     *                           order       Sort pipelines in asc or desc order (default: desc).
     *
     * @return mixed
     * )
     */
    public function pipelines(int $project_id, array $parameters = [])
    {
        $resolver          = $this->createOptionsResolver();
        $booleanNormalizer = static function (Options $resolver, $value) {
            return $value ? 'true' : 'false';
        };

        $resolver->setDefined('scope')
            ->setAllowedValues('scope', ['running', 'pending', 'finished', 'branches', 'tags']);
        $resolver->setDefined('status')
            ->setAllowedValues('status', ['running', 'pending', 'success', 'failed', 'canceled', 'skipped']);
        $resolver->setDefined('ref');
        $resolver->setDefined('sha');
        $resolver->setDefined('yaml_errors')
            ->setAllowedTypes('yaml_errors', 'bool')
            ->setNormalizer('yaml_errors', $booleanNormalizer);
        $resolver->setDefined('name');
        $resolver->setDefined('username');
        $resolver->setDefined('order_by')
            ->setAllowedValues('order_by', ['id', 'status', 'ref', 'user_id']);
        $resolver->setDefined('sort')
            ->setAllowedValues('sort', ['asc', 'desc']);

        return $this->get($this->getProjectPath($project_id, 'pipelines'), $resolver->resolve($parameters));
    }

    /**
     * @return mixed
     */
    public function pipeline(int $project_id, int $pipeline_id)
    {
        return $this->get($this->getProjectPath($project_id, 'pipelines/' . $this->encodePath((string)$pipeline_id)));
    }

    /**
     * @return mixed
     */
    public function createPipeline(int $project_id, string $commit_ref)
    {
        return $this->post($this->getProjectPath($project_id, 'pipeline'), ['ref' => $commit_ref]);
    }

    /**
     * @return mixed
     */
    public function retryPipeline(int $project_id, int $pipeline_id)
    {
        return $this->post($this->getProjectPath($project_id, 'pipelines/' . $this->encodePath((string)$pipeline_id)) . '/retry');
    }

    /**
     * @return mixed
     */
    public function cancelPipeline(int $project_id, int $pipeline_id)
    {
        return $this->post($this->getProjectPath($project_id, 'pipelines/' . $this->encodePath((string)$pipeline_id)) . '/cancel');
    }

    /**
     * @return mixed
     */
    public function deletePipeline(int $project_id, int $pipeline_id)
    {
        return $this->delete($this->getProjectPath($project_id, 'pipelines/' . $this->encodePath((string)$pipeline_id)));
    }

    /**
     * @param mixed[] $parameters
     *
     * @return mixed
     */
    public function allMembers(int $project_id, array $parameters = [])
    {
        $resolver = $this->createOptionsResolver();
        $resolver->setDefined('query');

        return $this->get('projects/' . $this->encodePath((string)$project_id) . '/members/all', $resolver->resolve($parameters));
    }

    /**
     * @param mixed[] $parameters Contains: query           The query you want to search members for.
     *
     * @return mixed
     */
    public function members(int $project_id, array $parameters = [])
    {
        $resolver = $this->createOptionsResolver();

        $resolver->setDefined('query')
            ->setAllowedTypes('query', 'string');

        return $this->get($this->getProjectPath($project_id, 'members'), $resolver->resolve($parameters));
    }

    /**
     * @return mixed
     */
    public function member(int $project_id, int $user_id)
    {
        return $this->get($this->getProjectPath($project_id, 'members/' . $this->encodePath((string)$user_id)));
    }

    /**
     * @return mixed
     */
    public function addMember(int $project_id, int $user_id, int $access_level)
    {
        return $this->post($this->getProjectPath($project_id, 'members'), [
            'user_id' => $user_id,
            'access_level' => $access_level,
        ]);
    }

    /**
     * @return mixed
     */
    public function saveMember(int $project_id, int $user_id, int $access_level)
    {
        return $this->put($this->getProjectPath($project_id, 'members/' . urldecode((string)$user_id)), ['access_level' => $access_level]);
    }

    /**
     * @return mixed
     */
    public function removeMember(int $project_id, int $user_id)
    {
        return $this->delete($this->getProjectPath($project_id, 'members/' . urldecode((string)$user_id)));
    }

    /**
     * @param mixed[] $parameters
     *
     * @return mixed
     */
    public function hooks(int $project_id, array $parameters = [])
    {
        $resolver = $this->createOptionsResolver();

        return $this->get($this->getProjectPath($project_id, 'hooks'), $resolver->resolve($parameters));
    }

    /**
     * @return mixed
     */
    public function hook(int $project_id, int $hook_id)
    {
        return $this->get($this->getProjectPath($project_id, 'hooks/' . $this->encodePath((string)$hook_id)));
    }

    /**
     * Get project issues.
     *
     * See https://docs.gitlab.com/ee/api/issues.html#list-project-issues for more info.
     *
     * @param int     $project_id
     *   Project id.
     * @param mixed[] $parameters
     *   Url parameters. For example: issue state (opened / closed).
     *
     * @return mixed[]
     *   List of project issues.
     */
    public function issues(int $project_id, array $parameters = []): array
    {
        return $this->get($this->getProjectPath($project_id, 'issues'), $parameters);
    }

    /**
     * Get projects board list.
     *
     * See https://docs.gitlab.com/ee/api/boards.html for more info.
     *
     * @param int $project_id
     *   Project id.
     *
     * @return mixed[]
     *   List of project boards.
     */
    public function boards(int $project_id): array
    {
        return $this->get($this->getProjectPath($project_id, 'boards'));
    }

    /**
     * @param mixed[] $params
     *
     * @return mixed
     */
    public function addHook(int $project_id, string $url, array $params = [])
    {
        if (empty($params)) {
            $params = ['push_events' => true];
        }

        $params['url'] = $url;

        return $this->post($this->getProjectPath($project_id, 'hooks'), $params);
    }

    /**
     * @param mixed[] $params
     *
     * @return mixed
     */
    public function updateHook(int $project_id, int $hook_id, array $params)
    {
        return $this->put($this->getProjectPath($project_id, 'hooks/' . $this->encodePath((string)$hook_id)), $params);
    }

    /**
     * @return mixed
     */
    public function removeHook(int $project_id, int $hook_id)
    {
        return $this->delete($this->getProjectPath($project_id, 'hooks/' . $this->encodePath((string)$hook_id)));
    }

    /**
     * @param mixed $namespace
     *
     * @return mixed
     */
    public function transfer(int $project_id, $namespace)
    {
        return $this->put($this->getProjectPath($project_id, 'transfer'), ['namespace' => $namespace]);
    }

    /**
     * @return mixed
     */
    public function deployKeys(int $project_id)
    {
        return $this->get($this->getProjectPath($project_id, 'deploy_keys'));
    }

    /**
     * @return mixed
     */
    public function deployKey(int $project_id, int $key_id)
    {
        return $this->get($this->getProjectPath($project_id, 'deploy_keys/' . $this->encodePath((string)$key_id)));
    }

    /**
     * @return mixed
     */
    public function addDeployKey(int $project_id, string $title, string $key, bool $canPush = false)
    {
        return $this->post($this->getProjectPath($project_id, 'deploy_keys'), [
            'title' => $title,
            'key' => $key,
            'can_push' => $canPush,
        ]);
    }

    /**
     * @return mixed
     */
    public function deleteDeployKey(int $project_id, int $key_id)
    {
        return $this->delete($this->getProjectPath($project_id, 'deploy_keys/' . $this->encodePath((string)$key_id)));
    }

    /**
     * @return mixed
     */
    public function enableDeployKey(int $project_id, int $key_id)
    {
        return $this->post($this->getProjectPath($project_id, 'deploy_keys/' . $this->encodePath((string)$key_id) . '/enable'));
    }

    /**
     * @param mixed[] $parameters
     * target_type Include only events of a particular target type.
     * before      Include only events created before a particular date.
     * after       Include only events created after a particular date.
     * sort        Sort events in asc or desc order by created_at. Default is desc.
     * action      Include only events of a particular action type.
     *
     * @return mixed
     */
    public function events(int $project_id, array $parameters = [])
    {
        $resolver           = $this->createOptionsResolver();
        $datetimeNormalizer = static function (Options $resolver, DateTimeInterface $value) {
            return $value->format('Y-m-d');
        };

        $resolver->setDefined('action')
            ->setAllowedValues('action', ['created', 'updated', 'closed', 'reopened', 'pushed', 'commented', 'merged', 'joined', 'left', 'destroyed', 'expired']);
        $resolver->setDefined('target_type')
            ->setAllowedValues('target_type', ['issue', 'milestone', 'merge_request', 'note', 'project', 'snippet', 'user']);
        $resolver->setDefined('before')
            ->setAllowedTypes('before', DateTimeInterface::class)
            ->setNormalizer('before', $datetimeNormalizer);
        $resolver->setDefined('after')
            ->setAllowedTypes('after', DateTimeInterface::class)
            ->setNormalizer('after', $datetimeNormalizer);
        $resolver->setDefined('sort')
            ->setAllowedValues('sort', ['asc', 'desc']);

        return $this->get($this->getProjectPath($project_id, 'events'), $resolver->resolve($parameters));
    }

    /**
     * @param mixed[] $parameters
     *
     * @return mixed
     */
    public function labels(int $project_id, array $parameters = [])
    {
        $resolver = $this->createOptionsResolver();

        return $this->get($this->getProjectPath($project_id, 'labels'), $resolver->resolve($parameters));
    }

    /**
     * @param mixed[] $params
     *
     * @return mixed
     */
    public function addLabel(int $project_id, array $params)
    {
        return $this->post($this->getProjectPath($project_id, 'labels'), $params);
    }

    /**
     * @param mixed[] $params
     *
     * @return mixed
     */
    public function updateLabel(int $project_id, array $params)
    {
        return $this->put($this->getProjectPath($project_id, 'labels'), $params);
    }

    /**
     * @return mixed
     */
    public function removeLabel(int $project_id, string $name)
    {
        return $this->delete($this->getProjectPath($project_id, 'labels'), ['name' => $name]);
    }

    /**
     * Get languages used in a project with percentage value.
     *
     * @return mixed
     */
    public function languages(int $project_id)
    {
        return $this->get($this->getProjectPath($project_id, 'languages'));
    }

    /**
     * @param mixed[] $parameters
     *                           path      The path of the forked project (optional)
     *                           name      The name of the forked project (optional)
     *                           namespace The ID or path of the namespace that the project will be forked to
     *
     * @return mixed
     */
    public function fork(int $project_id, array $parameters = [])
    {
        $resolver = new OptionsResolver();
        $resolver->setDefined(['namespace', 'path', 'name']);

        $resolved = $resolver->resolve($parameters);

        return $this->post($this->getProjectPath($project_id, 'fork'), $resolved);
    }

    /**
     * @return mixed
     */
    public function createForkRelation(int $project_id, int $forked_project_id)
    {
        return $this->post($this->getProjectPath($project_id, 'fork/' . $this->encodePath((string)$forked_project_id)));
    }

    /**
     * @return mixed
     */
    public function removeForkRelation(int $project_id)
    {
        return $this->delete($this->getProjectPath($project_id, 'fork'));
    }

    /**
     * @param mixed[] $params
     *
     * @return mixed
     */
    public function setService(int $project_id, string $service_name, array $params = [])
    {
        return $this->put($this->getProjectPath($project_id, 'services/' . $this->encodePath((string)$service_name)), $params);
    }

    /**
     * @return mixed
     */
    public function removeService(int $project_id, string $service_name)
    {
        return $this->delete($this->getProjectPath($project_id, 'services/' . $this->encodePath((string)$service_name)));
    }

    /**
     * @param mixed[] $parameters
     *
     * @return mixed
     */
    public function variables(int $project_id, array $parameters = [])
    {
        $resolver = $this->createOptionsResolver();

        return $this->get($this->getProjectPath($project_id, 'variables'), $resolver->resolve($parameters));
    }

    /**
     * @return mixed
     */
    public function variable(int $project_id, string $key)
    {
        return $this->get($this->getProjectPath($project_id, 'variables/' . $this->encodePath((string)$key)));
    }

    /**
     * @return mixed
     */
    public function addVariable(
        int $project_id,
        string $key,
        string $value,
        ?bool $protected = null,
        ?string $environment_scope = null
    ) {
        $payload = [
            'key' => $key,
            'value' => $value,
        ];

        if ($protected) {
            $payload['protected'] = $protected;
        }

        if ($environment_scope) {
            $payload['environment_scope'] = $environment_scope;
        }

        return $this->post($this->getProjectPath($project_id, 'variables'), $payload);
    }

    /**
     * @return mixed
     */
    public function updateVariable(
        int $project_id,
        string $key,
        string $value,
        ?bool $protected = null,
        ?string $environment_scope = null
    ) {
        $payload = ['value' => $value];

        if ($protected) {
            $payload['protected'] = $protected;
        }

        if ($environment_scope) {
            $payload['environment_scope'] = $environment_scope;
        }

        return $this->put($this->getProjectPath($project_id, 'variables/' . $this->encodePath($key)), $payload);
    }

    /**
     * @return mixed
     */
    public function removeVariable(int $project_id, string $key)
    {
        return $this->delete($this->getProjectPath($project_id, 'variables/' . $this->encodePath($key)));
    }

    /**
     * @return mixed
     */
    public function uploadFile(int $project_id, string $file)
    {
        return $this->post($this->getProjectPath($project_id, 'uploads'), [], [], ['file' => $file]);
    }

    /**
     * @param mixed[] $parameters
     *
     * @return mixed
     */
    public function deployments(int $project_id, array $parameters = [])
    {
        $resolver = $this->createOptionsResolver();

        return $this->get($this->getProjectPath($project_id, 'deployments'), $resolver->resolve($parameters));
    }

    /**
     * @return mixed
     */
    public function deployment(int $project_id, int $deployment_id)
    {
        return $this->get($this->getProjectPath($project_id, 'deployments/' . $this->encodePath((string)$deployment_id)));
    }

    /**
     * @param mixed[] $parameters
     *
     * @return mixed
     */
    public function addShare(int $project_id, array $parameters = [])
    {
        $resolver = $this->createOptionsResolver();

        $datetimeNormalizer = static function (OptionsResolver $optionsResolver, DateTimeInterface $value) {
            return $value->format('Y-m-d');
        };

        $resolver->setRequired('group_id')
            ->setAllowedTypes('group_id', 'int');

        $resolver->setRequired('group_access')
            ->setAllowedTypes('group_access', 'int')
            ->setAllowedValues('group_access', [0, 10, 20, 30, 40, 50]);

        $resolver->setDefined('expires_at')
            ->setAllowedTypes('expires_at', DateTimeInterface::class)
            ->setNormalizer('expires_at', $datetimeNormalizer);

        return $this->post($this->getProjectPath($project_id, 'share'), $resolver->resolve($parameters));
    }

    /**
     * @param mixed $project_id
     *
     * @return mixed
     */
    public function removeShare($project_id, int $group_id)
    {
        return $this->delete($this->getProjectPath($project_id, 'share/' . $group_id));
    }

    /**
     * @return mixed
     */
    public function badges(int $project_id)
    {
        return $this->get($this->getProjectPath($project_id, 'badges'));
    }

    /**
     * @return mixed
     */
    public function badge(int $project_id, int $badge_id)
    {
        return $this->get($this->getProjectPath($project_id, 'badges/' . $this->encodePath((string)$badge_id)));
    }

    /**
     * @param mixed[] $params
     *
     * @return mixed
     */
    public function addBadge(int $project_id, array $params = [])
    {
        return $this->post($this->getProjectPath($project_id, 'badges'), $params);
    }

    /**
     * @return mixed
     */
    public function removeBadge(int $project_id, int $badge_id)
    {
        return $this->delete($this->getProjectPath($project_id, 'badges/' . $this->encodePath((string)$badge_id)));
    }

    /**
     * @param mixed[] $params
     *
     * @return mixed
     */
    public function updateBadge(int $project_id, int $badge_id, array $params = [])
    {
        return $this->put($this->getProjectPath($project_id, 'badges/' . $this->encodePath((string)$badge_id)));
    }

    /**
     * @param mixed[] $params
     *
     * @return mixed
     */
    public function addProtectedBranch(int $project_id, array $params = [])
    {
        return $this->post($this->getProjectPath($project_id, 'protected_branches'), $params);
    }
}
