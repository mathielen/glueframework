<?php
namespace Infrastructure\Persistence\Doctrine2;

use Infrastructure\Persistence\Doctrine2\Search\DqlQuery;

use Infrastructure\Search\Dto\Query;

use Doctrine\ORM\EntityRepository;

class Finder implements \Infrastructure\Search\Finder
{

	/**
	 * @var EntityRepository
	 */
	private $doctrineEntityRepository;

	public function __construct(EntityRepository $doctrineEntityRepository)
	{
		$this->doctrineEntityRepository = $doctrineEntityRepository;
	}

	public function search(Query $query = null)
	{
		$qb = $this->doctrineEntityRepository->createQueryBuilder('table');

		if ($query instanceof DqlQuery) {
			$qb->andWhere($query->dql);
			foreach ($query->dqldata as $key=>$data) {
				$qb->setParameter($key, $data);
			}
		} elseif ($query) {
			$i = 0;
			foreach ($query->fields as $where=>$value) {
				if (empty($value)) {
					$qb->andWhere("table.$where IS NULL");
				} else {
					$qb->andWhere("table.$where = ?$i");
					$qb->setParameter($i, $value);
				}
				++$i;
			}
		}

		$q = $qb->getQuery();
		$q->useResultCache($query->useResultCache);
		if (!empty($query->resultCacheId)) {
			$q->setResultCacheId($query->resultCacheId);
		}
		$result = $q->execute();

		return $result;
	}

}