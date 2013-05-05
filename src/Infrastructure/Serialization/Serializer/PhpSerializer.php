<?php
namespace Infrastructure\Serialization\Serializer;

class PhpSerializer implements SerializerInterface
{

	public function getHttpContentType()
	{
		return 'text/plain';
	}

	public function serialize($value)
	{
		return serialize($value);
	}

	public function unserialize($value)
	{
		$object = @unserialize($value);

		if (!$object) {
			throw new Api_Exception_BadRequest($value. ' could not be decoded!');
		}

		return $object;
	}

}