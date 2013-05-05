<?php
namespace Infrastructure\Persistence\ElasticSearch;

class IndexFactory
{

	/**
	 * @var \Elastica_Type[]
	 */
	private $elasticaTypes;
	/**
	 * @var array DTO-Name => Elastica_Type_Mapping
	 */
	private $mappings;

	public function __construct(
		array $elasticaTypes = array(),
		array $mappings = array())
	{
		$this->elasticaTypes = $elasticaTypes;
		$this->mappings = $mappings;
	}

	public function sanitize()
	{
		foreach ($this->elasticaTypes as $elasticaType) {
			$this->createIndex($elasticaType);
		}
	}
	public function recreateIndex(\Elastica_Type $elasticaType)
	{
		$this->createIndex($elasticaType, true);
	}
	public function createIndex(\Elastica_Type $elasticaType, $recreate=false)
	{
		if ($recreate || !$elasticaType->getIndex()->exists()) {
		$createIndexResult = $elasticaType->getIndex()->create(
				array(
						'number_of_shards' => 4,
						'number_of_replicas' => 1,
						'analysis' => array(
								'analyzer' => array(
										'default' => array(
												'type' => 'custom',
												'tokenizer' => 'whitespace',
												'filter' => array('lowercase', 'standard')
										),
										'indexAnalyzer' => array(
												'tokenizer' => 'standard',
												'filter' => array('lowercase', 'standard')
										),
										'searchAnalyzer' => array(
												'tokenizer' => 'standard',
												'filter' => array('lowercase', 'standard')
										)
								)
						)
				),
					$recreate
		);
		}

		//set mapping if given
		$dtoName = $elasticaType->getName();
		if (array_key_exists($dtoName, $this->mappings)) {
			$elasticaType->setMapping($this->mappings[$dtoName]);
		}
	}

}