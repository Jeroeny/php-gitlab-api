<?php

declare(strict_types=1);

namespace Gitlab\Tests\Api;

use Gitlab\Api\Tags;

final class TagsTest extends TestCase
{
    /** @var Tags */
    protected $api;

    /**
     * @test
     */
    public function shouldGetAllTags(): void
    {
        $expectedArray = [
            ['name' => 'v1.0.0'],
            ['name' => 'v1.1.0'],
        ];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->all(1));
    }

    /**
     * @test
     */
    public function shouldShowTag(): void
    {
        $expectedArray = [
            ['name' => 'v1.0.0'],
        ];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->show(1, 'v1.0.0'));
    }

    /**
     * @test
     */
    public function shouldCreateTag(): void
    {
        $expectedArray = [
            ['name' => 'v1.1.0'],
        ];

        $params = [
            'id'       => 1,
            'tag_name' => 'v1.1.0',
            'ref'      => 'ref/heads/master',
        ];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->create(1, $params));
    }

    /**
     * @test
     */
    public function shouldRemoveTag(): void
    {
        $expectedArray = [
            ['name' => 'v1.1.0'],
        ];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->remove(1, 'v1.1.0'));
    }

    /**
     * @param mixed[] $expectedResult
     *
     * @test
     * @dataProvider releaseDataProvider
     */
    public function shouldCreateRelease(string $releaseName, string $description, array $expectedResult): void
    {
        $params = ['description' => $description];

        $this->setResponseBody($expectedResult);
        $this->assertEquals($expectedResult, $this->api->createRelease(1, $releaseName, $params));
    }

    /**
     * @param mixed[] $expectedResult
     *
     * @dataProvider releaseDataProvider
     */
    public function testShouldUpdateRelease(string $releaseName, string $description, array $expectedResult): void
    {
        $params = ['description' => $description];

        $this->setResponseBody($expectedResult);
        $this->assertEquals($expectedResult, $this->api->updateRelease(1, $releaseName, $params));
    }

    public function releaseDataProvider(): array
    {
        return [
            [
                'tagName' => 'v1.1.0',
                'description' => 'Amazing release. Wow',
                'expectedResult' => [
                    'tag_name' => '1.0.0',
                    'description' => 'Amazing release. Wow',
                ],
            ],
            [
                'tagName' => 'version/1.1.0',
                'description' => 'Amazing release. Wow',
                'expectedResult' => [
                    'tag_name' => 'version/1.1.0',
                    'description' => 'Amazing release. Wow',
                ],
            ],
        ];
    }

    protected function getApiClass(): string
    {
        return Tags::class;
    }
}
