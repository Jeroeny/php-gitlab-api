<?php

declare(strict_types=1);

namespace Gitlab\Tests\Api;

use Gitlab\Api\Snippets;

final class SnippetsTest extends TestCase
{
    /** @var Snippets */
    protected $api;

    /**
     * @test
     */
    public function shouldGetAllSnippets(): void
    {
        $expectedArray = [
            ['id' => 1, 'title' => 'A snippet'],
            ['id' => 2, 'title' => 'Another snippet'],
        ];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->all(1));
    }

    /**
     * @test
     */
    public function shouldShowSnippet(): void
    {
        $expectedArray = ['id' => 2, 'title' => 'Another snippet'];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->show(1, 2));
    }

    /**
     * @test
     */
    public function shouldCreateSnippet(): void
    {
        $expectedArray = ['id' => 3, 'title' => 'A new snippet'];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->create(1, 'A new snippet', 'file.txt', 'A file', 'public'));
    }

    /**
     * @test
     */
    public function shouldUpdateSnippet(): void
    {
        $expectedArray = ['id' => 3, 'title' => 'Updated snippet'];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->update(1, 3, ['file_name' => 'new_file.txt', 'code' => 'New content', 'title' => 'Updated snippet']));
    }

    /**
     * @test
     */
    public function shouldShowContent(): void
    {
        $expectedString = 'New content';

        $this->setResponseBody($expectedString);
        $this->assertEquals($expectedString, $this->api->content(1, 3));
    }

    /**
     * @test
     */
    public function shouldRemoveSnippet(): void
    {
        $this->setResponseBody(true);
        $this->assertTrue($this->api->remove(1, 3));
    }

    /**
     * @test
     */
    public function shouldGetSnippetAwardEmoji(): void
    {
        $expectedArray = [
            ['id' => 1, 'name' => 'sparkles'],
            ['id' => 2, 'name' => 'heart_eyes'],
        ];

        $this->setResponseBody($expectedArray);
        $this->assertEquals($expectedArray, $this->api->awardEmoji(1, 2));
    }

    protected function getApiClass(): string
    {
        return Snippets::class;
    }
}
