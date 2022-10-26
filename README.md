# restapi-demo
Restapi demo is a `Symfony` / `Doctrine` based sample `REST API` and application test builder, written in `PHP 8`.

## Requirements
- `php 8.1`
- `pdo-sqlite`
- `composer`

## Installation
- `git clone https://github.com/tims3l/restapi-demo.git`
- `cd product-demo`
- `cp .env.example .env` (and modify it to your needs)
- `cp .env.test.example .env.test` (for running tests; modify to your needs)
- `composer install`

## Usage

### Sample `REST API` with a `product` entity
Run `PHP` dev server with the following command:
- `php -S 127.0.0.1:80 -t public`


#### Insert product
- `POST` `/product`
    - Header
        - `Content-Type: application/x-www-form-urlencoded`
    - Data
        - `sku: sku-1`
        - `name: one`
        - `description: desc-one`
        - `price: 1000`
- Sample response (HTTP Status code: `201 Created`)
```json
{
	"success": true,
	"data": [
		{
			"id": 1,
			"name": "one",
			"sku": "sku-one",
			"description": "desc-one",
			"price": 1000
		}
	],
	"errors": []
}
```


#### List products
- `GET` `/product`
- Sample response (HTTP Status code: `200 OK`)
```json
{
	"success": true,
	"data": [
		{
			"id": 1,
			"name": "one",
			"sku": "sku-one",
			"description": "desc-one",
			"price": 1000
		},
		{
			"id": 2,
			"name": "two",
			"sku": "sku-two",
			"description": "desc-two",
			"price": 2000
		}
	],
	"errors": []
}
```

#### Show one product
- `GET` `/product/{id}`
- Sample response (HTTP Status code: `200 OK`)
```json
{
	"success": true,
	"data": [
		{
			"id": 1,
			"name": "one",
			"sku": "sku-one",
			"description": "desc-one",
			"price": 1000
		}
	],
	"errors": []
}
```

#### Modify product
- `PUT` `/product/{id}`
    - Header
        - `Content-Type: application/x-www-form-urlencoded`
    - Data
        - `description: modified-desc`
- Sample response (HTTP Status code: `200 OK`)
```json
{
	"success": true,
	"data": [
		{
			"id": 1,
			"name": "one",
			"sku": "sku-one",
			"description": "modified-desc",
			"price": 1000
		}
	],
	"errors": []
}
```

#### Remove product
- `DELETE` `/product/{id}`
    - Header
        - `Content-Type: application/x-www-form-urlencoded`
    - Data
        - `description: modified-desc`
- Response is always empty (HTTP Status code: `204 No Content`)


## Tests

### Unit
These tests ensure that individual units of source code (e.g. a single class) behave as intended.

You can run unit tests with the following command:

`php bin/phpunit tests/Unit`

### Application
Application tests test the behavior of a complete application. They make HTTP requests (both real and simulated ones) and test that the response is as expected.

Application tests run on separate test database (see `.env.test.example`).

You can run application tests with the following command:

`php bin/phpunit tests/Application`

**Note: HTTP server must be running to process application tests.**


## Fine-tuning application

### Rest API builder
You can quickly create `HTTP` endpoints, based on `REST` principles. The `RestApi` class is responsible for the standard `CRUD` operation through `HTTP` endpoints.
- Create a Doctrine entity in `App\Entity` namespace. You can use the `App\Entity\Product` class as an example. It is important to use the `#[Api]` attribute on your entity.
- Create a Doctrine repository in `App\Repository` namespace. You can use the `App\Entity\ProductRepository` class as an example.
- And that's it, you can use the standard `CRUD` endpoints (`POST`, `PUT`, `GET`, `DELETE`) on your new entity. The underlying logic makes sure that all of your endpoints will respond with the same `JSON` format, and can be called in the same way. 

### Testing your customized endpoints
You can quickly test your new endpoints using the `AbstractApiTest` class.
- Simply extend a new final class from the `AbstractApiTest` class. You can use the `ProductApiTest` class as an example.
- Customize the `testPostProvider()`, and `assertExpectedValues()` methods based on your needs.
- Simply run the new test with the following command:
    - `php bin/phpunit tests/Application/RestApi/ExampleApiTest.php`

## Todos
- Create separate `RestApi` and `Response` libraries.
- Create swagger configs.
