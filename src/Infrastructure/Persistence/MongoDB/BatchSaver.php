<?php
namespace Infrastructure\Persistence\MongoDB;

use Doctrine\ODM\MongoDB\DocumentManager;

class BatchSaver
{

    /**
     * @var DocumentManager
     */
    private $documentManager;

    private $chunkSize;

    private $progressListener;

    public function __construct(
        DocumentManager $documentManager,
        $chunkSize = 500)
    {
        $this->documentManager = $documentManager;
        $this->chunkSize = $chunkSize;

    }

    public function setProgressListener(callable $progressListener = null)
    {
        $this->progressListener = $progressListener;
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
        $this->chunkBegin();

        $i = 0;
        $size = count($list);
        for($i;$i<$size;$i++) {
            $document = $list[$i]; //TODO transform??

            try {
                if ($replace) {
                    $this->documentManager->remove($document);
                }

                $this->documentManager->persist($document);
            } catch (\Exception $e) {
                throw new \Exception('Error in batch saving with document: '.print_r($document, true), 0, $e);
            }

            if ($i % $this->chunkSize == 0) {
                $this->chunkComplete($i);
                $this->chunkBegin();
            }

            $list[$i] = null;
        }

        $this->chunkComplete($i);
    }

    private function chunkBegin()
    {
    }

    private function chunkComplete($i)
    {
        $this->documentManager->flush();
        //$this->documentManager->clear(); WE CANNOT DO THIS, as the references in the next documents become invalid
        gc_collect_cycles();

        if ($this->progressListener) {
            call_user_func($this->progressListener, $i);
        }
    }

}
