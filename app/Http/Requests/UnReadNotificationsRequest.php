<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;
use Illuminate\Validation\Rule;

class UnReadNotificationsRequest extends AbstractRequest
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
            'notifications' => ['required', 'array'],
            'notifications.*' => ['required', 'numeric', Rule::exists('notifications', 'uuid')->where(function ($query) {
                return $query->where('read', 1)->where('user_uuid', auth()->user()->getKey())->whereNull('deleted_at');
            })],
        ];
    }
}
