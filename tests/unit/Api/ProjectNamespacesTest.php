<?php

declare(strict_types=1);

namespace Gitlab\Tests\Api;

use Gitlab\Api\ProjectNamespaces;

final class ProjectNamespacesTest extends TestCase
{
    /** @var ProjectNamespaces */
    protected $api;

    /**
     * @test
     */
    public function shouldGetAllNamespaces(): void
    {
        $expectedArray = [
            ['id' => 1, 'name' => 'bespokes'],
            ['id' => 2, 'name' => 'internal'],
        ];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->all());
    }

    /**
     * @test
     */
    public function shouldShowNamespace(): void
    {
        $expectedArray = ['id' => 1, 'name' => 'internal'];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->show(1));
    }

    protected function getApiClass(): string
    {
        return ProjectNamespaces::class;
    }
}
