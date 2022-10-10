<?php
declare(strict_types=1);

namespace App\Tests\Unit\Service;

use App\Service\Response\Response;
use PHPUnit\Framework\TestCase;

final class ResponseTest extends TestCase {

    /**
     * @dataProvider responseTestProvider
     */
    public function testResponse(mixed $data, mixed $errors, string $expectedJson): void
    {
        $this->assertIsObject(new Response($data, $errors));
        $this->assertNotEmpty(new Response($data, $errors));
        
        $this->assertSame(json_encode(new Response($data, $errors)), $expectedJson);
    }

    protected function responseTestProvider(): array
    {
        $testObject = new \stdClass();
        $testObject->test = true;
        
        return [
            'simple numeric test' => [
                'data' => '1',
                'errors' => [],
                'expectedJson' => '{"success":true,"data":"1","errors":[]}',
            ],
            'simple alphanumeric test' => [
                'data' => 'a',
                'errors' => [],
                'expectedJson' => '{"success":true,"data":"a","errors":[]}',
            ],
            'empty array test' => [
                'data' => [],
                'errors' => [],
                'expectedJson' => '{"success":true,"data":[],"errors":[]}',
            ],
            'numeric array test' => [
                'data' => [1],
                'errors' => [],
                'expectedJson' => '{"success":true,"data":[1],"errors":[]}',
            ],
            'alphanumeric array test' => [
                'data' => ['a'],
                'errors' => [],
                'expectedJson' => '{"success":true,"data":["a"],"errors":[]}',
            ],
            'associative array test' => [
                'data' => [
                    'test' => true,
                ],
                'errors' => [],
                'expectedJson' => '{"success":true,"data":{"test":true},"errors":[]}',
            ],
            'associative array test with two keys' => [
                'data' => [
                    'is' => true,
                    'test' => false,
                ],
                'errors' => [],
                'expectedJson' => '{"success":true,"data":{"is":true,"test":false},"errors":[]}',
            ],
            'empty object test' => [
                'data' => new \stdClass(),
                'errors' => [],
                'expectedJson' => '{"success":true,"data":{},"errors":[]}',
            ],
            'object with fields' => [
                'data' => $testObject,
                'errors' => [],
                'expectedJson' => '{"success":true,"data":{"test":true},"errors":[]}',
            ],
            'has error' => [
                'data' => [],
                'errors' => ['error'],
                'expectedJson' => '{"success":false,"data":[],"errors":["error"]}',
            ],
            'has errors' => [
                'data' => [],
                'errors' => [
                    'error',
                    'error2'
                ],
                'expectedJson' => '{"success":false,"data":[],"errors":["error","error2"]}',
            ],
            'has field errors' => [
                'data' => [],
                'errors' => [
                    'field1' => 'error1',
                    'error2' => 'error2'
                ],
                'expectedJson' => '{"success":false,"data":[],"errors":{"field1":"error1","error2":"error2"}}',
            ],
        ];
    }
}
