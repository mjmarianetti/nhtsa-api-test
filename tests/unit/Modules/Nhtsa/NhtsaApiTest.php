<?php

namespace Tests\Integration;

use App\Modules\Nhtsa\NhtsaApi;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Exception\RequestException;

use Tests\TestCase;

class NhtsaApiTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->data = [
            'modelYear' => 2015,
            'manufacturer' => 'Audi',
            'model' => 'A3',
        ];

        $this->apiResponse = [
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

        $this->transformedResponse = [
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

        $this->transformedResponseWithRatings = [
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
        ];

        $this->urlParsed = 'modelyear/2015/make/Audi/model/A3';
    }

    public function testConstructor()
    {

        $client = $this->getMockBuilder('GuzzleHttp\Client')
            ->disableOriginalConstructor()
            ->getMock();
        $transform = $this->getMockBuilder('App\Modules\Nhtsa\NhtsaResponseTransform')
            ->disableOriginalConstructor()
            ->getMock();

        $urlHelper = $this->getMockBuilder('App\Modules\Nhtsa\Helpers\UrlHelper')
            ->disableOriginalConstructor()
            ->getMock();

        $this->assertInstanceOf(NhtsaApi::class, new NhtsaApi($client, $transform, $urlHelper));
    }

    public function testGetVehicles()
    {

        $body = Psr7\stream_for(json_encode($this->apiResponse));
        $mock = new MockHandler([
            new Response(200, ['X-Foo' => 'Bar'], $body),
        ]);

        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);

        $transform = $this->getMockBuilder('App\Modules\Nhtsa\NhtsaResponseTransform')
            ->disableOriginalConstructor()
            ->getMock();

        $urlHelper = $this->getMockBuilder('App\Modules\Nhtsa\Helpers\UrlHelper')
            ->disableOriginalConstructor()
            ->getMock();

        $urlHelper->method('replaceUrlPathParameters')->willReturn($this->urlParsed);
        $transform->method('transform')->willReturn($this->transformedResponse);

        $api = new NhtsaApi($client, $transform, $urlHelper);

        $result = $api->getVehicles($this->data['modelYear'], $this->data['manufacturer'], $this->data['model'], false);

        $this->assertEquals($result, $this->transformedResponse);
    }

    public function testGetVehiclesWithDetails()
    {

        $body = Psr7\stream_for(json_encode($this->apiResponse));
        $mock = new MockHandler([
            new Response(200, ['X-Foo' => 'Bar'], $body),
            new Response(200, ['X-Foo' => 'Bar'], $body),
            new Response(200, ['X-Foo' => 'Bar'], $body),
            new Response(200, ['X-Foo' => 'Bar'], $body),
            new Response(200, ['X-Foo' => 'Bar'], $body),
        ]);

        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);

        $transform = $this->getMockBuilder('App\Modules\Nhtsa\NhtsaResponseTransform')
            ->disableOriginalConstructor()
            ->getMock();

        $urlHelper = $this->getMockBuilder('App\Modules\Nhtsa\Helpers\UrlHelper')
            ->disableOriginalConstructor()
            ->getMock();

        $urlHelper->method('replaceUrlPathParameters')->willReturn($this->urlParsed);
        $transform->method('transform')->willReturn($this->transformedResponseWithRatings);

        $api = new NhtsaApi($client, $transform, $urlHelper);

        $result = $api->getVehicles($this->data['modelYear'], $this->data['manufacturer'], $this->data['model'], true);

        $this->assertEquals($result, $this->transformedResponseWithRatings);
    }

    public function testGetVehiclesError()
    {

        $body = Psr7\stream_for(json_encode($this->apiResponse));
        $mock = new MockHandler([
            new Response(200, ['X-Foo' => 'Bar'], $body),
            new RequestException("Error Communicating with Server", new Request('GET', 'test'))
        ]);

        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);

        $transform = $this->getMockBuilder('App\Modules\Nhtsa\NhtsaResponseTransform')
            ->disableOriginalConstructor()
            ->getMock();

        $urlHelper = $this->getMockBuilder('App\Modules\Nhtsa\Helpers\UrlHelper')
            ->disableOriginalConstructor()
            ->getMock();

        $urlHelper->method('replaceUrlPathParameters')->willReturn($this->urlParsed);
        $transform->method('transform')->willReturn($this->transformedResponse);

        $api = new NhtsaApi($client, $transform, $urlHelper);

        $result = $api->getVehicles($this->data['modelYear'], $this->data['manufacturer'], $this->data['model'], true);

        $this->assertEquals($result, $this->transformedResponse);
    }

}
