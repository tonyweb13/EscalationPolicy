<?php
declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AclRulesRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = [
            'fields.rule_name' => 'required|unique:acl_rules,rule_name',
            'fields.label' => 'required|string',
            'fields.description' => 'required|string',
        ];

        if ($this->acl_rule) {
            $rules['fields.rule_name'] = 'required|unique:acl_rules,rule_name, '. $this->acl_rule->id;
        }

        return $rules;
    }
}
