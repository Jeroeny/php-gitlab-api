<?php

declare(strict_types=1);

namespace Gitlab\Model;

use Gitlab\Client;

/**
 * @property-read string $link_url
 * @property-read string $image_url
 */
final class Badge extends Model
{
    /** @var mixed[] */
    protected static $properties = [
        'id',
        'link_url',
        'image_url',
        'rendered_link_url',
        'rendered_image_url',
        'kind',
    ];

    /**
     * @param mixed[] $data
     */
    public static function fromArray(?Client $client, Project $project, array $data): Badge
    {
        $badge = new static($project, $client);

        return $badge->hydrate($data);
    }

    public function __construct(Project $project, ?Client $client = null)
    {
        $this->setClient($client);
        $this->setData('project', $project);
    }
}
