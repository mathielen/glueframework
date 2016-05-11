<?php

namespace Infrastructure\Search;

use JMS\Serializer\Annotation\Type;

class QueryResult
{
    /**
     * @Type("array")
     */
    protected $metadata = null;

    /**
     * @Type("array")
     */
    protected $data = array();

    /**
     * @return QueryResult
     */
    public static function fromDataset($data, $total = null)
    {
        return new self(
            $data,
            array('total' => is_null($total) ? count($data) : $total)
        );
    }

    public function __construct($data, $metadata = null)
    {
        $this->data = $data;
        $this->metadata = $metadata;
    }
}
