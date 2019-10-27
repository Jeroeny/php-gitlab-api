<?php

declare(strict_types=1);

namespace Gitlab\Model;

use Gitlab\Client;

/**
 * @property-read int $id
 * @property-read int $iid
 * @property-read Group $group
 * @property-read int $group_id
 * @property-read string $title
 * @property-read string $description
 * @property-read string $state
 * @property-read string $created_at
 * @property-read string $updated_at
 * @property-read string $due_date
 * @property-read string $start_date
 */
final class GroupMilestone extends Model
{
    /** @var mixed[] */
    protected static $properties = [
        'id',
        'iid',
        'group',
        'group_id',
        'title',
        'description',
        'state',
        'created_at',
        'updated_at',
        'due_date',
        'start_date',
    ];

    /**
     * @param mixed[] $data
     */
    public static function fromArray(?Client $client, Group $group, array $data): GroupMilestone
    {
        $milestone = new static($group, $data['id'], $client);

        return $milestone->hydrate($data);
    }

    /**
     * @param string|int $id
     */
    public function __construct(Group $group, $id, ?Client $client = null)
    {
        $this->setClient($client);
        $this->setData('id', $id);
        $this->setData('group', $group);
    }
}
