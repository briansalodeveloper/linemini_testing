@if(!empty(config('const.pageBreadCrumbs')[_isRoute()]) || trim($__env->yieldContent('contentHeader')) || trim($__env->yieldContent('contentHeaderTitle')))
    <div class="content-header">
        @if(!empty(config('const.pageBreadCrumbs')[_isRoute()]))
            @if(!empty(config('const.pageBreadCrumbs')[_isRoute()]['routes']))
                <ol class="breadcrumb float-sm-right">
                    @if(count(config('const.pageBreadCrumbs')[_isRoute()]['routes']) != 0)
                        @foreach(config('const.pageBreadCrumbs')[_isRoute()]['routes'] as $title => $route)
                            @php
                                $_title = explode(',', $title);
                                $title = (count($_title) > 1 ? $_title[1] . ' ' : '') . __($_title[0]);
                            @endphp
                            @if (empty($route))
                                <li class="breadcrumb-item">{!! $title !!}</li>
                            @elseif(_isRoute($route))
                                <li class="breadcrumb-item active">{!! $title !!}</li>
                            @else
                                @if(is_array($route))
                                    <li class="breadcrumb-item"><a href="{{ route($route[0], $route[1]) }}">{!! $title !!}</a></li>
                                @else
                                    <li class="breadcrumb-item"><a href="{{ route($route) }}">{!! $title !!}</a></li>
                                @endif
                            @endif
                        @endforeach
                        <li class="breadcrumb-item active">
                            @if(trim($__env->yieldContent('contentHeaderTitle')))
                                @yield('contentHeaderTitle')
                            @elseif(!empty(config('const.pageBreadCrumbs')[_isRoute()]))
                                @php
                                    $_title = config('const.pageBreadCrumbs')[_isRoute()]['title'];
                                    $_title = explode(',', $_title);
                                    $title = (count($_title) > 1 ? $_title[1] . ' ' : '') . __($_title[0]);
                                @endphp
                                {!! $title !!}
                            @else
                                &nbsp;
                            @endif
                        </li>
                    @endif
                </ol>
            @endif
        @endif
        <div class="container-fluid">
            <div class="row">
                <div class="px-4 col-md-12 d-flex justify-content-between">
                    @if(trim($__env->yieldContent('contentHeaderTitle')))
                        <h1 class="m-0">@yield('contentHeaderTitle')</h1>
                    @elseif(!empty(config('const.pageBreadCrumbs')[_isRoute()]))
                        @php
                            $_title = config('const.pageBreadCrumbs')[_isRoute()]['title'];
                            $_title = explode(',', $_title);
                            $title = (count($_title) > 1 ? $_title[1] . ' ' : '') . __($_title[0]);
                        @endphp
                        <h1 class="m-0">{!! $title !!}</h1>
                    @else
                        <h1>&nbsp;</h1>
                    @endif
                    @yield('contentHeader')
                </div>
            </div>
        </div>
    </div>
@endif