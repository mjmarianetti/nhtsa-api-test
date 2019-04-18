<?php

namespace App\Modules\Nhtsa\Http;

use App\Modules\Nhtsa\Helpers\UrlHelper;
use App\Modules\Nhtsa\Http\Responses\Transform;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Promise;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;

class NhtsaApi
{
    private $client;
    private $responseTransform;
    private $urlHelper;

    const API_URLS = [
        'Vehicles' => 'modelyear/{modelYear}/make/{manufacturer}/model/{model}',
        'VehicleDetails' => 'VehicleId/{vehicleId}',
    ];
    const RESPONSE_FORMAT = 'json';

    /**
     * Constructor
     *
     * @param GuzzleHttp\Client $client guzzle client for making http requests
     * @param App\Modules\Nhtsa\Transform $transform transformation class
     * @param App\Modules\Nhtsa\Helpers\UrlHelper $urlHelper url helper to parse path params
     */
    public function __construct(Client $client, Transform $transform,UrlHelper $urlHelper)
    {
        $this->client = $client;
        $this->responseTransform = $transform;
        $this->urlHelper = $urlHelper;
    }

    /**
     * _getDefaultOptions
     *
     * @return array with default options
     */
    public function _getDefaultOptions() : array
    {
        return [
            'query' => [
                'format' => self::RESPONSE_FORMAT,
            ],
        ];
    }

    /**
     * _getVehicleId function
     *
     * @param array $data array containing API data
     * @param integer $count index to search
     * @return integer vehicleId
     */
    public function _getVehicleId(array $data, int $count): int
    {
        return $data['Results'][$count]['VehicleId'];
    }

    /**
     * getVehicles function
     *
     * @param string $modelYear
     * @param string $manufacturer
     * @param string $model
     * @param boolean $withRating
     * @return array with the response already transformed to the format needed
     */
    public function getVehicles(string $modelYear, string $manufacturer, string $model, bool $withRating): array
    {

        $url = $this->urlHelper->replaceUrlPathParameters(self::API_URLS['Vehicles'], [
            'modelYear' => $modelYear,
            'manufacturer' => $manufacturer,
            'model' => $model,
        ]);

        $options = $this->_getDefaultOptions();

        try {
            $response = $this->client->get($url, $options);
            $parsedResponse = json_decode($response->getBody(), true);

            if ($withRating) {
                $parsedResponse = $this->_addVehiclesRating($parsedResponse);
            }
        } catch (\Throwable $th) {
            //in order to not fail we return an empty response
            //TODO: report this error either with an event, an email, etc.
            $parsedResponse = [
                'Count' => 0,
                'Results' => [],
            ];
        }

        return $this->responseTransform->transform($parsedResponse);
    }

    /**
     * Undocumented function
     *
     * @param array $parsedResponse response from getVehicles
     * @return array with the response plus vehicle details
     */
    public function _addVehiclesRating(array $parsedResponse): array
    {
        $ids = [];

        for ($i = 0; $i < $parsedResponse['Count']; $i++) {
            $ids[] = $this->_getVehicleId($parsedResponse, $i);
        }

        //Asynchronous call the api to get the details of each vehicle
        $results = $this->_getVehicleDetailsAsync($ids);

        for ($i = 0; $i < $parsedResponse['Count']; $i++) {
            $vehicleId = $this->_getVehicleId($parsedResponse, $i);

            //We need to parse the result (it is a fullfiled promise)
            $res = (string) $results[$vehicleId]['value']->getBody();
            //Parse the body received as a string into an Object
            $res = json_decode($res);
            $parsedResponse['Results'][$i]['VehicleDetails'] = $res->Results[0];
        }

        return $parsedResponse;
    }

    /**
     * _getVehicleDetailsAsync function
     *
     * loops and gets the details of every vehicle ID received
     *
     * @param [type] $ids
     * @return array of promises containing the response from the HTTP endpoint
     */
    public function _getVehicleDetailsAsync($ids): array
    {
        $promises = [];

        //Asynchronous call the api to get the details of each vehicle
        for ($i = 0; $i < count($ids); $i++) {
            $vehicleId = $ids[$i];
            $promises[$vehicleId] = $this->getVehicleAsync($vehicleId);
        }

        return Promise\settle($promises)->wait();
    }

    /**
     * getVehicleAsync function
     *
     * performs an async http request to get the vehicle details
     *
     * @param integer $vehicleId
     * @return Response
     */
    public function getVehicleAsync(int $vehicleId): Response
    {

        $url = $this->urlHelper->replaceUrlPathParameters(self::API_URLS['VehicleDetails'], [
            'vehicleId' => $vehicleId,
        ]);

        $options = $this->_getDefaultOptions();

        $promise = $this->client->getAsync($url, $options);

        $promise->then(
            function (ResponseInterface $res) {
                return $res;
            },
            function (RequestException $e) {
                throw $e;
            }
        );

        return $promise->wait();
    }
}
