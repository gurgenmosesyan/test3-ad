<?php
$head->appendScript('/core/js/admin.js');
$pageTitle = $pageSubTitle = trans('admin.admin.form.title');
$pageMenu = 'admin';
?>
@extends('core.layout')
@section('navButtons')
    <a href="{{route('core_admin_create')}}" class="btn btn-primary pull-right">{{trans('admin.base.label.add')}}</a>
@stop
@section('content')
<div class="box-body">
    <table id="data-table" class="table table-bordered table-striped table-hover">
        <thead>
        <tr>
            <th>{{trans('admin.base.label.id')}}</th>
            <th>{{trans('admin.base.label.email')}}</th>
            <th>{{trans('admin.base.label.super_admin')}}</th>
            <th class="th-actions"></th>
        </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>
@stop