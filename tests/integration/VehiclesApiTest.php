<?php

namespace Tests\Integration;

use Tests\TestCase;

class VehiclesApiTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testAppRoot()
    {
        $this->get('/');

        $this->assertEquals(
            $this->app->version(), $this->response->getContent()
        );
    }

    public function testGetVehicles()
    {
        $this->json('GET', '/vehicles/2015/Audi/A3', [])
            ->seeJson([
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
            ]);
    }

    public function testGetVehiclesWithRating()
    {
        $this->json('GET', '/vehicles/2015/Audi/A3', ['withRating'=> true])
            ->seeJson([
                'Count' => 4,
                'Results' => [
                    [
                        'CrashRating' => '5',
                        'Description' => '2015 Audi A3 4 DR AWD',
                        'VehicleId' => 9403,
                    ],
                    [
                        'CrashRating' => '5',
                        'Description' => '2015 Audi A3 4 DR FWD',
                        'VehicleId' => 9408,
                    ],
                    [
                        'CrashRating' => 'Not Rated',
                        'Description' => '2015 Audi A3 C AWD',
                        'VehicleId' => 9405,
                    ],
                    [
                        'CrashRating' => 'Not Rated',
                        'Description' => '2015 Audi A3 C FWD',
                        'VehicleId' => 9406,
                    ],
                ],
            ]);
    }

    public function testPostVehicles()
    {
        $this->json('POST', '/vehicles', [
            'modelYear' => 2015,
            'manufacturer' => 'Audi',
            'model' => 'A3',
        ])
        ->seeJson([
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
        ]);
    }

    public function testPostVehiclesWithRating()
    {
        $this->json('POST', '/vehicles', [
            'modelYear' => 2015,
            'manufacturer' => 'Audi',
            'model' => 'A3',
            'withRating'=> true
        ])
        ->seeJson([
            'Count' => 4,
            'Results' => [
                [
                    'CrashRating' => '5',
                    'Description' => '2015 Audi A3 4 DR AWD',
                    'VehicleId' => 9403,
                ],
                [
                    'CrashRating' => '5',
                    'Description' => '2015 Audi A3 4 DR FWD',
                    'VehicleId' => 9408,
                ],
                [
                    'CrashRating' => 'Not Rated',
                    'Description' => '2015 Audi A3 C AWD',
                    'VehicleId' => 9405,
                ],
                [
                    'CrashRating' => 'Not Rated',
                    'Description' => '2015 Audi A3 C FWD',
                    'VehicleId' => 9406,
                ],
            ],
        ]);
    }

    public function testGetVehiclesEmptyResult()
    {
        $this->json('GET', '/vehicles/2013/Ford/Crown Victoria', [])
            ->seeJson([
                'Count' => 0,
                'Results' => [
                ],
            ]);
    }

    public function testGetVehiclesToyota()
    {
        $this->json('GET', '/vehicles/2015/Toyota/Yaris', [])
            ->seeJson([
                'Count' => 2,
                'Results' => [
                    [
                        'Description' => '2015 Toyota Yaris 3 HB FWD',
                        'VehicleId' => 9791,
                    ],
                    [
                        'Description' => '2015 Toyota Yaris Liftback 5 HB FWD',
                        'VehicleId' => 9146,
                    ]
                ],
            ]);
    }

    public function testGetVehiclesUndefinedYear()
    {
        $this->json('GET', '/vehicles/undefined/Toyota/Yaris', [])
            ->seeJson([
                'Count' => 0,
                'Results' => [
                ],
            ]);
    }
}
