<?php

declare(strict_types=1);

namespace Gitlab\Tests\Model;

use Gitlab\Model\Label;
use Gitlab\Model\Project;
use Gitlab\Tests\TestCase;

final class LabelTest extends TestCase
{
    public function testCorrectConstructWithoutClient(): void
    {
        $project = new Project();
        $sUT     = new Label($project);

        $this->assertNull($sUT->getClient());
    }

    public function testCorrectConstruct(): void
    {
        $project = new Project();
        $sUT     = new Label($project, $this->client);

        $this->assertSame($this->client, $sUT->getClient());
    }

    public function testFromArray(): void
    {
        $project = new Project();

        $sUT = Label::fromArray($this->client, $project, ['color' => '#FF0000', 'name' => 'Testing', 'id' => 123]);

        $this->assertSame('#FF0000', $sUT->color);
        $this->assertSame('Testing', $sUT->name);
        $this->assertSame(123, $sUT->id);
        $this->assertSame($this->client, $sUT->getClient());
    }
}
