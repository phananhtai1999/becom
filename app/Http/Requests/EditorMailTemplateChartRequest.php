<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EditorMailTemplateChartRequest extends AbstractRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return True;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'start_date' => ['nullable', 'before_or_equal:end_date'],
            'end_date' => ['nullable', 'after_or_equal:start_date'],
            'group_by' => ['nullable', 'in:hour,date,month'],
            'type' => ['nullable', 'in:sms,email,telegram,viber']
        ];
    }
}
