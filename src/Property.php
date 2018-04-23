<?php

declare(strict_types=1);

namespace TreeHouse\SwaggerValidation;

class Property
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var array|null
     */
    protected $allowedTypes;

    /**
     * @var array|null
     */
    protected $allowedSubTypes;

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
     * @param string           $name
     * @param array|null       $allowedTypes
     * @param array|null       $allowedSubTypes
     * @param null|string      $definitionReference
     * @param array|null       $properties
     * @param DefinitionsStore $definitionsStore
     */
    public function __construct(
        string $name,
        ?array $allowedTypes,
        ?array $allowedSubTypes,
        ?string $definitionReference,
        ?array $properties,
        DefinitionsStore $definitionsStore
    ) {
        $this->name = $name;
        $this->allowedTypes = $allowedTypes;
        $this->allowedSubTypes = $allowedSubTypes;
        $this->definitionReference = $definitionReference;
        $this->properties = $properties;
        $this->definitionsStore = $definitionsStore;
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
        $type = TypeTransformer::getTransformedTypeFor($data);

        Validator\TypeValidator::assertOneOf(
            $data,
            $this->allowedTypes,
            'Property ' . $this->getName()
        );

        if ($type === 'object'
            && $this->definitionReference
            && ($this->allowedTypes == null || in_array('object', $this->allowedTypes))
        ) {
            $this->definitionsStore->getDefinition($this->definitionReference)->checkWith($data);

            return true;
        }

        if ($type === 'object' && $this->properties && !$this->definitionReference) {
            Validator\ObjectValidator::hasValidProperties($data, $this->properties, 'Property ' . $this->getName());

            return true;
        }

        if ($type === 'array'
            && $this->allowedTypes
            && in_array('array', $this->allowedTypes)
            && $this->definitionReference
        ) {
            if (is_array($data)) {
                foreach ($data as $ref) {
                    $this->definitionsStore->getDefinition($this->definitionReference)->checkWith($ref);
                }

                return true;
            }
        }

        if ($type === 'array'
            && $this->allowedSubTypes
            && $this->allowedTypes && in_array('array', $this->allowedTypes)) {
            foreach ($data as $key => $item) {
                Validator\TypeValidator::assertOneOf(
                    $data,
                    $this->allowedTypes,
                    'in array of Property ' . $this->getName()
                );
            }

            return true;
        }

        return true;
    }
}
