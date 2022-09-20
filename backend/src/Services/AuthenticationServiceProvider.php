<?php
declare(strict_types=1);

namespace App\Services;

use App\Identifier\Resolver\TokenResolver;
use App\Model\Entity\User;
use Authentication\AuthenticationService;
use Authentication\AuthenticationServiceInterface;
use Authentication\Identifier\IdentifierInterface;
use Authentication\PasswordHasher\FallbackPasswordHasher;
use Cake\Core\Configure;
use Cake\Http\Cookie\CookieInterface;
use Cake\I18n\FrozenTime;
use Psr\Http\Message\ServerRequestInterface;

class AuthenticationServiceProvider implements \Authentication\AuthenticationServiceProviderInterface {
	public function getAuthenticationService(ServerRequestInterface $request): AuthenticationServiceInterface {
		$service = new AuthenticationService();

		$fields = [
			IdentifierInterface::CREDENTIAL_USERNAME => 'email',
			IdentifierInterface::CREDENTIAL_PASSWORD => 'password',
		];

		$service->loadAuthenticator('Authentication.Token', [
			'header' => Configure::read('Authentication.tokenHeader', 'Authorization'),
		]);

		$service->loadAuthenticator('Authentication.Session');
		$service->loadAuthenticator('Authentication.Form', [
			'fields' => $fields,
			'loginUrl' => '/users/login',
		]);

		/** @var string|\DateInterval|null $expires */
		$expires = Configure::read('Authentication.expires');

		$expiresDate = null;
		if (is_string($expires)) {
			$expiresDate = FrozenTime::now()->modify($expires);
		} elseif ($expires instanceof \DateInterval) {
			$expiresDate = FrozenTime::now()->add($expires);
		}

		$service->loadAuthenticator('Authentication.Cookie', [
			'fields' => $fields,
			'cookie' => [
				'name' => Configure::read('Authentication.cookieName'),
				'secure' => true,
				'samesite' => CookieInterface::SAMESITE_NONE,
				'httponly' => true,
				'expires' => $expiresDate,
			],
		]);

		$service->loadIdentifier('Authentication.Token', [
			'resolver' => [
				'className' => TokenResolver::class,
			],
		]);
		$service->loadIdentifier('Authentication.Password', [
			'fields' => $fields,
			'passwordHasher' => [
				'className' => FallbackPasswordHasher::class,
				'hashers' => User::getHasherConfig(),
			],
		]);

		return $service;
	}
}
