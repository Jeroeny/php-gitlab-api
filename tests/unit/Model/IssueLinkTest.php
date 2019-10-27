<?php

declare(strict_types=1);

namespace Gitlab\Tests\Model;

use Gitlab\Model\Issue;
use Gitlab\Model\IssueLink;
use Gitlab\Model\Project;
use Gitlab\Tests\TestCase;

final class IssueLinkTest extends TestCase
{
    /**
     * @test
     */
    public function testCorrectConstruct(): void
    {
        $issue = new Issue(new Project());

        $issueLink = new IssueLink($issue, 1, $this->client);

        $this->assertSame(1, $issueLink->issue_link_id);
        $this->assertSame($issue, $issueLink->issue);
        $this->assertSame($this->client, $issueLink->getClient());
    }

    public function testFromArray(): void
    {
        $issueLink = IssueLink::fromArray($this->client, new Project(), ['issue_link_id' => 1, 'iid' => 10]);

        $this->assertSame(1, $issueLink->issue_link_id);
        $this->assertInstanceOf(Issue::class, $issueLink->issue);
        $this->assertSame(10, $issueLink->issue->iid);
        $this->assertSame($this->client, $issueLink->getClient());
    }
}
