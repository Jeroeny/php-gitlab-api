<?php

declare(strict_types=1);

namespace Gitlab\Tests\Api;

use Gitlab\Api\Jobs;
use GuzzleHttp\Psr7\Response;

final class JobsTest extends TestCase
{
    /** @var Jobs */
    protected $api;

    /**
     * @test
     */
    public function shouldGetAllJobs(): void
    {
        $expectedArray = [
            ['id' => 1, 'name' => 'A job'],
            ['id' => 2, 'name' => 'Another job'],
        ];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->all(1, ['scope' => Jobs::SCOPE_PENDING]));
    }

    /**
     * @test
     */
    public function shouldGetPipelineJobs(): void
    {
        $expectedArray = [
            ['id' => 1, 'name' => 'A job'],
            ['id' => 2, 'name' => 'Another job'],
        ];
        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->pipelineJobs(1, 2, ['scope' => [Jobs::SCOPE_PENDING, Jobs::SCOPE_RUNNING]]));
    }

    /**
     * @test
     */
    public function shouldGetJob(): void
    {
        $expectedArray = ['id' => 3, 'name' => 'A job'];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->show(1, 3));
    }

    /**
     * @test
     */
    public function shouldGetArtifacts(): void
    {
        $this->setResponse(new Response(200, [], 'foobar'));
        $this->assertEquals('foobar', $this->api->artifacts(1, 3)->getContents());
    }

    /**
     * @test
     */
    public function shouldGetArtifactsByRefName(): void
    {
        $this->setResponse(new Response(200, [], 'foobar'));
        $this->assertEquals('foobar', $this->api->artifactsByRefName(1, 'master', 'job_name')->getContents());
    }

    /**
     * @test
     */
    public function shouldGetArtifactByRefName(): void
    {
        $this->setResponse(new Response(200, [], 'foobar'));
        $this->assertEquals('foobar', $this->api->artifactByRefName(1, 'master', 'job_name', 'artifact_path')->getContents());
    }

    /**
     * @test
     */
    public function shouldGetTrace(): void
    {
        $expectedString = 'some trace';

        $this->setResponseBody($expectedString);
        $this->assertEquals($expectedString, $this->api->trace(1, 3));
    }

    /**
     * @test
     */
    public function shouldCancel(): void
    {
        $expectedArray = ['id' => 3, 'name' => 'A job'];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->cancel(1, 3));
    }

    /**
     * @test
     */
    public function shouldRetry(): void
    {
        $expectedArray = ['id' => 3, 'name' => 'A job'];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->retry(1, 3));
    }

    /**
     * @test
     */
    public function shouldErase(): void
    {
        $expectedArray = ['id' => 3, 'name' => 'A job'];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->erase(1, 3));
    }

    /**
     * @test
     */
    public function shouldKeepArtifacts(): void
    {
        $expectedArray = ['id' => 3, 'name' => 'A job'];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->keepArtifacts(1, 3));
    }

    /**
     * @test
     */
    public function shouldPlay(): void
    {
        $expectedArray = ['id' => 3, 'name' => 'A job'];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->play(1, 3));
    }

    protected function getApiClass(): string
    {
        return Jobs::class;
    }
}
