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

    /**
     * here, we collect all document classes, for flushing only them. this way we dont flush documents, that may
     * be used as a reference
     */
    private $flushDocumentClasses;

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
        for ($i;$i<$size;$i++) {
            $document = $list[$i]; //TODO transform??
            $this->flushDocumentClasses[get_class($document)] = true;

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
        $this->flushDocumentClasses = array();
    }

    private function chunkComplete($i)
    {
        $this->documentManager->flush();

        foreach (array_keys($this->flushDocumentClasses) as $documentClass) {
            $this->documentManager->clear($documentClass);
        }
        gc_collect_cycles();

        if ($this->progressListener) {
            call_user_func($this->progressListener, $i);
        }
    }

}
