<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Http;

class IdentityProofValidation implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    protected $location;
    protected $key;
    protected $passes = false;

    public function __construct($location, $key)
    {
        $this->location = $location;
        $this->key = $key;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        //

        $response = Http::get("https://dns.google/resolve", [
            'name' => $this->location,
            'type' => 'TXT',
        ]);

        if ($response->successful()) {
            $records = $response->json('Answer', []); //get Answer key from response


            // Check if any TXT record contains the key
            foreach ($records as $record) {
                if (strpos($record['data'], $this->key) !== false) {
                    $this->passes = true; // set validation as pass
                    return;
                }
            }
        }

        //if fail validation
        $this->passes = false;
    }

    public function didPass(): bool
    {
        return $this->passes;
    }
}
