<?php
namespace Infrastructure\Search;

class Resultset
{

	private $data;
	private $metadata;

	public function __construct()
	{
		$this->data = array();
		$this->metadata = array();
	}

	public function setMetadata($class, $value)
	{
		if (empty($value)) {
			return;
		}

		$this->metadata[$class] = $value;
	}

	public function getMetadata($class)
	{
		return $this->metadata[$class];
	}

	public function add($object)
	{
		if (empty($object)) {
			return;
		}

		$this->data[] = $object;
	}

}