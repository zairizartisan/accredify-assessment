<?php

namespace App\Traits;

use App\Rules\IdentityProofValidation;
use Illuminate\Http\Exceptions\HttpResponseException;

trait DataMappingHashTrait
{

    function hashedData(array $data): string
    {

        $mappedData = $this->mappingAndHash($data);

        $hashedResult = hash('sha256', json_encode($mappedData));

        return $hashedResult;
    }

    function mappingAndHash(array $array, $prefix = ''): array
    {
        $result = array();

        foreach ($array as $key => $value) {
            $dotNotationKey = $prefix === '' ? $key : $prefix . '.' . $key;

            if (is_array($value)) {
                //if value is array, use recursive method pass the current value and current dotnotationkey
                $result = array_merge($result, $this->mappingAndHash($value, $dotNotationKey));
            } else {

                //json_encode if value is not array
                $jsonString = json_encode([$dotNotationKey => $value]);

                //hash row value
                $hashedValue = hash('sha256', $jsonString);

                //set hashedvalue to result array
                $result[] = $hashedValue;
            }
        }

        sort($result); //sort value

        return $result;
    }
}
