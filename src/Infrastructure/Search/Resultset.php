<?php
namespace Infrastructure\Search;

class Resultset implements \IteratorAggregate, \Countable
{

    private $data;
    private $metadata = array();

    public function __construct($data = array())
    {
        $this->data = $data;
        $this->setMetadata('count', $this->count());
    }

    public function setMetadata($class, $value=null)
    {
        if (is_null($value)) {
            $this->metadata = $class;

            return;
        }

        $this->metadata[$class] = $value;
    }

    public function getMetadata($class)
    {
        return $this->metadata[$class];
    }

    public function add($object)
    {
        if (empty($object)) {
            return;
        }

        $this->data[] = $object;
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->data);
    }

    public function count()
    {
        return count($this->data);
    }

}
