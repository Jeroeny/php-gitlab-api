<?php

declare(strict_types=1);

namespace Gitlab\Tests\Api;

use DateTimeImmutable;
use Gitlab\Api\Users;

final class UsersTest extends TestCase
{
    /** @var Users */
    protected $api;

    /**
     * @test
     */
    public function shouldGetAllUsers(): void
    {
        $expectedArray = [
            ['id' => 1, 'name' => 'Matt'],
            ['id' => 2, 'name' => 'John'],
        ];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->all());
    }

    /**
     * @test
     */
    public function shouldGetActiveUsers(): void
    {
        $expectedArray = [
            ['id' => 1, 'name' => 'Matt'],
            ['id' => 2, 'name' => 'John'],
        ];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->all(['active' => true]));
    }

    /**
     * @test
     */
    public function shouldGetUsersWithDateTimeParams(): void
    {
        $expectedArray = [
            ['id' => 1, 'name' => 'Matt'],
            ['id' => 2, 'name' => 'John'],
        ];

        $createdAfter  = new DateTimeImmutable('2018-01-01 00:00:00');
        $createdBefore = new DateTimeImmutable('2018-01-31 00:00:00');

        $this->setResponseBody($expectedArray);
        $this->assertEquals(
            $expectedArray,
            $this->api->all(['created_after' => $createdAfter, 'created_before' => $createdBefore])
        );
    }

    /**
     * @test
     */
    public function shouldShowUser(): void
    {
        $expectedArray = ['id' => 1, 'name' => 'Matt'];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->show(1));
    }

    /**
     * @test
     */
    public function shouldShowUsersProjects(): void
    {
        $expectedArray = [
            ['id' => 1, 'name' => 'matt-project-1'],
            ['id' => 2, 'name' => 'matt-project-2'],
        ];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->usersProjects(1));
    }

    /**
     * @test
     */
    public function shouldCreateUser(): void
    {
        $expectedArray = ['id' => 3, 'name' => 'Billy'];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->create('billy@example.com', 'password'));
    }

    /**
     * @test
     */
    public function shouldCreateUserWithAdditionalInfo(): void
    {
        $expectedArray = ['id' => 3, 'name' => 'Billy'];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->create('billy@example.com', 'password', ['name' => 'Billy', 'bio' => 'A person']));
    }

    /**
     * @test
     */
    public function shouldUpdateUser(): void
    {
        $expectedArray = ['id' => 3, 'name' => 'Billy Bob'];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->update(3, ['name' => 'Billy Bob']));
    }

    /**
     * @test
     */
    public function shouldRemoveUser(): void
    {
        $this->setResponseBody(true);
        $this->assertTrue($this->api->remove(1));
    }

    /**
     * @test
     */
    public function shouldBlockUser(): void
    {
        $this->setResponseBody(true);
        $this->assertTrue($this->api->block(1));
    }

    /**
     * @test
     */
    public function shouldUnblockUser(): void
    {
        $this->setResponseBody(true);
        $this->assertTrue($this->api->unblock(1));
    }

    /**
     * @test
     */
    public function shouldShowCurrentUser(): void
    {
        $expectedArray = ['id' => 1, 'name' => 'Matt'];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->me());
    }

    /**
     * @test
     */
    public function shouldGetCurrentUserKeys(): void
    {
        $expectedArray = [
            ['id' => 1, 'title' => 'A key'],
            ['id' => 2, 'name' => 'Another key'],
        ];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->keys());
    }

    /**
     * @test
     */
    public function shouldGetCurrentUserKey(): void
    {
        $expectedArray = ['id' => 1, 'title' => 'A key'];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->key(1));
    }

    /**
     * @test
     */
    public function shouldCreateKeyForCurrentUser(): void
    {
        $expectedArray = ['id' => 3, 'title' => 'A new key'];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->createKey('A new key', '...'));
    }

    /**
     * @test
     */
    public function shouldDeleteKeyForCurrentUser(): void
    {
        $this->setResponseBody(true);
        $this->assertTrue($this->api->removeKey(3));
    }

    /**
     * @test
     */
    public function shouldGetUserKeys(): void
    {
        $expectedArray = [
            ['id' => 1, 'title' => 'A key'],
            ['id' => 2, 'name' => 'Another key'],
        ];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->userKeys(1));
    }

    /**
     * @test
     */
    public function shouldGetUserKey(): void
    {
        $expectedArray = ['id' => 2, 'title' => 'Another key'];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->userKey(1, 2));
    }

    /**
     * @test
     */
    public function shouldCreateKeyForUser(): void
    {
        $expectedArray = ['id' => 3, 'title' => 'A new key'];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->createKeyForUser(1, 'A new key', '...'));
    }

    /**
     * @test
     */
    public function shouldDeleteKeyForUser(): void
    {
        $this->setResponseBody(true);
        $this->assertTrue($this->api->removeUserKey(1, 3));
    }

    /**
     * @test
     */
    public function shouldAttemptLogin(): void
    {
        $expectedArray = ['id' => 1, 'name' => 'Matt'];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->session('matt', 'password'));
        $this->assertEquals($expectedArray, $this->api->login('matt', 'password'));
    }

    /**
     * @test
     */
    public function shouldGetUserEmails(): void
    {
        $expectedArray = [
            ['id' => 1, 'email' => 'foo@bar.baz'],
            ['id' => 2, 'email' => 'foo@bar.qux'],
        ];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->emails());
    }

    /**
     * @test
     */
    public function shouldGetSpecificUserEmail(): void
    {
        $expectedArray = ['id' => 1, 'email' => 'foo@bar.baz'];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->email(1));
    }

    /**
     * @test
     */
    public function shouldGetEmailsForUser(): void
    {
        $expectedArray = [
            ['id' => 1, 'email' => 'foo@bar.baz'],
            ['id' => 2, 'email' => 'foo@bar.qux'],
        ];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->userEmails(1));
    }

    /**
     * @test
     */
    public function shouldCreateEmailForUser(): void
    {
        $expectedArray = ['id' => 3, 'email' => 'foo@bar.example'];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->createEmailForUser(1, 'foo@bar.example'));
    }

    /**
     * @test
     */
    public function shouldCreateConfirmedEmailForUser(): void
    {
        $expectedArray = ['id' => 4, 'email' => 'foo@baz.example'];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->createEmailForUser(1, 'foo@baz.example', true));
    }

    /**
     * @test
     */
    public function shouldDeleteEmailForUser(): void
    {
        $this->setResponseBody(true);
        $this->assertTrue($this->api->removeUserEmail(1, 3));
    }

    /**
     * @test
     */
    public function shouldGetCurrentUserImpersonationTokens(): void
    {
        $expectedArray = [
            ['id' => 1, 'name' => 'A Name', 'revoked' => false],
            ['id' => 2, 'name' => 'A Name', 'revoked' => false],
        ];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->userImpersonationTokens(1));
    }

    /**
     * @test
     */
    public function shouldGetUserImpersonationToken(): void
    {
        $expectedArray = ['id' => 2, 'name' => 'name'];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->userImpersonationToken(1, 1));
    }

    /**
     * @test
     */
    public function shouldCreateImpersonationTokenForUser(): void
    {
        $expectedArray = ['id' => 1, 'name' => 'name'];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->createImpersonationToken(1, 'name', ['api']));
    }

    /**
     * @test
     */
    public function shouldDeleteImpersonationTokenForUser(): void
    {
        $this->setResponseBody(true);
        $this->assertTrue($this->api->removeImpersonationToken(1, 1));
    }

    /**
     * @test
     */
    public function shouldGetCurrentUserActiveImpersonationTokens(): void
    {
        $expectedArray = [
            ['id' => 1, 'name' => 'A Name', 'revoked' => true],
        ];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->userImpersonationTokens(1, ['state' => 'active']));
    }

    /**
     * @test
     */
    public function shouldGetCurrentUserInactiveImpersonationTokens(): void
    {
        $expectedArray = [
            ['id' => 2, 'name' => 'A Name', 'revoked' => false],
        ];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->userImpersonationTokens(1, ['state' => 'inactive']));
    }

    protected function getApiClass(): string
    {
        return Users::class;
    }
}
