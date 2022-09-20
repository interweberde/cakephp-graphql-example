<?php
declare(strict_types=1);

namespace App\Controller;

use Authentication\Identifier\PasswordIdentifier;
use Cake\Event\EventInterface;

/**
 * Users Controller
 *
 * @property \App\Model\Table\UsersTable $Users
 */
class UsersController extends AppController {
	public function beforeFilter(EventInterface $event) {
		parent::beforeFilter($event);

		$this->Authentication->allowUnauthenticated(['login', 'check']);
	}

	/**
	 * @throws \Exception
	 */
	public function login() {
		$this->getRequest()->allowMethod(['post']);
		$this->Authorization->skipAuthorization();

		$result = $this->Authentication->getResult();

		$this->viewBuilder()
			->setOption('serialize', [
				'success',
			]);

		if (!$result || !$result->isValid()) {
			$this->set([
				'success' => false,
			]);

			return;
		}

		/** @var \Authentication\AuthenticationService $authenticationService */
		$authenticationService = $this->Authentication->getAuthenticationService();
		$provider = $authenticationService->identifiers()->getIdentificationProvider();
		if ($provider instanceof PasswordIdentifier && $provider->needsPasswordRehash()) {
			// rehash password
			$passwordField = $provider->getConfig('fields.' . $provider::CREDENTIAL_PASSWORD);

			/** @var \App\Model\Entity\User $user */
			$user = $this->Authentication->getIdentity();
			$user->password = $this->getRequest()->getData($passwordField);
			$this->Users->save($user);
		}

		$this->set([
			'success' => $result->isValid(),
		]);
	}

	public function check() {
		$this->Authorization->skipAuthorization();
		$result = $this->Authentication->getResult();

		$this->set([
			'authenticated' => $result && $result->isValid(),
		]);

		$this->viewBuilder()
			->setOption('serialize', [
				'authenticated',
			]);
	}

	public function logout() {
		$this->Authorization->skipAuthorization();

		$this->Authentication->logout();

		return $this->getResponse()->withStatus(204);
	}
}
