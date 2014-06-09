<?php
namespace Infrastructure\Persistence\Doctrine2;

use Infrastructure\Persistence\Doctrine2\Search\DqlQuery;

use Infrastructure\Search\Dto\Query;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;

class Doctrine2Finder implements \Infrastructure\Search\Finder
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

        if ($query instanceof DqlQuery && !empty($query->dql)) {
            $dql = $qb->getDQL() . ' ' . $query->dql;

            //TODO a hack! what about HAVING etc?
            if (!empty($query->sortField)) {
                $dql .= ' ORDER BY '.$query->sortField.' '.$query->sortDirection;
            }

            $q = $qb->getEntityManager()->createQuery($dql);
            $q->setParameters($query->dqldata);

        } elseif ($query && count($query->fields) > 0) {
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

            if (!empty($query->sortField)) {
                $qb->orderBy($query->sortField, $query->sortDirection);
            }
        }

        if (!@$q) {
            $q = $qb->getQuery();
        }

        $q->useResultCache($query->useResultCache);
        if (!empty($query->resultCacheId)) {
            $q->setResultCacheId($query->resultCacheId);
        }

        if (!empty($query->offset)) {
            $q->setFirstResult($query->offset);
        }
        if (!empty($query->limit)) {
            $q->setMaxResults($query->limit);
        }

        if ($query->paginated && !empty($query->limit)) {
            $result = new Paginator($q, true);
        } else {
            $result = $q->execute();
        }

        return $result;
    }

}
