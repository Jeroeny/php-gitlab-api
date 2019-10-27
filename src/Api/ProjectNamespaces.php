<?php

declare(strict_types=1);

namespace Gitlab\Api;

final class ProjectNamespaces extends ApiBase
{
    /**
     * @param mixed[] $parameters (
     *
     *     @var string $search Returns a list of namespaces the user is authorized to see based on the search criteria.
     * )
     *
     * @return mixed
     */
    public function all(array $parameters = [])
    {
        $resolver = $this->createOptionsResolver();
        $resolver->setDefined('search');

        return $this->get('namespaces', $resolver->resolve($parameters));
    }

    /**
     * @return mixed
     */
    public function show(int $namespace_id)
    {
        return $this->get('namespaces/' . $this->encodePath((string)$namespace_id));
    }
}
