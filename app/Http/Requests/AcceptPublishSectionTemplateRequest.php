<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;
use App\Models\SectionTemplate;
use App\Models\WebsitePage;
use Illuminate\Validation\Rule;

class AcceptPublishSectionTemplateRequest extends AbstractRequest
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
            'section_templates' => ['required', 'array', 'min:1'],
            'section_templates.*' => ['numeric', 'min:1', Rule::exists('section_templates', 'uuid')->where(function ($query) {
                return $query->where('publish_status', '<>', $this->request->get('publish_status'))->whereNull('deleted_at');
            })],
            'publish_status' => ['required', 'numeric', Rule::in(SectionTemplate::PUBLISHED_PUBLISH_STATUS, SectionTemplate::REJECT_PUBLISH_STATUS)]
        ];

        if ($this->request->get('publish_status') == SectionTemplate::REJECT_PUBLISH_STATUS){
            $validate['reject_reason'] = ['required', 'string'];
        }

        return $validate;
    }
}
