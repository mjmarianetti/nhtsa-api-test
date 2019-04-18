<?php

namespace App\Modules\Nhtsa\Helpers;

class UrlHelper
{

    /**
     * replaceUrlPathParameters function
     *
     * Replaces strings between {} with the values provided in $data in the given $url string
     * @param string $url
     * @param array $data
     * @return string
     */
    public function replaceUrlPathParameters(string $url, array $data) : string
    {
        foreach ($data as $key => $value) {
            $url = str_replace('{' . $key . '}', $value, $url);
        }

        return $url;
    }
}
