<?php

namespace Infrastructure\Serialization\Serializer;

class JsonSerializer implements SerializerInterface
{
    public function getHttpContentType()
    {
        return 'application/json';
    }

    public function serialize($value)
    {
        if (empty($value)) {
            return;
        }
        $this->decorateCls($value);
        @$json = json_encode($value);

        return $json;
    }

    private function decorateCls($object)
    {
        if (is_array($object)) {
            foreach ($object as &$obj) {
                $this->decorateCls($obj);
            }

            return;
        } elseif (!is_object($object)) {
            return;
        }

        foreach ($object as &$value) {
            $this->decorateCls($value);
        }

        $object->cls = get_class($object);
    }

    public function unserialize($value)
    {
        if (empty($value)) {
            return;
        }
        $object = json_decode($value);

        if ($object === null) {
            throw new SerializationException($value.' could not be decoded!');
        }

        $this->castRecursiveToObject($object);

        return $object;
    }

    private function castRecursiveToObject(&$object)
    {
        if (is_array($object)) {
            foreach ($object as &$obj) {
                $this->castRecursiveToObject($obj);
            }

            return;
        } elseif (!is_object($object) || !isset($object->cls)) {
            return;
        }

        foreach ($object as &$value) {
            $this->castRecursiveToObject($value);
        }

        $className = $object->cls;
        unset($object->cls);

        $object = Utils::recursiveCastToObject($object, $className);
    }
}
