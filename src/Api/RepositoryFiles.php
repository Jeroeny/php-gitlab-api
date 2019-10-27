<?php

declare(strict_types=1);

namespace Gitlab\Api;

use Symfony\Component\OptionsResolver\OptionsResolver;

final class RepositoryFiles extends ApiBase
{
    /**
     * @return mixed
     */
    public function getFile(int $project_id, string $file_path, string $ref)
    {
        return $this->get($this->getProjectPath($project_id, 'repository/files/' . $this->encodePath($file_path)), ['ref' => $ref]);
    }

    /**
     * @return mixed
     */
    public function getRawFile(int $project_id, string $file_path, string $ref)
    {
        return $this->get($this->getProjectPath($project_id, 'repository/files/' . $this->encodePath($file_path) . '/raw'), ['ref' => $ref]);
    }

    /**
     * @param mixed[] $parameters (
     *
     *     @var string $file_path      Url encoded full path to new file. Ex. lib%2Fclass%2Erb.
     *     @var string $branch         Name of the branch.
     *     @var string $start_branch   Name of the branch to start the new commit from.
     *     @var string $encoding       Change encoding to 'base64'. Default is text.
     *     @var string $author_email   Specify the commit author's email address.
     *     @var string $author_name    Specify the commit author's name.
     *     @var string $content        File content.
     *     @var string $commit_message Commit message.
     * )
     *
     * @return mixed
     */
    public function createFile(int $project_id, array $parameters = [])
    {
        $resolver = new OptionsResolver();
        $resolver->setRequired('file_path');
        $resolver->setRequired('branch');
        $resolver->setDefined('start_branch');
        $resolver->setDefined('encoding')
            ->setAllowedValues('encoding', ['text', 'base64']);
        $resolver->setDefined('author_email');
        $resolver->setDefined('author_name');
        $resolver->setRequired('content');
        $resolver->setRequired('commit_message');

        $resolved = $resolver->resolve($parameters);

        return $this->post($this->getProjectPath($project_id, 'repository/files/' . $this->encodePath($resolved['file_path'])), $resolved);
    }

    /**
     * @param mixed[] $parameters (
     *
     *     @var string $file_path      Url encoded full path to new file. Ex. lib%2Fclass%2Erb.
     *     @var string $branch         Name of the branch.
     *     @var string $start_branch   Name of the branch to start the new commit from.
     *     @var string $encoding       Change encoding to 'base64'. Default is text.
     *     @var string $author_email   Specify the commit author's email address.
     *     @var string $author_name    Specify the commit author's name.
     *     @var string $content        File content.
     *     @var string $commit_message Commit message.
     *     @var string $last_commit_id Last known file commit id.
     * )
     *
     * @return mixed
     */
    public function updateFile(int $project_id, array $parameters = [])
    {
        $resolver = new OptionsResolver();
        $resolver->setRequired('file_path');
        $resolver->setRequired('branch');
        $resolver->setDefined('start_branch');
        $resolver->setDefined('encoding')
            ->setAllowedValues('encoding', ['text', 'base64']);
        $resolver->setDefined('author_email');
        $resolver->setDefined('author_name');
        $resolver->setRequired('content');
        $resolver->setRequired('commit_message');
        $resolver->setDefined('last_commit_id');

        $resolved = $resolver->resolve($parameters);

        return $this->put($this->getProjectPath($project_id, 'repository/files/' . $this->encodePath($resolved['file_path'])), $resolved);
    }

    /**
     * @param mixed[] $parameters (
     *
     *     @var string $file_path      Url encoded full path to new file. Ex. lib%2Fclass%2Erb.
     *     @var string $branch         Name of the branch.
     *     @var string $start_branch   Name of the branch to start the new commit from.
     *     @var string $author_email   Specify the commit author's email address.
     *     @var string $author_name    Specify the commit author's name.
     *     @var string $commit_message Commit message.
     * )
     *
     * @return mixed
     */
    public function deleteFile(int $project_id, array $parameters = [])
    {
        $resolver = new OptionsResolver();
        $resolver->setRequired('file_path');
        $resolver->setRequired('branch');
        $resolver->setDefined('start_branch');
        $resolver->setDefined('author_email');
        $resolver->setDefined('author_name');
        $resolver->setRequired('commit_message');

        $resolved = $resolver->resolve($parameters);

        return $this->delete($this->getProjectPath($project_id, 'repository/files/' . $this->encodePath($resolved['file_path'])), $resolved);
    }
}
