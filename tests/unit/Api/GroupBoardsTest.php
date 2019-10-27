<?php

declare(strict_types=1);

namespace Gitlab\Tests\Api;

use Gitlab\Api\GroupsBoards;

final class GroupBoardsTest extends TestCase
{
    /** @var GroupsBoards */
    protected $api;

    /**
     * @test
     */
    public function shouldGetAllBoards(): void
    {
        $expectedArray = [
            ['id' => 1, 'title' => 'A board'],
            ['id' => 2, 'title' => 'Another board'],
        ];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->all());
    }

    /**
     * @test
     */
    public function shouldShowIssueBoard(): void
    {
        $expectedArray = ['id' => 2, 'name' => 'Another issue board'];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->show(1, 2));
    }

    /**
     * @test
     */
    public function shouldCreateIssueBoard(): void
    {
        $expectedArray = ['id' => 3, 'name' => 'A new issue board'];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->create(1, ['name' => 'A new issue board']));
    }

    /**
     * @test
     */
    public function shouldUpdateIssueBoard(): void
    {
        $expectedArray = ['id' => 2, 'name' => 'A renamed issue board'];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->update(1, 2, ['name' => 'A renamed issue board', 'labels' => 'foo']));
    }

    /**
     * @test
     */
    public function shouldRemoveIssueBoard(): void
    {
        $this->setResponseBody(true);
        $this->assertTrue($this->api->remove(1, 2));
    }

    /**
     * @test
     */
    public function shouldGetAllLists(): void
    {
        $expectedArray = [
            [
                'id' => 1,
                'label' => [
                    'name' => 'First label',
                    'color' => '#F0AD4E',
                    'description' => null,
                ],
                'position' => 1,
            ], [
                'id' => 2,
                'label' => [
                    'name' => 'Second label',
                    'color' => '#F0AD4E',
                    'description' => null,
                ],
                'position' => 2,
            ],
        ];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->allLists(1, 2));
    }

    /**
     * @test
     */
    public function shouldGetList(): void
    {
        $expectedArray = [
            [
                'id' => 3,
                'label' => [
                    'name' => 'Some label',
                    'color' => '#F0AD4E',
                    'description' => null,
                ],
                'position' => 3,
            ],
        ];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->showList(1, 2, 3));
    }

    /**
     * @test
     */
    public function shouldCreateList(): void
    {
        $expectedArray = [
            [
                'id' => 3,
                'label' => [
                    'name' => 'Some label',
                    'color' => '#F0AD4E',
                    'description' => null,
                ],
                'position' => 3,
            ],
        ];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->createList(1, 2, 4));
    }

    /**
     * @test
     */
    public function shouldUpdateList(): void
    {
        $expectedArray = [
            [
                'id' => 3,
                'label' => [
                    'name' => 'Some label',
                    'color' => '#F0AD4E',
                    'description' => null,
                ],
                'position' => 1,
            ],
        ];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->updateList(5, 2, 3, 1));
    }

    /**
     * @test
     */
    public function shouldDeleteList(): void
    {
        $this->setResponseBody(true);
        $this->assertTrue($this->api->deleteList(1, 2, 3));
    }

    protected function getApiClass(): string
    {
        return GroupsBoards::class;
    }
}
