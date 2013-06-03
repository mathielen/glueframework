<?php
namespace Infrastructure\Spore;

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

	public function __construct(SecurityContextInterface $securityContext, Finder $userFinder)
	{
		$this->securityContext = $securityContext;
		$this->userFinder = $userFinder;
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
				'providerKey');

			$this->securityContext->setToken($token);

			//set the current user
			$users = $this->userFinder->search(new Query(array('username'=>$headerUsername)));
			if (count($users) > 0) {
				$request->currentUser = $users[0];
			}
		}

		$this->next->call();
	}

}