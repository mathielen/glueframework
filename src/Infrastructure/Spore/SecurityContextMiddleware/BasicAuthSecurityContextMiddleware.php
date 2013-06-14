<?php
namespace Infrastructure\Spore\SecurityContextMiddleware;

use Infrastructure\Search\Dto\Query;

use Infrastructure\Search\Finder;

use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

use Symfony\Component\Security\Core\SecurityContextInterface;

use RieberCheck\Credentials\CredentialProvider;

use Slim\Middleware;

class BasicAuthSecurityContextMiddleware extends Middleware
{

	/**
	 * @var SecurityContextInterface
	 */
	private $securityContext;

	/**
	 * @var Finder
	 */
	private $userFinder;

	public function __construct(SecurityContextInterface $securityContext)
	{
		$this->securityContext = $securityContext;
	}

	public function call()
	{
		$request = $this->app->request();

		$headerUsername = $request->headers('PHP_AUTH_USER');
		$headerPassword = $request->headers('PHP_AUTH_PW');

		if (!empty($headerUsername)) {
			$token = new UsernamePasswordToken(
				$headerUsername,
				$headerPassword,
				'providerKey'); //TODO providerkey?

			$this->securityContext->setToken($token);
		}

		$this->next->call();
	}

}