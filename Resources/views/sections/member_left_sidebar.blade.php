
@if(user()->is_writer() && strpos(request()->url(), 'article-management') =='')
<script type="text/javascript">
	window.location.href = '{{route('member.article.index')}}';
</script>
@endif

@if(user()->is_writer_head_assistant() || user()->is_writer_head() || user()->is_publisher() || user()->hasRole('admin') || user()->is_writer() || user()->is_inhouse_writer() || user()->is_outreach_member())
<li><a href="javascript:;" class="waves-effect"><i class="ti-pencil"></i> <span class="hide-menu"> @lang('article::app.menu.article') <span class="fa arrow"></span></span></a>
	<ul class="nav nav-second-level collapse">
		<li><a href="{{is_null(route('member.article.index')) ? 'javascript:;' : route('member.article.index')}}" class="waves-effect">
			<span class="hide-menu">@lang('article::app.articles')</span></a>
		</li>

		@if(user()->is_writer_head_assistant() || user()->is_writer_head() || user()->hasRole('admin'))
		<li><a href="{{route('member.article.dailyReports')}}" class="waves-effect">
			<span class="hide-menu">@lang('article::app.dailyReports')</span></a>
		</li>
		<li><a href="{{is_null(route('member.article.reports')) ? 'javascript:;' : route('member.article.reports')}}" class="waves-effect">
			<span class="hide-menu">@lang('article::app.reports')</span></a>
		</li>
		@endif
		@if(user()->is_writer_head_assistant() || user()->is_writer_head() || user()->hasRole('admin') || user()->is_writer() || user()->is_inhouse_writer())
		<li><a href="{{is_null(route('member.article.writers')) ? 'javascript:;' : route('member.article.writers')}}" class="waves-effect">
			<span class="hide-menu">@if(user()->is_writer_head() || user()->is_writer_head_assistant() || auth()->user()->hasRole('admin')) @lang('article::app.writers') @else Profile @endif</span></a>
		</li>
		@if(user()->is_writer_head_assistant() || user()->is_writer_head() || user()->hasRole('admin') || user()->is_writer())
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

		@if(user()->is_writer_head_assistant() || user()->is_writer_head() || user()->hasRole('admin'))
		<li><a href="{{is_null(route('member.article.settings')) ? 'javascript:;' : route('member.article.settings')}}" class="waves-effect">
			<span class="hide-menu">@lang('article::app.settings')</span></a>
		</li>
		@endif
		@endif
	</ul>
</li>
@endif