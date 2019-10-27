<?php

declare(strict_types=1);

namespace Gitlab\Tests\Api;

use DateTimeImmutable;
use Gitlab\Api\Projects;

final class ProjectsTest extends TestCase
{
    /** @var Projects */
    protected $api;

    /**
     * @test
     */
    public function shouldGetAllProjects(): void
    {
        $expectedArray = $this->getMultipleProjectsData();

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->all());
    }

    /**
     * @test
     */
    public function shouldGetAllProjectsSortedByName(): void
    {
        $expectedArray = $this->getMultipleProjectsData();

        $this->setResponseBody($expectedArray);
        $this->assertEquals(
            $expectedArray,
            $this->api->all(['page' => 1, 'per_page' => 5, 'order_by' => 'name', 'sort' => 'asc'])
        );
    }

    /**
     * @test
     */
    public function shouldNotNeedPaginationWhenGettingProjects(): void
    {
        $expectedArray = $this->getMultipleProjectsData();

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->all());
    }

    /**
     * @test
     */
    public function shouldGetAccessibleProjects(): void
    {
        $expectedArray = $this->getMultipleProjectsData();

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->all());
    }

    /**
     * @test
     */
    public function shouldGetOwnedProjects(): void
    {
        $expectedArray = $this->getMultipleProjectsData();

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->all(['owned' => true]));
    }

    /**
     * @test
     */
    public function shouldGetNotArchivedProjects(): void
    {
        $expectedArray = $this->getMultipleProjectsData();

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->all(['archived' => false]));
    }

    /**
     * @test
     * @dataProvider possibleAccessLevels
     */
    public function shouldGetProjectsWithMinimumAccessLevel(int $level): void
    {
        $expectedArray = $this->getMultipleProjectsData();

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->all(['min_access_level' => $level]));
    }

    /**
     * @test
     */
    public function shouldSearchProjects(): void
    {
        $expectedArray = $this->getMultipleProjectsData();

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->all(['search' => 'a project']));
    }

    /**
     * @test
     */
    public function shouldShowProject(): void
    {
        $expectedArray = ['id' => 1, 'name' => 'Project Name'];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->show(1));
    }

    /**
     * @test
     */
    public function shouldShowProjectWithStatistics(): void
    {
        $expectedArray = [
            'id' => 1,
            'name' => 'Project Name',
            'statistics' => [
                'commit_count' => 37,
                'storage_size' => 1038090,
                'repository_size' => 1038090,
                'lfs_objects_size' => 0,
                'job_artifacts_size' => 0,
            ],
        ];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->show(1, ['statistics' => true]));
    }

    /**
     * @test
     */
    public function shouldCreateProject(): void
    {
        $expectedArray = ['id' => 1, 'name' => 'Project Name'];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->create('Project Name', ['issues_enabled' => true]));
    }

    /**
     * @test
     */
    public function shouldUpdateProject(): void
    {
        $expectedArray = ['id' => 1, 'name' => 'Updated Name'];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->update(1, [
            'name' => 'Updated Name',
            'issues_enabled' => true,
        ]));
    }

    /**
     * @test
     */
    public function shouldArchiveProject(): void
    {
        $expectedArray = ['id' => 1, 'archived' => true];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->archive(1));
    }

    /**
     * @test
     */
    public function shouldUnarchiveProject(): void
    {
        $expectedArray = ['id' => 1, 'archived' => false];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->unarchive(1));
    }

    /**
     * @test
     */
    public function shouldCreateProjectForUser(): void
    {
        $expectedArray = ['id' => 1, 'name' => 'Project Name'];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->createForUser(1, 'Project Name', ['issues_enabled' => true]));
    }

    /**
     * @test
     */
    public function shouldRemoveProject(): void
    {
        $this->setResponseBody(true);
        $this->assertTrue($this->api->remove(1));
    }

    /**
     * @test
     */
    public function shouldGetPipelines(): void
    {
        $expectedArray = [
            ['id' => 1, 'status' => 'success', 'ref' => 'new-pipeline'],
            ['id' => 2, 'status' => 'failed', 'ref' => 'new-pipeline'],
            ['id' => 3, 'status' => 'pending', 'ref' => 'test-pipeline'],
        ];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->pipelines(1));
    }

    /**
     * Check we can request project issues.
     *
     * @test
     */
    public function shouldGetProjectIssues(): void
    {
        $expectedArray = $this->getProjectIssuesExpectedArray();

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->issues(1));
    }

    /**
     * Check we can request project issues with query parameters.
     *
     * @test
     */
    public function shouldGetProjectIssuesParameters(): void
    {
        $expectedArray = $this->getProjectIssuesExpectedArray();

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->issues(1, ['state' => 'opened']));
    }

    /**
     * Get expected array for tests which check project issues method.
     *
     * @return mixed[]
     *   Project issues list.
     */
    public function getProjectIssuesExpectedArray(): array
    {
        return [
            [
                'state' => 'opened',
                'description' => 'Ratione dolores corrupti mollitia soluta quia.',
                'author' => [
                    'state' => 'active',
                    'id' => 18,
                    'web_url' => 'https => //gitlab.example.com/eileen.lowe',
                    'name' => 'Alexandra Bashirian',
                    'avatar_url' => null,
                    'username' => 'eileen.lowe',
                ],
                'milestone' => [
                    'project_id' => 1,
                    'description' => 'Ducimus nam enim ex consequatur cumque ratione.',
                    'state' => 'closed',
                    'due_date' => null,
                    'iid' => 2,
                    'created_at' => '2016-01-04T15 => 31 => 39.996Z',
                    'title' => 'v4.0',
                    'id' => 17,
                    'updated_at' => '2016-01-04T15 => 31 => 39.996Z',
                ],
                'project_id' => 1,
                'assignees' => [
                    [
                        'state' => 'active',
                        'id' => 1,
                        'name' => 'Administrator',
                        'web_url' => 'https => //gitlab.example.com/root',
                        'avatar_url' => null,
                        'username' => 'root',
                    ],
                ],
                'assignee' => [
                    'state' => 'active',
                    'id' => 1,
                    'name' => 'Administrator',
                    'web_url' => 'https => //gitlab.example.com/root',
                    'avatar_url' => null,
                    'username' => 'root',
                ],
                'updated_at' => '2016-01-04T15 => 31 => 51.081Z',
                'closed_at' => null,
                'closed_by' => null,
                'id' => 76,
                'title' => 'Consequatur vero maxime deserunt laboriosam est voluptas dolorem.',
                'created_at' => '2016-01-04T15 => 31 => 51.081Z',
                'iid' => 6,
                'labels' => [],
                'user_notes_count' => 1,
                'due_date' => '2016-07-22',
                'web_url' => 'http => //example.com/example/example/issues/6',
                'confidential' => false,
                'weight' => null,
                'discussion_locked' => false,
                'time_stats' => [
                    'time_estimate' => 0,
                    'total_time_spent' => 0,
                    'human_time_estimate' => null,
                    'human_total_time_spent' => null,
                ],
            ],
        ];
    }

    /**
     * @test
     */
    public function shouldGetBoards(): void
    {
        $expectedArray = $this->getProjectIssuesExpectedArray();

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->boards(1));
    }

    /**
     * Get expected array for tests which check project boards
     *
     * @return mixed[]
     *   Project issues list.
     */
    public function getProjectBoardsExpectedArray(): array
    {
        return [
            [
                'id' => 1,
                'project' => [
                    'id' => 5,
                    'name' => 'Diaspora Project Site',
                    'name_with_namespace' => 'Diaspora / Diaspora Project Site',
                    'path' => 'diaspora-project-site',
                    'path_with_namespace' => 'diaspora/diaspora-project-site',
                    'http_url_to_repo' => 'http => //example.com/diaspora/diaspora-project-site.git',
                    'web_url' => 'http => //example.com/diaspora/diaspora-project-site',
                ],
                'milestone' => [
                    'id' => 12,
                    'title' => '10.0',
                ],
                'lists' => [
                    [
                        'id' => 1,
                        'label' => [
                            'name' => 'Testing',
                            'color' => '#F0AD4E',
                            'description' => null,
                        ],
                        'position' => 1,
                    ],
                    [
                        'id' => 2,
                        'label' => [
                            'name' => 'Ready',
                            'color' => '#FF0000',
                            'description' => null,
                        ],
                        'position' => 2,
                    ],
                    [
                        'id' => 3,
                        'label' => [
                            'name' => 'Production',
                            'color' => '#FF5F00',
                            'description' => null,
                        ],
                        'position' => 3,
                    ],
                ],
            ],
        ];
    }

    /**
     * @test
     */
    public function shouldGetPipelinesWithBooleanParam(): void
    {
        $expectedArray = [
            ['id' => 1, 'status' => 'success', 'ref' => 'new-pipeline'],
            ['id' => 2, 'status' => 'failed', 'ref' => 'new-pipeline'],
            ['id' => 3, 'status' => 'pending', 'ref' => 'test-pipeline'],
        ];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->pipelines(1, ['yaml_errors' => false]));
    }

    /**
     * @test
     */
    public function shouldGetPipelinesWithSHA(): void
    {
        $expectedArray = [
            ['id' => 1, 'status' => 'success', 'ref' => 'new-pipeline'],
            ['id' => 2, 'status' => 'failed', 'ref' => 'new-pipeline'],
            ['id' => 3, 'status' => 'pending', 'ref' => 'test-pipeline'],
        ];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->pipelines(1, ['sha' => '123']));
    }

    /**
     * @test
     */
    public function shouldGetPipeline(): void
    {
        $expectedArray = [
            ['id' => 1, 'status' => 'success', 'ref' => 'new-pipeline'],
            ['id' => 2, 'status' => 'failed', 'ref' => 'new-pipeline'],
            ['id' => 3, 'status' => 'pending', 'ref' => 'test-pipeline'],
        ];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->pipeline(1, 3));
    }

    /**
     * @test
     */
    public function shouldCreatePipeline(): void
    {
        $expectedArray = [
            ['id' => 4, 'status' => 'created', 'ref' => 'test-pipeline'],
        ];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->createPipeline(1, 'test-pipeline'));
    }

    /**
     * @test
     */
    public function shouldRetryPipeline(): void
    {
        $expectedArray = [
            ['id' => 5, 'status' => 'pending', 'ref' => 'test-pipeline'],
        ];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->retryPipeline(1, 4));
    }

    /**
     * @test
     */
    public function shouldCancelPipeline(): void
    {
        $expectedArray = [
            ['id' => 6, 'status' => 'cancelled', 'ref' => 'test-pipeline'],
        ];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->cancelPipeline(1, 6));
    }

    /**
     * @test
     */
    public function shouldDeletePipeline(): void
    {
        $this->setResponseBody(true);
        $this->assertTrue($this->api->deletePipeline(1, 3));
    }

    /**
     * @test
     */
    public function shouldGetAllMembers(): void
    {
        $expectedArray = [
            ['id' => 1, 'name' => 'Matt'],
            ['id' => 2, 'name' => 'Bob'],
        ];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->allMembers(1));
    }

    /**
     * @test
     */
    public function shouldGetMembers(): void
    {
        $expectedArray = [
            ['id' => 1, 'name' => 'Matt'],
            ['id' => 2, 'name' => 'Bob'],
        ];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->members(1));
    }

    /**
     * @test
     */
    public function shouldGetMembersWithQuery(): void
    {
        $expectedArray = [
            ['id' => 1, 'name' => 'Matt'],
        ];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->members(1, ['query' => 'at']));
    }

    /**
     * @test
     */
    public function shouldGetMembersWithNullQuery(): void
    {
        $expectedArray = [
            ['id' => 1, 'name' => 'Matt'],
            ['id' => 2, 'name' => 'Bob'],
        ];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->members(1, []));
    }

    /**
     * @test
     */
    public function shouldGetMembersWithPagination(): void
    {
        $expectedArray = [
            ['id' => 1, 'name' => 'Matt'],
            ['id' => 2, 'name' => 'Bob'],
        ];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->members(1, ['page' => 2, 'per_page' => 15]));
    }

    /**
     * @test
     */
    public function shouldGetMember(): void
    {
        $expectedArray = ['id' => 2, 'name' => 'Matt'];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->member(1, 2));
    }

    /**
     * @test
     */
    public function shouldAddMember(): void
    {
        $expectedArray = ['id' => 1, 'name' => 'Matt'];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->addMember(1, 2, 3));
    }

    /**
     * @test
     */
    public function shouldSaveMember(): void
    {
        $expectedArray = ['id' => 1, 'name' => 'Matt'];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->saveMember(1, 2, 4));
    }

    /**
     * @test
     */
    public function shouldRemoveMember(): void
    {
        $this->setResponseBody(true);
        $this->assertTrue($this->api->removeMember(1, 2));
    }

    /**
     * @test
     */
    public function shouldGetHooks(): void
    {
        $expectedArray = [
            ['id' => 1, 'name' => 'Test hook'],
            ['id' => 2, 'name' => 'Another hook'],
        ];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->hooks(1));
    }

    /**
     * @test
     */
    public function shouldGetHook(): void
    {
        $expectedArray = ['id' => 2, 'name' => 'Another hook'];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->hook(1, 2));
    }

    /**
     * @test
     */
    public function shouldAddHook(): void
    {
        $expectedArray = ['id' => 3, 'name' => 'A new hook', 'url' => 'http://www.example.com'];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->addHook(
            1,
            'http://www.example.com',
            ['push_events' => true, 'issues_events' => true, 'merge_requests_events' => true]
        ));
    }

    /**
     * @test
     */
    public function shouldAddHookWithOnlyUrl(): void
    {
        $expectedArray = ['id' => 3, 'name' => 'A new hook', 'url' => 'http://www.example.com'];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->addHook(1, 'http://www.example.com'));
    }

    /**
     * @test
     */
    public function shouldAddHookWithoutPushEvents(): void
    {
        $expectedArray = ['id' => 3, 'name' => 'A new hook', 'url' => 'http://www.example.com'];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->addHook(1, 'http://www.example.com', ['push_events' => false]));
    }

    /**
     * @test
     */
    public function shouldUpdateHook(): void
    {
        $expectedArray = ['id' => 3, 'name' => 'A new hook', 'url' => 'http://www.example.com'];

        $this->setResponseBody($expectedArray);
        $this->assertEquals(
            $expectedArray,
            $this->api->updateHook(1, 3, ['url' => 'http://www.example-test.com', 'push_events' => false])
        );
    }

    /**
     * @test
     */
    public function shouldRemoveHook(): void
    {
        $this->setResponseBody(true);
        $this->assertTrue($this->api->removeHook(1, 2));
    }

    /**
     * @test
     */
    public function shouldTransfer(): void
    {
        $expectedArray = [
            'id' => 1,
            'name' => 'Project Name',
            'namespace' => ['name' => 'a_namespace'],
        ];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->transfer(1, 'a_namespace'));
    }

    /**
     * @test
     */
    public function shouldGetDeployKeys(): void
    {
        $expectedArray = [
            ['id' => 1, 'title' => 'test-key'],
            ['id' => 2, 'title' => 'another-key'],
        ];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->deployKeys(1));
    }

    /**
     * @test
     */
    public function shouldGetDeployKey(): void
    {
        $expectedArray = ['id' => 2, 'title' => 'another-key'];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->deployKey(1, 2));
    }

    /**
     * @test
     */
    public function shouldAddKey(): void
    {
        $expectedArray = ['id' => 3, 'title' => 'new-key', 'can_push' => false];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->addDeployKey(1, 'new-key', '...'));
    }

    /**
     * @test
     */
    public function shouldAddKeyWithPushOption(): void
    {
        $expectedArray = ['id' => 3, 'title' => 'new-key', 'can_push' => true];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->addDeployKey(1, 'new-key', '...', true));
    }

    /**
     * @test
     */
    public function shouldDeleteDeployKey(): void
    {
        $this->setResponseBody(true);
        $this->assertTrue($this->api->deleteDeployKey(1, 3));
    }

    /**
     * @test
     */
    public function shoudEnableDeployKey(): void
    {
        $this->setResponseBody(true);
        $this->assertTrue($this->api->enableDeployKey(1, 3));
    }

    /**
     * @test
     */
    public function shouldGetEvents(): void
    {
        $expectedArray = [
            ['id' => 1, 'title' => 'An event'],
            ['id' => 2, 'title' => 'Another event'],
        ];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->events(1));
    }

    /**
     * @test
     */
    public function shouldGetEventsWithDateTimeParams(): void
    {
        $expectedArray = [
            ['id' => 1, 'title' => 'An event'],
            ['id' => 2, 'title' => 'Another event'],
        ];

        $after  = new DateTimeImmutable('2018-01-01 00:00:00');
        $before = new DateTimeImmutable('2018-01-31 00:00:00');

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->events(1, ['after' => $after, 'before' => $before]));
    }

    /**
     * @test
     */
    public function shouldGetEventsWithPagination(): void
    {
        $expectedArray = [
            ['id' => 1, 'title' => 'An event'],
            ['id' => 2, 'title' => 'Another event'],
        ];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->events(1, ['page' => 2, 'per_page' => 15]));
    }

    /**
     * @test
     */
    public function shouldGetLabels(): void
    {
        $expectedArray = [
            ['id' => 987, 'name' => 'bug', 'color' => '#000000'],
            ['id' => 123, 'name' => 'feature', 'color' => '#ff0000'],
        ];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->labels(1));
    }

    /**
     * @test
     */
    public function shouldAddLabel(): void
    {
        $expectedArray = ['name' => 'bug', 'color' => '#000000'];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->addLabel(1, ['name' => 'wont-fix', 'color' => '#ffffff']));
    }

    /**
     * @test
     */
    public function shouldUpdateLabel(): void
    {
        $expectedArray = ['name' => 'bug', 'color' => '#00ffff'];

        $this->setResponseBody($expectedArray);
        $this->assertEquals(
            $expectedArray,
            $this->api->updateLabel(1, ['name' => 'bug', 'new_name' => 'big-bug', 'color' => '#00ffff'])
        );
    }

    /**
     * @test
     */
    public function shouldRemoveLabel(): void
    {
        $this->setResponseBody(true);
        $this->assertTrue($this->api->removeLabel(1, 'bug'));
    }

    /**
     * @test
     */
    public function shouldGetLanguages(): void
    {
        $expectedArray = ['php' => 100];
        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->languages(1));
    }

    /**
     * @test
     */
    public function shouldForkWithNamespace(): void
    {
        $expectedArray = ['namespace' => 'new_namespace'];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->fork(1, ['namespace' => 'new_namespace']));
    }

    /**
     * @test
     */
    public function shouldForkWithNamespaceAndPath(): void
    {
        $expectedArray = [
            'namespace' => 'new_namespace',
            'path' => 'new_path',
        ];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->fork(1, [
            'namespace' => 'new_namespace',
            'path' => 'new_path',
        ]));
    }

    /**
     * @test
     */
    public function shouldForkWithNamespaceAndPathAndName(): void
    {
        $expectedArray = [
            'namespace' => 'new_namespace',
            'path' => 'new_path',
            'name' => 'new_name',
        ];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->fork(1, [
            'namespace' => 'new_namespace',
            'path' => 'new_path',
            'name' => 'new_name',
        ]));
    }

    /**
     * @test
     */
    public function shouldCreateForkRelation(): void
    {
        $expectedArray = ['project_id' => 1, 'forked_id' => 2];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->createForkRelation(1, 2));
    }

    /**
     * @test
     */
    public function shouldRemoveForkRelation(): void
    {
        $this->setResponseBody(true);
        $this->assertTrue($this->api->removeForkRelation(2));
    }

    /**
     * @test
     */
    public function shouldSetService(): void
    {
        $this->setResponseBody(true);
        $this->assertTrue($this->api->setService(1, 'hipchat', ['param' => 'value']));
    }

    /**
     * @test
     */
    public function shouldRemoveService(): void
    {
        $this->setResponseBody(true);
        $this->assertTrue($this->api->removeService(1, 'hipchat'));
    }

    /**
     * @test
     */
    public function shouldGetVariables(): void
    {
        $expectedArray = [
            ['key' => 'ftp_username', 'value' => 'ftp'],
            ['key' => 'ftp_password', 'value' => 'somepassword'],
        ];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->variables(1));
    }

    /**
     * @test
     */
    public function shouldGetVariable(): void
    {
        $expectedArray = ['key' => 'ftp_username', 'value' => 'ftp'];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->variable(1, 'ftp_username'));
    }

    /**
     * @test
     */
    public function shouldAddVariable(): void
    {
        $expectedKey   = 'ftp_port';
        $expectedValue = '21';

        $expectedArray = [
            'key' => $expectedKey,
            'value' => $expectedValue,
        ];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->addVariable(1, $expectedKey, $expectedValue));
    }

    /**
     * @test
     */
    public function shouldAddVariableWithProtected(): void
    {
        $expectedArray = [
            'key' => 'DEPLOY_SERVER',
            'value' => 'stage.example.com',
            'protected' => true,
        ];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->addVariable(1, 'DEPLOY_SERVER', 'stage.example.com', true));
    }

    /**
     * @test
     */
    public function shouldAddVariableWithEnvironment(): void
    {
        $expectedArray = [
            'key' => 'DEPLOY_SERVER',
            'value' => 'stage.example.com',
            'environment_scope' => 'staging',
        ];

        $this->setResponseBody($expectedArray);
        $this->assertEquals(
            $expectedArray,
            $this->api->addVariable(1, 'DEPLOY_SERVER', 'stage.example.com', null, 'staging')
        );
    }

    /**
     * @test
     */
    public function shouldAddVariableWithProtectionAndEnvironment(): void
    {
        $expectedArray = [
            'key' => 'DEPLOY_SERVER',
            'value' => 'stage.example.com',
            'protected' => true,
            'environment_scope' => 'staging',
        ];

        $this->setResponseBody($expectedArray);
        $this->assertEquals(
            $expectedArray,
            $this->api->addVariable(1, 'DEPLOY_SERVER', 'stage.example.com', true, 'staging')
        );
    }

    /**
     * @test
     */
    public function shouldUpdateVariable(): void
    {
        $expectedKey   = 'ftp_port';
        $expectedValue = '22';

        $expectedArray = [
            'key' => 'ftp_port',
            'value' => '22',
        ];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->updateVariable(1, $expectedKey, $expectedValue));
    }

    /**
     * @test
     */
    public function shouldUpdateVariableWithProtected(): void
    {
        $expectedArray = [
            'key' => 'DEPLOY_SERVER',
            'value' => 'stage.example.com',
            'protected' => true,
        ];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->updateVariable(1, 'DEPLOY_SERVER', 'stage.example.com', true));
    }

    /**
     * @test
     */
    public function shouldUpdateVariableWithEnvironment(): void
    {
        $expectedArray = [
            'key' => 'DEPLOY_SERVER',
            'value' => 'stage.example.com',
            'environment_scope' => 'staging',
        ];

        $this->setResponseBody($expectedArray);
        $this->assertEquals(
            $expectedArray,
            $this->api->updateVariable(1, 'DEPLOY_SERVER', 'stage.example.com', null, 'staging')
        );
    }

    /**
     * @test
     */
    public function shouldUpdateVariableWithProtectedAndEnvironment(): void
    {
        $expectedArray = [
            'key' => 'DEPLOY_SERVER',
            'value' => 'stage.example.com',
            'protected' => true,
            'environment_scope' => 'staging',
        ];

        $this->setResponseBody($expectedArray);
        $this->assertEquals(
            $expectedArray,
            $this->api->updateVariable(1, 'DEPLOY_SERVER', 'stage.example.com', true, 'staging')
        );
    }

    /**
     * @test
     */
    public function shouldRemoveVariable(): void
    {
        $this->setResponseBody(true);
        $this->assertTrue($this->api->removeVariable(1, 'ftp_password'));
    }

    /**
     * @test
     */
    public function shouldGetDeployments(): void
    {
        $expectedArray = [
            ['id' => 1, 'sha' => '0000001'],
            ['id' => 2, 'sha' => '0000002'],
        ];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->deployments(1));
    }

    /**
     * @test
     */
    public function shouldGetDeploymentsWithPagination(): void
    {
        $expectedArray = [
            ['id' => 1, 'sha' => '0000001'],
            ['id' => 2, 'sha' => '0000002'],
        ];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->deployments(1, ['page' => 2, 'per_page' => 15]));
    }

    protected function getMultipleProjectsData(): array
    {
        return [
            ['id' => 1, 'name' => 'A project'],
            ['id' => 2, 'name' => 'Another project'],
        ];
    }

    public function possibleAccessLevels(): array
    {
        return [
            [10],
            [20],
            [30],
            [40],
            [50],
        ];
    }

    public function getBadgeExpectedArray(): array
    {
        return [
            [
                'id' => 1,
                'link_url' => 'http://example.com/ci_status.svg?project=%{project_path}&ref=%{default_branch}',
                'image_url' => 'https://shields.io/my/badge',
                'rendered_link_url' => 'http://example.com/ci_status.svg?project=example-org/example-project&ref=master',
                'rendered_image_url' => 'https://shields.io/my/badge',
                'kind' => 'project',
            ],
            [
                'id' => 2,
                'link_url' => 'http://example.com/ci_status.svg?project=%{project_path}&ref=%{default_branch}',
                'image_url' => 'https://shields.io/my/badge',
                'rendered_link_url' => 'http://example.com/ci_status.svg?project=example-org/example-project&ref=master',
                'rendered_image_url' => 'https://shields.io/my/badge',
                'kind' => 'group',
            ],
        ];
    }

    /**
     * @test
     */
    public function shouldGetBadges(): void
    {
        $expectedArray = $this->getBadgeExpectedArray();

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->badges(1));
    }

    /**
     * @test
     */
    public function shouldGetBadge(): void
    {
        $expectedBadgesArray = $this->getBadgeExpectedArray();
        $expectedArray       = [$expectedBadgesArray[0]];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->badge(1, 1));
    }

    /**
     * @test
     */
    public function shouldAddBadge(): void
    {
        $link_url      = 'http://example.com/ci_status.svg?project=%{project_path}&ref=%{default_branch}';
        $image_url     = 'https://shields.io/my/badge';
        $expectedArray = [
            'id' => 3,
            'link_url' => $link_url,
            'image_url' => $image_url,
        ];

        $this->setResponseBody($expectedArray);
        $this->assertEquals(
            $expectedArray,
            $this->api->addBadge(1, ['link_url' => $link_url, 'image_url' => $image_url])
        );
    }

    /**
     * @test
     */
    public function shouldUpdateBadge(): void
    {
        $image_url     = 'https://shields.io/my/new/badge';
        $expectedArray = ['id' => 2];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->updateBadge(1, 2, ['image_url' => $image_url]));
    }

    /**
     * @test
     */
    public function shouldRemoveBadge(): void
    {
        $this->setResponseBody(true);
        $this->assertTrue($this->api->removeBadge(1, 1));
    }

    /**
     * @test
     */
    public function shouldAddProtectedBranch(): void
    {
        $expectedArray = [
            'name' => 'master',
            'push_access_level' => [
                'access_level' => 0,
                'access_level_description' => 'No one',
            ],
            'merge_access_levels' => [
                'access_level' => 0,
                'access_level_description' => 'Developers + Maintainers',
            ],
        ];
        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->addProtectedBranch(1, ['name' => 'master', 'push_access_level' => 0, 'merge_access_level' => 30]));
    }

    protected function getApiClass(): string
    {
        return Projects::class;
    }
}
