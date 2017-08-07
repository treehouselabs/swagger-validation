<?php

declare(strict_types=1);

namespace TreeHouse\SwaggerValidation;

use Exception;

class SwaggerFile
{
    /**
     * @var array
     */
    protected $data;

    /**
     * @var DefinitionsStore
     */
    protected $definitionsStore;

    /**
     * @param array            $data
     * @param DefinitionsStore $definitionsStore
     */
    public function __construct(array $data, DefinitionsStore $definitionsStore)
    {
        $this->data = $data;
        $this->definitionsStore = $definitionsStore;

        if (isset($data['definitions'])) {
            foreach ($data['definitions'] as $name => $definitionData) {
                $properties = $this->createPropertiesFromSpecification($definitionData['properties']);

                $this->definitionsStore->addDefinition(new Definition($name, $properties, $definitionsStore));
            }
        }
    }

    /**
     * @param string $path
     * @param string $httpMethod
     * @param int    $responseCode
     *
     * @throws Exception
     *
     * @return ResponseSchema
     */
    public function getResponseSchema(string $path, string $httpMethod, int $responseCode): ResponseSchema
    {
        $httpMethod = strtolower($httpMethod);
        $responseCode = (string) $responseCode;

        if (!array_key_exists($path, $this->data['paths'])) {
            throw new Exception(sprintf('Path %s not found in specification', $path));
        }

        if (!array_key_exists($httpMethod, $this->data['paths'][$path])) {
            throw new Exception(sprintf('Http method %s not found in specification', $httpMethod));
        }

        if (!array_key_exists($responseCode, $this->data['paths'][$path][$httpMethod]['responses'])) {
            throw new Exception(sprintf('Response code %s not found in specification', $responseCode));
        }

        $schemaData = $this->data['paths'][$path][$httpMethod]['responses'][$responseCode]['schema'];

        return new ResponseSchema(
            $schemaData['type'] ?? null,
            $schemaData['$ref'] ?? null,
            isset($schemaData['properties']) ? $this->createPropertiesFromSpecification($schemaData['properties']) : null,
            $this->definitionsStore
        );
    }

    /**
     * @param array $propertySpecs
     *
     * @return array
     */
    private function createPropertiesFromSpecification(array $propertySpecs): array
    {
        $properties = [];

        foreach ($propertySpecs as $name => $propertyData) {
            $properties[$name] = $this->createPropertyFromPropertySpecification($name, $propertyData);
        }

        return $properties;
    }

    /**
     * @param string $name
     * @param array  $specs
     *
     * @return Property
     */
    private function createPropertyFromPropertySpecification(string $name, array $specs): Property
    {
        $allowedTypes = null;
        $allowedSubTypes = null;
        $definitionReference = null;
        $properties = null;

        if (isset($specs['type'])) {
            $allowedTypes = is_array($specs['type']) ? $specs['type'] : [$specs['type']];
        }

        if (isset($specs['items'])) {
            if (isset($specs['items']['$ref'])) {
                $definitionReference = $specs['items']['$ref'];
            } else {
                $allowedSubTypes = is_array($specs['items']['type']) ? $specs['items']['type'] : [$specs['items']['type']];
            }
        }

        if (isset($specs['$ref'])) {
            $definitionReference = $specs['$ref'];
        }

        if (isset($specs['properties'])) {
            $this->createPropertiesFromSpecification($specs['properties']);
        }

        return new Property(
            $name,
            $allowedTypes,
            $allowedSubTypes,
            $definitionReference,
            $properties,
            $this->definitionsStore
        );
    }
}
