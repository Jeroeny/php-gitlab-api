<?php

declare(strict_types=1);

namespace Gitlab\Tests\Api;

use Gitlab\Api\Groups;

final class GroupsTest extends TestCase
{
    /** @var Groups */
    protected $api;

    protected function getApiClass(): string
    {
        return Groups::class;
    }

    /**
     * @test
     */
    public function shouldGetAllGroups(): void
    {
        $expectedArray = [
            ['id' => 1, 'name' => 'A group'],
            ['id' => 2, 'name' => 'Another group'],
        ];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->all(['page' => 1, 'per_page' => 10]));
    }

    /**
     * @test
     */
    public function shouldGetAllGroupsWithBooleanParam(): void
    {
        $expectedArray = [
            ['id' => 1, 'name' => 'A group'],
            ['id' => 2, 'name' => 'Another group'],
        ];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->all(['all_available' => false]));
    }

    /**
     * @test
     */
    public function shouldGetAllGroupProjectsWithBooleanParam(): void
    {
        $expectedArray = [
            ['id' => 1, 'name' => 'A group'],
            ['id' => 2, 'name' => 'Another group'],
        ];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->projects(1, ['archived' => false]));
    }

    /**
     * @test
     */
    public function shouldNotNeedPaginationWhenGettingGroups(): void
    {
        $expectedArray = [
            ['id' => 1, 'name' => 'A group'],
            ['id' => 2, 'name' => 'Another group'],
        ];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->all());
    }

    /**
     * @test
     */
    public function shouldShowGroup(): void
    {
        $expectedArray = ['id' => 1, 'name' => 'A group'];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->show(1));
    }

    /**
     * @test
     */
    public function shouldCreateGroup(): void
    {
        $expectedArray = ['id' => 1, 'name' => 'A new group'];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->create('A new group', 'a-new-group'));
    }

    /**
     * @test
     */
    public function shouldCreateGroupWithDescriptionAndVisibility(): void
    {
        $expectedArray = ['id' => 1, 'name' => 'A new group', 'visibility_level' => 2];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->create('A new group', 'a-new-group', 'Description', 'public'));
    }

    /**
     * @test
     */
    public function shouldCreateGroupWithDescriptionVisibilityAndParentId(): void
    {
        $expectedArray = ['id' => 1, 'name' => 'A new group', 'visibility_level' => 2, 'parent_id' => 666];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->create('A new group', 'a-new-group', 'Description', 'public', null, null, 666));
    }

    /**
     * @test
     */
    public function shouldUpdateGroup(): void
    {
        $expectedArray = ['id' => 3, 'name' => 'Group name', 'path' => 'group-path'];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->update(3, ['name' => 'Group name', 'path' => 'group-path']));
    }

    /**
     * @test
     */
    public function shouldTransferProjectToGroup(): void
    {
        $this->setResponseBody(true);
        $this->assertTrue($this->api->transfer(1, 2));
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
    public function shouldRemoveGroup(): void
    {
        $this->setResponseBody(true);
        $this->assertTrue($this->api->remove(1));
    }

    /**
     * @test
     */
    public function shouldGetAllSubgroups(): void
    {
        $expectedArray = [
            ['id' => 101, 'name' => 'A subgroup'],
            ['id' => 1 - 2, 'name' => 'Another subggroup'],
        ];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->subgroups(1, ['page' => 1, 'per_page' => 10]));
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
        $this->assertEquals($expectedArray, $this->api->updateLabel(1, ['name' => 'bug', 'new_name' => 'big-bug', 'color' => '#00ffff']));
    }

    /**
     * @test
     */
    public function shouldRemoveLabel(): void
    {
        $this->setResponseBody(true);
        $this->assertTrue($this->api->removeLabel(1, 'bug'));
    }

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

    public function shouldAddVariable(): void
    {
        $expectedKey   = 'ftp_port';
        $expectedValue = '21';

        $expectedArray = [
            'key'   => $expectedKey,
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
            'key'   => 'DEPLOY_SERVER',
            'value' => 'stage.example.com',
            'protected' => true,
        ];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->addVariable(1, 'DEPLOY_SERVER', 'stage.example.com', true));
    }

    /**
     * @test
     */
    public function shouldUpdateVariable(): void
    {
        $expectedKey   = 'ftp_port';
        $expectedValue = '22';

        $expectedArray = [
            'key'   => 'ftp_port',
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
            'key'   => 'DEPLOY_SERVER',
            'value' => 'stage.example.com',
            'protected' => true,
        ];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->updateVariable(1, 'DEPLOY_SERVER', 'stage.example.com', true));
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
    public function shouldGetAllGroupProjectsWithIssuesEnabled(): void
    {
        $expectedArray = [
            ['id' => 1, 'name' => 'A group', 'issues_enabled' => true],
            ['id' => 2, 'name' => 'Another group', 'issues_enabled' => true],
        ];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->projects(1, ['with_issues_enabled' => true]));
    }

    /**
     * @test
     */
    public function shouldGetAllGroupProjectsWithMergeRequestsEnabled(): void
    {
        $expectedArray = [
            ['id' => 1, 'name' => 'A group', 'merge_requests_enabled' => true],
            ['id' => 2, 'name' => 'Another group', 'merge_requests_enabled' => true],
        ];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->projects(1, ['with_merge_requests_enabled' => true]));
    }

    /**
     * @test
     */
    public function shouldGetAllGroupProjectsSharedToGroup(): void
    {
        $expectedArray = [
            ['id' => 1, 'name' => 'A project', 'shared_with_groups' => [1]],
            ['id' => 2, 'name' => 'Another project', 'shared_with_groups' => [1]],
        ];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->projects(1, ['with_shared' => true]));
    }

    /**
     * @test
     */
    public function shouldGetAllGroupProjectsIncludingSubsgroups(): void
    {
        $expectedArray = [
            ['id' => 1, 'name' => 'A project'],
            ['id' => 2, 'name' => 'Another project', 'shared_with_groups' => [1]],
        ];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->projects(1, ['include_subgroups' => true]));
    }

    /**
     * @test
     */
    public function shouldGetAllGroupProjectsIncludingCustomAttributes(): void
    {
        $expectedArray = [
            ['id' => 1, 'name' => 'A project', 'custom_Attr' => true],
            ['id' => 2, 'name' => 'Another project', 'custom_Attr' => true],
        ];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->projects(1, ['with_custom_attributes' => true]));
    }
}
