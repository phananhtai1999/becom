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
        return [
            'image' => ['nullable', 'string'],
            'video' => ['nullable', 'string'],
            'slug' => ['string', "regex:/^[a-z0-9-]+$/", Rule::unique('articles')->ignore($this->id,'uuid')->whereNull('deleted_at')],
            'title' => ['array', 'min:1'],
            'title.*' => ['string'],
            'content' => ['array', 'min:1'],
            'content.*' => ['string'],
            'publish_status' => ['numeric', 'min:1', 'max:3'],
            'content_for_user' => ['string', 'in:public,login,payment,editor,admin'],
            'article_category_uuid' => ['nullable', 'numeric', Rule::exists('article_categories', 'uuid')->whereNull('deleted_at')]
        ];
    }
}
