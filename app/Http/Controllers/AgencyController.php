<?php

namespace App\Http\Controllers;

use App\Models\Agency\Agency;
use App\Models\AgencyCategory\CategoryMl;
use App\Models\Brand\Brand;
use Illuminate\Http\Request;
use DB;

class AgencyController extends Controller
{
    public function all(Request $request)
    {
        $categories = CategoryMl::current()->get();

        $categoryId = $request->input('category');

        $query = Agency::joinMl();
        if (!empty($categoryId)) {
            $query->where('agencies.category_id', $categoryId);
        }
        $agencies = $query->latest()->paginate(42);

        return view('agency.all')->with([
            'categories' => $categories,
            'categoryId' => $categoryId,
            'agencies' => $agencies
        ]);
    }

    public function index($lngCode, $alias, $id, Request $request)
    {
        $agency = Agency::joinMl()->where('agencies.id', $id)->firstOrFail();
        if ($agency->alias != $alias) {
            return redirect(url_with_lng('/agencies/'.$agency->alias.'/'.$agency->id));
        }
        $alias = 'work';
        $scroll = $request->input('work');
        $items = $agency->commercials()->select('commercials.id','commercials.alias','commercials.image','commercials.rating','commercials.comments_count','commercials.views_count','ml.title')->joinMl()->latest()->paginate(42);
        return view('agency.index')->with([
            'agency' => $agency,
            'alias' => $alias,
            'items' => $items,
            'scroll' => $scroll
        ]);
    }

    public function creatives($lngCode, $alias, $id)
    {
        $agency = Agency::joinMl()->where('agencies.id', $id)->firstOrFail();
        if ($agency->alias != $alias) {
            return redirect(url_with_lng('/agencies/'.$agency->alias.'/'.$agency->id));
        }
        $alias = 'creatives';
        $items = $agency->creatives()->joinMl()->latest()->paginate(42);
        return view('agency.index')->with([
            'agency' => $agency,
            'alias' => $alias,
            'items' => $items,
            'scroll' => true
        ]);
    }

    public function awards($lngCode, $alias, $id)
    {
        $agency = Agency::joinMl()->where('agencies.id', $id)->firstOrFail();
        if ($agency->alias != $alias) {
            return redirect(url_with_lng('/agencies/'.$agency->alias.'/'.$agency->id));
        }
        $alias = 'awards';
        $awards = $agency->awards()->joinMl()->orderBy('awards.year', 'desc')->get();
        return view('agency.index')->with([
            'agency' => $agency,
            'alias' => $alias,
            'awards' => $awards,
            'scroll' => true
        ]);
    }

    public function vacancies($lngCode, $alias, $id)
    {
        $agency = Agency::joinMl()->where('agencies.id', $id)->firstOrFail();
        if ($agency->alias != $alias) {
            return redirect(url_with_lng('/agencies/'.$agency->alias.'/'.$agency->id));
        }
        $alias = 'vacancies';
        $vacancies = $agency->vacancies()->joinMl()->latest()->get();
        return view('agency.index')->with([
            'agency' => $agency,
            'alias' => $alias,
            'vacancies' => $vacancies,
            'scroll' => true
        ]);
    }

    public function news($lngCode, $alias, $id)
    {
        $agency = Agency::joinMl()->where('agencies.id', $id)->firstOrFail();
        if ($agency->alias != $alias) {
            return redirect(url_with_lng('/agencies/'.$agency->alias.'/'.$agency->id));
        }
        $alias = 'news';
        $news = $agency->news()->select('news.id','news.alias','news.image','ml.title','ml.description')->joinMl()->latest()->paginate(12);
        return view('agency.index')->with([
            'agency' => $agency,
            'alias' => $alias,
            'news' => $news,
            'scroll' => true
        ]);
    }

    public function brands($lngCode, $alias, $id)
    {
        $agency = Agency::joinMl()->where('agencies.id', $id)->firstOrFail();
        if ($agency->alias != $alias) {
            return redirect(url_with_lng('/agencies/'.$agency->alias.'/'.$agency->id));
        }
        $alias = 'brands';
        $brandIds = DB::table('commercial_agencies')->join('commercial_brands', function($query) {
            $query->on('commercial_brands.commercial_id', '=', 'commercial_agencies.commercial_id');
        })->where('commercial_agencies.agency_id', $agency->id)->lists('commercial_brands.brand_id');
        $items = Brand::joinMl()->whereIn('brands.id', $brandIds)->latest()->paginate(42);
        return view('agency.index')->with([
            'agency' => $agency,
            'alias' => $alias,
            'items' => $items,
            'scroll' => true
        ]);
    }

    public function about($lngCode, $alias, $id)
    {
        $agency = Agency::joinMl()->where('agencies.id', $id)->firstOrFail();
        if ($agency->alias != $alias) {
            return redirect(url_with_lng('/agencies/'.$agency->alias.'/'.$agency->id));
        }
        $alias = 'about';
        return view('agency.index')->with([
            'agency' => $agency,
            'alias' => $alias,
            'scroll' => true
        ]);
    }

    public function branches($lngCode, $alias, $id)
    {
        $agency = Agency::joinMl()->where('agencies.id', $id)->firstOrFail();
        if ($agency->alias != $alias) {
            return redirect(url_with_lng('/agencies/'.$agency->alias.'/'.$agency->id));
        }
        $alias = 'contacts';
        $branches = $agency->branches()->joinMl()->latest()->get();
        return view('agency.index')->with([
            'agency' => $agency,
            'alias' => $alias,
            'branches' => $branches,
            'scroll' => true
        ]);
    }
}