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
		@if(auth()->id() == writer_head || auth()->user()->hasRole('admin'))
		<li><a href="{{is_null(route('member.article.settings')) ? 'javascript:;' : route('member.article.settings')}}" class="waves-effect">
			<span class="hide-menu">@lang('article::app.settings')</span></a>
		</li>
		@endif
		@endif
	</ul>
</li>
@endif