<?php

namespace App\Models\Agency;

use App\Core\DataTable;

class AgencySearch extends DataTable
{
    public function totalCount()
    {
        return Agency::count();
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
        $conf = config('main.show_status');
        foreach ($data as $value) {
            $value->show_status = $value->show_status == Agency::STATUS_ACTIVE ? '<i class="fa fa-check"></i>' : '';
            $value->preview = '<a href="'.url_with_lng('/agencies/'.$value->alias.'/'.$value->id.'?hash='.$conf['start_salt'].$value->hash.$conf['end_salt']).'" target="_blank">'.trans('admin.base.label.preview').'</a>';
        }
        return $data;
    }

    protected function constructQuery()
    {
        $query = Agency::select('agencies.id', 'ml.title', 'agencies.alias', 'agencies.show_status', 'agencies.hash', 'admin1.email as created_by', 'admin2.email as updated_by')
            ->leftJoinMl()
            ->leftJoin('adm_users as admin1', function($query) {
                $query->on('admin1.id', '=', 'agencies.add_admin_id');
            })
            ->leftJoin('adm_users as admin2', function($query) {
                $query->on('admin2.id', '=', 'agencies.update_admin_id');
            });

        if ($this->search != null) {
            $query->where('ml.title', 'LIKE', '%'.$this->search.'%');
        }
        if (isset($this->searchData['title'])) {
            $query->where('ml.title', 'LIKE', '%'.$this->searchData['title'].'%');
        }
        if (isset($this->searchData['skip_ids'])) {
            $query->whereNotIn('agencies.id', $this->searchData['skip_ids']);
        }
        return $query;
    }

    protected function constructOrder($query)
    {
        switch ($this->orderCol) {
            case 'title':
                $orderCol = 'ml.title';
                break;
            case 'show_status':
                $orderCol = 'agencies.show_status';
                break;
            default:
                $orderCol = 'agencies.id';
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