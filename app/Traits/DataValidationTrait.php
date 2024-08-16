<?php

namespace App\Traits;

use App\Rules\IdentityProofValidation;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Validator;

trait DataValidationTrait
{
    //

    public function validateData(array $data)
    {
        $validator = Validator::make($data, [
            'data.recipient.name' => ['required', 'string'],
            'data.recipient.email' => ['required', 'email'],
            'data.issuer.name' => ['required', 'string'],
            'data.issuer.identityProof' => ['required', 'array'],
            'data.issuer.identityProof.type' => ['required', 'string'],
            'data.issuer.identityProof.key' => [
                'required',
                'string',
                //validate identitiyProof key check google DNS
                function ($attribute, $value, $fail) use ($data) {
                    $rule = new IdentityProofValidation(
                        $data['data']['issuer']['identityProof']['location'] ?? null,
                        $value
                    );

                    $rule->validate($attribute, $value, $fail);

                    if (!$rule->didPass()) {
                        $fail('invalid_issuer');
                    }
                }
            ],
            'data.issuer.identityProof.location' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            $this->handleFailResponse($validator, $data);
        }
    }

    public function validateSignature($generatedSignature, $data)
    {
        $validator = Validator::make(
            [],
            [],
            []
        ); // init empty validator

        if ($generatedSignature !== $data['signature']['targetHash']) { // check if signature same
            $validator->errors()->add('signature', 'invalid_signature'); //add custom errors
            $this->handleFailResponse($validator, $data); //handle fail response
        }
    }

    private function handleFailResponse($validator, $data)
    {
        $errors = $validator->errors()->getMessages();

        $customErrors = [];

        foreach ($errors as $key => $messages) {
            foreach ($messages as $message) {
                if (strpos($key, 'issuer.identityProof') !== false && $message === 'invalid_issuer') {
                    $customErrors = [
                        'issuer' => $data['data']['issuer']['name'] ?? '',
                        'result' => 'invalid_issuer',
                    ];
                } elseif (strpos($key, 'recipient.') !== false) {
                    $customErrors = [
                        'issuer' => $data['data']['issuer']['name'] ?? '',
                        'result' => 'invalid_recipient',
                    ];
                } else {
                    $customErrors = [
                        'issuer' => $data['data']['issuer']['name'] ?? '',
                        'message' => $message,
                    ];
                }
            }
        }

        throw new HttpResponseException(response()->json([
            'data' => $customErrors
        ], 200));
    }
}
