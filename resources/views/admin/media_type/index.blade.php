<?php
$head->appendScript('/admin/media_type/media_type.js');
$pageTitle = $pageSubTitle = trans('admin.media_type.form.title');
$pageMenu = 'media_type';
?>
@extends('core.layout')
@section('navButtons')
    <a href="{{route('admin_media_type_create')}}" class="btn btn-primary pull-right">{{trans('admin.base.label.add')}}</a>
@stop
@section('content')
<div class="box-body">
    <table id="data-table" class="table table-bordered table-striped table-hover">
        <thead>
        <tr>
            <th>{{trans('admin.base.label.id')}}</th>
            <th>{{trans('admin.base.label.title')}}</th>
            @if(Auth::guard('admin')->check() && Auth::guard('admin')->user()->isSuperAdmin())
                <th>{{trans('admin.base.label.created_by')}}</th>
                <th>{{trans('admin.base.label.updated_by')}}</th>
            @endif
            <th class="th-actions"></th>
        </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>
@stop