# Swagger validation

A library to validate an api response with a given Swagger definition.

## Installation

```sh
composer require treehouselabs/swagger-validation
```

## Usage

```php
        $response = '{"result": {"name" : "TreeHouse"}, "error" : "null"}';
        $data = json_decode($response);
        
        $method = "GET";
        $code = "200";
        $path = "/companies/{id}"
        
        $file = SwaggerParser::parse(file_get_contents('/path/to/a/swagger/file.yaml'));
        
        $schema = $file->getResponseSchema($path, $method, $code);
        
        Assert::assertTrue($schema->checkWith($data));
```

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
