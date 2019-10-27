<?php

declare(strict_types=1);

namespace Gitlab\Api;

final class Version extends ApiBase
{
    /**
     * @return mixed
     */
    public function show()
    {
        return $this->get('version');
    }
}
