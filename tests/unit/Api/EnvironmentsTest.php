<?php

declare(strict_types=1);

namespace Gitlab\Tests\Api;

use Gitlab\Api\Environments;

final class EnvironmentsTest extends TestCase
{
    /** @var Environments */
    protected $api;

    /**
     * @test
     */
    public function shouldGetAllEnvironments(): void
    {
        $expectedArray = [
            [
                'id' => 1,
                'name' => 'review/fix-foo',
                'slug' => 'review-fix-foo-dfjre3',
                'external_url' => 'https://review-fix-foo-dfjre3.example.gitlab.com',
            ],
            [
                'id' => 2,
                'name' => 'review/fix-bar',
                'slug' => 'review-fix-bar-dfjre4',
                'external_url' => 'https://review-fix-bar-dfjre4.example.gitlab.com',
            ],
        ];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->all(1));
    }

    /**
     * @test
     */
    public function shouldCreateEnvironment(): void
    {
        $expectedArray = [
            [
                'id' => 3,
                'name' => 'review/fix-baz',
                'slug' => 'review-fix-baz-dfjre5',
                'external_url' => 'https://review-fix-baz-dfjre5.example.gitlab.com',
            ],
        ];

        $params = [
            'name' => 'review/fix-baz',
            'external_url' => 'https://review-fix-baz-dfjre5.example.gitlab.com',
        ];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->create(1, $params));
    }

    /**
     * @test
     */
    public function shouldRemoveEnvironment(): void
    {
        $this->setResponseBody(true);
        $this->assertTrue($this->api->remove(1, 3));
    }

    public function testShouldStopEnvironment(): void
    {
        $this->setResponseBody(true);
        $this->assertTrue($this->api->stop(1, 3));
    }

    protected function getApiClass(): string
    {
        return Environments::class;
    }
}
