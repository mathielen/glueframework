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
