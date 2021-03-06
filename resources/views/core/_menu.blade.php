<?php
$isAdmin = Auth::guard('admin')->check();
$admin = Auth::guard('admin')->user();
$isCreative = Auth::guard('creative')->check();
?>
<ul class="sidebar-menu">
    @if($isAdmin)
        @if($admin->isRoleAdmin())
            <li class="treeview{{$pageMenu == 'media_type' || $pageMenu == 'industry_type' || $pageMenu == 'category' || $pageMenu == 'agency_category' ? ' active' : ''}}">
                <a href="#">
                    <i class="fa fa-pencil"></i> <span>{{trans('admin.admin_menu.options')}}</span> <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu">
                    <li{{$pageMenu == 'media_type' ? ' class=active' : ''}}><a href="{{route('admin_media_type_table')}}"><i class="fa fa-pencil"></i> <span>{{trans('admin.media_type.form.title')}}</span></a></li>
                    <li{{$pageMenu == 'category' ? ' class=active' : ''}}><a href="{{route('admin_category_table')}}"><i class="fa fa-pencil"></i> <span>{{trans('admin.industry_type.form.title')}}</span></a></li>
                    <li{{$pageMenu == 'agency_category' ? ' class=active' : ''}}><a href="{{route('admin_agency_category_table')}}"><i class="fa fa-pencil"></i> <span>{{trans('admin.agency_category.form.title')}}</span></a></li>
                </ul>
            </li>
        @endif
        <li{{$pageMenu == 'brand' ? ' class=active' : ''}}><a href="{{route('admin_brand_table')}}"><i class="fa fa-btc"></i> <span>{{trans('admin.brand.form.title')}}</span></a></li>
        <li{{$pageMenu == 'agency' ? ' class=active' : ''}}><a href="{{route('admin_agency_table')}}"><i class="fa fa-briefcase"></i> <span>{{trans('admin.agency.form.title')}}</span></a></li>
    @else
        <?php
        if (Auth::guard('brand')->check()) {
            $brand = Auth::guard('brand')->user();
            $profileLink = route('admin_brand_edit', $brand->id);
            $class = $pageMenu == 'brand' ? ' class="active"' : '';
        } else if (Auth::guard('agency')->check()) {
            $agency = Auth::guard('agency')->user();
            $profileLink = route('admin_agency_edit', $agency->id);
            $class = $pageMenu == 'agency' ? ' class="active"' : '';
        } else {
            $creative = Auth::guard('creative')->user();
            $profileLink = route('admin_creative_edit', $creative->id);
            $class = $pageMenu == 'creative' ? ' class="active"' : '';
        }
        ?>
        <li{!!$class!!}><a href="{{$profileLink}}"><i class="fa fa-user"></i> <span>{{trans('admin.profile.title')}}</span></a></li>
    @endif
    <li{{$pageMenu == 'commercial' ? ' class=active' : ''}}><a href="{{route('admin_commercial_table')}}"><i class="fa fa-bullhorn"></i> <span>{{trans('admin.commercial.form.title')}}</span></a></li>
    @if(!$isCreative)
        <li{{$pageMenu == 'creative' ? ' class=active' : ''}}><a href="{{route('admin_creative_table')}}"><i class="fa fa-user"></i> <span>{{trans('admin.creative.form.title')}}</span></a></li>
    @endif
    @if(!$isAdmin || $admin->isRoleAdmin())
        <li{{$pageMenu == 'award' ? ' class=active' : ''}}><a href="{{route('admin_award_table')}}"><i class="fa fa-gift"></i> <span>{{trans('admin.award.form.title')}}</span></a></li>
    @endif
    @if(!$isCreative && (!$isAdmin || $admin->isRoleAdmin()))
        <li{{$pageMenu == 'vacancy' ? ' class=active' : ''}}><a href="{{route('admin_vacancy_table')}}"><i class="fa fa-cube"></i> <span>{{trans('admin.vacancy.form.title')}}</span></a></li>
        <li{{$pageMenu == 'branch' ? ' class=active' : ''}}><a href="{{route('admin_branch_table')}}"><i class="fa fa-sitemap"></i> <span>{{trans('admin.branch.form.title')}}</span></a></li>
    @endif
    @if($isAdmin)
        <li{{$pageMenu == 'news' ? ' class=active' : ''}}><a href="{{route('admin_news_table')}}"><i class="fa fa-rss"></i> <span>{{trans('admin.news.form.title')}}</span></a></li>
        @if($admin->isRoleAdmin())
            <li{{$pageMenu == 'team' ? ' class=active' : ''}}><a href="{{route('admin_team_table')}}"><i class="fa fa-group"></i> <span>{{trans('admin.team.form.title')}}</span></a></li>
            <li{{$pageMenu == 'banner' ? ' class=active' : ''}}><a href="{{route('admin_banner_table')}}"><i class="fa fa-bookmark"></i> <span>{{trans('admin.banner.form.title')}}</span></a></li>
            <li{{$pageMenu == 'footer_menu' ? ' class=active' : ''}}><a href="{{route('admin_footer_menu_table')}}"><i class="fa fa-reorder"></i> <span>{{trans('admin.footer_menu.form.title')}}</span></a></li>
            <li{{$pageMenu == 'subscribe' ? ' class=active' : ''}}><a href="{{route('admin_subscribe_table')}}"><i class="fa fa-envelope"></i> <span>{{trans('admin.subscribe.form.title')}}</span></a></li>
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
        @endif
    @endif
</ul>