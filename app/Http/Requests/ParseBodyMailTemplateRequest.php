<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;
use App\Models\Note;
use App\Models\Remind;
use Illuminate\Validation\Rule;

class ParseBodyMailTemplateRequest extends AbstractRequest
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
            'activity_uuid' => ['required', 'numeric', 'min:1', Rule::exists('activity_histories', 'uuid')->whereNull('deleted_at')],
        ];
    }
}
