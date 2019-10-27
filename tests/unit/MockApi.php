<?php

declare(strict_types=1);

namespace Gitlab\Tests;

use Gitlab\Api\Api;
use Gitlab\Client;

final class MockApi implements Api
{
    /** @var string[] */
    public $all = [];

    public function __construct(Client $client)
    {
    }

    /**
     * @return string[]
     */
    public function all(int $project_id): array
    {
        return $this->all;
    }
}
