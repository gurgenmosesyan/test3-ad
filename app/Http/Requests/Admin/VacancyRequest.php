<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\Request;
use App\Models\Vacancy\Vacancy;

class VacancyRequest extends Request
{
    public function rules()
    {
        $rules = [
            'type' => 'required|in:'.Vacancy::TYPE_BRAND.','.Vacancy::TYPE_AGENCY,
            'ml' => 'ml',
            'ml.*.title' => 'required|max:255',
            'ml.*.description' => 'required|max:1000',
            'ml.*.text' => 'required|max:65000'
        ];

        $type = $this->get('type');
        if (!empty($type)) {
            if ($type == Vacancy::TYPE_BRAND) {
                $rules['type_id'] = 'required|exists:brands,id';
            } else if ($type == Vacancy::TYPE_AGENCY) {
                $rules['type_id'] = 'required|exists:agencies,id';
            }
        }

        return $rules;
    }
}