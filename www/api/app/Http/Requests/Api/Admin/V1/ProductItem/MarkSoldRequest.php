<?php

namespace App\Http\Requests\Api\Admin\V1\ProductItem;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @property float|null $price_sell
 */
class MarkSoldRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'price_sell' => ['sometimes', 'nullable', 'decimal:0,2', 'min:0'],
        ];
    }
}
