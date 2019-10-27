<?php

declare(strict_types=1);

namespace Gitlab\Api;

final class Deployments extends ApiBase
{
    /**
     * @param mixed[] $parameters
     *
     * @return mixed
     */
    public function all(int $project_id, array $parameters = [])
    {
        $resolver = $this->createOptionsResolver();

        return $this->get($this->getProjectPath($project_id, 'deployments'), $resolver->resolve($parameters));
    }

    /**
     * @return mixed
     */
    public function show(int $project_id, int $deployment_id)
    {
        return $this->get($this->getProjectPath($project_id, 'deployments/' . $deployment_id));
    }
}
