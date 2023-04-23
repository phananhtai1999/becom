<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;

class UploadVideoRequest extends AbstractRequest
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
            'type' => ['required', 'string'],
            'video' => ['required', 'mimes:mp4'],
        ];
    }
}
