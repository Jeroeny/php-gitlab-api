<?php

declare(strict_types=1);

namespace Gitlab\Model;

use Gitlab\Client;
use function get_class;

/**
 * @property-read int $id
 * @property-read User $author
 * @property-read string $body
 * @property-read string $created_at
 * @property-read string $updated_at
 * @property-read string $parent_type
 * @property-read Issue|MergeRequest $parent
 * @property-read string $attachment
 * @property-read bool $system
 */
final class Note extends Model
{
    /** @var mixed[] */
    protected static $properties = [
        'id',
        'author',
        'body',
        'created_at',
        'updated_at',
        'parent_type',
        'parent',
        'attachment',
        'system',
    ];

    /**
     * @param mixed[] $data
     *
     * @return mixed
     */
    public static function fromArray(?Client $client, Noteable $type, array $data)
    {
        $comment = new static($type, $client);

        if (isset($data['author'])) {
            $data['author'] = User::fromArray($client, $data['author']);
        }

        return $comment->hydrate($data);
    }

    public function __construct(Noteable $type, ?Client $client = null)
    {
        $this->setClient($client);
        $this->setData('parent_type', get_class($type));
        $this->setData('parent', $type);
    }
}
