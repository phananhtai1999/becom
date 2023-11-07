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
        $validate = [];
        $websitePage = WebsitePage::find($this->id);
        if ($websitePage->type == WebsitePage::ARTICLE_DETAIL_TYPE) {
            $validate['article_slug'] = ['required', 'exists:articles,slug'];
        } elseif ($websitePage->type == WebsitePage::ARTICLE_CATEGORY_TYPE) {
            $validate['article_category_slug'] = ['required', 'exists:article_categories,slug'];
        }

        return $validate;
    }
}
