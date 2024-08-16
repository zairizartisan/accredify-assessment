<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\FileValidationRequest;

use App\Models\Verification;

use App\Traits\DataValidationTrait;
use App\Traits\ResponseTrait;
use App\Traits\DataMappingHashTrait;

class VerificationController extends Controller
{
    use DataValidationTrait, DataMappingHashTrait, ResponseTrait;

    /**
     * Store a newly created resource in storage.
     */
    public function store(FileValidationRequest $fileRequest): JsonResponse
    {


        $fileRequest->validated(); //validate file upload

        $fileType = $fileRequest->file('file')->extension(); //Get file type
        $fileContent = $fileRequest->file('file')->get();
        $hashedData = array();

        switch ($fileType) {
            case 'json':

                $data = json_decode($fileContent, true);

                break;

            default:
                return $this->responseResult(['result' => 'invalid'], 200);
                break;
        }

        $this->validateData($data); //validate received data

        $hashedData = $this->hashedData($data['data']); //Map and hash data using MappingAndHash method in DataMappingHashTrait

        $this->validateSignature($hashedData, $data);

        //store hashed data into 'verification' table
        Verification::create(
            [
                'user_id'      => Auth::user()->id,
                'file_type'    => $fileType,
                'result'       => json_encode($hashedData)
            ]
        );

        $result = [
            'issuer' => $data['data']['issuer']['name'],
            'result' => 'verified' //set as verified
        ];

        //return result
        return $this->responseResult($result, 200);
    }
}
