<?php

declare(strict_types=1);

namespace Gitlab\Tests\Api;

use DateTimeImmutable;
use Gitlab\Api\IssuesStatistics;

final class IssuesStatisticsTest extends TestCase
{
    /** @var IssuesStatistics */
    protected $api;

    /**
     * @test
     */
    public function shouldGetAll(): void
    {
        $expectedArray = [];
        $now           = new DateTimeImmutable();
        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->all([
            'milestone' => '',
            'labels' => '',
            'scope' => 'created-by-me',
            'author_id' => 1,
            'author_username' => '',
            'assignee_id' => 1,
            'assignee_username' => '',
            'my_reaction_emoji' => '',
            'search' => '',
            'created_after' => $now,
            'created_before' => $now,
            'updated_after' => $now,
            'updated_before' => $now,
            'confidential' => false,
        ]));
    }

    /**
     * @test
     */
    public function shouldGetProject(): void
    {
        $expectedArray = [];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->project(1, []));
    }

    /**
     * @test
     */
    public function shouldGetGroup(): void
    {
        $expectedArray = [];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->group(1, []));
    }

    protected function getApiClass(): string
    {
        return IssuesStatistics::class;
    }
}
