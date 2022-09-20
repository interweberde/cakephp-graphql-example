<?php
declare(strict_types=1);

namespace App\Helper;

use App\Classes\Enum\Role;
use App\Model\Entity\User;
use Authorization\Policy\Result;
use Authorization\Policy\ResultInterface;

class SystemUser extends User {
	private static ?User $user = null;

	public static function getInstance(): User {
		if (static::$user === null) {
			static::$user = new static([
				'name' => 'System',
			], [
				'markNew' => false,
				'markClean' => true,
				'guard' => true,
			]);

			static::$user->id = 0;

			static::$user->setSource('Users');
		}

		return static::$user;
	}

	protected $_accessible = [];

	private function __construct(array $properties = [], array $options = []) {
		parent::__construct($properties, $options);
	}

	public function canResult(string $action, $resource): ResultInterface {
		return new Result(true, 'root');
	}

	public function applyScope(string $action, $resource) {
		return $resource;
	}
}
