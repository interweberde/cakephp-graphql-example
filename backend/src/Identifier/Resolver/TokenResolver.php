<?php
declare(strict_types=1);

namespace App\Identifier\Resolver;

use App\Helper\SystemUser;
use Authentication\Identifier\Resolver\ResolverInterface;
use Cake\Core\Configure;

class TokenResolver implements ResolverInterface {
	/**
	 * @inheritDoc
	 */
	public function find(array $conditions, string $type = self::TYPE_AND) {
		$token = Configure::read('Development.token', null);

		if (!$token) {
			return null;
		}

		return SystemUser::getInstance();
	}
}
