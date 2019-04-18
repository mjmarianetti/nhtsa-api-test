<?php

namespace Tests\Integration;

use App\Modules\Nhtsa\Http\Responses\NhtsaResponseTransform;
use Tests\TestCase;

class NhtsaResponseTransformTest extends TestCase
{
    public function testConstructor()
    {
        $this->assertInstanceOf(NhtsaResponseTransform::class, new NhtsaResponseTransform());
    }

    public function testTransformData()
    {
        $data = [
            'Count' => '4',
            'Message' => 'Results returned successfully',
            'Results' => [
                [
                    'VehicleDescription' => '2015 Audi A3 4 DR AWD',
                    'VehicleId' => 9403,
                ],
                [
                    'VehicleDescription' => '2015 Audi A3 4 DR FWD',
                    'VehicleId' => 9408,
                ],
                [
                    'VehicleDescription' => '2015 Audi A3 C AWD',
                    'VehicleId' => 9405,
                ],
                [
                    'VehicleDescription' => '2015 Audi A3 C FWD',
                    'VehicleId' => 9406,
                ],
            ],
        ];

        $expectedData = [
            'Count' => 4,
            'Results' => [
                [
                    'Description' => '2015 Audi A3 4 DR AWD',
                    'VehicleId' => 9403,
                ],
                [
                    'Description' => '2015 Audi A3 4 DR FWD',
                    'VehicleId' => 9408,
                ],
                [
                    'Description' => '2015 Audi A3 C AWD',
                    'VehicleId' => 9405,
                ],
                [
                    'Description' => '2015 Audi A3 C FWD',
                    'VehicleId' => 9406,
                ],
            ],
        ];

        $transform = new NhtsaResponseTransform();

        $this->assertEquals($transform->transform($data), $expectedData);
    }

    public function testTransformDataEmpty()
    {
        $data = [
            'Count' => 0,
            'Results' => [],
        ];

        $expectedData = [
            'Count' => 0,
            'Results' => [],
        ];

        $transform = new NhtsaResponseTransform();

        $this->assertEquals($transform->transform($data), $expectedData);
    }

}
