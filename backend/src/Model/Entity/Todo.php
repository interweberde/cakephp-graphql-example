<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Todo Entity
 *
 * @property int $id
 * @property int $user_id
 * @property int $sort_by
 * @property string $title
 * @property string $content
 * @property \Cake\I18n\FrozenTime|null $done
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 *
 * @property \App\Model\Entity\User $user
 */
class Todo extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array<string, bool>
     */
    protected $_accessible = [
        'user_id' => true,
        'sort_by' => true,
        'title' => true,
        'content' => true,
        'done' => true,
        'created' => true,
        'modified' => true,
        'user' => true,
    ];

	public const FIELD_ID = 'id';
	public const FIELD_USER_ID = 'user_id';
	public const FIELD_SORT_BY = 'sort_by';
	public const FIELD_TITLE = 'title';
	public const FIELD_CONTENT = 'content';
	public const FIELD_DONE = 'done';
	public const FIELD_CREATED = 'created';
	public const FIELD_MODIFIED = 'modified';
	public const FIELD_USER = 'user';
}
