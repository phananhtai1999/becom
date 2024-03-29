<?php

namespace App\Http\Requests\Article;

use App\Abstracts\AbstractRequest;
use App\Models\Article;
use App\Rules\ArticleContentRule;
use App\Rules\CustomDescriptionRule;
use App\Rules\CustomKeywordRule;
use Illuminate\Validation\Rule;

class UpdateUnpublishedArticleRequest extends AbstractRequest
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
            'title.en' => ['string'],
            'title.*' => ['string'],
            'content' => ['array', 'min:1'],
            'content.*' => ['string'],
            'publish_status' => ['numeric', Rule::in(Article::PENDING_PUBLISH_STATUS, Article::DRAFT_PUBLISH_STATUS)],
            'content_for_user' => ['nullable', 'string', 'in:public,login,payment,editor,admin'],
            'article_category_uuid' => ['nullable', 'numeric', Rule::exists('article_categories', 'uuid')->whereNull('deleted_at')],
            'content_type' => ['required', 'string', 'in:single,paragraph'],
            'single_purpose_uuid' => ['nullable', 'required_if:content_type,single', 'numeric', 'min:1', Rule::exists('single_purposes', 'uuid')->whereNull('deleted_at')],
            'paragraph_type_uuid' => ['nullable', 'required_if:content_type,paragraph', 'numeric', 'min:1', Rule::exists('paragraph_types', 'uuid')->whereNull('deleted_at')],
            'keyword' => ['nullable', 'array', new CustomKeywordRule($this->id, $this->request->get('keyword'), 'articles')],
            'keyword.*' => ['nullable', 'string', 'not_in:0'],
            'description' => ['nullable', 'array', new CustomDescriptionRule($this->id, $this->request->get('keyword'), $this->request->get('description'), 'articles')],
            'description.*' => ['nullable', 'string', 'not_in:0'],
        ];

        if ($this->request->get('content_type') === 'single') {
            $validate['paragraph_type_uuid'] = ['nullable', 'in:NULL'];
        } elseif ($this->request->get('content_type') === 'paragraph') {
            $validate['single_purpose_uuid'] = ['nullable', 'in:NULL'];
            $validate['content'] = ['array', 'min:1', new ArticleContentRule($this->request->get('paragraph_type_uuid'))];
        }

        return $validate;
    }
}
