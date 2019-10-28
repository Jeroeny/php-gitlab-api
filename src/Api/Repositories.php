<?php

declare(strict_types=1);

namespace Gitlab\Api;

use DateTimeInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use const E_USER_DEPRECATED;
use function array_map;
use function sprintf;
use function trigger_error;

final class Repositories extends ApiBase
{
    public const TYPE_BRANCH = 'branch';
    public const TYPE_TAG    = 'tag';

    /**
     * @param mixed[] $parameters (
     *
     *     @var string $search
     * )
     *
     * @return mixed
     */
    public function branches(int $project_id, array $parameters = [])
    {
        $resolver = $this->createOptionsResolver();
        $resolver->setDefined('search')
            ->setAllowedTypes('search', 'string');

        return $this->get($this->getProjectPath($project_id, 'repository/branches'), $resolver->resolve($parameters));
    }

    /**
     * @param string|int $branch_id
     *
     * @return mixed
     */
    public function branch(int $project_id, $branch_id)
    {
        return $this->get($this->getProjectPath($project_id, 'repository/branches/' . $this->encodePath((string)$branch_id)));
    }

    /**
     * @return mixed
     */
    public function createBranch(int $project_id, string $branch, string $ref)
    {
        return $this->post($this->getProjectPath($project_id, 'repository/branches'), [
            'branch' => $branch,
            'ref' => $ref,
        ]);
    }

    /**
     * @return mixed
     */
    public function deleteBranch(int $project_id, string $branch)
    {
        return $this->delete($this->getProjectPath($project_id, 'repository/branches/' . $this->encodePath($branch)));
    }

    /**
     * @return mixed
     */
    public function protectBranch(int $project_id, string $branch_name, bool $devPush = false, bool $devMerge = false)
    {
        return $this->put($this->getProjectPath($project_id, 'repository/branches/' . $this->encodePath($branch_name) . '/protect'), [
            'developers_can_push' => $devPush,
            'developers_can_merge' => $devMerge,
        ]);
    }

    /**
     * @return mixed
     */
    public function unprotectBranch(int $project_id, string $branch_name)
    {
        return $this->put($this->getProjectPath($project_id, 'repository/branches/' . $this->encodePath($branch_name) . '/unprotect'));
    }

    /**
     * @param mixed[] $parameters
     *
     * @return mixed
     */
    public function tags(int $project_id, array $parameters = [])
    {
        $resolver = $this->createOptionsResolver();

        return $this->get($this->getProjectPath($project_id, 'repository/tags'), $resolver->resolve($parameters));
    }

    /**
     * @return mixed
     */
    public function createTag(int $project_id, string $name, string $ref, ?string $message = null)
    {
        return $this->post($this->getProjectPath($project_id, 'repository/tags'), [
            'tag_name' => $name,
            'ref' => $ref,
            'message' => $message,
        ]);
    }

    /**
     * @return mixed
     */
    public function createRelease(int $project_id, string $tag_name, string $description)
    {
        return $this->post($this->getProjectPath($project_id, 'repository/tags/' . $this->encodePath($tag_name) . '/release'), [
            'id'          => $project_id,
            'tag_name'    => $tag_name,
            'description' => $description,
        ]);
    }

    /**
     * @return mixed
     */
    public function updateRelease(int $project_id, string $tag_name, string $description)
    {
        return $this->put($this->getProjectPath($project_id, 'repository/tags/' . $this->encodePath($tag_name) . '/release'), [
            'id'          => $project_id,
            'tag_name'    => $tag_name,
            'description' => $description,
        ]);
    }

    /**
     * @return mixed
     */
    public function releases(int $project_id)
    {
        return $this->get($this->getProjectPath($project_id, 'releases'));
    }

    /**
     * @param mixed[] $parameters (
     *
     *     @var string             $ref_name The name of a repository branch or tag or if not given the default branch.
     *     @var \DateTimeInterface $since    Only commits after or on this date will be returned.
     *     @var \DateTimeInterface $until    Only commits before or on this date will be returned.
     * )
     *
     * @return mixed
     */
    public function commits(int $project_id, array $parameters = [])
    {
        $resolver           = $this->createOptionsResolver();
        $datetimeNormalizer = static function (Options $options, DateTimeInterface $value) {
            return $value->format('c');
        };

        $resolver->setDefined('path');
        $resolver->setDefined('ref_name');
        $resolver->setDefined('since')
            ->setAllowedTypes('since', DateTimeInterface::class)
            ->setNormalizer('since', $datetimeNormalizer);
        $resolver->setDefined('until')
            ->setAllowedTypes('until', DateTimeInterface::class)
            ->setNormalizer('until', $datetimeNormalizer);
        $resolver->setDefined('all');
        $resolver->setDefined('with_stats');

        return $this->get($this->getProjectPath($project_id, 'repository/commits'), $resolver->resolve($parameters));
    }

    /**
     * @return mixed
     */
    public function commit(int $project_id, string $sha)
    {
        return $this->get($this->getProjectPath($project_id, 'repository/commits/' . $this->encodePath((string)$sha)));
    }

    /**
     * @param mixed[] $parameters
     *
     * @return mixed
     */
    public function commitRefs(int $project_id, string $sha, array $parameters = [])
    {
        $resolver = $this->createOptionsResolver();

        return $this->get(
            $this->getProjectPath($project_id, 'repository/commits/' . $this->encodePath((string)$sha) . '/refs'),
            $resolver->resolve($parameters)
        );
    }

    /**
     * @param mixed[] $parameters (
     *
     *     @var string $branch         Name of the branch to commit into. To create a new branch, also provide start_branch.
     *     @var string $commit_message Commit message.
     *     @var string $start_branch   Name of the branch to start the new commit from.
     *     @var mixed[] $actions (
     *
     *         @var string $action        he action to perform, create, delete, move, update.
     *         @var string $file_path     Full path to the file.
     *         @var string $previous_path Original full path to the file being moved.
     *         @var string $content       File content, required for all except delete. Optional for move.
     *         @var string $encoding      text or base64. text is default.
     *     )
     *     @var string $author_email   Specify the commit author's email address.
     *     @var string $author_name    Specify the commit author's name.
     * )
     *
     * @return mixed
     */
    public function createCommit(int $project_id, array $parameters = [])
    {
        $resolver = new OptionsResolver();
        $resolver->setDefined('branch')
            ->setRequired('branch');
        $resolver->setDefined('commit_message')
            ->setRequired('commit_message');
        $resolver->setDefined('start_branch');
        $resolver->setDefined('actions')
            ->setRequired('actions')
            ->setAllowedTypes('actions', 'array')
            ->setAllowedValues('actions', static function (array $actions) {
                return ! empty($actions);
            })
            ->setNormalizer('actions', static function (Options $resolver, array $actions) {
                $actionsOptionsResolver = new OptionsResolver();
                $actionsOptionsResolver->setDefined('action')
                    ->setRequired('action')
                    ->setAllowedValues('action', ['create', 'delete', 'move', 'update']);
                $actionsOptionsResolver->setDefined('file_path')
                    ->setRequired('file_path');
                $actionsOptionsResolver->setDefined('previous_path');
                $actionsOptionsResolver->setDefined('content');
                $actionsOptionsResolver->setDefined('encoding')
                    ->setAllowedValues('encoding', ['test', 'base64']);

                return array_map(static function ($action) use ($actionsOptionsResolver) {
                    return $actionsOptionsResolver->resolve($action);
                }, $actions);
            });
        $resolver->setDefined('author_email');
        $resolver->setDefined('author_name');

        return $this->post($this->getProjectPath($project_id, 'repository/commits'), $resolver->resolve($parameters));
    }

    /**
     * @param mixed[] $parameters
     *
     * @return mixed
     */
    public function commitComments(int $project_id, string $sha, array $parameters = [])
    {
        $resolver = $this->createOptionsResolver();

        return $this->get(
            $this->getProjectPath($project_id, 'repository/commits/' . $this->encodePath($sha) . '/comments'),
            $resolver->resolve($parameters)
        );
    }

    /**
     * @param mixed[] $params
     *
     * @return mixed
     */
    public function createCommitComment(int $project_id, string $sha, string $note, array $params = [])
    {
        $params['note'] = $note;

        return $this->post($this->getProjectPath($project_id, 'repository/commits/' . $this->encodePath($sha) . '/comments'), $params);
    }

    /**
     * @param mixed[] $params
     *
     * @return mixed
     */
    public function getCommitBuildStatus(int $project_id, string $sha, array $params = [])
    {
        return $this->get($this->getProjectPath($project_id, 'repository/commits/' . $this->encodePath($sha) . '/statuses'), $params);
    }

    /**
     * @param mixed[] $params
     *
     * @return mixed
     */
    public function postCommitBuildStatus(int $project_id, string $sha, string $state, array $params = [])
    {
        $params['state'] = $state;

        return $this->post($this->getProjectPath($project_id, 'statuses/' . $this->encodePath($sha)), $params);
    }

    /**
     * @return mixed
     */
    public function compare(int $project_id, string $fromShaOrMaster, string $toShaOrMaster, bool $straight = false)
    {
        return $this->get($this->getProjectPath(
            $project_id,
            'repository/compare?from=' . $this->encodePath($fromShaOrMaster) . '&to=' . $this->encodePath($toShaOrMaster) . '&straight=' . $this->encodePath($straight ? 'true' : 'false')
        ));
    }

    /**
     * @return string[]|string
     */
    public function diff(int $project_id, string $sha)
    {
        return $this->get($this->getProjectPath($project_id, 'repository/commits/' . $this->encodePath($sha) . '/diff'));
    }

    /**
     * @param mixed[] $params
     *
     * @return mixed
     */
    public function tree(int $project_id, array $params = [])
    {
        return $this->get($this->getProjectPath($project_id, 'repository/tree'), $params);
    }

    /**
     * @return mixed
     */
    public function blob(int $project_id, string $sha, string $filepath)
    {
        @trigger_error(sprintf('The %s() method is deprecated since version 9.2 and will be removed in 10.0. Use the %s::getRawFile() method instead.', __METHOD__, RepositoryFiles::class), E_USER_DEPRECATED);

        return $this->client->repositoryFiles()->getRawFile($project_id, $filepath, $sha);
    }

    /**
     * @return mixed
     */
    public function getFile(int $project_id, string $file_path, string $ref)
    {
        @trigger_error(sprintf('The %s() method is deprecated since version 9.2 and will be removed in 10.0. Use the %s::getFile() method instead.', __METHOD__, RepositoryFiles::class), E_USER_DEPRECATED);

        return $this->client->repositoryFiles()->getFile($project_id, $file_path, $ref);
    }

    /**
     * @return mixed
     */
    public function createFile(int $project_id, string $file_path, string $content, string $branch, string $commit_message, ?string $encoding = null, ?string $author_email = null, ?string $author_name = null)
    {
        @trigger_error(sprintf('The %s() method is deprecated since version 9.2 and will be removed in 10.0. Use the %s::createFile() method instead.', __METHOD__, RepositoryFiles::class), E_USER_DEPRECATED);

        return $this->client->repositoryFiles()->createFile($project_id, [
            'file_path' => $file_path,
            'branch' => $branch,
            'content' => $content,
            'commit_message' => $commit_message,
            'encoding' => $encoding,
            'author_email' => $author_email,
            'author_name' => $author_name,
        ]);
    }

    /**
     * @return mixed
     */
    public function updateFile(int $project_id, string $file_path, string $content, string $branch, string $commit_message, ?string $encoding = null, ?string $author_email = null, ?string $author_name = null)
    {
        @trigger_error(sprintf('The %s() method is deprecated since version 9.2 and will be removed in 10.0. Use the %s::updateFile() method instead.', __METHOD__, RepositoryFiles::class), E_USER_DEPRECATED);

        return $this->client->repositoryFiles()->updateFile($project_id, [
            'file_path' => $file_path,
            'branch' => $branch,
            'content' => $content,
            'commit_message' => $commit_message,
            'encoding' => $encoding,
            'author_email' => $author_email,
            'author_name' => $author_name,
        ]);
    }

    /**
     * @return mixed
     */
    public function deleteFile(int $project_id, string $file_path, string $branch, string $commit_message, ?string $author_email = null, ?string $author_name = null)
    {
        @trigger_error(sprintf('The %s() method is deprecated since version 9.2 and will be removed in 10.0. Use the %s::deleteFile() method instead.', __METHOD__, RepositoryFiles::class), E_USER_DEPRECATED);

        return $this->client->repositoryFiles()->deleteFile($project_id, [
            'file_path' => $file_path,
            'branch' => $branch,
            'commit_message' => $commit_message,
            'author_email' => $author_email,
            'author_name' => $author_name,
        ]);
    }

    /**
     * @return mixed
     */
    public function contributors(int $project_id)
    {
        return $this->get($this->getProjectPath($project_id, 'repository/contributors'));
    }

    /**
     * @param mixed[] $params
     * @param string  $format Options: "tar.gz", "zip", "tar.bz2" and "tar"
     *
     * @return mixed
     */
    public function archive(int $project_id, array $params = [], string $format = 'tar.gz')
    {
        return $this->get($this->getProjectPath($project_id, 'repository/archive.' . $format), $params);
    }

    /**
     * @param mixed[] $refs
     *
     * @return mixed
     */
    public function mergeBase(int $project_id, array $refs)
    {
        return $this->get($this->getProjectPath($project_id, 'repository/merge_base'), ['refs' => $refs]);
    }

    protected function createOptionsResolver(): OptionsResolver
    {
        $allowedTypeValues = [
            self::TYPE_BRANCH,
            self::TYPE_TAG,
        ];

        $resolver = parent::createOptionsResolver();
        $resolver->setDefined('type')
            ->setAllowedTypes('type', 'string')
            ->setAllowedValues('type', $allowedTypeValues);

        return $resolver;
    }
}
