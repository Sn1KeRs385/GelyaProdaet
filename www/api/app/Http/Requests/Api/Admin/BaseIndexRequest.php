<?php

namespace App\Http\Requests\Api\Admin;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @property int $per_page
 */
class BaseIndexRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'per_page' => ['sometimes', 'nullable', 'int', 'min:1'],
            'page' => ['sometimes', 'nullable', 'int', 'min:1'],
        ];
    }
}
