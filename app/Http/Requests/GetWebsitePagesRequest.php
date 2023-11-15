<?php

namespace App\Http\Requests;

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
        return [
            'article_slug' => ['required', 'exists:articles,slug'],
            'article_category_slug' => ['required', 'exists:article_categories,slug']
        ];
    }
}
