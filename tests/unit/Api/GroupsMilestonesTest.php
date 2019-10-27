<?php

declare(strict_types=1);

namespace Gitlab\Tests\Api;

use Gitlab\Api\GroupsMilestones;

final class GroupsMilestonesTest extends TestCase
{
    /** @var GroupsMilestones */
    protected $api;

    /**
     * @test
     */
    public function shouldGetAllMilestones(): void
    {
        $expectedArray = [
            ['id' => 1, 'title' => 'A milestone'],
            ['id' => 2, 'title' => 'Another milestone'],
        ];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->all(1));
    }

    /**
     * @test
     */
    public function shouldShowMilestone(): void
    {
        $expectedArray = ['id' => 1, 'name' => 'A milestone'];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->show(1, 2));
    }

    /**
     * @test
     */
    public function shouldCreateMilestone(): void
    {
        $expectedArray = ['id' => 3, 'title' => 'A new milestone'];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->create(1, ['description' => 'Some text', 'title' => 'A new milestone']));
    }

    /**
     * @test
     */
    public function shouldUpdateMilestone(): void
    {
        $expectedArray = ['id' => 3, 'title' => 'Updated milestone'];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->update(1, 3, ['title' => 'Updated milestone', 'due_date' => '2015-04-01', 'state_event' => 'close']));
    }

    /**
     * @test
     */
    public function shouldRemoveMilestone(): void
    {
        $this->setResponseBody(true);
        $this->assertTrue($this->api->remove(1, 2));
    }

    /**
     * @test
     */
    public function shouldGetMilestonesIssues(): void
    {
        $expectedArray = [
            ['id' => 1, 'title' => 'An issue'],
            ['id' => 2, 'title' => 'Another issue'],
        ];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->issues(1, 3));
    }

    /**
     * @test
     */
    public function shouldGetMilestonesMergeRequests(): void
    {
        $expectedArray = [
            ['id' => 1, 'title' => 'A merge request'],
            ['id' => 2, 'title' => 'Another merge request'],
        ];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->mergeRequests(1, 3));
    }

    protected function getApiClass(): string
    {
        return GroupsMilestones::class;
    }
}
