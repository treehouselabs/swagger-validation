<?php

declare(strict_types=1);

namespace TreeHouse\SwaggerValidation\Validator;

use Exception;
use TreeHouse\SwaggerValidation\TypeTransformer;

class Type
{
    /**
     * @param mixed      $item
     * @param array|null $allowedTypes
     * @param string     $context
     *
     * @throws Exception
     */
    public static function assertOneOf($item, ?array $allowedTypes, string $context)
    {
        $type = TypeTransformer::getTransformedTypeFor($item);

        if ($allowedTypes && !in_array($type, $allowedTypes)) {
            throw new Exception(sprintf('Type %s not allowed for %s', $type, $context));
        }
    }
}
