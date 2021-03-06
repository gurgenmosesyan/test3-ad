<?php

namespace App\Http\Controllers;

use App\Models\Agency\Agency;
use App\Models\Brand\Brand;
use App\Models\Category\CategoryMl;
use App\Models\Commercial\Commercial;
use App\Models\Country\CountryMl;
use App\Models\Creative\Creative;
use App\Models\MediaType\MediaTypeMl;
use Illuminate\Http\Request;

class CommercialController extends Controller
{
    public function all(Request $request)
    {
        $mediaTypes = MediaTypeMl::current()->get();
        $industryTypes = CategoryMl::current()->get();
        $countries = CountryMl::current()->get();

        $mediaTypeId = $request->input('media');
        $industryTypeId = $request->input('industry');
        $countryId = $request->input('country');
        $month = $request->input('month');
        $year = $request->input('year');

        $query = Commercial::joinMl()->active();
        if (!empty($mediaTypeId)) {
            $query->where('commercials.media_type_id', $mediaTypeId);
        }
        if (!empty($industryTypeId)) {
            $query->where('commercials.category_id', $industryTypeId);
        }
        if (!empty($countryId)) {
            $query->where('commercials.country_id', $countryId);
        }
        if (!empty($month) && !empty($year)) {
            $date = date('Y-m-01', strtotime($year.'-'.$month.'-01'));
            $query->where('commercials.published_date', $date);
        }
        $commercials = $query->latest()->paginate(42);

        return view('commercial.all')->with([
            'mediaTypes' => $mediaTypes,
            'industryTypes' => $industryTypes,
            'countries' => $countries,
            'mediaTypeId' => $mediaTypeId,
            'industryTypeId' => $industryTypeId,
            'countryId' => $countryId,
            'month' => $month,
            'year' => $year,
            'commercials' => $commercials
        ]);
    }

    protected function getCommercial($id, Request $request)
    {
        $hash = $request->input('hash');
        $query = Commercial::joinMl()->where('commercials.id', $id);
        if (empty($hash)) {
            return $query->active()->firstOrFail();
        } else {
            $commercial = $query->firstOrFail();
            if ($commercial->show_status == Commercial::STATUS_ACTIVE) {
                return $commercial;
            }
            if ($hash !== $commercial->hash) {
                abort(404);
            }
            return $commercial;
        }
    }

    public function index($lngCode, $alias, $id, Request $request)
    {
        $ad = $this->getCommercial($id, $request);
        if ($ad->alias != $alias) {
            return redirect(url_with_lng('/ads/'.$ad->alias.'/'.$ad->id));
        }

        $prevAd = Commercial::joinMl()->where('commercials.id', '>', $ad->id)->active()->orderBy('commercials.id', 'asc')->first();
        $nextAd = Commercial::joinMl()->where('commercials.id', '<', $ad->id)->active()->orderBy('commercials.id', 'desc')->first();

        $topAds = Commercial::joinMl()->where('commercials.top', Commercial::TOP)->latest()->take(3)->get();

        $credits = $ad->credits()->ordered()->with('persons')->get();
        foreach ($credits as $value) {
            foreach ($value->persons as $person) {
                if ($person->type_id != 0) {
                    if ($person->type == 'brand') {
                        $item = Brand::joinMl()->where('brands.id', $person->type_id)->active()->first();
                        $link = 'brands';
                    } else if ($person->type == 'agency') {
                        $item = Agency::joinMl()->where('agencies.id', $person->type_id)->active()->first();
                        $link = 'agencies';
                    } else {
                        $item = Creative::joinMl()->where('creatives.id', $person->type_id)->active()->first();
                        $link = 'creative';
                    }
                    if ($item != null) {
                        $person->id = $item->id;
                        $person->link = $link;
                        $person->alias = $item->alias;
                        $person->name = $item->title;
                    } else {
                        $person->id = '';
                        $person->alias = '';
                        $person->name = '';
                    }
                }
            }
        }

        return view('commercial.index')->with([
            'ad' => $ad,
            'prevAd' => $prevAd,
            'nextAd' => $nextAd,
            'topAds' => $topAds,
            'credits' => $credits
        ]);
    }

    public function views(Request $request)
    {
        $id = $request->input('id');
        Commercial::where('id', $id)->increment('views_count');
        die();
    }
}