<?php

declare(strict_types=1);

namespace Gitlab;

use Gitlab\Api\ApiBase;
use Gitlab\Api\DeployKeys;
use Gitlab\Api\Deployments;
use Gitlab\Api\Environments;
use Gitlab\Api\Groups;
use Gitlab\Api\GroupsBoards;
use Gitlab\Api\GroupsMilestones;
use Gitlab\Api\IssueBoards;
use Gitlab\Api\IssueLinks;
use Gitlab\Api\Issues;
use Gitlab\Api\IssuesStatistics;
use Gitlab\Api\Jobs;
use Gitlab\Api\Keys;
use Gitlab\Api\MergeRequests;
use Gitlab\Api\Milestones;
use Gitlab\Api\ProjectNamespaces;
use Gitlab\Api\Projects;
use Gitlab\Api\Repositories;
use Gitlab\Api\RepositoryFiles;
use Gitlab\Api\Schedules;
use Gitlab\Api\Snippets;
use Gitlab\Api\SystemHooks;
use Gitlab\Api\Tags;
use Gitlab\Api\Users;
use Gitlab\Api\Version;
use Gitlab\Exception\InvalidArgumentException;
use Gitlab\HttpClient\Builder;
use Gitlab\HttpClient\Plugin\ApiVersion;
use Gitlab\HttpClient\Plugin\Authentication;
use Gitlab\HttpClient\Plugin\GitlabExceptionThrower;
use Gitlab\HttpClient\Plugin\History;
use Http\Client\Common\HttpMethodsClient;
use Http\Client\Common\Plugin\AddHostPlugin;
use Http\Client\Common\Plugin\HeaderDefaultsPlugin;
use Http\Client\Common\Plugin\HistoryPlugin;
use Http\Client\Common\Plugin\RedirectPlugin;
use Http\Client\HttpClient;
use Http\Discovery\UriFactoryDiscovery;

/**
 * Simple API wrapper for Gitlab
 *
 * @property-read Groups                        $groups
 * @property-read Issues $issues
 * @property-read Jobs $jobs
 * @property-read MergeRequests $merge_requests
 * @property-read MergeRequests $mr
 * @property-read Milestones $milestones
 * @property-read Milestones $ms
 * @property-read ProjectNamespaces $namespaces
 * @property-read ProjectNamespaces $ns
 * @property-read Projects $projects
 * @property-read Repositories $repositories
 * @property-read Repositories $repo
 * @property-read Snippets $snippets
 * @property-read SystemHooks $hooks
 * @property-read SystemHooks $system_hooks
 * @property-read Users $users
 * @property-read Keys $keys
 * @property-read Tags $tags
 * @property-read Version $version
 */
final class Client
{
    /**
     * Constant for authentication method. Indicates the default, but deprecated
     * login with username and token in URL.
     */
    public const AUTH_URL_TOKEN = 'url_token';

    /**
     * Constant for authentication method. Indicates the new login method with
     * with username and token via HTTP Authentication.
     */
    public const AUTH_HTTP_TOKEN = 'http_token';

    /**
     * Constant for authentication method. Indicates the OAuth method with a key
     * obtain using Gitlab's OAuth provider.
     */
    public const AUTH_OAUTH_TOKEN = 'oauth_token';

    /** @var History */
    private $responseHistory;

    /** @var Builder */
    private $httpClientBuilder;

    /**
     * Instantiate a new Gitlab client
     */
    public function __construct(?Builder $httpClientBuilder = null)
    {
        $this->responseHistory   = new History();
        $this->httpClientBuilder = $httpClientBuilder ?: new Builder();

        $this->httpClientBuilder->addPlugin(new GitlabExceptionThrower());
        $this->httpClientBuilder->addPlugin(new HistoryPlugin($this->responseHistory));
        $this->httpClientBuilder->addPlugin(new HeaderDefaultsPlugin(['User-Agent' => 'php-gitlab-api (http://github.com/jeroeny/gitlab-api)']));
        $this->httpClientBuilder->addPlugin(new RedirectPlugin());
        $this->httpClientBuilder->addPlugin(new ApiVersion());

        $this->setUrl('https://gitlab.com');
    }

    /**
     * Create a Gitlab\Client using an url.
     *
     * @return Client
     */
    public static function create(string $url): self
    {
        $client = new self();
        $client->setUrl($url);

        return $client;
    }

    /**
     * Create a Gitlab\Api\Client using an HttpClient.
     */
    public static function createWithHttpClient(HttpClient $httpClient): self
    {
        $builder = new Builder($httpClient);

        return new self($builder);
    }

    public function deployKeys(): DeployKeys
    {
        return new DeployKeys($this);
    }

    public function groups(): Groups
    {
        return new Groups($this);
    }

    public function groupsMilestones(): GroupsMilestones
    {
        return new GroupsMilestones($this);
    }

    public function issues(): Issues
    {
        return new Issues($this);
    }

    public function issueBoards(): IssueBoards
    {
        return new IssueBoards($this);
    }

    public function groupsBoards(): GroupsBoards
    {
        return new GroupsBoards($this);
    }

    public function issueLinks(): IssueLinks
    {
        return new IssueLinks($this);
    }

    public function jobs(): Jobs
    {
        return new Jobs($this);
    }

    public function mergeRequests(): MergeRequests
    {
        return new MergeRequests($this);
    }

    public function milestones(): Milestones
    {
        return new Milestones($this);
    }

    public function namespaces(): ProjectNamespaces
    {
        return new ProjectNamespaces($this);
    }

    public function projects(): Projects
    {
        return new Projects($this);
    }

    public function repositories(): Repositories
    {
        return new Repositories($this);
    }

    public function repositoryFiles(): RepositoryFiles
    {
        return new RepositoryFiles($this);
    }

    public function snippets(): Snippets
    {
        return new Snippets($this);
    }

    public function systemHooks(): SystemHooks
    {
        return new SystemHooks($this);
    }

    public function users(): Users
    {
        return new Users($this);
    }

    public function keys(): Keys
    {
        return new Keys($this);
    }

    public function tags(): Tags
    {
        return new Tags($this);
    }

    public function version(): Version
    {
        return new Version($this);
    }

    public function deployments(): Deployments
    {
        return new Deployments($this);
    }

    public function environments(): Environments
    {
        return new Environments($this);
    }

    public function schedules(): Schedules
    {
        return new Schedules($this);
    }

    public function issuesStatistics(): IssuesStatistics
    {
        return new IssuesStatistics($this);
    }

    public function api(string $name): ApiBase
    {
        switch ($name) {
            case 'deploy_keys':
                return $this->deployKeys();
            case 'groups':
                return $this->groups();
            case 'groupsMilestones':
                return $this->groupsMilestones();
            case 'issues':
                return $this->issues();
            case 'board':
            case 'issue_boards':
                return $this->issueBoards();
            case 'group_boards':
                return $this->groupsBoards();
            case 'issue_links':
                return $this->issueLinks();
            case 'jobs':
                return $this->jobs();
            case 'mr':
            case 'merge_requests':
                return $this->mergeRequests();
            case 'milestones':
            case 'ms':
                return $this->milestones();
            case 'namespaces':
            case 'ns':
                return $this->namespaces();
            case 'projects':
                return $this->projects();
            case 'repo':
            case 'repositories':
                return $this->repositories();
            case 'repositoryFiles':
                return $this->repositoryFiles();
            case 'snippets':
                return $this->snippets();
            case 'hooks':
            case 'system_hooks':
                return $this->systemHooks();
            case 'users':
                return $this->users();
            case 'keys':
                return $this->keys();
            case 'tags':
                return $this->tags();
            case 'version':
                return $this->version();
            case 'environments':
                return $this->environments();
            case 'deployments':
                return $this->deployments();
            case 'schedules':
                return $this->schedules();
            case 'issues_statistics':
                return $this->issuesStatistics();
            default:
                throw new InvalidArgumentException('Invalid endpoint: "' . $name . '"');
        }
    }

    /**
     * Authenticate a user for all next requests
     *
     * @param string $token      Gitlab private token
     * @param string $authMethod One of the AUTH_* class constants
     *
     * @return $this
     */
    public function authenticate(string $token, string $authMethod = self::AUTH_URL_TOKEN, ?string $sudo = null): self
    {
        $this->httpClientBuilder->removePlugin(Authentication::class);
        $this->httpClientBuilder->addPlugin(new Authentication($authMethod, $token, $sudo));

        return $this;
    }

    public function setUrl(string $url): self
    {
        $this->httpClientBuilder->removePlugin(AddHostPlugin::class);
        $this->httpClientBuilder->addPlugin(new AddHostPlugin(UriFactoryDiscovery::find()->createUri($url)));

        return $this;
    }

    public function __get(string $api): ApiBase
    {
        return $this->api($api);
    }

    public function getHttpClient(): HttpMethodsClient
    {
        return $this->httpClientBuilder->getHttpClient();
    }

    public function getResponseHistory(): History
    {
        return $this->responseHistory;
    }
}
