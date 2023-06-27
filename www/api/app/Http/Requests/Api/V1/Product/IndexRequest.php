<?php

namespace App\Http\Requests\Api\V1\Product;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @property int $per_page
 * @property int $page
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
            'per_page' => ['sometimes', 'nullable', 'int', 'min:1', 'max:1000'],
            'page' => ['sometimes', 'nullable', 'int', 'min:1'],
        ];
    }
}
