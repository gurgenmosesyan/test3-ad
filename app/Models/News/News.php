<?php

namespace App\Models\News;

use App\Core\Model;
use App\Models\Agency\Agency;
use App\Models\Brand\Brand;
use App\Models\Creative\Creative;

class News extends Model
{
    const IMAGES_PATH = 'images/news';
    const TOP = '1';
    const NOT_TOP = '0';

    public $adminInfo = true;

    protected $fillable = [
        'alias',
        'top',
        'date',
        'show_status'
    ];

    protected $table = 'news';

    public function isTop()
    {
        return $this->top == self::TOP;
    }

    public function getImage()
    {
        return url('/'.self::IMAGES_PATH.'/'.$this->image);
    }

    public function getLink()
    {
        return url_with_lng('/news/'.$this->alias.'/'.$this->id);
    }

    public function scopeTop($query)
    {
        return $query->where('top', self::TOP);
    }

    public function ml()
    {
        return $this->hasMany(NewsMl::class, 'id', 'id');
    }

    public function brands()
    {
        return $this->belongsToMany(Brand::class, 'news_brands', 'news_id', 'brand_id');
    }

    public function agencies()
    {
        return $this->belongsToMany(Agency::class, 'news_agencies', 'news_id', 'agency_id');
    }

    public function creatives()
    {
        return $this->belongsToMany(Creative::class, 'news_creatives', 'news_id', 'creative_id');
    }

    public function tags()
    {
        return $this->hasMany(NewsTag::class, 'news_id', 'id');
    }

    public function images()
    {
        return $this->hasMany(NewsImage::class)->active();
    }

    public function getFile($column)
    {
        return $this->{$column};
    }

    public function setFile($file, $column)
    {
        $this->attributes[$column] = $file;
    }

    public function getStorePath()
    {
        return self::IMAGES_PATH;
    }
}