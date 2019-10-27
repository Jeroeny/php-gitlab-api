<?php

declare(strict_types=1);

namespace Gitlab\Model;

use Gitlab\Client;

/**
 * @property-read int $issue_link_id
 * @property-read Issue $issue
 */
final class IssueLink extends Model
{
    /** @var mixed[] */
    protected static $properties = [
        'issue_link_id',
        'issue',
    ];

    /**
     * @param mixed[] $data
     */
    public static function fromArray(?Client $client, Project $project, array $data): IssueLink
    {
        $issue     = Issue::fromArray($client, $project, $data);
        $issueLink = new static($issue, $data['issue_link_id'], $client);

        return $issueLink->hydrate($data);
    }

    public function __construct(Issue $issue, ?int $issue_link_id = null, ?Client $client = null)
    {
        $this->setClient($client);
        $this->setData('issue', $issue);
        $this->setData('issue_link_id', $issue_link_id);
    }
}
