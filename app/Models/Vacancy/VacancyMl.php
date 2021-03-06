<?php

namespace App\Models\Vacancy;

use App\Core\Model;

class VacancyMl extends Model
{
    protected $table = 'vacancies_ml';

    public $timestamps = false;

    protected $fillable = [
        'id',
        'lng_id',
        'title',
        'description',
        'text'
    ];
}