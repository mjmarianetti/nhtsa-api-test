<?php

namespace App\Modules\Nhtsa;

use App\Modules\Nhtsa\Vehicle;

class NhtsaResponseTransform
{
    public function _getDefaultResponse()
    {
        return [
            'Count' => 0,
            'Results' => [],
        ];
    }

    public function transform(array $data)
    {

        $response = $this->_getDefaultResponse();

        if ($data['Count'] > 0) {
            $response['Count'] = $data['Count'];
        }

        for ($i = 0; $i < $data['Count']; $i++) {
            $element = $data['Results'][$i];
            $response['Results'][] = (new Vehicle($element))->toArray();
        }

        return $response;
    }
}
