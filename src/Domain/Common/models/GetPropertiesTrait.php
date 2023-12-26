<?php

namespace App\Domain\Common\models;

use ReflectionClass;
use ReflectionException;

trait GetPropertiesTrait
{
    /**
     * @throws ReflectionException
     */
    public function getPropertyNames($class): array
    {
        try {
            $reflection = new \ReflectionClass($class);
        } catch (ReflectionException $e) {
            throw $e;
        }
        $properties = $reflection->getProperties();

        $propertyNames = array();
        foreach ($properties as $property) {
            if (!$property->isStatic()) {
                $propertyNames[] = $property->getName();
            }
        }
        //echo PHP_EOL.json_encode($propertyNames).PHP_EOL;
        return $propertyNames;
    }
}