<?php

namespace App\Http\Requests\Api\Admin;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @property int|null $per_page
 * @property int|null $page
 * @property string|null $order_by
 * @property bool|null $order_desc
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
            'order_by' => ['sometimes', 'nullable', 'string'],
            'order_desc' => ['sometimes', 'nullable', 'string'],
        ];
    }
}
