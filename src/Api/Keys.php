<?php

declare(strict_types=1);

namespace Gitlab\Api;

final class Keys extends ApiBase
{
    /**
     * @return mixed
     */
    public function show(int $id)
    {
        return $this->get('keys/' . $this->encodePath((string)$id));
    }
}
