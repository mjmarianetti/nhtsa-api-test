<?php

namespace App\Modules\Nhtsa;

use Exception;

class Vehicle
{
    private $data;

    public function __construct(array $data)
    {
        $this->data = $this->transform($data);
    }

    public function transform(array $data) : array
    {

        if(!isset($data['VehicleDescription'])) {
            throw new Exception("a Vehicle needs a description");
        }

        if(!isset($data['VehicleId'])) {
            throw new Exception("a Vehicle needs an ID");
        }


        $response = [
            'Description' => $data['VehicleDescription'],
            'VehicleId' => $data['VehicleId'],
        ];

        if (isset($data['VehicleDetails'])) {
            $response['CrashRating'] = $data['VehicleDetails']->OverallRating;
        }

        return $response;
    }

    public function toArray() : array
    {
        return $this->data;
    }

}
