<div class="navbar-default sidebar" role="navigation">
    <div class="navbar-header">
        <!-- Toggle icon for mobile view -->
        <a class="navbar-toggle hidden-sm hidden-md hidden-lg " href="javascript:void(0)" data-toggle="collapse"
        data-target=".navbar-collapse"><i class="ti-menu"></i></a>

        <div class="top-left-part">
            <!-- Logo -->
            <a class="logo hidden-xs text-center" href="{{ route('admin.dashboard') }}">
                <span class="visible-md"><img src="{{ $global->logo_url }}" alt="home" class=" admin-logo"/></span>
                <span class="visible-sm"><img src="{{ $global->logo_url }}" alt="home" class=" admin-logo"/></span>
            </a>

        </div>

    </div>
    <!-- /.navbar-header -->

    <div class="top-left-part">
        <a class="logo hidden-xs hidden-sm text-center" href="{{ route('admin.dashboard') }}">
            <img src="{{ $global->logo_url }}" alt="home" class=" admin-logo"/>
        </a>
    </div>
    <div class="sidebar-nav navbar-collapse slimscrollsidebar">
        <!-- .User Profile -->
        <ul class="nav" id="side-menu">
            {{--<li class="sidebar-search hidden-sm hidden-md hidden-lg">--}}
                {{--<!-- / Search input-group this is only view in mobile-->--}}
                {{--<div class="input-group custom-search-form">--}}
                    {{--<input type="text" class="form-control" placeholder="Search...">--}}
                    {{--<span class="input-group-btn">--}}
                        {{--<button class="btn btn-default" type="button"> <i class="fa fa-search"></i> </button>--}}
                    {{--</span>--}}
                {{--</div>--}}
                {{--<!-- /input-group -->--}}
            {{--</li>--}}

            <li class="user-pro  hidden-sm hidden-md hidden-lg">
                @if(is_null($user->image))
                <a href="#" class="waves-effect"><img src="{{ asset('img/default-profile-3.png') }}" alt="user-img" class="img-circle"> <span class="hide-menu">{{ (strlen($user->name) > 24) ? substr(ucwords($user->name), 0, 20).'..' : ucwords($user->name) }}
                    <span class="fa arrow"></span></span>
                </a>
                @else
                <a href="#" class="waves-effect"><img src="{{ asset_url('avatar/'.$user->image) }}" alt="user-img" class="img-circle"> <span class="hide-menu">{{ ucwords($user->name) }}
                    <span class="fa arrow"></span></span>
                </a>
                @endif
                <ul class="nav nav-second-level">
                    <li><a href="{{ route('member.article.writers') }}"><i class="ti-user"></i> @lang("app.menu.profileSettings")</a></li>
                    <li role="separator" class="divider"></li>
                    <li><a href="{{ route('logout') }}" onclick="event.preventDefault();
                    document.getElementById('logout-form').submit();"
                    ><i class="fa fa-power-off"></i> @lang('app.logout')</a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        {{ csrf_field() }}
                    </form>
                </li>
            </ul>
        </li>
        @php ($writer = \Modules\Article\Entities\ArticleSetting::where('type', 'writer')->first()->value)
        @php ($inhouseWriter = \Modules\Article\Entities\ArticleSetting::where('type', 'inhouse_writer')->first()->value)
        @php ($head = \Modules\Article\Entities\ArticleSetting::where('type', 'writer_head')->first()->value)
        @php ($publisherHead = \Modules\Article\Entities\ArticleSetting::where('type', 'publisher')->first()->value)

        @if(auth()->user()->hasRole($writer) && strpos(request()->url(), 'article-management') =='')
        <script type="text/javascript">
            window.location.href = '{{route('member.article.index')}}';
        </script>
        @endif

        @foreach(\Modules\Article\Entities\ArticleSetting::all() as $setting)
        @php(define($setting->type, $setting->value))
        @endforeach


        @if(auth()->user()->hasRole(writer) && strpos(request()->url(), 'article-management') =='')
        <script type="text/javascript">
            window.location.href = '{{route('member.article.index')}}';
        </script>
        @endif

        @if(auth()->id() == writer_head || auth()->id() == publisher || auth()->user()->hasRole('admin') || auth()->user()->hasRole(writer) || auth()->user()->hasRole(inhouse_writer) || auth()->id() == outreach_head)
        <li><a href="javascript:;" class="waves-effect"><i class="ti-pencil"></i> <span class="hide-menu"> @lang('article::app.menu.article')<span class="frow"></span> </span></a>
            <ul class="nav nav-second-level collapse">
                <li><a href="{{is_null(route('member.article.index')) ? 'javascript:;' : route('member.article.index')}}" class="waves-effect">
                    <span class="hide-menu">@lang('article::app.articles')</span></a>
                </li>
                @if(auth()->id() == writer_head || auth()->user()->hasRole('admin'))
                <li><a href="{{is_null(route('member.article.reports')) ? 'javascript:;' : route('member.article.reports')}}" class="waves-effect">
                    <span class="hide-menu">@lang('article::app.reports')</span></a>
                </li>
                @endif
                @if(auth()->id() != publisher && auth()->id() != outreach_head)
                <li><a href="{{is_null(route('member.article.writers')) ? 'javascript:;' : route('member.article.writers')}}" class="waves-effect">
                    <span class="hide-menu">@if(!auth()->user()->hasRole(writer) && !auth()->user()->hasRole(inhouse_writer)) @lang('article::app.writers') @else Profile @endif</span></a>
                </li>
                @if(!auth()->user()->hasRole(inhouse_writer))
                <li><a href="{{route('member.article.leaves')}}" class="waves-effect">
                    <span class="hide-menu">@lang('article::app.leaves')</span></a>
                </li>
                <li><a href="{{is_null(route('member.article.invoices')) ? 'javascript:;' : route('member.article.invoices')}}" class="waves-effect">
                    <span class="hide-menu">@lang('article::app.invoices')</span></a>
                </li>
                @endif
                
                <li><a href="{{route('member.article.sop')}}" class="waves-effect">
                    <span class="hide-menu">@lang('article::app.sop')</span></a>
                </li>

                @if(auth()->id() == writer_head || auth()->user()->hasRole('admin'))
                <li><a href="{{is_null(route('member.article.settings')) ? 'javascript:;' : route('member.article.settings')}}" class="waves-effect">
                    <span class="hide-menu">@lang('article::app.settings')</span></a>
                </li>
                @endif
                @endif
            </ul>
        </li>
        @endif
    </ul>

    <div class="menu-footer">
        <div class="menu-user row">
            <div class="col-lg-6 m-b-5">
                <div class="btn-group dropup user-dropdown">
                    @if(is_null($user->image))
                    <img  aria-expanded="false" data-toggle="dropdown" src="{{ asset('img/default-profile-3.png') }}" alt="user-img" class="img-circle dropdown-toggle h-30 w-30">

                    @else
                    <img aria-expanded="false" data-toggle="dropdown" src="{{ asset_url('avatar/'.$user->image) }}" alt="user-img" class="img-circle dropdown-toggle h-30 w-30">

                    @endif
                    <ul role="menu" class="dropdown-menu">
                        <li><a class="bg-inverse"><strong class="text-info">{{ ucwords($user->name) }}</strong></a></li>
                        <li><a href="{{ route('logout') }}" onclick="event.preventDefault();
                        document.getElementById('logout-form').submit();"
                        ><i class="fa fa-power-off"></i> @lang('app.logout')</a>

                    </li>

                </ul>
            </div>
        </div>

    </div>
    


</div>
</div>
</div>
