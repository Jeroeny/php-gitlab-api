<?php

declare(strict_types=1);

namespace Gitlab\Model;

use Gitlab\Client;

/**
 * @property-read string $note
 * @property-read string $path
 * @property-read string $line
 * @property-read string $line_type
 * @property-read User $author
 */
final class CommitNote extends Model
{
    /** @var mixed[] */
    protected static $properties = [
        'note',
        'path',
        'line',
        'line_type',
        'author',
    ];

    /**
     * @param mixed[] $data
     */
    public static function fromArray(?Client $client, array $data): CommitNote
    {
        $comment = new static($client);

        if (isset($data['author'])) {
            $data['author'] = User::fromArray($client, $data['author']);
        }

        return $comment->hydrate($data);
    }

    public function __construct(?Client $client = null)
    {
        $this->setClient($client);
    }
}
