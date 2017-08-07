<?php

declare(strict_types=1);

namespace TreeHouse\SwaggerValidation;

class TypeTransformer
{
    /**
     * @param string $type
     *
     * @return string
     */
    public static function transform(string $type): string
    {
        switch ($type) {
            case 'double':
                return 'number';
            default:
                return strtolower($type);
        }
    }

    /**
     * @param mixed $item
     *
     * @return string
     */
    public static function getTransformedTypeFor($item): string
    {
        return self::transform(gettype($item));
    }
}
