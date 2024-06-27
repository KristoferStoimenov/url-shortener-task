<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\UniqueUrl;

class StoreUrlRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'original_url' => ['required', 'url', new UniqueUrl]
        ];
    }

    public function messages()
    {
        return [
            'original_url.required' => 'Please enter a URL to be shortened.',
            'original_url.url' => 'The URL entered is not valid. Please enter a correct URL.'
        ];
    }
    
}
