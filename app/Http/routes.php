<?php

Route::group(['middleware' => ['short_link']], function() {

    Route::get('/language', ['uses' => 'LanguageController@index', 'as' => 'language']);

    Route::group(['middleware' => ['web', 'front']], function() {

        Route::get('/', 'IndexController@index');

        Route::group(['prefix' => '{lngCode}'], function() {

            Route::get('/', 'IndexController@index');

            Route::get('/search', 'SearchController@index');

            Route::get('/contacts', 'ContactController@index');
            Route::post('/api/contacts', 'ApiController@contact');
            Route::post('/api/subscribe', 'ApiController@subscribe');
            Route::post('/api/rate', 'ApiController@rate');

            Route::group(['middleware' => 'guest:all'], function() {
                Route::get('/sign-in', 'AccountController@login');
                Route::get('/register', 'AccountController@register');
                Route::get('/activation/{hash}', 'AccountController@activation');
                Route::get('/reset', 'AccountController@forgot');
                Route::get('/reset/{hash}', 'AccountController@reset');
                Route::post('/api/login', 'AccountApiController@login');
                Route::post('/api/register', 'AccountApiController@register');
                Route::post('/api/forgot', 'AccountApiController@forgot');
                Route::post('/api/reset', 'AccountApiController@reset');
            });

            Route::get('/ads', 'CommercialController@all');
            Route::get('/ads/{alias}/{id}', 'CommercialController@index');
            Route::post('/ads/st', 'CommercialController@views');

            Route::get('/brands', 'BrandController@all');
            Route::get('/brands/{alias}/{id}', 'BrandController@index');
            Route::get('/brands/{alias}/{id}/key-people', 'BrandController@creatives');
            Route::get('/brands/{alias}/{id}/awards', 'BrandController@awards');
            Route::get('/brands/{alias}/{id}/vacancies', 'BrandController@vacancies');
            Route::get('/brands/{alias}/{id}/news', 'BrandController@news');
            Route::get('/brands/{alias}/{id}/partner-agencies', 'BrandController@agencies');
            Route::get('/brands/{alias}/{id}/about', 'BrandController@about');
            Route::get('/brands/{alias}/{id}/contacts', 'BrandController@branches');

            Route::get('/agencies', 'AgencyController@all');
            Route::get('/agencies/{alias}/{id}', 'AgencyController@index');
            Route::get('/agencies/{alias}/{id}/creatives', 'AgencyController@creatives');
            Route::get('/agencies/{alias}/{id}/awards', 'AgencyController@awards');
            Route::get('/agencies/{alias}/{id}/vacancies', 'AgencyController@vacancies');
            Route::get('/agencies/{alias}/{id}/news', 'AgencyController@news');
            Route::get('/agencies/{alias}/{id}/clients', 'AgencyController@brands');
            Route::get('/agencies/{alias}/{id}/about', 'AgencyController@about');
            Route::get('/agencies/{alias}/{id}/contacts', 'AgencyController@branches');

            Route::get('/creative/{alias}/{id}', 'CreativeController@index');
            Route::get('/creative/{alias}/{id}/clients', 'CreativeController@brands');
            Route::get('/creative/{alias}/{id}/awards', 'CreativeController@awards');
            Route::get('/creative/{alias}/{id}/about', 'CreativeController@about');

            Route::get('/page/{alias}', 'PageController@index');

            Route::get('/team', 'TeamController@index');

            Route::get('/news', 'NewsController@all');
            Route::get('/news/{alias}/{id}', 'NewsController@index');
        });

    });

});
