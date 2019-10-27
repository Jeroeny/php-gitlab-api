<?php

declare(strict_types=1);

namespace Gitlab\Tests\Model;

use Gitlab\Client;
use Gitlab\Model\Issue;
use Gitlab\Model\IssueLink;
use Gitlab\Model\Project;
use Gitlab\Tests\TestCase;
use Http\Message\RequestMatcher\RequestMatcher;

final class IssueTest extends TestCase
{
    public function testCorrectConstructWithoutIidAndClient(): void
    {
        $project = new Project();
        $sUT     = new Issue($project);

        $this->assertSame($project, $sUT->project);
        $this->assertNull($sUT->iid);
        $this->assertNull($sUT->getClient());
    }

    public function testCorrectConstructWithoutClient(): void
    {
        $project = new Project();

        $sUT = new Issue($project, 10);

        $this->assertSame($project, $sUT->project);
        $this->assertSame(10, $sUT->iid);
        $this->assertNull($sUT->getClient());
    }

    public function testCorrectConstruct(): void
    {
        $project = new Project();

        $sUT = new Issue($project, 10, $this->client);

        $this->assertSame($project, $sUT->project);
        $this->assertSame(10, $sUT->iid);
        $this->assertSame($this->client, $sUT->getClient());
    }

    public function testFromArray(): void
    {
        $project = new Project();

        $sUT = Issue::fromArray($this->client, $project, ['iid' => 10]);

        $this->assertSame($project, $sUT->project);
        $this->assertSame(10, $sUT->iid);
        $this->assertSame($this->client, $sUT->getClient());
    }

    /**
     * @param mixed[] $data
     */
    public function getIssueMock(array $data = []): Issue
    {
        $client  = new Client();
        $project = new Project(1, $client);

        return Issue::fromArray($client, $project, $data);
    }

    public function testIsClosed(): void
    {
        $opened_data  = [
            'iid' => 1,
            'state' => 'opened',
        ];
        $opened_issue = $this->getIssueMock($opened_data);

        $this->assertFalse($opened_issue->isClosed());

        $closed_data  = [
            'iid' => 1,
            'state' => 'closed',
        ];
        $closed_issue = $this->getIssueMock($closed_data);

        $this->assertTrue($closed_issue->isClosed());
    }

    public function testHasLabel(): void
    {
        $data  = [
            'iid' => 1,
            'labels' => ['foo', 'bar'],
        ];
        $issue = $this->getIssueMock($data);

        $this->assertTrue($issue->hasLabel('foo'));
        $this->assertTrue($issue->hasLabel('bar'));
        $this->assertFalse($issue->hasLabel(''));
    }

    public function testMove(): void
    {
        $project   = new Project(1);
        $toProject = new Project(2);

        $this->setResponseBody(['iid' => 11]);

        $issue = Issue::fromArray($this->client, $project, ['iid' => 10])->move($toProject);

        $this->assertInstanceOf(Issue::class, $issue);
        $this->assertSame($this->client, $issue->getClient());
        $this->assertSame($toProject, $issue->project);
        $this->assertSame(11, $issue->iid);
    }

    public function testLinks(): void
    {
        $this->httpClient->on(
            new RequestMatcher('issues/'),
            $this->createResponse([
                ['issue_link_id' => 100, 'iid' => 10, 'project_id' => 1],
                ['issue_link_id' => 200, 'iid' => 20, 'project_id' => 2],
            ])
        );

        $this->httpClient->on(
            new RequestMatcher('projects/'),
            $this->createResponse(['id' => 1])
        );

        $issue      = new Issue(new Project(1, $this->client), 10, $this->client);
        $issueLinks = $issue->links();

        $this->assertIsArray($issueLinks);
        $this->assertCount(2, $issueLinks);

        $this->assertInstanceOf(IssueLink::class, $issueLinks[0]);
        $this->assertSame(100, $issueLinks[0]->issue_link_id);
        $this->assertInstanceOf(Issue::class, $issueLinks[0]->issue);
        $this->assertSame(10, $issueLinks[0]->issue->iid);
        $this->assertInstanceOf(Project::class, $issueLinks[0]->issue->project);
        $this->assertSame(1, $issueLinks[0]->issue->project->id);

        $this->assertInstanceOf(IssueLink::class, $issueLinks[1]);
        $this->assertSame(200, $issueLinks[1]->issue_link_id);
        $this->assertInstanceOf(Issue::class, $issueLinks[1]->issue);
        $this->assertSame(20, $issueLinks[1]->issue->iid);
        $this->assertInstanceOf(Project::class, $issueLinks[1]->issue->project);
        $this->assertSame(1, $issueLinks[1]->issue->project->id);
    }

    public function testAddLink(): void
    {
        $this->setResponseBody([
            'source_issue' => ['iid' => 10, 'project_id' => 1],
            'target_issue' => ['iid' => 20, 'project_id' => 2],
        ]);
        $issue      = new Issue(new Project(1, $this->client), 10, $this->client);
        $issueLinks = $issue->addLink(new Issue(new Project(2, $this->client), 20, $this->client));

        $this->assertIsArray($issueLinks);
        $this->assertCount(2, $issueLinks);

        $this->assertInstanceOf(Issue::class, $issueLinks['source_issue']);
        $this->assertSame(10, $issueLinks['source_issue']->iid);
        $this->assertInstanceOf(Project::class, $issueLinks['source_issue']->project);
        $this->assertSame(1, $issueLinks['source_issue']->project->id);

        $this->assertInstanceOf(Issue::class, $issueLinks['target_issue']);
        $this->assertSame(20, $issueLinks['target_issue']->iid);
        $this->assertInstanceOf(Project::class, $issueLinks['target_issue']->project);
        $this->assertSame(2, $issueLinks['target_issue']->project->id);
    }

    public function testRemoveLink(): void
    {
        $this->setResponseBody([
            'id' => 2,
            'source_issue' => ['iid' => 10, 'project_id' => 1],
            'target_issue' => ['iid' => 20, 'project_id' => 2],
        ]);

        $issue      = new Issue(new Project(1, $this->client), 10, $this->client);
        $issueLinks = $issue->removeLink(100);

        $this->assertIsArray($issueLinks);
        $this->assertCount(2, $issueLinks);

        $this->assertInstanceOf(Issue::class, $issueLinks['source_issue']);
        $this->assertSame(10, $issueLinks['source_issue']->iid);
        $this->assertInstanceOf(Project::class, $issueLinks['source_issue']->project);
        $this->assertSame(1, $issueLinks['source_issue']->project->id);

        $this->assertInstanceOf(Issue::class, $issueLinks['target_issue']);
        $this->assertSame(20, $issueLinks['target_issue']->iid);
        $this->assertInstanceOf(Project::class, $issueLinks['target_issue']->project);
        $this->assertSame(2, $issueLinks['target_issue']->project->id);
    }
}
