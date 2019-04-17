<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Modules\Nhtsa\NhtsaApi;

class VehiclesController extends Controller
{

    const VALIDATION_RULES = [
        /*'modelYear' => 'required',
        'manufacturer' => 'required',
        'model' => 'required',
        'withRating' => 'boolean'*/
    ];

    public function __construct(NhtsaApi $NhtsaApi)
    {
        $this->Nhtsa = $NhtsaApi;
    }

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

    public function store(Request $request)
    {
        $request['withRating'] = filter_var($request->get('withRating'), FILTER_VALIDATE_BOOLEAN);

        $this->validate($request, self::VALIDATION_RULES);

        $result = $this->_getVehicles($request->only(['modelYear','manufacturer','model','withRating']));
        return response()->json($result);
    }

    public function _getVehicles($input) {
        return $this->Nhtsa->getVehicles($input['modelYear'], $input['manufacturer'], $input['model'], $input['withRating']);
    }
}
