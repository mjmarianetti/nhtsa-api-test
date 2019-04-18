<?php
namespace App\Modules\Nhtsa\Http\Responses;

interface Transform {
    public function transform(array $data) :array;
}
