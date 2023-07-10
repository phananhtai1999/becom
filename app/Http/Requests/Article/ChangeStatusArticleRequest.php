<?php

namespace App\Http\Requests\Article;

use App\Abstracts\AbstractRequest;
use App\Models\Article;
use Illuminate\Validation\Rule;

class ChangeStatusArticleRequest extends AbstractRequest
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
            'articles' => ['required', 'array', 'min:1'],
            'articles.*' => ['numeric', 'min:1', Rule::exists('articles', 'uuid')->where(function ($query) {
                return $query->where([
                    ['publish_status', '<>', $this->request->get('publish_status')],
                    ['publish_status', '<>', Article::DRAFT_PUBLISH_STATUS]
                ])->whereNull('deleted_at');
            })],
            'publish_status' => ['required', 'numeric', Rule::in(Article::PUBLISHED_PUBLISH_STATUS, Article::REJECT_PUBLISH_STATUS, Article::BLOCKED_PUBLISH_STATUS, Article::PENDING_PUBLISH_STATUS, Article::DRAFT_PUBLISH_STATUS)],
            'content_for_user' => ['nullable', 'string', 'in:public,login,payment,editor,admin'],
        ];

        if ($this->request->get('publish_status') == Article::REJECT_PUBLISH_STATUS) {
            $validate['reject_reason'] = ['required', 'string'];
        }

        return $validate;
    }
}
