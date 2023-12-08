<?php

namespace App\Http\Requests;

use App\Models\Article;
use App\Models\Role;
use App\Services\ConfigService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ChangeStatusWebsiteRequest extends FormRequest
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
            'websites' => ['required', 'array', 'min:1'],
            'websites.*' => ['numeric', 'min:1', Rule::exists('websites', 'uuid')->where(function ($query) {
                return $query->where([
                    ['publish_status', '<>', $this->request->get('publish_status')]
                ]);
            })],
            'publish_status' => ['required', 'numeric',
                Rule::in(
                    Article::PUBLISHED_PUBLISH_STATUS,
                    Article::REJECT_PUBLISH_STATUS,
                    Article::BLOCKED_PUBLISH_STATUS,
                    Article::PENDING_PUBLISH_STATUS,
                    Article::DRAFT_PUBLISH_STATUS
                )
            ],
        ];

        if ($this->request->get('publish_status') == Article::REJECT_PUBLISH_STATUS) {
            $validate['reject_reason'] = ['required', 'string'];
        }
        if ((new ConfigService())->checkUserRoles([Role::ROLE_EDITOR])) {
            $validate['publish_status'] = ['required', 'numeric',
                Rule::in(
                    Article::PENDING_PUBLISH_STATUS,
                    Article::DRAFT_PUBLISH_STATUS
                )
            ];
        }

        return $validate;
    }
}
