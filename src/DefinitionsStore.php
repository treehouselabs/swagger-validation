<?php

declare(strict_types=1);

namespace TreeHouse\SwaggerValidation;

use Exception;

class DefinitionsStore
{
    /** @var Definition[] */
    protected $definitions = [];

    /**
     * @param Definition $definition
     */
    public function addDefinition(Definition $definition): void
    {
        $this->definitions[$definition->getName()] = $definition;
    }

    /**
     * @param string $name
     *
     * @throws Exception
     *
     * @return null|Definition
     */
    public function getDefinition(string $name): ?Definition
    {
        $name = str_replace('#/definitions/', '', $name);

        if (!array_key_exists($name, $this->definitions)) {
            throw new Exception(sprintf('Unknown Definition %s', $name));
        }

        return $this->definitions[$name];
    }
}
