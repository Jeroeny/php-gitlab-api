<?php

declare(strict_types=1);

namespace Gitlab\Api;

final class SystemHooks extends ApiBase
{
    /**
     * @return mixed
     */
    public function all()
    {
        return $this->get('hooks');
    }

    /**
     * @return mixed
     */
    public function create(string $url)
    {
        return $this->post('hooks', ['url' => $url]);
    }

    /**
     * @return mixed
     */
    public function test(int $id)
    {
        return $this->get('hooks/' . $this->encodePath((string)$id));
    }

    /**
     * @return mixed
     */
    public function remove(int $id)
    {
        return $this->delete('hooks/' . $this->encodePath((string)$id));
    }
}
