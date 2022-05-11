<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;

class LoadAnalyticDataRequest extends AbstractRequest
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
     * @return array
     */
    public function rules()
    {
        return [
            'from_date' => ['date_format:Y-m-d', 'before_or_equal:to_date'],
            'to_date' => ['date_format:Y-m-d', 'after_or_equal:from_date'],
        ];
    }
}
