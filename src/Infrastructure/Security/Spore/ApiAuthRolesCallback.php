<?php
namespace Infrastructure\Security\Spore;

use Symfony\Component\Security\Core\SecurityContextInterface;

use Symfony\Component\DependencyInjection\ContainerInterface;

use Symfony\Component\DependencyInjection\ContainerAware;

class ApiAuthRolesCallback
{

	/**
	 * @var SecurityContextInterface
	 */
	private $securityContext;

	public function __construct(SecurityContextInterface $securityContext)
	{
		$this->securityContext = $securityContext;
	}

	public function userHasRole($roles)
	{
		if (empty($roles)) {
			return true;
		}

		foreach ($roles as $role) {
			if ($this->securityContext->isGranted($role)) {
				return true;
			}
		}

		return false;
	}

}