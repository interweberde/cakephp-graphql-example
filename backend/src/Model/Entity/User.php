<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Authentication\PasswordHasher\FallbackPasswordHasher;
use Authorization\AuthorizationServiceInterface;
use Authorization\Policy\Exception\MissingPolicyException;
use Authorization\Policy\ResultInterface;
use Cake\ORM\Entity;
use Cake\ORM\Query;
use TheCodingMachine\GraphQLite\Annotations\Field;
use TheCodingMachine\GraphQLite\Annotations\MagicField;
use TheCodingMachine\GraphQLite\Annotations\Type;

/**
 * User Entity
 *
 * @property int $id
 * @property string $email
 * @property string $password
 * @property string $first_name
 * @property string|null $middle_name
 * @property string $last_name
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 *
 * @Type()
 * @MagicField(name="id", phpType="int")
 * @MagicField(name="email", phpType="string")
 * @MagicField(name="name", phpType="string")
 * @MagicField(name="created", phpType="\Cake\I18n\FrozenTime")
 * @MagicField(name="modified", phpType="\Cake\I18n\FrozenTime")
 */
class User extends Entity implements \Authorization\IdentityInterface, \Authentication\IdentityInterface {
	/**
	 * Fields that can be mass assigned using newEntity() or patchEntity().
	 *
	 * Note that when '*' is set to true, this allows all unspecified fields to
	 * be mass assigned. For security purposes, it is advised to set '*' to false
	 * (or remove it), and explicitly make individual fields accessible as needed.
	 *
	 * @var array
	 */
	protected $_accessible = [
		'email' => true,
		'password' => true,
		'name' => true,
		'created' => true,
		'modified' => true,
	];

	/**
	 * Fields that are excluded from JSON versions of the entity.
	 *
	 * @var array
	 */
	protected $_hidden = [
		'password',
	];

	/**
	 * @var \Authorization\AuthorizationServiceInterface
	 */
	private AuthorizationServiceInterface $authorization;

	public static function getHasherConfig() {
		return [
			[
				'className' => \Authentication\PasswordHasher\DefaultPasswordHasher::class,
				'hashType' => PASSWORD_ARGON2ID,
			],
		];
	}

	private static function getPasswordHasher() {
		return new FallbackPasswordHasher([
			'hashers' => self::getHasherConfig(),
		]);
	}

	protected function _setPassword($password) {
		if ($password && strlen($password) > 0) {
			return $this->getPasswordHasher()->hash($password);
		}

		return $password;
	}

	/**
	 * @inheritDoc
	 */
	public function can(string $action, $resource): bool {
		return $this->canResult($action, $resource)->getStatus();
	}

	/**
	 * @inheritDoc
	 */
	public function canResult(string $action, $resource): ResultInterface {
		return $this->authorization->canResult($this, $action, $resource);
	}

	/**
	 * @inheritDoc
	 */
	public function applyScope(string $action, $resource) {
		try {
			return $this->authorization->applyScope($this, $action, $resource);
		} catch (MissingPolicyException $e) {
			if (!($resource instanceof Query)) {
				throw $e;
			}

			throw new MissingPolicyException(
				sprintf("Missing Policy for '%s'", get_class($resource->getRepository())),
				(int) $e->getCode(),
				$e
			);
		}
	}

	public function setAuthorization(AuthorizationServiceInterface $service) {
		$this->authorization = $service;

		return $this;
	}

	/**
	 * @inerhitDoc
	 */
	public function getIdentifier() {
		return $this->id;
	}

	/**
	 * @inerhitDoc
	 */
	public function getOriginalData() {
		return $this;
	}

	public const FIELD_ID = 'id';
	public const FIELD_EMAIL = 'email';
	public const FIELD_PASSWORD = 'password';
	public const FIELD_FIRST_NAME = 'first_name';
	public const FIELD_MIDDLE_NAME = 'middle_name';
	public const FIELD_LAST_NAME = 'last_name';
	public const FIELD_CREATED = 'created';
	public const FIELD_MODIFIED = 'modified';
}
