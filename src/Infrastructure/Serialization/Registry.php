<?php
class Infrastructure_Serialization_Registry
{

	const DEFAULT_CONTENTTYPE = 'application/xml';

	private $serializers;

	public function __construct(array $serializers)
	{
		$this->serializers = $serializers;
	}

	/**
	 * @return Infrastructure_Serialization_Serializer_Interface
	 */
	public function serializer($contentType = null)
	{
		if (is_null($contentType)) {
			$contentType = self::DEFAULT_CONTENTTYPE;
		}

		list($contentType) = explode(';', $contentType);
		$contentType = trim($contentType);

		if (!array_key_exists($contentType, $this->serializers)) {
			throw new Infrastructure_Serialization_Exception("No serializer found for contentType: $contentType");
		}

		return $this->serializers[$contentType];
	}

}