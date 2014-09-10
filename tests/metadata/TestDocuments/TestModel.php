<?php
namespace TestDocuments;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * @ODM\Document
 */
class TestModel
{

    /**
     * @ODM\Id(strategy="auto")
     */
    private $id;

    /**
     *@ODM\Field(type="string")
     */
    private $value;

    public function __construct($initialData = array())
    {
        foreach ($initialData as $key=>$value) {
            $this->$key = $value;
        }
    }

}
