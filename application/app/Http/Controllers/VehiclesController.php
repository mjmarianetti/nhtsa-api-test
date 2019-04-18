<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Modules\Nhtsa\Http\NhtsaApi;

class VehiclesController extends Controller
{

    const VALIDATION_RULES = [];

    /**
     * __construct function
     *
     * @param NhtsaApi $NhtsaApi
     */
    public function __construct(NhtsaApi $NhtsaApi)
    {
        $this->Nhtsa = $NhtsaApi;
    }

    /**
     * get function
     *
     * @param Request $request
     * @param [type] $modelYear
     * @param [type] $manufacturer
     * @param [type] $model
     * @return void
     */
    public function get(Request $request, $modelYear, $manufacturer, $model)
    {
        $request['modelYear'] = $modelYear;
        $request['manufacturer'] = $manufacturer;
        $request['model'] = $model;

        //assign default value if not present
        $request['withRating'] = filter_var($request->get('withRating'), FILTER_VALIDATE_BOOLEAN);

        $this->validate($request, self::VALIDATION_RULES);

        $result = $this->_getVehicles($request->only(['modelYear','manufacturer','model','withRating']));
        return response()->json($result);
    }

    /**
     * store function
     *
     * @param Request $request
     * @return void
     */
    public function store(Request $request)
    {
        $request['withRating'] = filter_var($request->get('withRating'), FILTER_VALIDATE_BOOLEAN);

        $this->validate($request, self::VALIDATION_RULES);

        $result = $this->_getVehicles($request->only(['modelYear','manufacturer','model','withRating']));
        return response()->json($result);
    }

    /**
     * _getVehicles function
     *
     * it is a helper function to perform requests to the Nhtsa api
     *
     * @param array $input
     * @return array
     */
    public function _getVehicles(array $input) : array {
        return $this->Nhtsa->getVehicles($input['modelYear'], $input['manufacturer'], $input['model'], $input['withRating']);
    }
}
