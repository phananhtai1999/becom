<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;

class UploadMailBoxFileRequest extends AbstractRequest
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
            'type' => ['required', 'string', 'in:mailbox'],
            'file' => ['required', 'file', 'mimes:mp4,jpg,jpeg,png,gif,doc,docx,pdf,zip,rar,mov,ogg']
        ];
    }
}
