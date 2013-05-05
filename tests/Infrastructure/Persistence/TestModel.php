<?php
namespace Infrastructure\Persistence;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class TestModel
{

	/**
	 * @ORM\Column(type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private $id;

	/**
	 * @ORM\Column(type="string", length=255, nullable = true)
	 */
	private $value;

	public function __construct($initialData = array())
	{
		foreach ($initialData as $key=>$value) {
			$this->$key = $value;
		}
	}

}