<?php
namespace Infrastructure\Search;

use Infrastructure\Search\Dto\Query;

interface Finder
{

	public function search(Query $query = null);

}