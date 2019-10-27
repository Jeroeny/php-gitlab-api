<?php

declare(strict_types=1);

namespace Gitlab\Tests\Api;

use Gitlab\Api\Issues;

final class IssuesTest extends TestCase
{
    /** @var Issues */
    protected $api;

    /**
     * @test
     */
    public function shouldGetAllIssues(): void
    {
        $expectedArray = [
            ['id' => 1, 'title' => 'An issue'],
            ['id' => 2, 'title' => 'Another issue'],
        ];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->all());
    }

    /**
     * @test
     */
    public function shouldGetProjectIssuesWithPagination(): void
    {
        $expectedArray = [
            ['id' => 1, 'title' => 'An issue'],
            ['id' => 2, 'title' => 'Another issue'],
        ];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->all(1, ['page' => 2, 'per_page' => 5]));
    }

    /**
     * @test
     */
    public function shouldGetProjectIssuesWithParams(): void
    {
        $expectedArray = [
            ['id' => 1, 'title' => 'An issue'],
            ['id' => 2, 'title' => 'Another issue'],
        ];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->all(1, ['order_by' => 'created_at', 'sort' => 'desc', 'labels' => 'foo,bar', 'state' => 'opened']));
    }

    /**
     * @test
     */
    public function shouldShowIssue(): void
    {
        $expectedArray = ['id' => 2, 'title' => 'Another issue'];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->show(1, 2));
    }

    /**
     * @test
     */
    public function shouldCreateIssue(): void
    {
        $expectedArray = ['id' => 3, 'title' => 'A new issue'];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->create(1, ['title' => 'A new issue', 'labels' => 'foo,bar']));
    }

    /**
     * @test
     */
    public function shouldUpdateIssue(): void
    {
        $expectedArray = ['id' => 2, 'title' => 'A renamed issue'];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->update(1, 2, ['title' => 'A renamed issue', 'labels' => 'foo']));
    }

    /**
     * @test
     */
    public function shouldMoveIssue(): void
    {
        $expectedArray = ['id' => 2, 'title' => 'A moved issue'];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->move(1, 2, 3));
    }

    /**
     * @test
     */
    public function shouldGetIssueComments(): void
    {
        $expectedArray = [
            ['id' => 1, 'body' => 'A comment'],
            ['id' => 2, 'body' => 'Another comment'],
        ];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->showComments(1, 2));
    }

    /**
     * @test
     */
    public function shouldGetIssueComment(): void
    {
        $expectedArray = ['id' => 3, 'body' => 'A new comment'];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->showComment(1, 2, 3));
    }

    /**
     * @test
     */
    public function shouldCreateComment(): void
    {
        $expectedArray = ['id' => 3, 'body' => 'A new comment'];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->addComment(1, 2, ['body' => 'A new comment']));
        $this->assertEquals($expectedArray, $this->api->addComment(1, 2, 'A new comment'));
    }

    /**
     * @test
     */
    public function shouldUpdateComment(): void
    {
        $expectedArray = ['id' => 3, 'body' => 'An edited comment'];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->updateComment(1, 2, 3, 'An edited comment'));
    }

    /**
     * @test
     */
    public function shouldGetIssueDiscussions(): void
    {
        $expectedArray = [
            ['id' => 'abc', 'body' => 'A discussion'],
            ['id' => 'def', 'body' => 'Another discussion'],
        ];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->showDiscussions(1, 2));
    }

    /**
     * @test
     */
    public function shouldGetIssueDiscussion(): void
    {
        $expectedArray = ['id' => 'abc', 'body' => 'A discussion'];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->showDiscussion(1, 2, 'abc'));
    }

    /**
     * @test
     */
    public function shouldCreateDiscussion(): void
    {
        $expectedArray = ['id' => 'abc', 'body' => 'A new discussion'];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->addDiscussion(1, 2, ['body' => 'A new discussion']));
        $this->assertEquals($expectedArray, $this->api->addDiscussion(1, 2, 'A new discussion'));
    }

    /**
     * @test
     */
    public function shouldCreateDiscussionNote(): void
    {
        $expectedArray = ['id' => 3, 'body' => 'A new discussion note'];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->addDiscussionNote(1, 2, 'abc', ['body' => 'A new discussion note']));
        $this->assertEquals($expectedArray, $this->api->addDiscussionNote(1, 2, 'abc', 'A new discussion note'));
    }

    /**
     * @test
     */
    public function shouldUpdateDiscussionNote(): void
    {
        $expectedArray = ['id' => 3, 'body' => 'An edited discussion note'];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->updateDiscussionNote(1, 2, 'abc', 3, 'An edited discussion note'));
    }

    /**
     * @test
     */
    public function shouldRemoveDiscussionNote(): void
    {
        $this->setResponseBody(true);
        $this->assertTrue($this->api->removeDiscussionNote(1, 2, 'abc', 3));
    }

    /**
     * @test
     */
    public function shouldSetTimeEstimate(): void
    {
        $expectedArray = ['time_estimate' => 14400, 'total_time_spent' => 0, 'human_time_estimate' => '4h', 'human_total_time_spent' => null];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->setTimeEstimate(1, 2, '4h'));
    }

    /**
     * @test
     */
    public function shouldResetTimeEstimate(): void
    {
        $expectedArray = ['time_estimate' => 0, 'total_time_spent' => 0, 'human_time_estimate' => null, 'human_total_time_spent' => null];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->resetTimeEstimate(1, 2));
    }

    /**
     * @test
     */
    public function shouldAddSpentTime(): void
    {
        $expectedArray = ['time_estimate' => 0, 'total_time_spent' => 14400, 'human_time_estimate' => null, 'human_total_time_spent' => '4h'];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->addSpentTime(1, 2, '4h'));
    }

    /**
     * @test
     */
    public function shouldResetSpentTime(): void
    {
        $expectedArray = ['time_estimate' => 0, 'total_time_spent' => 0, 'human_time_estimate' => null, 'human_total_time_spent' => null];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->resetSpentTime(1, 2));
    }

    /**
     * @test
     */
    public function shouldGetIssueTimeStats(): void
    {
        $expectedArray = ['time_estimate' => 14400, 'total_time_spent' => 5400, 'human_time_estimate' => '4h', 'human_total_time_spent' => '1h 30m'];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->getTimeStats(1, 2));
    }

    /**
     * @test
     */
    public function shouldGetIssueAwardEmoji(): void
    {
        $expectedArray = [
            ['id' => 1, 'name' => 'sparkles'],
            ['id' => 2, 'name' => 'heart_eyes'],
        ];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->awardEmoji(1, 2));
    }

    /**
     * @test
     */
    public function shouldGetIssueClosedByMergeRequests(): void
    {
        $expectedArray = [
            ['id' => 1, 'iid' => '1111', 'title' => 'Just saving the world'],
            ['id' => 2, 'iid' => '1112', 'title' => 'Adding new feature to get merge requests that close an issue'],
        ];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->closedByMergeRequests(1, 2));
    }

    /**
     * @test
     */
    public function shouldGetProjectIssuesByAssignee(): void
    {
        $expectedArray = [
            ['id' => 1, 'title' => 'An issue'],
            ['id' => 2, 'title' => 'Another issue'],
        ];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->all(1, ['assignee_id' => 1]));
    }

    protected function getApiClass(): string
    {
        return Issues::class;
    }
}
