<?php
namespace Infrastructure\Serialization\Serializer;

class XmlSerializer implements SerializerInterface
{

	private $targetNamespace;

	public function __construct($targetNamespace = null)
	{
		$this->targetNamespace = $targetNamespace;
	}

	public function getHttpContentType()
	{
		return 'application/xml';
	}

	public function serialize($value)
	{
		if (is_object($value) && method_exists($value, 'toArray')) {
			$value = $value->toArray();
		} else {
			$value = (array)$value;
		}


		$xml = self::arrayToXml($value, new \SimpleXMLElement('<result/>'));
		return $xml->asXML();
	}

	public function unserialize($value)
	{
		@$result = simplexml_load_string($value);

		if ($result === false) {
			return $value;
		}

		return $result;
	}

	public static function arrayToXml(array $arr, SimpleXMLElement $xml)
	{
		foreach ($arr as $k => $v) {
			$k = (is_numeric($k)) ? 'item' : $k;
			$v = (is_object($v)) ? (array)$v: $v;

			//replace special chars that would break xml
			$k = preg_replace('/[^a-zA-Z0-9_ %\[\]\.\(\)%&-]/s', '', $k);

			is_array($v)
				? self::arrayToXml($v, $xml->addChild($k))
				: $xml->addChild($k, $v);
		}

		return $xml;
	}

	public static function xmlToArray(\SimpleXMLElement $xmlObject, $removeEmptyNodes = false, $targetNamespace=null)
	{
		$config = array();

		if (count($xmlObject->children($targetNamespace)) > 0) {
			foreach ($xmlObject->children($targetNamespace) as $key => $value) {
				if (count($value->children($targetNamespace)) > 0) {
					$value = self::xmlToArray($value, $removeEmptyNodes, $targetNamespace);
				} else {
					$value = (string)$value;
				}

				if (empty($value) && $removeEmptyNodes) {
					continue;
				}

				if (array_key_exists($key, $config)) {
					if (!is_array($config[$key]) || !array_key_exists(0, $config[$key])) {
						$config[$key] = array($config[$key]);
					}

					$config[$key][] = $value;
				} else {
					$config[$key] = $value;
				}
			}
		} else if (count($config) === 0) {
			// Object has no children nor attributes
			// attribute: it's a string
			$config = (string) $xmlObject;
		}

		return $config;
	}

}