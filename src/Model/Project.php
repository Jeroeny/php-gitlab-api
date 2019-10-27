<?php

declare(strict_types=1);

namespace Gitlab\Model;

use Gitlab\Api\Projects;
use Gitlab\Api\Repositories;
use Gitlab\Client;
use function is_array;

/**
 * @property-read int $id
 * @property-read string $description
 * @property-read string $default_branch
 * @property-read string $visibility
 * @property-read string $ssh_url_to_repo
 * @property-read string $http_url_to_repo
 * @property-read string $web_url
 * @property-read string $readme_url
 * @property-read string[] $tag_list
 * @property-read User $owner
 * @property-read string $name
 * @property-read string $name_with_namespace
 * @property-read string $path
 * @property-read string $path_with_namespace
 * @property-read bool $issues_enabled
 * @property-read int $open_issues_count
 * @property-read bool $merge_requests_enabled
 * @property-read bool $jobs_enabled
 * @property-read bool $wiki_enabled
 * @property-read bool $snippets_enabled
 * @property-read bool $resolve_outdated_diff_discussions
 * @property-read bool $container_registry_enabled
 * @property-read string $created_at
 * @property-read string $last_activity_at
 * @property-read int $creator_id
 * @property-read ProjectNamespace $namespace
 * @property-read string $import_status
 * @property-read bool $archived
 * @property-read string $avatar_url
 * @property-read bool $shared_runners_enabled
 * @property-read int $forks_count
 * @property-read int $star_count
 * @property-read string $runners_token
 * @property-read bool $public_jobs
 * @property-read Group[] $shared_with_groups
 * @property-read bool $only_allow_merge_if_pipeline_succeeds
 * @property-read bool $only_allow_merge_if_all_discussions_are_resolved
 * @property-read bool $request_access_enabled
 * @property-read string $merge_method
 * @property-read bool $approvals_before_merge
 */
final class Project extends Model
{
    /** @var mixed[] */
    protected static $properties = [
        'id',
        'description',
        'default_branch',
        'visibility',
        'ssh_url_to_repo',
        'http_url_to_repo',
        'web_url',
        'readme_url',
        'tag_list',
        'owner',
        'name',
        'name_with_namespace',
        'path',
        'path_with_namespace',
        'issues_enabled',
        'open_issues_count',
        'merge_requests_enabled',
        'jobs_enabled',
        'wiki_enabled',
        'snippets_enabled',
        'resolve_outdated_diff_discussions',
        'container_registry_enabled',
        'created_at',
        'last_activity_at',
        'creator_id',
        'namespace',
        'import_status',
        'archived',
        'avatar_url',
        'shared_runners_enabled',
        'forks_count',
        'star_count',
        'runners_token',
        'public_jobs',
        'shared_with_groups',
        'only_allow_merge_if_pipeline_succeeds',
        'only_allow_merge_if_all_discussions_are_resolved',
        'request_access_enabled',
        'merge_method',
        'approvals_before_merge',
    ];

    /**
     * @param mixed[] $data
     */
    public static function fromArray(?Client $client, array $data): Project
    {
        $project = new static($data['id']);
        $project->setClient($client);

        if (isset($data['owner'])) {
            $data['owner'] = User::fromArray($client, $data['owner']);
        }

        if (isset($data['namespace']) && is_array($data['namespace'])) {
            $data['namespace'] = ProjectNamespace::fromArray($client, $data['namespace']);
        }

        if (isset($data['shared_with_groups'])) {
            $groups = [];
            foreach ($data['shared_with_groups'] as $group) {
                $groups[] = Group::fromArray($client, $group);
            }
            $data['shared_with_groups'] = $groups;
        }

        return $project->hydrate($data);
    }

    /**
     * @param mixed[] $params
     */
    public static function create(Client $client, string $name, array $params = []): Project
    {
        $data = $client->projects()->create($name, $params);

        return static::fromArray($client, $data);
    }

    /**
     * @param mixed[] $params
     */
    public static function createForUser(int $user_id, Client $client, string $name, array $params = []): Project
    {
        $data = $client->projects()->createForUser($user_id, $name, $params);

        return static::fromArray($client, $data);
    }

    public function __construct(?int $id = null, ?Client $client = null)
    {
        $this->setClient($client);
        $this->setData('id', $id);
    }

    public function show(): Project
    {
        $data = $this->client->projects()->show($this->id);

        return static::fromArray($this->getClient(), $data);
    }

    /**
     * @param mixed[] $params
     */
    public function update(array $params): Project
    {
        $data = $this->client->projects()->update($this->id, $params);

        return static::fromArray($this->getClient(), $data);
    }

    public function archive(): Project
    {
        $data = $this->client->projects()->archive($this->id);

        return static::fromArray($this->getClient(), $data);
    }

    public function unarchive(): Project
    {
        $data = $this->client->projects()->unarchive($this->id);

        return static::fromArray($this->getClient(), $data);
    }

    public function remove(): bool
    {
        $this->client->projects()->remove($this->id);

        return true;
    }

    /**
     * @return User[]
     */
    public function members(?string $username_query = null): array
    {
        $data = $this->client->projects()->members($this->id, ['query' => $username_query]);

        $members = [];
        foreach ($data as $member) {
            $members[] = User::fromArray($this->getClient(), $member);
        }

        return $members;
    }

    public function member(int $user_id): User
    {
        $data = $this->client->projects()->member($this->id, $user_id);

        return User::fromArray($this->getClient(), $data);
    }

    public function addMember(int $user_id, int $access_level): User
    {
        $data = $this->client->projects()->addMember($this->id, $user_id, $access_level);

        return User::fromArray($this->getClient(), $data);
    }

    public function saveMember(int $user_id, int $access_level): User
    {
        $data = $this->client->projects()->saveMember($this->id, $user_id, $access_level);

        return User::fromArray($this->getClient(), $data);
    }

    public function removeMember(int $user_id): bool
    {
        $this->client->projects()->removeMember($this->id, $user_id);

        return true;
    }

    /**
     * @see Projects::hooks() for available parameters.
     *
     * @param mixed[] $parameters
     *
     * @return ProjectHook[]
     */
    public function hooks(array $parameters = []): array
    {
        $data = $this->client->projects()->hooks($this->id, $parameters);

        $hooks = [];
        foreach ($data as $hook) {
            $hooks[] = ProjectHook::fromArray($this->getClient(), $this, $hook);
        }

        return $hooks;
    }

    public function hook(int $id): ProjectHook
    {
        $hook = new ProjectHook($this, $id, $this->getClient());

        return $hook->show();
    }

    /**
     * @param mixed[] $events
     */
    public function addHook(string $url, array $events = []): ProjectHook
    {
        $data = $this->client->projects()->addHook($this->id, $url, $events);

        return ProjectHook::fromArray($this->getClient(), $this, $data);
    }

    /**
     * @param mixed[] $params
     *
     * @return mixed
     */
    public function updateHook(int $hook_id, array $params)
    {
        $hook = new ProjectHook($this, $hook_id, $this->getClient());

        return $hook->update($params);
    }

    public function removeHook(int $hook_id): bool
    {
        $hook = new ProjectHook($this, $hook_id, $this->getClient());

        return $hook->delete();
    }

    /**
     * @return Key[]
     */
    public function deployKeys(): array
    {
        $data = $this->client->projects()->deployKeys($this->id);

        $keys = [];
        foreach ($data as $key) {
            $keys[] = Key::fromArray($this->getClient(), $key);
        }

        return $keys;
    }

    public function deployKey(int $key_id): Key
    {
        $data = $this->client->projects()->deployKey($this->id, $key_id);

        return Key::fromArray($this->getClient(), $data);
    }

    public function addDeployKey(string $title, string $key, bool $canPush = false): Key
    {
        $data = $this->client->projects()->addDeployKey($this->id, $title, $key, $canPush);

        return Key::fromArray($this->getClient(), $data);
    }

    public function deleteDeployKey(int $key_id): bool
    {
        $this->client->projects()->deleteDeployKey($this->id, $key_id);

        return true;
    }

    public function enableDeployKey(int $key_id): bool
    {
        $this->client->projects()->enableDeployKey($this->id, $key_id);

        return true;
    }

    public function createBranch(string $name, string $ref): Branch
    {
        $data = $this->client->repositories()->createBranch($this->id, $name, $ref);

        return Branch::fromArray($this->getClient(), $this, $data);
    }

    public function deleteBranch(string $name): bool
    {
        $this->client->repositories()->deleteBranch($this->id, $name);

        return true;
    }

    /**
     * @return Branch[]
     */
    public function branches(): array
    {
        $data = $this->client->repositories()->branches($this->id);

        $branches = [];
        foreach ($data as $branch) {
            $branches[] = Branch::fromArray($this->getClient(), $this, $branch);
        }

        return $branches;
    }

    public function branch(string $branch_name): Branch
    {
        $branch = new Branch($this, $branch_name);
        $branch->setClient($this->getClient());

        return $branch->show();
    }

    public function protectBranch(string $branch_name, bool $devPush = false, bool $devMerge = false): Branch
    {
        $branch = new Branch($this, $branch_name);
        $branch->setClient($this->getClient());

        return $branch->protect($devPush, $devMerge);
    }

    public function unprotectBranch(string $branch_name): Branch
    {
        $branch = new Branch($this, $branch_name);
        $branch->setClient($this->getClient());

        return $branch->unprotect();
    }

    /**
     * @return Tag[]
     */
    public function tags(): array
    {
        $data = $this->client->repositories()->tags($this->id);

        $tags = [];
        foreach ($data as $tag) {
            $tags[] = Tag::fromArray($this->getClient(), $this, $tag);
        }

        return $tags;
    }

    /**
     * @see Repositories::commits() for available parameters.
     *
     * @param mixed[] $parameters
     *
     * @return Commit[]
     */
    public function commits(array $parameters = []): array
    {
        $data = $this->client->repositories()->commits($this->id, $parameters);

        $commits = [];
        foreach ($data as $commit) {
            $commits[] = Commit::fromArray($this->getClient(), $this, $commit);
        }

        return $commits;
    }

    public function commit(string $sha): Commit
    {
        $data = $this->client->repositories()->commit($this->id, $sha);

        return Commit::fromArray($this->getClient(), $this, $data);
    }

    /**
     * @see Repositories::commitComments() for available parameters.
     *
     * @param mixed[] $parameters
     *
     * @return CommitNote[]
     */
    public function commitComments(string $ref, array $parameters = []): array
    {
        $data = $this->client->repositories()->commitComments($this->id, $ref, $parameters);

        $comments = [];
        foreach ($data as $comment) {
            $comments[] = CommitNote::fromArray($this->getClient(), $comment);
        }

        return $comments;
    }

    /**
     * @param mixed[] $params
     */
    public function createCommitComment(string $ref, string $note, array $params = []): CommitNote
    {
        $data = $this->client->repositories()->createCommitComment($this->id, $ref, $note, $params);

        return CommitNote::fromArray($this->getClient(), $data);
    }

    /**
     * @return string|string[]
     */
    public function diff(string $sha)
    {
        return $this->client->repositories()->diff($this->id, $sha);
    }

    public function compare(string $from, string $to): Comparison
    {
        $data = $this->client->repositories()->compare($this->id, $from, $to);

        return Comparison::fromArray($this->getClient(), $this, $data);
    }

    /**
     * @param mixed[] $params
     *
     * @return Node[]
     */
    public function tree(array $params = []): array
    {
        $data = $this->client->repositories()->tree($this->id, $params);

        $tree = [];
        foreach ($data as $node) {
            $tree[] = Node::fromArray($this->getClient(), $this, $node);
        }

        return $tree;
    }

    public function blob(string $sha, string $filepath): string
    {
        return $this->client->repositories()->blob($this->id, $sha, $filepath);
    }

    /**
     * @return mixed[]
     */
    public function getFile(string $sha, string $filepath): array
    {
        return $this->client->repositories()->getFile($this->id, $filepath, $sha);
    }

    public function createFile(
        string $file_path,
        string $content,
        string $branch_name,
        string $commit_message,
        ?string $author_email = null,
        ?string $author_name = null
    ): File {
        $parameters = [
            'file_path' => $file_path,
            'branch' => $branch_name,
            'content' => $content,
            'commit_message' => $commit_message,
        ];

        if ($author_email !== null) {
            $parameters['author_email'] = $author_email;
        }

        if ($author_name !== null) {
            $parameters['author_name'] = $author_name;
        }

        $data = $this->client->repositoryFiles()->createFile($this->id, $parameters);

        return File::fromArray($this->getClient(), $this, $data);
    }

    public function updateFile(
        string $file_path,
        string $content,
        string $branch_name,
        string $commit_message,
        ?string $author_email = null,
        ?string $author_name = null
    ): File {
        $parameters = [
            'file_path' => $file_path,
            'branch' => $branch_name,
            'content' => $content,
            'commit_message' => $commit_message,
        ];

        if ($author_email !== null) {
            $parameters['author_email'] = $author_email;
        }

        if ($author_name !== null) {
            $parameters['author_name'] = $author_name;
        }

        $data = $this->client->repositoryFiles()->updateFile($this->id, $parameters);

        return File::fromArray($this->getClient(), $this, $data);
    }

    public function deleteFile(string $file_path, string $branch_name, string $commit_message, ?string $author_email = null, ?string $author_name = null): bool
    {
        $parameters = [
            'file_path' => $file_path,
            'branch' => $branch_name,
            'commit_message' => $commit_message,
        ];

        if ($author_email !== null) {
            $parameters['author_email'] = $author_email;
        }

        if ($author_name !== null) {
            $parameters['author_name'] = $author_name;
        }

        $this->client->repositoryFiles()->deleteFile($this->id, $parameters);

        return true;
    }

    /**
     * @see Projects::events() for available parameters.
     *
     * @param mixed[] $parameters
     *
     * @return Event[]
     */
    public function events(array $parameters = []): array
    {
        $data = $this->client->projects()->events($this->id, $parameters);

        $events = [];
        foreach ($data as $event) {
            $events[] = Event::fromArray($this->getClient(), $this, $event);
        }

        return $events;
    }

    /**
     * @see MergeRequests::all() for available parameters.
     *
     * @param mixed[] $parameters
     *
     * @return MergeRequest[]
     */
    public function mergeRequests(array $parameters = []): array
    {
        $data = $this->client->mergeRequests()->all($this->id, $parameters);

        $mrs = [];
        foreach ($data as $mr) {
            $mrs[] = MergeRequest::fromArray($this->getClient(), $this, $mr);
        }

        return $mrs;
    }

    public function mergeRequest(int $id): MergeRequest
    {
        $mr = new MergeRequest($this, $id, $this->getClient());

        return $mr->show();
    }

    public function createMergeRequest(string $source, string $target, string $title, ?int $assignee = null, ?string $description = null): MergeRequest
    {
        $data = $this->client->mergeRequests()->create(
            $this->id,
            $source,
            $target,
            $title,
            $assignee,
            $this->id,
            $description
        );

        return MergeRequest::fromArray($this->getClient(), $this, $data);
    }

    /**
     * @param mixed[] $params
     */
    public function updateMergeRequest(int $id, array $params): MergeRequest
    {
        $mr = new MergeRequest($this, $id, $this->getClient());

        return $mr->update($params);
    }

    public function closeMergeRequest(int $id): MergeRequest
    {
        $mr = new MergeRequest($this, $id, $this->getClient());

        return $mr->close();
    }

    public function openMergeRequest(int $id): MergeRequest
    {
        $mr = new MergeRequest($this, $id, $this->getClient());

        return $mr->reopen();
    }

    public function mergeMergeRequest(int $id): MergeRequest
    {
        $mr = new MergeRequest($this, $id, $this->getClient());

        return $mr->merge();
    }

    /**
     * @see Issues::all() for available parameters.
     *
     * @param mixed[] $parameters
     *
     * @return Issue[]
     */
    public function issues(array $parameters = []): array
    {
        $data = $this->client->issues()->all($this->id, $parameters);

        $issues = [];
        foreach ($data as $issue) {
            $issues[] = Issue::fromArray($this->getClient(), $this, $issue);
        }

        return $issues;
    }

    /**
     * @param mixed[] $params
     */
    public function createIssue(string $title, array $params = []): Issue
    {
        $params['title'] = $title;
        $data            = $this->client->issues()->create($this->id, $params);

        return Issue::fromArray($this->getClient(), $this, $data);
    }

    public function issue(int $iid): Issue
    {
        $issue = new Issue($this, $iid, $this->getClient());

        return $issue->show();
    }

    /**
     * @param mixed[] $params
     */
    public function updateIssue(int $iid, array $params): Issue
    {
        $issue = new Issue($this, $iid, $this->getClient());

        return $issue->update($params);
    }

    public function closeIssue(int $iid, ?string $comment = null): Issue
    {
        $issue = new Issue($this, $iid, $this->getClient());

        return $issue->close($comment);
    }

    public function openIssue(int $iid): Issue
    {
        $issue = new Issue($this, $iid, $this->getClient());

        return $issue->open();
    }

    /**
     * @see Milestones::all() for available parameters.
     *
     * @param mixed[] $parameters
     *
     * @return Milestone[]
     */
    public function milestones(array $parameters = []): array
    {
        $data = $this->client->milestones()->all($this->id, $parameters);

        $milestones = [];
        foreach ($data as $milestone) {
            $milestones[] = Milestone::fromArray($this->getClient(), $this, $milestone);
        }

        return $milestones;
    }

    /**
     * @param mixed[] $params
     */
    public function createMilestone(string $title, array $params = []): Milestone
    {
        $params['title'] = $title;
        $data            = $this->client->milestones()->create($this->id, $params);

        return Milestone::fromArray($this->getClient(), $this, $data);
    }

    public function milestone(int $id): Milestone
    {
        $milestone = new Milestone($this, $id, $this->getClient());

        return $milestone->show();
    }

    /**
     * @param mixed[] $params
     */
    public function updateMilestone(int $id, array $params): Milestone
    {
        $milestone = new Milestone($this, $id, $this->getClient());

        return $milestone->update($params);
    }

    /**
     * @return Issue[]
     */
    public function milestoneIssues(int $id): array
    {
        $milestone = new Milestone($this, $id, $this->getClient());

        return $milestone->issues();
    }

    /**
     * @return Snippet[]
     */
    public function snippets(): array
    {
        $data = $this->client->snippets()->all($this->id);

        $snippets = [];
        foreach ($data as $snippet) {
            $snippets[] = Snippet::fromArray($this->getClient(), $this, $snippet);
        }

        return $snippets;
    }

    public function createSnippet(string $title, string $filename, string $code, string $visibility): Snippet
    {
        $data = $this->client->snippets()->create($this->id, $title, $filename, $code, $visibility);

        return Snippet::fromArray($this->getClient(), $this, $data);
    }

    public function snippet(int $id): Snippet
    {
        $snippet = new Snippet($this, $id, $this->getClient());

        return $snippet->show();
    }

    public function snippetContent(int $id): string
    {
        $snippet = new Snippet($this, $id, $this->getClient());

        return $snippet->content();
    }

    /**
     * @param mixed[] $params
     */
    public function updateSnippet(int $id, array $params): Snippet
    {
        $snippet = new Snippet($this, $id, $this->getClient());

        return $snippet->update($params);
    }

    public function removeSnippet(int $id): bool
    {
        $snippet = new Snippet($this, $id, $this->getClient());

        return $snippet->remove();
    }

    public function transfer(int $group_id): Group
    {
        $group = new Group($group_id, $this->getClient());

        return $group->transfer($this->id);
    }

    public function forkTo(int $id): Project
    {
        $data = $this->client->projects()->createForkRelation($id, $this->id);

        return self::fromArray($this->getClient(), $data);
    }

    public function forkFrom(int $id): Project
    {
        return $this->createForkRelation($id);
    }

    public function createForkRelation(int $id): Project
    {
        $data = $this->client->projects()->createForkRelation($this->id, $id);

        return self::fromArray($this->getClient(), $data);
    }

    public function removeForkRelation(): bool
    {
        $this->client->projects()->removeForkRelation($this->id);

        return true;
    }

    /**
     * @param mixed[] $params
     */
    public function setService(string $service_name, array $params = []): bool
    {
        $this->client->projects()->setService($this->id, $service_name, $params);

        return true;
    }

    public function removeService(string $service_name): bool
    {
        $this->client->projects()->removeService($this->id, $service_name);

        return true;
    }

    /**
     * @return Label[]
     */
    public function labels(): array
    {
        $data = $this->client->projects()->labels($this->id);

        $labels = [];
        foreach ($data as $label) {
            $labels[] = Label::fromArray($this->getClient(), $this, $label);
        }

        return $labels;
    }

    public function addLabel(string $name, string $color): Label
    {
        $data = $this->client->projects()->addLabel($this->id, [
            'name' => $name,
            'color' => $color,
        ]);

        return Label::fromArray($this->getClient(), $this, $data);
    }

    /**
     * @param mixed[] $params
     */
    public function updateLabel(string $name, array $params): Label
    {
        if (isset($params['name'])) {
            $params['new_name'] = $params['name'];
        }

        $params['name'] = $name;

        $data = $this->client->projects()->updateLabel($this->id, $params);

        return Label::fromArray($this->getClient(), $this, $data);
    }

    public function removeLabel(string $name): bool
    {
        $this->client->projects()->removeLabel($this->id, $name);

        return true;
    }

    /**
     * @return mixed[]
     */
    public function contributors(): array
    {
        $data = $this->client->repositories()->contributors($this->id);

        $contributors = [];
        foreach ($data as $contributor) {
            $contributors[] = Contributor::fromArray($this->getClient(), $this, $contributor);
        }

        return $contributors;
    }

    /**
     * @param mixed[] $scopes
     *
     * @return Job[]
     */
    public function jobs(array $scopes = []): array
    {
        $data = $this->client->jobs()->all($this->id, $scopes);

        $jobs = [];
        foreach ($data as $job) {
            $jobs[] = Job::fromArray($this->getClient(), $this, $job);
        }

        return $jobs;
    }

    /**
     * @param mixed[] $scopes
     *
     * @return Job[]
     */
    public function pipelineJobs(int $pipeline_id, array $scopes = []): array
    {
        $data = $this->client->jobs()->pipelineJobs($this->id, $pipeline_id, $scopes);

        $jobs = [];
        foreach ($data as $job) {
            $jobs[] = Job::fromArray($this->getClient(), $this, $job);
        }

        return $jobs;
    }

    public function job(int $job_id): Job
    {
        $data = $this->client->jobs()->show($this->id, $job_id);

        return Job::fromArray($this->getClient(), $this, $data);
    }

    /**
     * @return Badge[]
     */
    public function badges(): array
    {
        $data = $this->client->projects()->badges($this->id);

        $badges = [];
        foreach ($data as $badge) {
            $badges[] = Badge::fromArray($this->getClient(), $this, $badge);
        }

        return $badges;
    }

    /**
     * @param mixed[] $params
     */
    public function addBadge(array $params): Badge
    {
        $data = $this->client->projects()->addBadge($this->id, $params);

        return Badge::fromArray($this->getClient(), $this, $data);
    }

    /**
     * @param mixed[] $params
     */
    public function updateBadge(int $badge_id, array $params): Badge
    {
        $params['badge_id'] = $badge_id;

        $data = $this->client->projects()->updateBadge($this->id, $badge_id, $params);

        return Badge::fromArray($this->getClient(), $this, $data);
    }

    public function removeBadge(int $badge_id): bool
    {
        $this->client->projects()->removeBadge($this->id, $badge_id);

        return true;
    }

    /**
     * @param mixed[] $params
     */
    public function addProtectedBranch(array $params = []): Branch
    {
        $data = $this->client->projects()->addProtectedBranch($this->id, $params);

        return Branch::fromArray($this->getClient(), $this, $data);
    }
}
