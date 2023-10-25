<?php

namespace App\Http\Requests;

use App\Models\WebsitePage;
use Illuminate\Foundation\Http\FormRequest;

class ShowWebsitePageRequest extends FormRequest
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
            'per_page' => ['integer', 'min:1', 'max:100'],
            'page' => ['integer', 'min:1'],
            'publish_status' => ['integer', 'min:1'],
        ];
        $websitePage = WebsitePage::find($this->id);
        if ($websitePage->type == WebsitePage::ARTICLE_DETAIL_TYPE) {
            $validate['article_slug'] = ['required', 'exists:articles,slug'];
        } else {
            $validate['article_category_slug'] = ['required', 'exists:article_categories,slug'];
        }

        return $validate;
    }
}
