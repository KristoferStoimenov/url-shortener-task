<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Models\ShortenedUrl;
class UniqueUrl implements Rule
{

    private $shortenedUrl;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $url = ShortenedUrl::where('original_url', $value)->first();
        if ($url) {
            $this->shortenedUrl = $url->shortened_url;
            return false;
        }
        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return "This URL has already been shortened to: {$this->shortenedUrl}";
    }
}
