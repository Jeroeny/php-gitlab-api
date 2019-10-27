<?php

declare(strict_types=1);

namespace Gitlab\Model;

use Gitlab\Client;

/**
 * @property-read bool $compare_timeout
 * @property-read bool $compare_same_ref
 * @property-read Commit $commit
 * @property-read Commit[] $commits
 * @property-read Diff[] $diffs
 * @property-read Project $project
 */
final class Comparison extends Model
{
    /** @var mixed[] */
    protected static $properties = [
        'commit',
        'commits',
        'diffs',
        'compare_timeout',
        'compare_same_ref',
        'project',
    ];

    /**
     * @param mixed[] $data
     */
    public static function fromArray(?Client $client, Project $project, array $data): Comparison
    {
        $file = new static($project, $client);

        if (isset($data['commit'])) {
            $data['commit'] = Commit::fromArray($client, $project, $data['commit']);
        }

        if (isset($data['commits'])) {
            $commits = [];
            foreach ($data['commits'] as $commit) {
                $commits[] = Commit::fromArray($client, $project, $commit);
            }

            $data['commits'] = $commits;
        }

        if (isset($data['diffs'])) {
            $diffs = [];
            foreach ($data['diffs'] as $diff) {
                $diffs[] = Diff::fromArray($client, $project, $diff);
            }

            $data['diffs'] = $diffs;
        }

        return $file->hydrate($data);
    }

    public function __construct(Project $project, ?Client $client = null)
    {
        $this->setClient($client);
        $this->setData('project', $project);
    }
}
