<?php
namespace Infrastructure\Serialization;

class Utils
{

    /**
     * casts a stdClass object to a definied object
     */
    public static function recursiveCastToObject($stdClassObject, $className)
    {
        if (empty($className)) {
            throw new SerializationException('className cannot be empty');
        }
        if (empty($stdClassObject)) {
            throw new SerializationException('stdClassObject cannot be empty');
        }
        //already casted?
        if (@get_class($stdClassObject) == $className) {
            return $stdClassObject;
        }

        $object = self::castToObject($stdClassObject, $className);

        $reflectionClass = new \ReflectionClass($object);
        foreach ($reflectionClass->getProperties() as $property) {
            if (preg_match('/@var\s+([^\s]+)/', $property->getDocComment(), $matches)) {
                $propertyValue = $property->getValue($object);
                if (is_null($propertyValue)) {
                    continue;
                }

                $ns = $reflectionClass->getNamespaceName();
                list(, $type) = $matches;
                $type = $ns . (!empty($ns)?'\\':'') . $type;

                //cast properties that are array of class typed
                if (substr($type, -2, 2) == '[]') {
                    $singularType = substr($type, 0, -2);

                    if (@class_exists($singularType)) {
                        $typedArray = array();
                        foreach ($propertyValue as $key=>$singlePropertyValue) {
                            $typedArray[$key] =
                                self::recursiveCastToObject(
                                    (object) $singlePropertyValue,
                                    $singularType);
                        }

                        $property->setValue(
                            $object,
                            $typedArray);
                    }
                //cast properties that are class typed
                } elseif (@class_exists($type)) {
                    $property->setValue(
                        $object,
                        self::recursiveCastToObject(
                            (object) $propertyValue,
                            $type));
                }
            }

        }

        return $object;
    }

    public static function castToObject($stdClassObjectOrArray, $className)
    {
        if (@!class_exists($className)) {
            throw new SerializationException("Target class $className does not exist.");
        }

        if (is_array($stdClassObjectOrArray)) {
            $array = $stdClassObjectOrArray;
            //$array = array_change_key_case($stdClassObjectOrArray, CASE_LOWER);
            $stdClassObject = (object) $array;
        } else {
            $stdClassObject = $stdClassObjectOrArray;
        }

        return
            unserialize(
                sprintf(
                    'O:%d:"%s"%s',
                    strlen($className),
                    $className,
                    substr(serialize($stdClassObject), 14)
                ));
    }

}
