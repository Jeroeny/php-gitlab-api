<?php

declare(strict_types=1);

namespace Gitlab\Tests;

use Gitlab\SimpleResultPager;

final class ResultPagerTest extends TestCase
{
    public function testFetch(): void
    {
        $expected = ['project1', 'project2'];

        $api      = new MockApi($this->client);
        $api->all = $expected;
        $pager    = new SimpleResultPager($this->client);

        $result = $pager->fetch($api, 'all', [1]);

        $this->assertEquals($expected, $result);
    }
}
