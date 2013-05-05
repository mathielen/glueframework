<?php
namespace Infrastructure\DataImport\ItemConverter;

use Ddeboer\DataImport\ItemConverter\ItemConverterInterface;

/**
 * Converts items a map
 *
 * @author Markus Thielen <info@logicx.de>
 */
class MappingItemConverter implements ItemConverterInterface
{

	private $fieldMapping;

    /**
     * Constructor
     *
     * @param callable $callback
     */
    public function __construct(array $fieldMapping)
    {
        $this->fieldMapping = $fieldMapping;
    }

    /**
     * {@inheritDoc}
     */
    public function convert(array $input)
    {
    	$output = array();

    	foreach ($this->fieldMapping as $from=>$to) {
    		$output[$to] = $input[$from];
    	}

        return $output;
    }
}
