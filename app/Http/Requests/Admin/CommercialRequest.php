<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\Request;
use App\Models\Commercial\Commercial;
use App\Models\Commercial\CommercialCreditPerson;
use Auth;

class CommercialRequest extends Request
{
    public function rules()
    {
        $rules = [
            'media_type_id' => 'required|integer|exists:media_types,id',
            'country_id' => 'integer|exists:countries,id',
            'category_id' => 'integer|exists:categories,id',
            'alias' => 'required|max:255',
            'type' => 'required|in:'.Commercial::TYPE_VIDEO.','.Commercial::TYPE_PRINT,
            'featured' => 'in:'.Commercial::NOT_FEATURED.','.Commercial::FEATURED,
            'top' => 'in:'.Commercial::NOT_TOP.','.Commercial::TOP,
            'month' => 'required_with:year|integer|between:1,12',
            'year' => 'required_with:month|integer|between:1905,'.date('Y'),
            'image' => 'required|core_image',
            'views_count' => 'integer',
            'rating' => 'numeric',
            'qt' => 'integer',
            'show_status' => 'required|in:'.Commercial::STATUS_ACTIVE.','.Commercial::STATUS_INACTIVE,
            'ml' => 'ml',
            'ml.*.title' => 'required|max:255',
            'ml.*.description' => 'max:2000',
            'brands' => 'array',
            'brands.*.brand_id' => 'required|integer|exists:brands,id',
            'agencies' => 'array',
            'agencies.*.agency_id' => 'required|integer|exists:agencies,id',
            'tags' => 'array',
            'tags.*.tag' => 'required|max:255',
            'credits' => 'array',
            'clone_id' => 'integer|exists:commercials,id',
        ];

        $creative = null;
        if (Auth::guard('brand')->check()) {
            unset($rules['brands']);
            unset($rules['brands.*.brand_id']);
        } else if (Auth::guard('agency')->check()) {
            unset($rules['agencies']);
            unset($rules['agencies.*.agency_id']);
        } else if (Auth::guard('creative')->check()) {
            $creative = Auth::guard('creative')->user();
        }

        $type = $this->get('type');
        if ($type == Commercial::TYPE_VIDEO) {
            $rules['video_type'] = 'required|in:'.Commercial::VIDEO_TYPE_YOUTUBE.','.Commercial::VIDEO_TYPE_VIMEO.','.Commercial::VIDEO_TYPE_FB.','.Commercial::VIDEO_TYPE_EMBED;
            $videoType = $this->get('video_type');
            if ($videoType == Commercial::VIDEO_TYPE_YOUTUBE) {
                $rules['youtube_url'] = 'required|max:255';
                $rules['youtube_id'] = 'required|min:11|max:11';
            } else if ($videoType == Commercial::VIDEO_TYPE_VIMEO) {
                $rules['vimeo_url'] = 'required|max:255';
                $rules['vimeo_id'] = 'required|min:6|max:11';
            } else if ($videoType == Commercial::VIDEO_TYPE_FB) {
                $rules['fb_video_id'] = 'required|max:100';
            } else if ($videoType == Commercial::VIDEO_TYPE_EMBED) {
                $rules['embed_code'] = 'required|max:65000';
            }
        } else if ($type == Commercial::TYPE_PRINT) {
            $rules['image_print'] = 'required|core_image';
        }

        $advertisings = $this->get('advertisings');
        if (is_array($advertisings) && !empty($advertisings)) {
            $rules['advertising'] = 'required|max:255';
            foreach ($advertisings as $key => $value) {
                $rules['advertisings.'.$key.'.name'] = 'required|max:255';
                $rules['advertisings.'.$key.'.link'] = 'required|max:255';
            }
        }

        $issetCreative = false;
        $credits = $this->get('credits');
        if (is_array($credits)) {
            foreach ($credits as $key => $credit) {
                $rules['credits.'.$key.'.id'] = 'integer|exists:commercial_credits,id';
                $rules['credits.'.$key.'.position'] = 'required|max:255';
                $rules['credits.'.$key.'.sort_order'] = 'integer';
                $rules['credits.'.$key.'.persons'] = 'required|array';
                if (is_array($credit['persons'])) {
                    foreach ($credit['persons'] as $subKey => $person) {
                        $rules['credits.'.$key.'.persons.'.$subKey.'.type'] = 'required|in:'.CommercialCreditPerson::TYPE_CREATIVE.','.CommercialCreditPerson::TYPE_BRAND.','.CommercialCreditPerson::TYPE_AGENCY;
                        $type = $person['type'];
                        $typeIdRule = 'integer';
                        if (!empty($person['name']) && mb_substr($person['name'], 0, 1) == '@') {
                            $typeIdRule = 'required|'.$typeIdRule;
                            if ($type == CommercialCreditPerson::TYPE_BRAND) {
                                $typeIdRule .= '|exists:brands,id';
                            } else if ($type == CommercialCreditPerson::TYPE_AGENCY) {
                                $typeIdRule .= '|exists:agencies,id';
                            } else if ($type == CommercialCreditPerson::TYPE_CREATIVE) {
                                $typeIdRule .= '|exists:creatives,id';
                                if ($creative != null && $creative->id == $person['type_id']) {
                                    $issetCreative = true;
                                }

                            }
                        }
                        $rules['credits.'.$key.'.persons.'.$subKey.'.type_id'] = $typeIdRule;
                        $rules['credits.'.$key.'.persons.'.$subKey.'.name'] = 'required|max:255';
                        $rules['credits.'.$key.'.persons.'.$subKey.'.separator'] = 'required|max:1';
                    }
                }
            }
        }
        if ($creative != null && !$issetCreative) {
            $rules['credits_creative'] = ['required'];
        }
        return $rules;
    }
}