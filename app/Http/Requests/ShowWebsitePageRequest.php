<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;
use App\Models\WebsitePage;
use Illuminate\Foundation\Http\FormRequest;

class ShowWebsitePageRequest extends AbstractRequest
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
        $websitePage = WebsitePage::findOrFail($this->id);
        if ($websitePage->type == WebsitePage::ARTICLE_DETAIL_TYPE) {
            $validate['article_slug'] = ['nullable', 'exists:articles,slug'];
            $validate['article_id'] = ['nullable', 'exists:articles,uuid'];
        } elseif ($websitePage->type == WebsitePage::ARTICLE_CATEGORY_TYPE) {
            $validate['article_category_slug'] = ['nullable', 'exists:article_categories,slug'];
            $validate['article_category_id'] = ['nullable', 'exists:article_categories,uuid'];
        }elseif ($websitePage->type == WebsitePage::PRODUCT_DETAIL_TYPE) {
            $validate['product_uuid'] = ['required'];
        } elseif ($websitePage->type == WebsitePage::PRODUCT_CATEGORY_TYPE) {
            $validate['article_product_category_uuid'] = ['nullable'];
        }

        return $validate;
    }
}
