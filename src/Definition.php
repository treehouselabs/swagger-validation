<?php

declare(strict_types=1);

namespace TreeHouse\SwaggerValidation;

class Definition
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var Property[]
     */
    protected $properties;

    /**
     * @param string $name
     * @param array  $properties
     */
    public function __construct(string $name, array $properties)
    {
        $this->name = $name;
        $this->properties = $properties;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param $data
     *
     * @return bool
     */
    public function checkWith($data): bool
    {
        Validator\Object::hasValidProperties($data, $this->properties, 'Definition ' . $this->getName());

        return true;
    }
}
