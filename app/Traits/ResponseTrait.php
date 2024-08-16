<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;


trait ResponseTrait
{
    public function responseResult($data = [], $code = Response::HTTP_OK): JsonResponse
    {
        $response = [
            'data'      => $data
        ];
        return new JsonResponse($response, $code);
    }
}
