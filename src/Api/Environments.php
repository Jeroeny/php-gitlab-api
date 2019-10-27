<?php

declare(strict_types=1);

namespace Gitlab\Api;

use Symfony\Component\OptionsResolver\OptionsResolver;

final class Environments extends ApiBase
{
    /**
     * @param mixed[] $parameters
     *
     * @return mixed
     */
    public function all(int $project_id, array $parameters = [])
    {
        $resolver = $this->createOptionsResolver();

        return $this->get($this->getProjectPath($project_id, 'environments'), $resolver->resolve($parameters));
    }

    /**
     * @param mixed[] $parameters (
     *
     *     @var string $name         The name of the environment
     *     @var string $external_url Place to link to for this environment
     * )
     *
     * @return mixed
     */
    public function create(int $project_id, array $parameters = [])
    {
        $resolver = new OptionsResolver();
        $resolver->setDefined('name')
            ->setRequired('name')
            ->setAllowedTypes('name', 'string');
        $resolver->setDefined('external_url')
            ->setAllowedTypes('external_url', 'string');

        return $this->post($this->getProjectPath($project_id, 'environment'), $resolver->resolve($parameters));
    }

    /**
     * @return mixed
     */
    public function remove(int $project_id, int $environment_id)
    {
        return $this->delete($this->getProjectPath($project_id, 'environments/' . $environment_id));
    }

    /**
     * @return mixed
     */
    public function stop(int $project_id, int $environment_id)
    {
        return $this->post($this->getProjectPath($project_id, 'environments/' . $this->encodePath((string)$environment_id) . '/stop'));
    }
}
