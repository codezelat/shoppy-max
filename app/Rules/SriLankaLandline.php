<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class SriLankaLandline implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Sri Lankan landline strictly: 0XXXXXXXXX (10 digits, usually starts with 011, 038 etc but we check 0 followed by 9 digits)
        if (!preg_match('/^0\d{9}$/', $value)) {
            $fail('The :attribute must be a valid Sri Lankan landline number (e.g., 0112345678).');
        }
    }
}
