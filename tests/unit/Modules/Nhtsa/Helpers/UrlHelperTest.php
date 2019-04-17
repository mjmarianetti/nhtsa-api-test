<?php

namespace Tests\Integration;

use Tests\TestCase;
use App\Modules\Nhtsa\Helpers\UrlHelper;

class UrlHelperTest extends TestCase
{
    public function testreplaceUrlPathParameters()
    {
        $url = 'test/{data1}/{data2}';
        $data = [
            'data1' => 'test1',
            'data2' => 'test2'
        ];
        $expectedResult = 'test/test1/test2';

        $result = UrlHelper::replaceUrlPathParameters($url, $data);

        $this->assertEquals($result, $expectedResult);
    }

}
