<?php

declare(strict_types=1);

namespace Gitlab\Model;

use Gitlab\Client;

/**
 * @property-read string $tag_name
 * @property-read string $description
 * @property-read Commit $commit
 */
final class Release extends Model
{
    /** @var mixed[] */
    protected static $properties = [
        'tag_name',
        'description',
    ];

    /**
     * @param mixed[] $data
     */
    public static function fromArray(?Client $client, array $data): Release
    {
        $release = new static($client);

        return $release->hydrate($data);
    }

    public function __construct(?Client $client = null)
    {
        $this->setClient($client);
    }
}
