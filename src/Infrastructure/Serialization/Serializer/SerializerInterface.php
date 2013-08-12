<?php
namespace Infrastructure\Serialization\Serializer;

interface SerializerInterface
{

    public function getHttpContentType();
    public function serialize($value);
    public function unserialize($value);

}
