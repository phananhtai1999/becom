<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;
use Illuminate\Validation\Rule;

class UpdateArticleRequest extends AbstractRequest
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
            'image' => ['nullable', 'string'],
            'video' => ['nullable', 'string'],
            'slug' => ['string', "regex:/^[a-z0-9-]+$/", Rule::unique('articles')->ignore($this->id, 'uuid')->whereNull('deleted_at')],
            'title' => ['array', 'min:1'],
            'title.*' => ['string'],
            'content' => ['array', 'min:1'],
            'content.*' => ['string'],
            'publish_status' => ['numeric', 'min:1', 'max:4'],
            'content_for_user' => ['string', 'in:public,login,payment,editor,admin'],
            'article_category_uuid' => ['nullable', 'numeric', Rule::exists('article_categories', 'uuid')->whereNull('deleted_at')],
            'content_type' => ['required', 'string', 'in:single,paragraph'],
            'single_purpose_uuid' => ['nullable', 'required_if:content_type,single', 'numeric', 'min:1', Rule::exists('single_purposes', 'uuid')->whereNull('deleted_at')],
            'paragraph_type_uuid' => ['nullable', 'required_if:content_type,paragraph', 'numeric', 'min:1', Rule::exists('paragraph_types', 'uuid')->whereNull('deleted_at')],
        ];

        if ($this->request->get('content_type') === 'single') {
            $validate['paragraph_type_uuid'] = ['nullable', 'in:NULL'];
        } elseif ($this->request->get('content_type') === 'paragraph') {
            $validate['single_purpose_uuid'] = ['nullable', 'in:NULL'];
        }

        return $validate;
    }
}
