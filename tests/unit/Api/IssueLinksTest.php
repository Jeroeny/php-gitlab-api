<?php

declare(strict_types=1);

namespace Gitlab\Tests\Api;

use Gitlab\Api\IssueLinks;

final class IssueLinksTest extends TestCase
{
    /** @var IssueLinks */
    protected $api;

    /**
     * @inheritdoc
     */
    protected function getApiClass(): string
    {
        return IssueLinks::class;
    }

    /**
     * @test
     */
    public function shouldGetIssueLinks(): void
    {
        $expectedArray = [
            ['issue_link_id' => 100],
            ['issue_link_id' => 101],
        ];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->all(1, 10));
    }

    /**
     * @test
     */
    public function shouldCreateIssueLink(): void
    {
        $expectedArray = [
            'source_issue' => ['iid' => 10, 'project_id' => 1],
            'target_issue' => ['iid' => 20, 'project_id' => 2],
        ];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->create(1, 10, 2, 20));
    }

    /**
     * @test
     */
    public function shouldRemoveIssueLink(): void
    {
        $expectedArray = [
            'source_issue' => ['iid' => 10, 'project_id' => 1],
            'target_issue' => ['iid' => 20, 'project_id' => 2],
        ];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->remove(1, 10, 100));
    }
}
