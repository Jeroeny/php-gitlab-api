<?php

declare(strict_types=1);

namespace Gitlab\Tests\Api;

use Gitlab\Api\RepositoryFiles;

final class RepositoryFilesTest extends TestCase
{
    /** @var RepositoryFiles */
    protected $api;

    /**
     * @test
     */
    public function shouldGetBlob(): void
    {
        $expectedString = 'something in a file';

        $this->setResponseBody($expectedString);
        $this->assertEquals($expectedString, $this->api->getRawFile(1, 'dir/file1.txt', 'abcd1234'));
    }

    /**
     * @test
     */
    public function shouldGetFile(): void
    {
        $expectedArray = ['file_name' => 'file1.txt', 'file_path' => 'dir/file1.txt'];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->getFile(1, 'dir/file1.txt', 'abcd1234'));
    }

    /**
     * @test
     */
    public function shouldCreateFile(): void
    {
        $expectedArray = ['file_name' => 'file1.txt', 'file_path' => 'dir/file1.txt'];
        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->createFile(1, [
            'file_path' => 'dir/file1.txt',
            'content' => 'some contents',
            'branch' => 'master',
            'commit_message' => 'Added new file',
        ]));
    }

    /**
     * @test
     */
    public function shouldCreateFileWithEncoding(): void
    {
        $expectedArray = ['file_name' => 'file1.txt', 'file_path' => 'dir/file1.txt'];
        $this->setResponseBody($expectedArray);

        $this->assertEquals($expectedArray, $this->api->createFile(1, [
            'file_path' => 'dir/file1.txt',
            'content' => 'some contents',
            'branch' => 'master',
            'commit_message' => 'Added new file',
            'encoding' => 'text',
        ]));
    }

    /**
     * @test
     */
    public function shouldCreateFileWithAuthor(): void
    {
        $expectedArray = ['file_name' => 'file1.txt', 'file_path' => 'dir/file1.txt'];
        $this->setResponseBody($expectedArray);

        $this->assertEquals($expectedArray, $this->api->createFile(1, [
            'file_path' => 'dir/file1.txt',
            'content' => 'some contents',
            'branch' => 'master',
            'commit_message' => 'Added new file',
            'author_email' => 'gitlab@example.com',
            'author_name' => 'GitLab User',
        ]));
    }

    /**
     * @test
     */
    public function shouldUpdateFile(): void
    {
        $expectedArray = ['file_name' => 'file1.txt', 'file_path' => 'dir/file1.txt'];
        $this->setResponseBody($expectedArray);

        $this->assertEquals($expectedArray, $this->api->updateFile(1, [
            'file_path' => 'dir/file1.txt',
            'content' => 'some new contents',
            'branch' => 'master',
            'commit_message' => 'Updated new file',
        ]));
    }

    /**
     * @test
     */
    public function shouldUpdateFileWithEncoding(): void
    {
        $expectedArray = ['file_name' => 'file1.txt', 'file_path' => 'dir/file1.txt'];
        $this->setResponseBody($expectedArray);

        $this->assertEquals($expectedArray, $this->api->updateFile(1, [
            'file_path' => 'dir/file1.txt',
            'content' => 'c29tZSBuZXcgY29udGVudHM=',
            'branch' => 'master',
            'commit_message' => 'Updated file',
            'encoding' => 'base64',
        ]));
    }

    /**
     * @test
     */
    public function shouldUpdateFileWithAuthor(): void
    {
        $expectedArray = ['file_name' => 'file1.txt', 'file_path' => 'dir/file1.txt'];
        $this->setResponseBody($expectedArray);

        $this->assertEquals($expectedArray, $this->api->updateFile(1, [
            'file_path' => 'dir/file1.txt',
            'content' => 'some new contents',
            'branch' => 'master',
            'commit_message' => 'Updated file',
            'author_email' => 'gitlab@example.com',
            'author_name' => 'GitLab User',
        ]));
    }

    /**
     * @test
     */
    public function shouldDeleteFile(): void
    {
        $expectedArray = ['file_name' => 'app/project.rb', 'branch' => 'master'];
        $this->setResponseBody($expectedArray);

        $this->assertEquals($expectedArray, $this->api->deleteFile(1, [
            'file_path' => 'dir/file1.txt',
            'branch' => 'master',
            'commit_message' => 'Deleted file',
        ]));
    }

    /**
     * @test
     */
    public function shouldDeleteFileWithAuthor(): void
    {
        $expectedArray = ['file_name' => 'app/project.rb', 'branch' => 'master'];
        $this->setResponseBody($expectedArray);

        $this->assertEquals($expectedArray, $this->api->deleteFile(1, [
            'file_path' => 'dir/file1.txt',
            'branch' => 'master',
            'commit_message' => 'Deleted file',
            'author_email' => 'gitlab@example.com',
            'author_name' => 'GitLab User',
        ]));
    }

    protected function getApiClass(): string
    {
        return RepositoryFiles::class;
    }
}
