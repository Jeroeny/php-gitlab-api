<?php

declare(strict_types=1);

namespace Gitlab\Tests\Api;

use Gitlab\Api\Version;

final class VersionTest extends TestCase
{
    /** @var Version */
    protected $api;

    /**
     * @test
     */
    public function shouldShowVersion(): void
    {
        $expectedArray = [
            'version' => '8.13.0-pre',
            'revision' => '4e963fe',
        ];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->show());
    }

    protected function getApiClass(): string
    {
        return Version::class;
    }
}
