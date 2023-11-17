<?php

namespace App\Http\Requests;

use App\Models\WebsitePage;
use Illuminate\Foundation\Http\FormRequest;

class GetWebsitePagesRequest extends FormRequest
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
            'domain' => ['required', 'string', 'regex:/^(?!(www|http|https)\.)\w+(\.\w+)+$/'],
            'website_page_slug' => ['required', 'string'],
        ];
        $websitePage = WebsitePage::where(['slug' => $this->request->get('website_page_slug')])->first();
        if ($websitePage->type == WebsitePage::ARTICLE_DETAIL_TYPE) {
            $validate['article_slug'] = ['required', 'exists:articles,slug'];
        } elseif ($websitePage->type == WebsitePage::ARTICLE_CATEGORY_TYPE) {
            $validate['article_category_slug'] = ['required', 'exists:article_categories,slug'];
        }

        return $validate;
    }
}
