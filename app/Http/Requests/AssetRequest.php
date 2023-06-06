<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Sabberworm\CSS\Rule\Rule;

class AssetRequest extends FormRequest
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
        $validate = [
            'file' => ['required'],
            'title' => ['required', 'string'],
            'asset_size_uuid' => ['required', 'integer', 'exists:asset_sizes,uuid'],
            'type' => ['required', \Illuminate\Validation\Rule::in(['image', 'video'])],
        ];
        if($this->request->get('type') == 'video') {
            $validate['file'] = array_merge($validate['file'], ['mimes:mp4']);
        } else {
            $validate['file'] = array_merge($validate['file'], ['mimes:jpg,png,gif', 'max:153600']);
        }
        return $validate;
    }
}
