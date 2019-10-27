<?php

declare(strict_types=1);

namespace Gitlab\Tests\HttpClient;

use Gitlab\HttpClient\Builder;
use Http\Client\Common\HttpMethodsClient;
use Http\Client\Common\Plugin;
use Http\Client\HttpClient;
use Http\Message\RequestFactory;
use Http\Message\StreamFactory;
use PHPUnit\Framework\TestCase;

final class BuilderTest extends TestCase
{
    /** @var Builder */
    private $subject;

    public function setUp(): void
    {
        $this->subject = new Builder(
            $this->getMockBuilder(HttpClient::class)->getMock(),
            $this->getMockBuilder(RequestFactory::class)->getMock(),
            $this->getMockBuilder(StreamFactory::class)->getMock()
        );
    }

    public function testAddPluginShouldInvalidateHttpClient(): void
    {
        $client = $this->subject->getHttpClient();

        $this->subject->addPlugin($this->getMockBuilder(Plugin::class)->getMock());

        $this->assertNotSame($client, $this->subject->getHttpClient());
    }

    public function testRemovePluginShouldInvalidateHttpClient(): void
    {
        $this->subject->addPlugin($this->getMockBuilder(Plugin::class)->getMock());

        $client = $this->subject->getHttpClient();

        $this->subject->removePlugin(Plugin::class);

        $this->assertNotSame($client, $this->subject->getHttpClient());
    }

    public function testHttpClientShouldBeAnHttpMethodsClient(): void
    {
        $this->assertInstanceOf(HttpMethodsClient::class, $this->subject->getHttpClient());
    }
}
