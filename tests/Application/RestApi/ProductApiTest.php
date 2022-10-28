<?php
declare(strict_types=1);

namespace App\Tests\Application\RestApi;

use App\Entity\Product;
use Symfony\Component\HttpFoundation\Response;
use Tims3l\RestApi\Tests\Application\RestApi\AbstractApiTest;

final class ProductApiTest extends AbstractApiTest
{
    protected const ENTITY_CLASS = Product::class;

    protected function testPostProvider(): array
    {
        return [
            'correct' => [
                'uri' => '/product',
                'data' => [
                    'sku' => 'test-1-sku',
                    'name' => 'test-1-new',
                    'description' => 'test-1-description',
                    'price' => 10001.0,
                ],
                'expectedSuccessfulness' => true,
                'expectedStatusCode' => Response::HTTP_CREATED,
                'expectedResponse' => __DIR__ . '/../../fixtures/RestApi/expected-post-correct.json.schema',
                'dbRecordCount' => 1,
            ],
            'sku_exists' => [
                'uri' => '/product',
                'data' => [
                    'sku' => 'test-1-sku',
                    'name' => 'test-2-new',
                    'description' => 'test-2-description',
                    'price' => 20001.0,
                ],
                'expectedSuccessfulness' => false,
                'expectedStatusCode' => Response::HTTP_BAD_REQUEST,
                'expectedResponse' => __DIR__ . '/../../fixtures/RestApi/expected-post-sku-exists.json.schema',
                'dbRecordCount' => 1,
            ],
            'name_is_empty' => [
                'uri' => '/product',
                'data' => [
                    'sku' => 'test-3-sku',
                    'name' => '',
                    'description' => 'test-3-description',
                    'price' => 30001.0,
                ],
                'expectedSuccessfulness' => false,
                'expectedStatusCode' => Response::HTTP_BAD_REQUEST,
                'expectedResponse' => __DIR__ . '/../../fixtures/RestApi/expected-post-name-is-empty.json.schema',
                'dbRecordCount' => 1,
            ],
            'more_validation_errors' => [
                'uri' => '/product',
                'data' => [
                    'sku' => 'test-1-sku',
                    'name' => '',
                    'description' => 'test-4-description',
                    'price' => 40001.0,
                ],
                'expectedSuccessfulness' => false,
                'expectedStatusCode' => Response::HTTP_BAD_REQUEST,
                'expectedResponse' => __DIR__ . '/../../fixtures/RestApi/expected-post-more-validation-errors.json.schema',
                'dbRecordCount' => 1,
            ],
        ];
    }

    protected function assertExpectedValues(array $data)
    {
        /** @var Product $product */
        $product = $this->getLastInsertEntity();
        
        $this->assertSame($product->getSku(), $data['sku']);
        $this->assertSame($product->getName(), $data['name']);
        $this->assertSame($product->getDescription(), $data['description']);
        $this->assertSame($product->getPrice(), $data['price']);
    }
    
}
