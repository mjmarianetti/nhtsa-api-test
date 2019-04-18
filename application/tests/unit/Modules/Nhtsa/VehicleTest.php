<?php

namespace Tests\Integration;

use Tests\TestCase;
use App\Modules\Nhtsa\Models\Vehicle;
use Exception;

class VehicleTest extends TestCase
{
    public function testConstructor()
    {
        $data = [
            'VehicleDescription' => 'desc',
            'VehicleId' => 1
        ];
        $this->assertInstanceOf(Vehicle::class, new Vehicle($data));
    }

    public function testConstructorWithoutDescription()
    {
        $data = [
            'VehicleId' => 1
        ];

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('a Vehicle needs a description');
        $vehicle = new Vehicle($data);
    }

    public function testConstructorWithoutId()
    {
        $data = [
            'VehicleDescription' => 'desc',
        ];

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('a Vehicle needs an ID');
        $vehicle = new Vehicle($data);
    }

    public function testToArray()
    {
        $data = [
            'VehicleDescription' => 'desc',
            'VehicleId' => 1
        ];

        $expectedResult = [
            'Description' => $data['VehicleDescription'],
            'VehicleId' => $data['VehicleId'],
        ];

        $vehicle = new Vehicle($data);
        $result = $vehicle->toArray();
        $this->assertEquals($result, $expectedResult);
    }




}
