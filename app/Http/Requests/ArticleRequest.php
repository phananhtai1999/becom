<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;
use App\Rules\ArticleContentRule;
use Illuminate\Validation\Rule;

class ArticleRequest extends AbstractRequest
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
            'slug' => ['required', 'string', "regex:/^[a-z0-9-]+$/", Rule::unique('articles')->whereNull('deleted_at')],
            'title' => ['required', 'array', 'min:1'],
            'title.en' => ['required', 'string'],
            'title.*' => ['required', 'string'],
            'content' => ['required', 'array', 'min:1'],
            'content.en' => ['required', 'string'],
            'content.*' => ['required', 'string'],
            'publish_status' => ['required', 'numeric', 'min:1', 'max:5'],
            'content_for_user' => ['nullable', 'string', 'in:public,login,payment,editor,admin'],
            'article_category_uuid' => ['nullable', 'numeric', 'min:1', Rule::exists('article_categories', 'uuid')->whereNull('deleted_at')],
            'content_type' => ['required', 'string', 'in:single,paragraph'],
            'single_purpose_uuid' => ['nullable', 'required_if:content_type,single', 'numeric', 'min:1', Rule::exists('single_purposes', 'uuid')->whereNull('deleted_at')],
            'paragraph_type_uuid' => ['nullable', 'required_if:content_type,paragraph', 'numeric', 'min:1', Rule::exists('paragraph_types', 'uuid')->whereNull('deleted_at')],
            'article_series_uuid' => ['nullable', 'numeric', 'min:1', Rule::exists('article_series', 'uuid')->where(function ($query) {
                return $query->whereNull('article_uuid')->whereNotNull('parent_uuid')->whereNull('deleted_at');
            })],
        ];

        if ($this->request->get('content_type') === 'single') {
            $validate['paragraph_type_uuid'] = ['nullable', 'in:NULL'];
        } elseif ($this->request->get('content_type') === 'paragraph') {
            $validate['single_purpose_uuid'] = ['nullable', 'in:NULL'];
            $validate['content'] = ['required', 'array', 'min:1', new ArticleContentRule($this->request->get('paragraph_type_uuid'))];
        }

        return $validate;
    }
}
