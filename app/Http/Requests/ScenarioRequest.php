<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;
use App\Rules\OpenWithinByTypeRule;
use Carbon\Carbon;
use Illuminate\Validation\Rule;

class ScenarioRequest extends AbstractRequest
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
            'name' => ['required', "string"],
            'nodes' => ['required', 'array', 'min:1'],
            'nodes.*' => ['required'],
            'nodes.*.id' => ['required', 'string'],
            'nodes.*.campaign_uuid' => ['required', 'numeric', 'min:1', Rule::exists('campaigns', 'uuid')->where(function ($q) {
                return $q->where('user_uuid', auth()->user()->getkey())
                    ->where('type','<>','birthday')
                    ->where('to_date', '>=', Carbon::now())->whereNull('deleted_at');
            })],
            'nodes.*.source' => ['nullable','required_unless:nodes.*.type,null', 'string', 'different:nodes.*.id'],
            'nodes.*.type' => ['nullable', 'required_unless:nodes.*.source,null','string', 'in:open,not_open'],
        ];

        foreach ($this->request->get('nodes') as $key => $node) {
            if ($node['type'] === 'not_open') {
                $validate['nodes.'.$key.'.open_within'] = ['required', 'numeric', 'min:1'];
            }else{
                $validate['nodes.'.$key.'.open_within'] = [new OpenWithinByTypeRule($node['type'])];
            }
        }

        return $validate;
    }
}
