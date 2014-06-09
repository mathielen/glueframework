<?php
namespace Infrastructure\Persistence\ElasticSearch;

use Elastica\Exception\NotFoundException;
class BatchSaver
{

    /**
     * @var \Elastica\Type
     */
    private $elasticaType;

    private $chunkSize;

    public function __construct(\Elastica\Type $elasticaType, $chunkSize = 500)
    {
        $this->elasticaType = $elasticaType;
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
        $this->chunkBegin();

        $i = 0;
        foreach ($list as $object) {
            $document = $object; //TODO transform??

            try {
                if ($replace) {
                    try {
                        $this->elasticaType->deleteDocument($document);
                    } catch (NotFoundException $e) {}
                }

                $this->elasticaType->addDocument($document);
            } catch (\Exception $e) {
                throw new \Exception('Error in batch saving with document: '.print_r($document, true), 0, $e);
            }
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
    }

    private function chunkComplete()
    {
        $this->elasticaType->getIndex()->refresh();
    }

}
