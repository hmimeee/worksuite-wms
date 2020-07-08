<li><a href="javascript:;" class="waves-effect"><i class="ti-pencil"></i> <span class="hide-menu"> @lang('article::app.menu.article')<span class="fa arrow"></span> </span></a>
	<ul class="nav nav-second-level collapse">
		<li><a href="{{is_null(route('admin.article.index')) ? 'javascript:;' : route('admin.article.index')}}" class="waves-effect">
			<span class="hide-menu">@lang('article::app.articles')</span></a>
		</li>
		<li><a href="{{is_null(route('admin.article.reports')) ? 'javascript:;' : route('admin.article.reports')}}" class="waves-effect">
			<span class="hide-menu">@lang('article::app.reports')</span></a>
		</li>
		<li><a href="{{is_null(route('admin.article.writers')) ? 'javascript:;' : route('admin.article.writers')}}" class="waves-effect">
			<span class="hide-menu">@lang('article::app.writers')</span></a>
		</li>
		<li><a href="{{is_null(route('admin.article.invoices')) ? 'javascript:;' : route('admin.article.invoices')}}" class="waves-effect">
			<span class="hide-menu">@lang('article::app.invoices')</span></a>
		</li>
		<li><a href="{{is_null(route('admin.article.settings')) ? 'javascript:;' : route('admin.article.settings')}}" class="waves-effect">
			<span class="hide-menu">@lang('article::app.settings')</span></a>
		</li>
	</ul>
</li>

