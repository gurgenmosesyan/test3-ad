<?php

namespace App\Models\Commercial;

use App\Core\DataTable;
use Auth;

class CommercialSearch extends DataTable
{
    public function totalCount()
    {
        if (Auth::guard('admin')->check()) {
            return Commercial::count();
        }
        if (Auth::guard('brand')->check()) {
            $brand = Auth::guard('brand')->user();
            return Commercial::join('commercial_brands as brands', function($query) use($brand) {
                $query->on('brands.commercial_id', '=', 'commercials.id')->where('brands.brand_id', '=', $brand->id);
            })->count();
        } else if (Auth::guard('agency')->check()) {
            $agency = Auth::guard('agency')->user();
            return Commercial::join('commercial_agencies as agencies', function($query) use($agency) {
                $query->on('agencies.commercial_id', '=', 'commercials.id')->where('agencies.agency_id', '=', $agency->id);
            })->count();
        } else {
            $creative = Auth::guard('creative')->user();
            $commercialIds = CommercialCredit::join('commercial_credit_persons as person', function($query) use($creative) {
                $query->on('person.credit_id', '=', 'commercial_credits.id')->where('person.type', '=', CommercialCreditPerson::TYPE_CREATIVE)->where('type_id', '=', $creative->id);
            })->lists('commercial_credits.commercial_id')->toArray();
            return Commercial::whereIn('id', $commercialIds)->count();
        }
    }

    public function filteredCount()
    {
        $query = $this->constructQuery();
        return $query->count();
    }

    public function search()
    {
        $query = $this->constructQuery();
        $this->constructOrder($query);
        $this->constructLimit($query);
        $data = $query->get();
        foreach ($data as $value) {
            $value->show_status = $value->show_status == Commercial::STATUS_ACTIVE ? '<i class="fa fa-check"></i>' : '';
            $value->preview = '<a href="'.url_with_lng('/ads/'.$value->alias.'/'.$value->id.'?hash='.$value->hash).'" target="_blank">'.trans('admin.base.label.preview').'</a>';
        }
        return $data;
    }

    protected function constructQuery()
    {
        $query = Commercial::joinMl();

        if (Auth::guard('brand')->check()) {
            $brand = Auth::guard('brand')->user();
            $query->join('commercial_brands as brands', function($query) use($brand) {
                $query->on('brands.commercial_id', '=', 'commercials.id')->where('brands.brand_id', '=', $brand->id);
            });
        } else if (Auth::guard('agency')->check()) {
            $agency = Auth::guard('agency')->user();
            $query->join('commercial_agencies as agencies', function($query) use($agency) {
                $query->on('agencies.commercial_id', '=', 'commercials.id')->where('agencies.agency_id', '=', $agency->id);
            });
        } else if (Auth::guard('creative')->check()) {
            $creative = Auth::guard('creative')->user();
            $commercialIds = CommercialCredit::join('commercial_credit_persons as person', function($query) use($creative) {
                $query->on('person.credit_id', '=', 'commercial_credits.id')->where('person.type', '=', CommercialCreditPerson::TYPE_CREATIVE)->where('type_id', '=', $creative->id);
            })->lists('commercial_credits.commercial_id')->toArray();
            $query->whereIn('commercials.id', $commercialIds);
        } else {
            $query->select('commercials.id', 'commercials.published_date', 'commercials.alias', 'commercials.show_status', 'commercials.hash', 'ml.title', 'admin1.email as created_by', 'admin2.email as updated_by')
                ->leftJoin('adm_users as admin1', function($query) {
                    $query->on('admin1.id', '=', 'commercials.add_admin_id');
                })
                ->leftJoin('adm_users as admin2', function($query) {
                    $query->on('admin2.id', '=', 'commercials.update_admin_id');
                });
        }

        if ($this->search != null) {
            $query->where(function($query) {
                $query->where('ml.title', 'LIKE', '%'.$this->search.'%')
                    ->orWhere('ml.description', 'LIKE', '%'.$this->search.'%');;
            });
        }
        if (isset($this->searchData['title'])) {
            $query->where('ml.title', 'LIKE', '%'.$this->searchData['title'].'%');
        }
        if (isset($this->searchData['skip_ids'])) {
            $query->whereNotIn('commercials.id', $this->searchData['skip_ids']);
        }
        return $query;
    }

    protected function constructOrder($query)
    {
        switch ($this->orderCol) {
            case 'title':
                $orderCol = 'ml.title';
                break;
            case 'published_date':
                $orderCol = 'commercials.published_date';
                break;
            case 'show_status':
                $orderCol = 'commercials.show_status';
                break;
            default:
                $orderCol = 'commercials.id';
        }
        $orderType = 'desc';
        if ($this->orderType == 'asc') {
            $orderType = 'asc';
        }
        $query->orderBy($orderCol, $orderType);
    }

    protected function constructLimit($query)
    {
        $query->skip($this->start)->take($this->length);
    }
}