<?php

namespace App\Http\Requests\Api\Admin\V1\Dashboard;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @property Carbon|null $from
 * @property Carbon|null $to
 */
class GetMainDashboardRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'from' => ['sometimes', 'nullable', 'int'],
            'to' => ['sometimes', 'nullable', 'int', 'gte:from'],
        ];
    }
}
