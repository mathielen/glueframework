<?php
namespace Infrastructure\Persistence\Driver;

use Doctrine\Common\Annotations\Annotation;

/** @Annotation */
final class EntityAnnotation extends Annotation
{
    /**
     * @var string
     */
    public $name = null;

    /**
     * @var string
     */
    public $type = null; //string, date, float, double, integer, long, short, byte, boolean, binary

    /**
     * @var string
     */
    public $null_value = null; //na

    /**
     * @var string
     */
    public $index = null; //not_analyzed

    /**
     * @var boolean
     */
    public $include_in_all = null;

}