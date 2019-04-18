<?php

namespace App\Modules\Nhtsa\Http\Responses;

use App\Modules\Nhtsa\Models\Vehicle;
use App\Modules\Nhtsa\Http\Responses\Transform;

class NhtsaResponseTransform implements Transform
{
    /**
     * Undocumented function
     *
     * @return array with a default response
     */
    public function _getDefaultResponse() : array
    {
        return [
            'Count' => 0,
            'Results' => [],
        ];
    }

    /**
     * Undocumented function
     *
     * @param array $data to transform
     * @return array with the transformes data
     */
    public function transform(array $data): array
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
