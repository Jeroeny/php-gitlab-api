<?php

declare(strict_types=1);

namespace Gitlab\HttpClient;

use Http\Client\Common\HttpMethodsClient;
use Http\Client\Common\Plugin;
use Http\Client\Common\PluginClientFactory;
use Http\Client\HttpClient;
use Http\Discovery\HttpClientDiscovery;
use Http\Discovery\MessageFactoryDiscovery;
use Http\Message\RequestFactory;

/**
 * A builder that builds the API client.
 * This will allow you to fluently add and remove plugins.
 */
final class Builder
{
    /**
     * The object that sends HTTP messages.
     *
     * @var HttpClient
     */
    private $httpClient;

    /**
     * A HTTP client with all our plugins.
     *
     * @var HttpMethodsClient
     */
    private $pluginClient;

    /** @var RequestFactory */
    private $requestFactory;

    /**
     * True if we should create a new Plugin client at next request.
     *
     * @var bool
     */
    private $httpClientModified = true;

    /** @var Plugin[] */
    private $plugins = [];

    public function __construct(
        ?HttpClient $httpClient = null,
        ?RequestFactory $requestFactory = null
    ) {
        $this->httpClient     = $httpClient ?: HttpClientDiscovery::find();
        $this->requestFactory = $requestFactory ?: MessageFactoryDiscovery::find();
    }

    public function getHttpClient(): HttpMethodsClient
    {
        if ($this->httpClientModified) {
            $this->httpClientModified = false;

            $this->pluginClient = new HttpMethodsClient(
                (new PluginClientFactory())->createClient($this->httpClient, $this->plugins),
                $this->requestFactory
            );
        }

        return $this->pluginClient;
    }

    /**
     * Add a new plugin to the end of the plugin chain.
     */
    public function addPlugin(Plugin $plugin): void
    {
        $this->plugins[]          = $plugin;
        $this->httpClientModified = true;
    }

    /**
     * Remove a plugin by its fully qualified class name (FQCN).
     */
    public function removePlugin(string $fqcn): void
    {
        foreach ($this->plugins as $idx => $plugin) {
            if (! ($plugin instanceof $fqcn)) {
                continue;
            }

            unset($this->plugins[$idx]);
            $this->httpClientModified = true;
        }
    }
}
