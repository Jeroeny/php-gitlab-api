<?php

declare(strict_types=1);

namespace Gitlab\Tests\Api;

use Gitlab\Api\Api;
use Gitlab\Tests\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /** @var Api */
    protected $api;

    abstract protected function getApiClass(): string;

    public function setUp(): void
    {
        parent::setUp();
        $this->api = $this->getApiInstance();
    }

    protected function getApiInstance(): Api
    {
        $apiClass = $this->getApiClass();

        return new $apiClass($this->client);
    }
}
