<?php
namespace Infrastructure\Serialization\Serializer\Xml;

class XmlValidator
{

	private $xsdFilename;

	public function __construct($xsdFilename)
	{
		if (!is_readable($xsdFilename) || !is_file($xsdFilename)) {
			throw new InvalidArgumentException("File $xsdFilename cannot be read!");
		}

		$this->xsdFilename = $xsdFilename;
	}

	/**
	 * returns true, if given xml is valid against given xsd or array of validation error strings
	 *
	 * @retrun true | array
	 */
	public function validate(\SimpleXMLElement $data)
	{
		libxml_use_internal_errors(true);

		$doc = new DOMDocument();
		$doc->loadXML($data->asXML());

		if (!$doc->schemaValidate($this->xsdFilename)) {
			$libXmlErrors = libxml_get_errors();
			$errors = array();
			foreach ($libXmlErrors as $libXmlError) {
				$errors[] = $libXmlError->message;
			}
			return $errors;
		}

		return true;
	}

}