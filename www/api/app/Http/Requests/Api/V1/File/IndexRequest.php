<?php

namespace App\Http\Requests\Api\V1\File;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @property int $per_page
 */
class IndexRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'per_page' => ['sometimes', 'nullable', 'int', 'min:0', 'max:200']
        ];
    }
}
