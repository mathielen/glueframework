<?php
namespace Infrastructure\Persistence\Doctrine2\Search;

use Infrastructure\Search\Dto\Query;

class DqlQuery extends Query
{

	public $dql;
	public $dqldata;

	public function __construct($dql, $dqldata)
	{
		$this->dql = $dql;
		$this->dqldata = $dqldata;
	}

}