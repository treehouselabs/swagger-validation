<?php

declare(strict_types=1);

namespace TreeHouse\SwaggerValidation\Validator;

use Exception;
use TreeHouse\SwaggerValidation\Property;

class Object
{
    /**
     * @param string     $property
     * @param Property[] $allowedProperties
     * @param string     $context
     *
     * @throws Exception
     */
    public static function mayContainProperty(string $property, array $allowedProperties, string $context)
    {
        $properties = array_map(function (Property $property) {
            return $property->getName();
        }, $allowedProperties);

        if (!array_key_exists($property, $properties)) {
            throw new Exception(sprintf('Property %s is not allowed in %s', $property, $context));
        }
    }

    /**
     * @param array      $has
     * @param Property[] $mustHave
     * @param string     $context
     *
     * @throws Exception
     */
    public static function hasAllRequiredProperties(array $has, array $mustHave, string $context)
    {
        //@TODO this check does not take in account a required true/false of a property
        foreach ($mustHave as $property) {
            if (!$property instanceof Property) {
                throw new Exception(sprintf('Expected object of type %s, %s given', Property::class, (is_object($property) ? get_class($property) : gettype($property))));
            }

            if (!in_array($property->getName(), $has)) {
                throw new Exception(sprintf('Property %s is required in %s', $property->getName(), $context));
            }
        }
    }

    /**
     * @param mixed      $responseData
     * @param Property[] $allowedProperties
     * @param string     $context
     */
    public static function hasValidProperties($responseData, array $allowedProperties, string $context)
    {
        $propertiesToCheck = get_object_vars($responseData);

        self::hasAllRequiredProperties(
            array_keys($propertiesToCheck),
            $allowedProperties,
            $context
        );

        $propertyNames = array_map(function (Property $property) {
            return $property->getName();
        }, $allowedProperties);

        $properties = array_combine($propertyNames, $allowedProperties);

        foreach ($propertiesToCheck as $propertyName => $value) {
            self::mayContainProperty(
                $propertyName,
                $allowedProperties,
                $context
            );

            /** @var Property $property */
            $property = $properties[$propertyName];

            $property->checkWith($value);
        }
    }
}
