<?php

declare(strict_types=1);

namespace TreeHouse\SwaggerValidation;

use Symfony\Component\Yaml\Yaml;

class SwaggerParser
{
    /**
     * @param $input
     *
     * @return SwaggerFile
     */
    public static function parse($input): SwaggerFile
    {
        $parsedFile = Yaml::parse($input, Yaml::PARSE_KEYS_AS_STRINGS);

        return new SwaggerFile($parsedFile, new DefinitionsStore());
    }
}
