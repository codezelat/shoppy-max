<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class SriLankaMobile implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Sri Lankan mobile number strictly: 07XXXXXXXX (10 digits)
        if (! preg_match('/^07\d{8}$/', $value)) {
            $fail('The :attribute must be a valid Sri Lankan mobile number (e.g., 0712345678).');
        }
    }
}
