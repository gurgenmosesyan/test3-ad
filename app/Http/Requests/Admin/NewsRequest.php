<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\Request;
use App\Models\News\News;

class NewsRequest extends Request
{
    public function rules()
    {
        return [
            'alias' => 'required|max:255',
            'image' => 'required|core_image',
            'top' => 'in:'.News::NOT_TOP.','.News::TOP,
            'date' => 'date',
            'ml' => 'ml',
            'ml.*.title' => 'required|max:255',
            'ml.*.sub_title' => 'required|max:255',
            'ml.*.text' => 'required|max:65000',
            'brands' => 'array',
            'brands.*.brand_id' => 'required|integer|exists:brands,id',
            'agencies.*.agency_id' => 'required|integer|exists:agencies,id',
            'creatives.*.creative_id' => 'required|integer|exists:creatives,id'
        ];
    }
}