<?php
declare(strict_types=1);

/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link      https://cakephp.org CakePHP(tm) Project
 * @since     3.3.0
 * @license   https://opensource.org/licenses/mit-license.php MIT License
 */
namespace App;

use App\Model\Entity\User;
use App\Services\AuthenticationServiceProvider;
use App\Services\AuthorizationServiceProvider;
use Authentication\Middleware\AuthenticationMiddleware;
use Authorization\AuthorizationServiceInterface;
use Authorization\Middleware\AuthorizationMiddleware;
use Cake\Core\Configure;
use Cake\Core\ContainerInterface;
use Cake\Datasource\FactoryLocator;
use Cake\Error\Middleware\ErrorHandlerMiddleware;
use Cake\Http\BaseApplication;
use Cake\Http\Exception\BadRequestException;
use Cake\Http\Middleware\BodyParserMiddleware;
use Cake\Http\Middleware\CsrfProtectionMiddleware;
use Cake\Http\MiddlewareQueue;
use Cake\Http\Response;
use Cake\Http\ServerRequest;
use Cake\ORM\Locator\TableLocator;
use Cake\Routing\Middleware\AssetMiddleware;
use Cake\Routing\Middleware\RoutingMiddleware;

/**
 * Application setup class.
 *
 * This defines the bootstrapping logic and middleware layers you
 * want to use in your application.
 */
class Application extends BaseApplication
{
    /**
     * Load all the application configuration and bootstrap logic.
     *
     * @return void
     */
    public function bootstrap(): void
    {
        // Call parent to load bootstrap from files.
        parent::bootstrap();

        if (PHP_SAPI === 'cli') {
            $this->bootstrapCli();
        } else {
            FactoryLocator::add(
                'Table',
                (new TableLocator())->allowFallbackClass(false)
            );
        }

        /*
         * Only try to load DebugKit in development mode
         * Debug Kit should not be installed on a production system
         */
        if (Configure::read('debug')) {
            $this->addPlugin('DebugKit');
        }

        // Load more plugins here
        $this->addPlugin('Interweber/GraphQL', ['bootstrap' => true, 'routes' => true]);
    }

    /**
     * Setup the middleware queue your application will use.
     *
     * @param \Cake\Http\MiddlewareQueue $middlewareQueue The middleware queue to setup.
     * @return \Cake\Http\MiddlewareQueue The updated middleware queue.
     */
    public function middleware(MiddlewareQueue $middlewareQueue): MiddlewareQueue
    {
        $middlewareQueue
            ->add(function (ServerRequest $request, Response $response, $next) {
				// remove glob header as this will lead CakePHP to respond with html even though
				// json is expected (apollo is great - isn't it?)
				$accept = array_filter($request->getHeader('Accept'), function (string $elem) {
					return $elem !== '*/*';
				});

				// add json as default type in case there are no other types defined
				if (!$accept) {
					$accept[] = 'application/json';
				}

				$request = $request->withHeader('Accept', $accept);

				return $next($request, $response);
			})
			->add(function (ServerRequest $request, Response $response, $next) {
				$allowedOrigins = Configure::read('Cors.origin', []);
				$origin = $request->getHeader('Origin')[0] ?? null;

				if ($origin && !in_array($origin, $allowedOrigins) && !in_array('*', $allowedOrigins)) {
					throw new BadRequestException();
				}

				if (!$request->is('options')) {
					$response = $next($request, $response);
				}

				return $response
					->cors($request)
					->allowOrigin($allowedOrigins)
					->allowHeaders(['Content-Type', 'Auth', 'Authentication'])
					->exposeHeaders([
						'Cache-Control',
						'Content-Language',
						'Content-Type',
						'Expires',
						'Last-Modified',
						'Pragma',
						'Retry-After',
					])
					->allowCredentials()
					->build();
			})
            // Catch any exceptions in the lower layers,
            // and make an error page/response
            ->add(new ErrorHandlerMiddleware(Configure::read('Error')))

            // Handle plugin/theme assets like CakePHP normally does.
            ->add(new AssetMiddleware([
                'cacheTime' => Configure::read('Asset.cacheTime'),
            ]))

            // Add routing middleware.
            // If you have a large number of routes connected, turning on routes
            // caching in production could improve performance. For that when
            // creating the middleware instance specify the cache config name by
            // using it's second constructor argument:
            // `new RoutingMiddleware($this, '_cake_routes_')`
            ->add(new RoutingMiddleware($this))

            ->add(new AuthenticationMiddleware($this->getContainer()->get(AuthenticationServiceProvider::class)))
			->add(new AuthorizationMiddleware($this->getContainer()->get(AuthorizationServiceProvider::class), [
				'identityDecorator' => function (AuthorizationServiceInterface $auth, User $user) {
					return $user->setAuthorization($auth);
				},
			]))

            // Parse various types of encoded request bodies so that they are
            // available as array through $request->getData()
            // https://book.cakephp.org/4/en/controllers/middleware.html#body-parser-middleware
            ->add(new BodyParserMiddleware())

            // Cross Site Request Forgery (CSRF) Protection Middleware
            // https://book.cakephp.org/4/en/security/csrf.html#cross-site-request-forgery-csrf-middleware
//            ->add(new CsrfProtectionMiddleware([
//                'httponly' => false,
//            ]))
            ;

        return $middlewareQueue;
    }

    /**
     * Register application container services.
     *
     * @param \Cake\Core\ContainerInterface $container The Container to update.
     * @return void
     * @link https://book.cakephp.org/4/en/development/dependency-injection.html#dependency-injection
     */
    public function services(ContainerInterface $container): void
    {
        $container->add(AuthenticationServiceProvider::class);
		$container->add(AuthorizationServiceProvider::class);
    }

    /**
     * Bootstrapping for CLI application.
     *
     * That is when running commands.
     *
     * @return void
     */
    protected function bootstrapCli(): void
    {
        $this->addOptionalPlugin('Cake/Repl');
        $this->addOptionalPlugin('Bake');

        $this->addPlugin('Migrations');

        $this->addOptionalPlugin('IdeHelper');
    }
}
