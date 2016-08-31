<?php
$title = $brand->title;

$fbSDK = true;

$url = $brand->getLink();
?>
@extends('layout')

@section('content')

<div class="page">

    <div id="main-inner">

        <div id="main-left" class="fl">
            <div class="cover" style="background-image: url('{{$brand->getCover()}}');"></div>
            <div class="logo-box fl">
                <div class="logo"><img src="{{$brand->getImage()}}" /></div>
                <div class="main-rating fs24 fb">{{$brand->rating}}</div>
            </div>
            <div class="main-title fl">
                <h1 class="fb fs32">{{$brand->title}}</h1>
                <p>{{$brand->sub_title}}</p>
            </div>
            <div class="phone-box fl fs18">
                @if(!empty($brand->phone))
                    <p class="phone">{{$brand->phone}}</p>
                @endif
                <p class="link"><a href="{{$brand->link}}" class="underline">{{$brand->link}}</a></p>
            </div>
            <div class="social-pages fl fs18">
                <p>{{trans('www.social_pages.title')}}</p>
                <ul>
                    @if(!empty($brand->fb))
                        <li><a href="{{$brand->fb}}" class="db facebook" target="_blank"></a></li>
                    @endif
                    @if(!empty($brand->twitter))
                        <li><a href="{{$brand->twitter}}" class="db twitter" target="_blank"></a></li>
                    @endif
                    @if(!empty($brand->google))
                        <li><a href="{{$brand->google}}" class="db google" target="_blank"></a></li>
                    @endif
                    @if(!empty($brand->youtube))
                        <li><a href="{{$brand->youtube}}" class="db youtube" target="_blank"></a></li>
                    @endif
                    @if(!empty($brand->linkedin))
                        <li><a href="{{$brand->linkedin}}" class="db linkedin" target="_blank"></a></li>
                    @endif
                    @if(!empty($brand->vimeo))
                        <li><a href="{{$brand->vimeo}}" class="db vimeo" target="_blank"></a></li>
                    @endif
                </ul>
            </div>
            <div class="cb"></div>
            <ul class="menu">
                <li class="first{{$alias == 'ads' ? ' active' : ''}}"><a href="{{$url}}" class="fsb fs18">{{trans('www.base.label.ads')}}</a></li><li{!!$alias == 'creatives' ? ' class="active"' : ''!!}><a href="{{$url.'/key-people'}}" class="fsb fs18">{{trans('www.base.label.key_people')}}</a></li><li{!!$alias == 'awards' ? ' class="active"' : ''!!}><a href="{{$url.'/awards'}}" class="fsb fs18">{{trans('www.base.label.awards')}}</a></li><li{!!$alias == 'vacancies' ? ' class="active"' : ''!!}><a href="{{$url.'/vacancies'}}" class="fsb fs18">{{trans('www.base.label.vacancies')}}</a></li><li{!!$alias == 'news' ? ' class="active"' : ''!!}><a href="{{$url.'/news'}}" class="fsb fs18">{{trans('www.base.label.news')}}</a></li><li{!!$alias == 'agencies' ? ' class="active"' : ''!!}><a href="{{$url.'/partner-agencies'}}" class="fsb fs18">{{trans('www.base.label.partner_agencies')}}</a></li><li class="last{{$alias == 'contacts' ? ' active' : ''}}"><a href="{{$url.'/contacts'}}" class="fsb fs18">{{trans('www.base.label.contacts')}}</a></li>
            </ul>

            @if($alias == 'ads' || $alias == 'creatives')
                @include('blocks.items', ['items' => $items])
            @elseif($alias == 'awards')
                @include('blocks.awards', ['awards' => $awards])
            @elseif($alias == 'vacancies')
                @include('blocks.vacancies', ['vacancies' => $vacancies])
            @endif

        </div>
        <div id="main-right" class="fr">
            {{--<div class="inner fr">--}}
                <div class="right-ad">
                    <a href="#"><img src="{{url('/imgs/temp/ad4.jpg')}}" alt="ad" /></a>
                </div>
                <div class="fb-like-box">
                    <div class="fb-page" data-href="https://web.facebook.com/aobpage" data-small-header="false" data-adapt-container-width="true" data-hide-cover="false" data-show-facepile="true"></div>
                </div>
            {{--</div>
            <div class="cb"></div>--}}
        </div>
        <div class="cb"></div>

    </div>

</div>

@stop