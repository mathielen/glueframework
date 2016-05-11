<?php

namespace Infrastructure\Persistence\MongoDB;

use Infrastructure\Search\Dto\Query;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\ODM\MongoDB\DocumentRepository;

class MongoDBFinder implements \Infrastructure\Search\Finder
{
    /**
     * @var DocumentRepository
     */
    private $doctrineDocumentRepository;

    public function __construct(DocumentRepository $doctrineDocumentRepository)
    {
        $this->doctrineDocumentRepository = $doctrineDocumentRepository;
    }

    public function search(Query $query = null)
    {
        $qb = $this->doctrineDocumentRepository->createQueryBuilder('table');

        if ($query && count($query->fields) > 0) {
            $i = 0;
            foreach ($query->fields as $where => $value) {
                if (empty($value)) {
                    $qb->field($where)->exists(false);
                } else {
                    $field = $qb->field($where);

                    if (is_array($value)) {
                        $operator = $value[0];
                        $field->$operator($value[1]);
                    } else {
                        $field->equals($value);
                    }
                }
                ++$i;
            }

            if (!empty($query->sortField)) {
                $qb->sort($query->sortField, $query->sortDirection);
            }
        }

        if (!empty($query->offset)) {
            $qb->skip($query->offset);
        }
        if (!empty($query->limit)) {
            $qb->limit($query->limit);
        }
        if (!empty($query->sortField)) {
            $qb->sort($query->sortField, $query->sortDirection);
        }

        $q = $qb->getQuery();

        if ($query && $query->paginated && !empty($query->limit)) {
            $result = new Paginator($q, true);
        } else {
            $result = $q->execute();
        }

        return array_values($result->toArray()); //TODO unified result class?
    }
}
