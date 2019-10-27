<?php

declare(strict_types=1);

namespace Gitlab\Tests\Api;

use Gitlab\Api\SystemHooks;

final class SystemHooksTest extends TestCase
{
    /** @var SystemHooks */
    protected $api;

    /**
     * @test
     */
    public function shouldGetAllHooks(): void
    {
        $expectedArray = [
            ['id' => 1, 'url' => 'http://www.example.com'],
            ['id' => 2, 'url' => 'http://www.example.org'],
        ];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->all());
    }

    /**
     * @test
     */
    public function shouldCreateHook(): void
    {
        $expectedArray = ['id' => 3, 'url' => 'http://www.example.net'];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->create('http://www.example.net'));
    }

    /**
     * @test
     */
    public function shouldTestHook(): void
    {
        $this->setResponseBody(true);
        $this->assertTrue($this->api->test(3));
    }

    /**
     * @test
     */
    public function shouldRemoveHook(): void
    {
        $this->setResponseBody(true);
        $this->assertTrue($this->api->remove(3));
    }

    protected function getApiClass(): string
    {
        return SystemHooks::class;
    }
}
