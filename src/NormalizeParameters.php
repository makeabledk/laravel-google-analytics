<?php

namespace Makeable\Analytics;

trait NormalizeParameters
{
    /**
     * @param $object
     * @return string
     */
    protected static function normalize($object)
    {
        if ($object === null) {
            return '~all';
        }
        if (is_object($object) && method_exists($object, 'getId')) {
            return $object->getId();
        }
        return $object;
    }
}
