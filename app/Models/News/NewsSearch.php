<?php

namespace App\Models\News;

use App\Core\DataTable;

class NewsSearch extends DataTable
{
    public function totalCount()
    {
        return News::count();
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
            $value->show_status = $value->show_status == News::STATUS_ACTIVE ? '<i class="fa fa-check"></i>' : '';
            $value->preview = '<a href="'.url_with_lng('/news/'.$value->alias.'/'.$value->id.'?hash='.$value->hash).'" target="_blank">'.trans('admin.base.label.preview').'</a>';
        }
        return $data;
    }

    protected function constructQuery()
    {
        $query = News::select('news.id', 'ml.title', 'news.alias', 'news.show_status', 'news.hash', 'ml.description', 'admin1.email as created_by', 'admin2.email as updated_by')
            ->joinMl()
            ->leftJoin('adm_users as admin1', function($query) {
                $query->on('admin1.id', '=', 'news.add_admin_id');
            })
            ->leftJoin('adm_users as admin2', function($query) {
                $query->on('admin2.id', '=', 'news.update_admin_id');
            });

        if ($this->search != null) {
            $query->where('ml.title', 'LIKE', '%'.$this->search.'%')
                ->orWhere('ml.sub_title', 'LIKE', '%'.$this->search.'%')
                ->orWhere('ml.text', 'LIKE', '%'.$this->search.'%');
        }
        return $query;
    }

    protected function constructOrder($query)
    {
        switch ($this->orderCol) {
            case 'title':
                $orderCol = 'ml.title';
                break;
            case 'sub_title':
                $orderCol = 'ml.sub_title';
                break;
            case 'show_status':
                $orderCol = 'news.show_status';
                break;
            default:
                $orderCol = 'news.id';
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