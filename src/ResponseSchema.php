<?php

declare(strict_types=1);

namespace TreeHouse\SwaggerValidation;

use Exception;

class ResponseSchema
{
    /**
     * @var string|null
     */
    protected $type;

    /**
     * @var string|null
     */
    protected $definitionReference;

    /**
     * @var Property[]|null
     */
    protected $properties;

    /**
     * @var DefinitionsStore
     */
    protected $definitionsStore;

    /**
     * @param string|null      $type
     * @param string|null      $definitionReference
     * @param array|null       $properties
     * @param DefinitionsStore $definitionsStore
     */
    public function __construct(
        ?string $type,
        ?string $definitionReference,
        ?array $properties,
        DefinitionsStore $definitionsStore
    ) {
        $this->type = $type;
        $this->definitionReference = $definitionReference;
        $this->properties = $properties;
        $this->definitionsStore = $definitionsStore;
    }

    /**
     * @param mixed $data
     *
     * @throws Exception
     *
     * @return bool
     */
    public function checkWith($data): bool
    {
        if ($data === null) {
            throw new Exception('Response schema cannot be null');
        }

        Validator\Type::assertOneOf($data, $this->type ? [$this->type] : null, 'ResponseSchema');

        if ($this->properties && is_object($data)) {
            Validator\Object::hasValidProperties($data, $this->properties, 'ResponseSchema');
        }

        if ($this->definitionReference !== null && in_array(gettype($data), ['array', 'object'])) {
            $this->definitionsStore->getDefinition($this->definitionReference)->checkWith((object) $data);
        }

        return true;
    }
}
