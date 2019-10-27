<?php

declare(strict_types=1);

namespace Gitlab\Tests\Api;

use DateTimeImmutable;
use Gitlab\Api\Repositories;
use const DATE_ATOM;

final class RepositoriesTest extends TestCase
{
    /** @var Repositories */
    protected $api;

    /**
     * @test
     */
    public function shouldGetBranches(): void
    {
        $expectedArray = [
            ['name' => 'master'],
            ['name' => 'develop'],
        ];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->branches(1, ['search' => '^term']));
    }

    /**
     * @test
     */
    public function shouldGetBranch(): void
    {
        $expectedArray = ['name' => 'master'];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->branch(1, 'master'));
    }

    /**
     * @test
     */
    public function shouldCreateBranch(): void
    {
        $expectedArray = ['name' => 'feature'];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->createBranch(1, 'feature', 'master'));
    }

    /**
     * @test
     */
    public function shouldDeleteBranch(): void
    {
        $this->setResponseBody(true);
        $this->assertTrue($this->api->deleteBranch(1, 'feature/TEST-15'));
    }

    /**
     * @test
     */
    public function shouldProtectBranch(): void
    {
        $expectedArray = ['name' => 'master'];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->protectBranch(1, 'master'));
    }

    /**
     * @test
     */
    public function shouldProtectBranchWithPermissions(): void
    {
        $expectedArray = ['name' => 'master'];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->protectBranch(1, 'master', true, true));
    }

    /**
     * @test
     */
    public function shouldUnprotectBranch(): void
    {
        $expectedArray = ['name' => 'master'];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->unprotectBranch(1, 'master'));
    }

    /**
     * @test
     */
    public function shouldGetTags(): void
    {
        $expectedArray = [
            ['name' => '1.0'],
            ['name' => '1.1'],
        ];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->tags(1));
    }

    /**
     * @test
     */
    public function shouldCreateTag(): void
    {
        $expectedArray = ['name' => '1.0'];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->createTag(1, '1.0', 'abcd1234', '1.0 release'));
    }

    /**
     * @test
     */
    public function shouldCreateRelease(): void
    {
        $project_id  = 1;
        $tagName     = 'sometag';
        $description = '1.0 release';

        $expectedArray = ['name' => $tagName];
        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->createRelease($project_id, $tagName, $description));
    }

    /**
     * @test
     */
    public function shouldUpdateRelease(): void
    {
        $project_id  = 1;
        $tagName     = 'sometag';
        $description = '1.0 release';

        $expectedArray = ['description' => $tagName];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->updateRelease($project_id, $tagName, $description));
    }

    /**
     * @test
     */
    public function shouldGetReleases(): void
    {
        $project_id = 1;

        $expectedArray = [
            [
                'tag_name' => 'v0.2',
                'description' => '## CHANGELOG\r\n\r\n- Escape label and milestone titles to prevent XSS in GFM autocomplete. !2740\r\n- Prevent private snippets from being embeddable.\r\n- Add subresources removal to member destroy service.',
                'name' => 'Awesome app v0.2 beta',
            ],
        ];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->releases($project_id));
    }

    /**
     * @test
     */
    public function shouldGetCommits(): void
    {
        $expectedArray = [
            ['id' => 'abcd1234', 'title' => 'A commit'],
            ['id' => 'efgh5678', 'title' => 'Another commit'],
        ];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->commits(1));
    }

    /**
     * @test
     */
    public function shouldGetCommitsWithParams(): void
    {
        $expectedArray = [
            ['id' => 'abcd1234', 'title' => 'A commit'],
            ['id' => 'efgh5678', 'title' => 'Another commit'],
        ];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->commits(1, ['page' => 2, 'per_page' => 25, 'ref_name' => 'master', 'all' => true, 'with_stats' => true, 'path' => 'file_path/file_name']));
    }

    /**
     * @test
     */
    public function shouldGetCommitsWithTimeParams(): void
    {
        $expectedArray = [
            ['id' => 'abcd1234', 'title' => 'A commit'],
            ['id' => 'efgh5678', 'title' => 'Another commit'],
        ];

        $since = new DateTimeImmutable('2018-01-01 00:00:00');
        $until = new DateTimeImmutable('2018-01-31 00:00:00');

        $expectedWithArray = [
            'since' => $since->format(DATE_ATOM),
            'until' => $until->format(DATE_ATOM),
        ];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->commits(1, ['since' => $since, 'until' => $until]));
    }

    /**
     * @test
     */
    public function shouldGetCommit(): void
    {
        $expectedArray = ['id' => 'abcd1234', 'title' => 'A commit'];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->commit(1, 'abcd1234'));
    }

    /**
     * @test
     */
    public function shouldGetCommitRefs(): void
    {
        $expectedArray = [
            ['type' => 'branch', 'name' => 'master'],
            ['type' => 'tag', 'name' => 'v1.1.0'],
        ];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->commitRefs(1, 'abcd1234'));
    }

    /**
     * @param mixed[] $expectedArray
     *
     * @dataProvider dataGetCommitRefsWithParams
     */
    public function testShouldGetCommitRefsWithParams(string $type, array $expectedArray): void
    {
        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->commitRefs(1, 'abcd1234', ['type' => $type]));
    }

    public function dataGetCommitRefsWithParams(): array
    {
        return [
            'type_tag' => [
                'type' => Repositories::TYPE_TAG,
                'expectedArray' => [['type' => 'tag', 'name' => 'v1.1.0']],
            ],
            'type_branch' => [
                'type' => Repositories::TYPE_BRANCH,
                'expectedArray' => [['type' => 'branch', 'name' => 'master']],
            ],
        ];
    }

    /**
     * @test
     */
    public function shouldCreateCommit(): void
    {
        $expectedArray = ['title' => 'Initial commit.', 'author_name' => 'John Doe', 'author_email' => 'john@example.com'];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->createCommit(1, [
            'branch' => 'master',
            'commit_message' => 'Initial commit.',
            'actions' => [
                [
                    'action' => 'create',
                    'file_path' => 'README.md',
                    'content' => '# My new project',
                ],
                [
                    'action' => 'create',
                    'file_path' => 'LICENSE',
                    'content' => 'MIT License...',
                ],
            ],
            'author_name' => 'John Doe',
            'author_email' => 'john@example.com',
        ]));
    }

    /**
     * @test
     */
    public function shouldGetCommitComments(): void
    {
        $expectedArray = [
            ['note' => 'A commit message'],
            ['note' => 'Another commit message'],
        ];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->commitComments(1, 'abcd1234'));
    }

    /**
     * @test
     */
    public function shouldCreateCommitComment(): void
    {
        $expectedArray = ['id' => 2, 'title' => 'A new comment'];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->createCommitComment(1, 'abcd1234', 'A new comment'));
    }

    /**
     * @test
     */
    public function shouldCreateCommitCommentWithParams(): void
    {
        $expectedArray = ['id' => 2, 'title' => 'A new comment'];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->createCommitComment(1, 'abcd1234', 'A new comment', [
            'path' => '/some/file.txt',
            'line' => 123,
            'line_type' => 'old',
        ]));
    }

    /**
     * @test
     */
    public function shouldCompareStraight(): void
    {
        $expectedArray = ['commit' => 'object'];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->compare(1, 'master', 'feature', true));
    }

    /**
     * @test
     */
    public function shouldNotCompareStraight(): void
    {
        $expectedArray = ['commit' => 'object'];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->compare(1, 'master', 'feature'));
    }

    /**
     * @test
     */
    public function shouldGetDiff(): void
    {
        $expectedArray = [
            ['diff' => '--- ...'],
            ['diff' => '+++ ...'],
        ];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->diff(1, 'abcd1234'));
    }

    /**
     * @test
     */
    public function shouldGetTree(): void
    {
        $expectedArray = [
            ['name' => 'file1.txt'],
            ['name' => 'file2.csv'],
        ];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->tree(1));
    }

    /**
     * @test
     */
    public function shouldGetTreeWithParams(): void
    {
        $expectedArray = [
            ['name' => 'dir/file1.txt'],
            ['name' => 'dir/file2.csv'],
        ];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->tree(1, ['path' => 'dir/', 'ref_name' => 'master']));
    }

    /**
     * @test
     */
    public function shouldGetContributors(): void
    {
        $expectedArray = [
            ['name' => 'Matt'],
            ['name' => 'Bob'],
        ];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->contributors(1));
    }

    /**
     * @test
     */
    public function shouldGetMergeBase(): void
    {
        $expectedArray = [
            'id' => 'abcd1234abcd1234abcd1234abcd1234abcd1234',
            'short_id' => 'abcd1234',
            'title' => 'A commit',
            'created_at' => '2018-01-01T00:00:00.000Z',
            'parent_ids' => ['efgh5678efgh5678efgh5678efgh5678efgh5678'],
            'message' => 'A commit',
            'author_name' => 'Jane Doe',
            'author_email' => 'jane@example.org',
            'authored_date' => '2018-01-01T00:00:00.000Z',
            'committer_name' => 'Jane Doe',
            'committer_email' => 'jane@example.org',
            'committed_date' => '2018-01-01T00:00:00.000Z',
        ];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->mergeBase(1, ['efgh5678efgh5678efgh5678efgh5678efgh5678', '1234567812345678123456781234567812345678']));
    }

    protected function getApiClass(): string
    {
        return Repositories::class;
    }
}
