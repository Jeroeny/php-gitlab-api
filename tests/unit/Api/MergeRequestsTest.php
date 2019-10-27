<?php

declare(strict_types=1);

namespace Gitlab\Tests\Api;

use DateTimeImmutable;
use Gitlab\Api\MergeRequests;

final class MergeRequestsTest extends TestCase
{
    /** @var MergeRequests */
    protected $api;

    /**
     * @test
     */
    public function shouldGetAll(): void
    {
        $expectedArray = $this->getMultipleMergeRequestsData();

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->all(1));
    }

    /**
     * @test
     */
    public function shouldGetAllWithParams(): void
    {
        $expectedArray = $this->getMultipleMergeRequestsData();
        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->all(1, [
            'page' => 2,
            'per_page' => 5,
            'labels' => 'label1,label2,label3',
            'milestone' => 'milestone1',
            'order_by' => 'updated_at',
            'state' => 'all',
            'sort' => 'desc',
            'scope' => 'all',
            'author_id' => 1,
            'assignee_id' => 1,
            'source_branch' => 'develop',
            'target_branch' => 'master',
        ]));
    }

    /**
     * @test
     */
    public function shouldGetAllWithDateTimeParams(): void
    {
        $expectedArray = $this->getMultipleMergeRequestsData();

        $createdAfter  = new DateTimeImmutable('2018-01-01 00:00:00');
        $createdBefore = new DateTimeImmutable('2018-01-31 00:00:00');

        $this->setResponseBody($expectedArray);
        $this->assertEquals(
            $expectedArray,
            $this->api->all(1, ['created_after' => $createdAfter, 'created_before' => $createdBefore])
        );
    }

    /**
     * @test
     */
    public function shouldShowMergeRequest(): void
    {
        $expectedArray = ['id' => 2, 'name' => 'A merge request'];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->show(1, 2));
    }

    /**
     * @test
     */
    public function shouldCreateMergeRequestWithoutOptionalParams(): void
    {
        $expectedArray = ['id' => 3, 'title' => 'Merge Request'];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->create(1, 'develop', 'master', 'Merge Request'));
    }

    /**
     * @test
     */
    public function shouldCreateMergeRequestWithOptionalParams(): void
    {
        $expectedArray = ['id' => 3, 'title' => 'Merge Request'];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->create(1, 'develop', 'master', 'Merge Request', 6, 20, 'Some changes'));
    }

    /**
     * @test
     */
    public function shouldUpdateMergeRequest(): void
    {
        $expectedArray = ['id' => 2, 'title' => 'Updated title'];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->update(1, 2, [
            'title' => 'Updated title',
            'description' => 'No so many changes now',
            'state_event' => 'close',
        ]));
    }

    /**
     * @test
     */
    public function shouldMergeMergeRequest(): void
    {
        $expectedArray = ['id' => 2, 'title' => 'Updated title'];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->merge(1, 2, 'Accepted'));
        $this->assertEquals($expectedArray, $this->api->merge(1, 2, ['merge_commit_message' => 'Accepted']));
    }

    /**
     * @test
     */
    public function shouldGetMergeRequestNotes(): void
    {
        $expectedArray = [
            ['id' => 1, 'body' => 'A comment'],
            ['id' => 2, 'body' => 'Another comment'],
        ];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->showNotes(1, 2));
    }

    /**
     * @test
     */
    public function shouldRemoveMergeRequestNote(): void
    {
        $this->setResponseBody(true);
        $this->assertTrue($this->api->removeNote(1, 2, 1));
    }

    /**
     * @test
     */
    public function shouldGetMergeRequestChanges(): void
    {
        $expectedArray = ['id' => 1, 'title' => 'A merge request'];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->changes(1, 2));
    }

    /**
     * @test
     */
    public function shouldGetMergeRequestDiscussions(): void
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
    public function shouldGetMergeRequestDiscussion(): void
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
    }

    /**
     * @test
     */
    public function shouldResolveDiscussion(): void
    {
        $expectedArray = ['id' => 'abc', 'resolved' => true];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->resolveDiscussion(1, 2, 'abc', true));
    }

    /**
     * @test
     */
    public function shouldUnresolveDiscussion(): void
    {
        $expectedArray = ['id' => 'abc', 'resolved' => false];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->resolveDiscussion(1, 2, 'abc', false));
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
        $this->assertEquals($expectedArray, $this->api->updateDiscussionNote(1, 2, 'abc', 3, ['body' => 'An edited discussion note']));
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
    public function shouldGetIssuesClosedByMergeRequest(): void
    {
        $expectedArray = ['id' => 1, 'title' => 'A merge request'];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->closesIssues(1, 2));
    }

    /**
     * @test
     */
    public function shouldGetMergeRequestByIid(): void
    {
        $expectedArray = ['id' => 1, 'title' => 'A merge request'];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->all(1, ['iids' => [2]]));
    }

    /**
     * @test
     */
    public function shouldApproveMergeRequest(): void
    {
        $expectedArray = ['id' => 1, 'title' => 'Approvals API'];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->approve(1, 2));
    }

    /**
     * @test
     */
    public function shouldUnApproveMergeRequest(): void
    {
        $expectedArray = ['id' => 1, 'title' => 'Approvals API'];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->unapprove(1, 2));
    }

    /**
     * @test
     */
    public function shouldGetMergeRequestApprovals(): void
    {
        $expectedArray = ['id' => 1, 'title' => 'Approvals API'];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->all(1, ['iids' => [2]]));
    }

    /**
     * @test
     */
    public function shouldGetMergeRequestAwardEmoji(): void
    {
        $expectedArray = [
            ['id' => 1, 'name' => 'sparkles'],
            ['id' => 2, 'name' => 'heart_eyes'],
        ];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->awardEmoji(1, 2));
    }

    protected function getMultipleMergeRequestsData(): array
    {
        return [
            ['id' => 1, 'title' => 'A merge request'],
            ['id' => 2, 'title' => 'Another merge request'],
        ];
    }

    protected function getApiClass(): string
    {
        return MergeRequests::class;
    }
}
