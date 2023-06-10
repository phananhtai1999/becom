<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAssetRequest extends FormRequest
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
            'title' => ['string'],
            'asset_size_uuid' => ['integer', 'exists:asset_sizes,uuid'],
            'type' => [Rule::in(['image', 'video']), 'required_if:file,*'],
        ];
        if ($this->request->get('type')) {
            if($this->request->get('type') == 'video') {
                $validate['file'] = ['required', 'mimes:mp4'];
            } else {
                $validate['file'] = ['required', 'mimes:jpg,png,gif', 'max:153600'];
            }
        }
        return $validate;
    }
}
