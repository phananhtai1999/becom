<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;
use Illuminate\Validation\Rule;

class NoteRequest extends AbstractRequest
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
            'note' => ['required', 'string'],
            'user_uuid' => ['nullable', 'numeric', Rule::exists('users','uuid')->whereNull('deleted_at')],
            'contact_uuid' => ['required', 'numeric', Rule::exists('contacts','uuid')->where(function ($query) {
                return $query->where('user_uuid', $this->request->get('user_uuid') ?? auth()->user()->getKey());
            })->whereNull('deleted_at')],
        ];
    }
}
