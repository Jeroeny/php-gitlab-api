<?php

declare(strict_types=1);

namespace Gitlab\Tests\Api;

use Gitlab\Api\Keys;

final class KeysTest extends TestCase
{
    /** @var Keys */
    protected $api;

    /**
     * @test
     */
    public function shouldShowKey(): void
    {
        $expectedArray = ['id' => 1, 'title' => 'A key', 'key' => 'ssh-rsa key', 'created_at' => '2016-01-01T01:00:00.000Z'];
        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->show(1));
    }

    protected function getApiClass(): string
    {
        return Keys::class;
    }
}
