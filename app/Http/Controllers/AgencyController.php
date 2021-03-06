<?php

namespace App\Http\Controllers;

use App\Models\Agency\Agency;
use App\Models\AgencyCategory\CategoryMl;
use App\Models\Brand\Brand;
use App\Models\Commercial\Commercial;
use App\Models\Commercial\CommercialCreditPerson;
use Illuminate\Http\Request;
use DB;

class AgencyController extends Controller
{
    public function all(Request $request)
    {
        $categories = CategoryMl::current()->get();

        $categoryId = $request->input('category');

        $query = Agency::joinMl()->where('agencies.show_status', Agency::STATUS_ACTIVE);

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

    protected function getAgency($id, Request $request)
    {
        $hash = $request->input('hash');
        $query = Agency::joinMl()->where('agencies.id', $id);
        if (empty($hash)) {
            return $query->where('agencies.show_status', Agency::STATUS_ACTIVE)->firstOrFail();
        } else {
            $agency = $query->firstOrFail();
            if ($agency->show_status == Agency::STATUS_ACTIVE) {
                return $agency;
            }
            $conf = config('main.show_status');
            if ($hash !== $conf['start_salt'].$agency->hash.$conf['end_salt']) {
                abort(404);
            }
            return $agency;
        }
    }

    public function index($lngCode, $alias, $id, Request $request)
    {
        $agency = $this->getAgency($id, $request);
        if ($agency->alias != $alias) {
            return redirect(url_with_lng('/agencies/'.$agency->alias.'/'.$agency->id));
        }
        $alias = 'work';
        $scroll = $request->input('work');

        $adIds = CommercialCreditPerson::select('credits.commercial_id')
            ->join('commercial_credits as credits', function($query) {
                $query->on('credits.id', '=', 'commercial_credit_persons.credit_id');
            })
            ->where('commercial_credit_persons.type', CommercialCreditPerson::TYPE_AGENCY)
            ->where('commercial_credit_persons.type_id', $agency->id)->lists('commercial_id')->toArray();

        $agencyAdIds = DB::table('commercial_agencies')->where('agency_id', $agency->id)->lists('commercial_id');
        $adIds = array_merge($adIds, $agencyAdIds);

        $items = Commercial::joinMl()->whereIn('commercials.id', $adIds)->active()->latest()->paginate(42);

        return view('agency.index')->with([
            'agency' => $agency,
            'alias' => $alias,
            'items' => $items,
            'scroll' => $scroll
        ]);
    }

    public function creatives($lngCode, $alias, $id, Request $request)
    {
        $agency = $this->getAgency($id, $request);
        if ($agency->alias != $alias) {
            return redirect(url_with_lng('/agencies/'.$agency->alias.'/'.$agency->id));
        }
        $alias = 'creatives';
        $items = $agency->creatives()->joinMl()->active()->latest()->paginate(42);
        return view('agency.index')->with([
            'agency' => $agency,
            'alias' => $alias,
            'items' => $items,
            'scroll' => true
        ]);
    }

    public function awards($lngCode, $alias, $id, Request $request)
    {
        $agency = $this->getAgency($id, $request);
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

    public function vacancies($lngCode, $alias, $id, Request $request)
    {
        $agency = $this->getAgency($id, $request);
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

    public function news($lngCode, $alias, $id, Request $request)
    {
        $agency = $this->getAgency($id, $request);
        if ($agency->alias != $alias) {
            return redirect(url_with_lng('/agencies/'.$agency->alias.'/'.$agency->id));
        }
        $alias = 'news';
        $news = $agency->news()->select('news.id','news.alias','news.image','ml.title','ml.description')->joinMl()->active()->latest()->paginate(12);
        return view('agency.index')->with([
            'agency' => $agency,
            'alias' => $alias,
            'news' => $news,
            'scroll' => true
        ]);
    }

    public function brands($lngCode, $alias, $id, Request $request)
    {
        $agency = $this->getAgency($id, $request);
        if ($agency->alias != $alias) {
            return redirect(url_with_lng('/agencies/'.$agency->alias.'/'.$agency->id));
        }
        $alias = 'brands';

        $adIds = DB::table('commercial_agencies')->where('agency_id', $agency->id)->lists('commercial_id');

        $creditAdIds = CommercialCreditPerson::select('credits.commercial_id')
            ->join('commercial_credits as credits', function($query) {
                $query->on('credits.id', '=', 'commercial_credit_persons.credit_id');
            })
            ->where('commercial_credit_persons.type', CommercialCreditPerson::TYPE_AGENCY)
            ->where('commercial_credit_persons.type_id', $agency->id)->lists('commercial_id')->toArray();

        $adIds = array_merge($adIds, $creditAdIds);

        $brandIds = DB::table('commercial_brands')->whereIn('commercial_id', $adIds)->lists('brand_id');

        $items = Brand::joinMl()->whereIn('brands.id', $brandIds)->active()->latest()->paginate(42);

        return view('agency.index')->with([
            'agency' => $agency,
            'alias' => $alias,
            'items' => $items,
            'scroll' => true
        ]);
    }

    public function about($lngCode, $alias, $id, Request $request)
    {
        $agency = $this->getAgency($id, $request);
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

    public function branches($lngCode, $alias, $id, Request $request)
    {
        $agency = $this->getAgency($id, $request);
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