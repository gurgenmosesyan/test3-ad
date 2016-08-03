<ul class="sidebar-menu">
    <li{{$pageMenu == 'media_type' ? ' class=active' : ''}}><a href="{{route('admin_media_type_table')}}"><i class="fa fa-pencil"></i> <span>{{trans('admin.media_type.form.title')}}</span></a></li>
    <li{{$pageMenu == 'industry_type' ? ' class=active' : ''}}><a href="{{route('admin_industry_type_table')}}"><i class="fa fa-pencil"></i> <span>{{trans('admin.industry_type.form.title')}}</span></a></li>
    <li{{$pageMenu == 'category' ? ' class=active' : ''}}><a href="{{route('admin_category_table')}}"><i class="fa fa-pencil"></i> <span>{{trans('admin.category.form.title')}}</span></a></li>
    <li class="treeview{{$pageMenu == 'admin' || $pageMenu == 'language' || $pageMenu == 'dictionary' ? ' active' : ''}}">
        <a href="#">
            <i class="fa fa-wrench"></i> <span>{{trans('admin.admin_menu.system')}}</span> <i class="fa fa-angle-left pull-right"></i>
        </a>
        <ul class="treeview-menu">
            <li{{$pageMenu == 'admin' ? ' class=active' : ''}}><a href="{{route('core_admin_table')}}"><i class="fa fa-user"></i> {{trans('admin.admin.form.title')}}</a></li>
            <li{{$pageMenu == 'language' ? ' class=active' : ''}}><a href="{{route('core_language_table')}}"><i class="fa fa-flag"></i> {{trans('admin.language.form.title')}}</a></li>
            <li{{$pageMenu == 'dictionary' ? ' class=active' : ''}}><a href="{{route('core_dictionary_table')}}"><i class="fa fa-book"></i> {{trans('admin.dictionary.form.title')}}</a></li>
        </ul>
    </li>
</ul>