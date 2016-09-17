<?php
$title = $news->title;

$fbSDK = true;
$pageMenu = 'news';
?>
@extends('layout')

@section('content')

    <div class="page">

        <div id="main-inner">

            <div id="main-left" class="fl">

                <div id="news-inner">
                    <div class="html">
                        <div class="news-img fr">
                            <img src="{{$news->getImage()}}" alt="{{$news->title}}" />
                        </div>
                        <h1 class="fsb fs40">{{$news->title}}</h1>
                        <p class="date tu">{{strftime('%b. %d, %Y', strtotime($news->created_at))}}</p>
                        {!!$news->text!!}
                        <div class="cb"></div>
                    </div>
                </div>

                @if(!$relNews->isEmpty())
                    <div id="rel-news">
                        <h3 class="rel-title fsb fs32">{{trans('www.related_news.title')}}</h3>
                        @include('blocks.news', ['news' => $relNews, 'notPaginate' => true])
                    </div>
                @endif

            </div>
            <div id="main-right" class="fr">
                @include('blocks.main_right')
            </div>
            <div class="cb"></div>

        </div>

    </div>

@stop