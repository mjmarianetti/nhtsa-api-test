<?php

namespace App\Modules\Nhtsa\Helpers;

class UrlHelper
{

    public static function replaceUrlPathParameters(string $url, array $data) : string
    {
        foreach ($data as $key => $value) {
            $url = str_replace('{' . $key . '}', $value, $url);
        }

        return $url;
    }
}
