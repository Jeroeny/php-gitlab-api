<?php

declare(strict_types=1);

namespace Gitlab\Tests\Api;

use Gitlab\Api\Schedules;

final class SchedulesTest extends TestCase
{
    /** @var Schedules */
    protected $api;

    /**
     * @test
     */
    public function shouldCreateSchedule(): void
    {
        $expectedArray = [
            'id' => 13,
            'description' => 'Test schedule pipeline',
            'ref' => 'master',
            'cron' => '* * * * *',
            'cron_timezone' => 'Asia/Tokyo',
            'next_run_at' => '2017-05-19T13:41:00.000Z',
            'active' => true,
            'created_at' => '2017-05-19T13:31:08.849Z',
            'updated_at' => '2017-05-19T13:40:17.727Z',
        ];
        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->create(
            1,
            [
                'id' => 13,
                'description' => 'Test schedule pipeline',
                'ref' => 'master',
                'cron' => '* * * * *',
                'cron_timezone' => 'Asia/Tokyo',
                'next_run_at' => '2017-05-19T13:41:00.000Z',
                'active' => true,
                'created_at' => '2017-05-19T13:31:08.849Z',
                'updated_at' => '2017-05-19T13:40:17.727Z',
            ]
        ));
    }

    /**
     * @test
     */
    public function shouldShowSchedule(): void
    {
        $expectedArray = ['id' => 1, 'name' => 'A schedule'];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->show(1, 2));
    }

    /**
     * @test
     */
    public function shouldShowAllSchedule(): void
    {
        $expectedArray = ['id' => 1, 'name' => 'A schedule'];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->showAll(1));
    }

    /**
     * @test
     */
    public function shouldUpdateSchedule(): void
    {
        $expectedArray = ['id' => 3, 'title' => 'Updated schedule'];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->update(1, 3, ['title' => 'Updated schedule', 'due_date' => '2015-04-01', 'state_event' => 'close']));
    }

    /**
     * @test
     */
    public function shouldRemoveSchedule(): void
    {
        $this->setResponseBody(true);
        $this->assertTrue($this->api->remove(1, 2));
    }

    protected function getApiClass(): string
    {
        return Schedules::class;
    }
}
