<?php

declare(strict_types=1);

namespace Gitlab\Tests\Model;

use Gitlab\Model\Release;
use Gitlab\Tests\TestCase;

final class ReleaseTest extends TestCase
{
    public function testFromArray(): void
    {
        $params = [
            'tag_name' => 'v1.0.0',
            'description' => 'Amazing release. Wow',
        ];

        $release = Release::fromArray($this->client, $params);

        $this->assertSame($params['tag_name'], $release->tag_name);
        $this->assertSame($params['description'], $release->description);
    }
}
