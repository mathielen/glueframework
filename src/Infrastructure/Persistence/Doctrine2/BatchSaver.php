<?php
namespace Infrastructure\Persistence\Doctrine2;

use Doctrine\ORM\EntityManager;

class BatchSaver
{

    /**
     * @var EntityManager
     */
    private $entityManager;

    private $chunkSize;

    public function __construct(EntityManager $entityManager, $chunkSize = 500)
    {
        $this->entityManager = $entityManager;
        $this->chunkSize = $chunkSize;
    }

    public function save(\Traversable $list)
    {
		$this->process($list);
    }

    public function replace(\Traversable $list)
    {
    	$this->process($list, true);
    }

    private function process(\Traversable $list, $replace=false)
    {
    	if ($replace) {
    		$q = $this->entityManager->createQuery('delete from '.get_class($list[0]));
			$q->execute();
    	}

        $this->chunkBegin();

        $i = 0;
        foreach ($list as $object) {
            $this->entityManager->persist($object);
            ++$i;

            if ($i % $this->chunkSize == 0) {
                $this->chunkComplete();
                $this->chunkBegin();
            }
        }

        $this->chunkComplete();
    }

    private function chunkBegin()
    {
        $this->entityManager->beginTransaction();
    }

    private function chunkComplete()
    {
        $this->entityManager->flush();
        $this->entityManager->commit();
    }

}
