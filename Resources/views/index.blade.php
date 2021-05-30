@extends('article::layouts.member-app')

@section('page-title')
<div class="row bg-title">
    <!-- .page title -->
    <div class="col-lg-8 col-md-5 col-sm-6 col-xs-12">
        <h4 class="page-title"><i class="{{ $pageIcon ?? '' }}"></i> {{ $pageTitle ?? '' }}
        </h4>
    </div>

    <!-- /.page title -->
    <!-- .breadcrumb -->
    <div class="col-lg-4 col-sm-6 col-md-7 col-xs-12 text-right">
        @if (auth()->user()->hasRole('admin') || auth()->id() == $writerHeadAssistant || auth()->id() == $writerHead)
        <a href="javascript:;" id="createArticle" 
        class="btn btn-outline btn-success btn-sm"><i class="fa fa-plus"
        aria-hidden="true"></i> Add New Articles</a>
        @endif
    </div>
    <!-- /.breadcrumb -->
</div>
@endsection

@push('head-script')
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/custom-select/custom-select.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.css') }}">
<style>
    .swal-footer {
        text-align: center !important;
    }
    #allTasks-table_wrapper .dt-buttons{
        display: none !important;
    }
    a{
        font-weight: 600;
    }
</style>
@endpush

@section('content')
<div class="row">

    <div class="col-md-12">
        <div class="white-box">
            @section('filter-section')
            <div class="row">
               <div class="col-md-12 p-b-10">
                <div class="form-group">
                    <a href="?type=All">
                        <button type="button" id="allTask" class="btn btn-info btn-sm col-md-12 @if(request()->type == 'All' || request()->type == null) active @endif"> All Articles</button>
                    </a>
                </div>
            </div>
            <div class="col-md-12">
                @if(auth()->id() == $publisher || auth()->id() == $outreachHead)
                @php($myArticles = count($all_articles->where('publisher', auth()->id())->where('publishing_status', '<>' , 1)))
                @else
                @php($myArticles = count($all_articles->where('assignee', auth()->id())->where('writing_status', 0)))
                @endif
                <div class="form-group">
                   <a href="?type=myArticles">
                    <button type="button" id="myArticles" class="btn btn-success btn-sm col-md-12 @if(request()->type == 'myArticles') active @endif"> 
                        My Articles @if($myArticles)
                        <span class="label label-primary text-dark"> {{$myArticles}}
                        </span>
                        @endif
                    </button>
                </a>
            </div>
        </div>

        @if (auth()->user()->hasRole('admin') || auth()->id() == $writerHeadAssistant || auth()->id() == $writerHead)
        <div class="col-md-12 p-t-10">
            @php($assignByMe = count($all_articles->where('creator', auth()->id())->where('writing_status', 0)))
            <div class="form-group">
                <a href="?type=assignByMe">
                    <button type="button" id="assignByMe" class="btn btn-primary btn-sm col-md-12 @if(request()->type == 'assignByMe') active @endif">Assign By Me @if($assignByMe)<span class="label label-primary text-dark">{{$assignByMe}}</span>@endif</button>
                </a>
            </div>
        </div>
        <div class="col-md-12 p-t-10">
            @php($overdueArticles = count($all_articles->where('writing_deadline', '<', date('Y-m-d'))->where('writing_status', 0)))
            <div class="form-group">
                <a href="?type=overdueArticles">
                    <button type="button" id="overdueArticles" class="btn btn-danger btn-sm col-md-12 @if(request()->type == 'overdueArticles') active @endif">Overdue Articles @if($overdueArticles)<span class="label label-danger">{{$overdueArticles}}</span>@endif</button>
                </a>
            </div>
        </div>
        <div class="col-md-12 p-t-10">
            @php($pendingAproval = count($all_articles->where('writing_status', 1)))
            <div class="form-group">
                <a href="?type=pendingAproval">
                    <button type="button" id="pendingAproval" class="btn btn-warning btn-sm col-md-12 @if(request()->type == 'pendingAproval') active @endif"> Pending Review @if($pendingAproval)<span class="label label-primary text-dark"> {{$pendingAproval}} </span>@endif</button>
                </a>
            </div>
        </div>
        @endif

        @if(auth()->user()->hasRole($inhouseWriterRole) || auth()->id() == $writerHead || auth()->user()->hasRole('admin'))
        <div class="col-md-12 p-t-10">
            <div class="form-group">
                <a href="?type=editable">
                    <button type="button" id="editable" class="btn btn-primary btn-sm col-md-12 @if(request()->type == 'editable') active @endif">
                        Editable Articles
                        @if(!is_null($editable_articles->first()))
                        <span class="label label-primary text-dark">
                            {{count($editable_articles)}}
                        </span>
                        @endif
                    </button>
                </a>
            </div>
        </div>

        <div class="col-md-12 p-t-10">
            <div class="form-group">
                <a href="?type=edited">
                    <button type="button" id="edited" class="btn btn-inverse btn-sm col-md-12 @if(request()->type == 'edited') active @endif">
                        Edited Articles
                    </button>
                </a>
            </div>
        </div>
        @endif

        @if (auth()->user()->hasRole($inhouseWriterRole) || auth()->id() == $writerHead || auth()->user()->hasRole($writerRole) || auth()->user()->hasRole('admin'))
        <div class="col-md-12 p-t-10">
            <div class="form-group">
                <a href="?type=completedArticles">
                    <button type="button" id="completedArticles" class="btn btn-default btn-sm col-md-12 @if(request()->type == 'completedArticles') active @endif">Completed Articles</button>
                </a>
            </div>
        </div>
        <div class="col-md-12 p-t-10">
            <div class="form-group">
                <a href="?type=unpaidArticles">
                    <button type="button" id="unpaidArticles" class="btn btn-success btn-sm col-md-12 @if(request()->type == 'unpaidArticles') active @endif">Unpaid Articles</button>
                </a>
            </div>
        </div>
        <div class="col-md-12 p-t-10">
            <div class="form-group">
                <a href="?type=paidArticles">
                    <button type="button" id="paidArticles" class="btn btn-success btn-sm col-md-12 @if(request()->type == 'paidArticles') active @endif">Paid Articles</button>
                </a>
            </div>
        </div>
        @endif

        @if (auth()->id() == $writerHeadAssistant || (!auth()->user()->hasRole($writerRole) && !auth()->user()->hasRole($inhouseWriterRole)))
        <form>
            <div class="col-md-12">
                <h5 class="box-title">@lang('app.selectDateRange') (Writing Deadline)</h5>
                <div class="input-daterange input-group" id="date-range">
                    <input type="text" name="startDate" class="form-control" id="start-date"  placeholder="@lang('app.startDate')" value="{{request()->startDate}}" />
                    <span class="input-group-addon bg-info b-0 text-white">@lang('app.to')</span>
                    <input type="text" name="endDate" class="form-control" id="end-date" placeholder="@lang('app.endDate')" value="{{request()->endDate}}" />
                </div>
            </div>

            <div class="col-md-12 p-t-10">
                <h5 style="margin-left: 5px;">Article Type</h5>
                <select class="select2 form-control col-md-6" name="type" id="type" data-style="form-control">
                    <option value="{{request()->type}}">Select Type</option>
                    <option value="All" {{ request()->type == 'All' ? 'selected' : '' }}>All Task</option>
                    <option value="assignByMe" {{ request()->type == 'assignByMe' ? 'selected' : '' }}>Assign By Me</option>
                    <option value="pendingAproval" {{ request()->type == 'pendingAproval' ? 'selected' : '' }}>Pending Aproval</option>
                    <option value="editable" {{ request()->type == 'editable' ? 'selected' : '' }}>Editable Aproval</option>
                    <option value="completedArticles" {{ request()->type == 'completedArticles' ? 'selected' : '' }}>Completed Articles</option>
                    <option value="paidArticles" {{ request()->type == 'paidArticles' ? 'selected' : '' }}>Paid Articles</option>
                    <option value="unpaidArticles" {{ request()->type == 'unpaidArticles' ? 'selected' : '' }}>Unpaid Articles</option>
                    <option value="writingNotStarted" {{ request()->type == 'writingNotStarted' ? 'selected' : '' }}>Not Started Writing</option>
                    <option value="writingStarted" {{ request()->type == 'writingStarted' ? 'selected' : '' }}>Started Writing</option>
                    <option value="waitingPublish" {{ request()->type == 'waitingPublish' ? 'selected' : '' }}>Waiting for Publish</option>
                    <option value="startedPublish" {{ request()->type == 'startedPublish' ? 'selected' : '' }}>Started Publishing</option>
                    <option value="completePublish" {{ request()->type == 'completePublish' ? 'selected' : '' }}>Completed Publishing</option>
                </select>
            </div>

            @if (auth()->id() == $writerHeadAssistant || auth()->id() == $writerHead || auth()->user()->hasRole($writerRole) || auth()->user()->hasRole('admin'))
            <div class="col-md-12 p-t-10">
                <h5 style="margin-left: 5px;">Writer</h5>
                <select class="select2 form-control col-md-6" name="writer" id="writer" data-style="form-control">
                    <option value="">Select Writer</option>
                    <option value="{{auth()->id()}}" @if(request()->writer == auth()->id()) selected @endif>{{auth()->user()->name}}</option>
                    @foreach ($writers as $writer)
                    <option value="{{$writer->id}}" @if(request()->writer == $writer->id) selected @endif>{{$writer->name}}</option>
                    @endforeach
                </select>
            </div>
            @endif

            <div class="col-md-12 p-t-10">
                <h5 style="margin-left: 5px;">Article Category</h5>
                <select class="select2 form-control col-md-6" name="category" id="category" data-style="form-control">
                    <option value="">Select Category</option>
                    @foreach ($categories as $category)
                    <option value="{{$category->name}}" @if(request()->category == $category->name) selected @endif>{{$category->name}}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-12 p-t-10">
                <h5 style="margin-left: 5px;">Project</h5>
                <select class="select2 form-control col-md-6" name="project" id="project" data-style="form-control">
                    <option value="">Select Project</option>
                    @foreach ($projects as $project)
                    <option value="{{$project->id}}" @if(request()->project == $project->id) selected @endif>{{$project->project_name}}</option>
                    @endforeach
                </select>
            </div>
            @if (auth()->id() != $publisher && !auth()->user()->hasRole($inhouseWriterRole) && auth()->id() != $outreachHead)
            <div class="col-md-12 m-t-10 m-b-10">
                <div class="checkbox checkbox-info">
                    <input type="checkbox" name="hide" id="hide-completed-tasks" {{ request()->hide == 'on' ? 'checked' : '' }}>
                    <label for="hide-completed-tasks">Hide Written Articles Only</label>
                </div>
            </div>
            @endif
            @if(request()->entries)
            <input type="hidden" name="entries" value="{{request()->entries ? request()->entries : 10}}">
            @endif
            <div class="col-md-12 m-b-10">
                <button id="apply-filters" class="btn btn-success btn-sm col-md-6 m-t-10"><i
                    class="fa fa-check"></i> Submit</button>
                </div>
            </form>
            @endif
        </div>
        @endsection

        <div class="row">
            <div class="col-md-3"> 
                Show 
                <select id="entries" class="form-control" style="width: 50%; display: inline;">
                    <option selected>{{request()->entries ? request()->entries : '...'}}</option>
                    <option @if(request()->entries == 10) selected @endif>10</option>
                    <option @if(request()->entries == 30) selected @endif>30</option>
                    <option @if(request()->entries == 50) selected @endif>50</option>
                    <option @if(request()->entries == 100) selected @endif>100</option>
                </select>
                entries
            </div>
            <div class="col-md-9">
                <form id="order">
                    @if(request()->type)
                    <input type="hidden" name="type" value="{{request()->type ? request()->type : ''}}">
                    @endif
                    @if(request()->writer)
                    <input type="hidden" name="writer" value="{{request()->writer ? request()->writer : 'all'}}">
                    @endif
                    @if(request()->hide)
                    <input type="hidden" name="hide" value="{{request()->hide ? request()->hide : 'off'}}">
                    @endif
                    @if(request()->category)
                    <input type="hidden" name="category" value="{{request()->category ? request()->category : ''}}">
                    @endif
                    @if(request()->project)
                    <input type="hidden" name="project" value="{{request()->project ? request()->project : 'off'}}">
                    @endif
                    @if(request()->q)
                    <input type="hidden" name="q" value="{{request()->q ? request()->q : ''}}">
                    @endif
                    @if(request()->entries)
                    <input type="hidden" name="entries" value="{{request()->entries ? request()->entries : 10}}">
                    @endif
                    @if(request()->startDate && request()->endDate)
                    <input type="hidden" name="startDate" value="{{request()->startDate}}">
                    <input type="hidden" name="endDate" value="{{request()->endDate}}">
                    @endif
                    <div class="col-md-2 text-right">Order By: </div>
                    <div class="col-md-3">
                        <select name="orderBy" id="orderBy" class="form-control">
                            <option value="{{request()->orderBy ? request()->orderBy : 'writing_deadline'}}" selected>{{request()->orderBy ? str_replace('_', ' ', strtoupper(request()->orderBy)) : 'WRITING DEADLINE'}}</option>
                            <option value="id">ID</option>
                            <option value="title" >TITLE</option>
                            <option value="assignee" >ASSIGNEE</option>
                            <option value="creator" >CREATOR</option>
                            <option value="word_count" >WORD COUNT</option>
                            <option value="priority" >PRIORITY</option>
                            <option value="writing_status" >WRITING STATUS</option>
                            <option value="writing_deadline" >WRITING DEADLINE</option>
                            <option value="publishing_deadline" >PUBLISHING DEADLINE</option>
                            <option value="created_at" >CREATED AT</option>
                        </select>
                    </div>
                    <div class="col-md-4" align="center">
                        <div class="col-md-5">
                            <button class="btn @if(request()->orderType == 'asc') btn-success @elseif(request()->orderType == null) btn-success @endif btn-sm" name="orderType" value="asc"><i class="fa fa-arrow-down"></i> Ascending</button>
                        </div>
                        <div class="col-md-5">
                            <button class="btn @if(request()->orderType == 'desc') btn-success @endif btn-sm" name="orderType" value="desc"><i class="fa fa-arrow-up"></i> Descending</button>
                        </div>
                    </div>
                </form>
                <div class="col-md-3" align="right">
                    <form method="get" id="search-form">
                        <input type="hidden" name="type" value="search">
                        <input type="text" name="q" class="form-control" placeholder="Type & Press Enter" value="{{request()->q}}">
                    </form>
                </div>
            </div>
        </div>

        <table class="table table-bordered table-hover">
            <thead>
                <tr role="row">
                    <th>#</th>
                    <th>Title</th>
                    @if(!auth()->user()->hasRole($writerRole))
                    <th width="20">Project</th>
                    @endif
                    <th>Status</th>
                    <th>Writer</th>
                    <th>Reviewed By</th>
                    @if(!auth()->user()->hasRole($writerRole) && request()->type !='pendingAproval')
                    <th>Published Link</th>
                    @endif
                    <th>Word Count</th>
                    @if(request()->type =='pendingAproval')
                    <th>Completed Writing</th>
                    @endif
                    <th>Parent Deadline</th>
                    <th>Deadline</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="list">
                @forelse ($articles as $article)
                <tr role="row" class="odd">
                    <td>{{$article->id}}</td>
                    <td>
                        <a target="_blank" href="{{route('member.article.show',$article->id)}}" class="@if($article->writing_status == 1) text-warning @endif" style="@if($article->writing_status == 2 && $article->assignee == auth()->id()) color: rgba(132,128,144,0.8); @elseif($article->publishing == 1 && $article->publishing_status == 1 && $article->writing_status == 2) color: rgba(132,128,144,0.8); @elseif($article->publishing != 1 && $article->writing_status == 2) color: rgba(132,128,144,0.8); @elseif($article->writing_status == 0) color: rgba(0,0,0,1); @endif">
                            {{$article->title}} 

                            @if($article->article_status == 1 && (!is_null($article->invoice) && $article->invoice->status != 1)) 
                            <label class="label label-primary">Unpaid</label>
                            @elseif($article->article_status == 1 && (!is_null($article->invoice) && $article->invoice->status == 1)) 
                            <label class="label label-info">Paid</label> 
                            @endif
                        </a>
                    </td>
                    @if(!auth()->user()->hasRole($writerRole))
                    <td>
                        <a target="_blank" href="{{route('member.projects.show', $article->project_id)}}" class="@if($article->writing_status == 1) text-warning @endif" style="font-weight: 350;@if($article->writing_status == 2 && $article->assignee == auth()->id()) color: rgba(132,128,144,0.8); @elseif($article->publishing == 1 && $article->publishing_status == 1 && $article->writing_status == 2) color: rgba(132,128,144,0.8); @elseif($article->publishing != 1 && $article->writing_status == 2) color: rgba(132,128,144,0.8); @elseif($article->writing_status == 0) color: rgba(0,0,0,1); @endif">{{$article->project->project_name}}</a>
                    </td>
                    @endif
                    <td>
                        @if($article->writing_status == 0 && $article->working_status == null) 
                        <label class="label label-primary">Not Started</label>
                        @elseif($article->writing_status == 0 && $article->working_status == 1)
                        <label class="label label-info">Working</label>
                        @elseif($article->writing_status == 1)

                        @if(!is_null($article->reviewWriter) && (is_null($article->reviewStatus) || $article->reviewStatus->value != 'completed'))
                        <label class="label label-primary">Under Review</label>
                        @elseif(!is_null($article->reviewStatus) && $article->reviewStatus->value == 'completed')
                        <label class="label label-success">Review Complete</label>
                        @else
                        <label class="label label-warning">Pending for Aproval</label> 
                        @endif

                        @elseif($article->writing_status == 2)
                        @if($article->publishing == 1 && !auth()->user()->hasRole($writerRole))

                        @if($article->publishing_status == 1)
                        <label class="label label-default" style="color: grey;">Completed Publishing</label>
                        @elseif($article->publishing_status != 1 && $article->publish != null)
                        <label class="label label-success">Started Publishing</label>
                        @else
                        <label class="label label-danger">Waiting for Publish</label>
                        @endif

                        @else
                        <label class="label label-default" style="color: grey;">Completed Writing</label>
                        @endif
                        @endif
                    </td>
                    <td>
                        @if(auth()->id() == $publisher && $article->writing_status == 2 && $article->publishing == 1) {{$article->getAssignee->name}} @else {{$article->getAssignee->name}} @endif
                    </td>
                    <td>
                        {{!is_null($article->reviewWriter) ? App\User::find($article->reviewWriter->value)->name : ''}}
                    </td>

                    @if(!auth()->user()->hasRole($writerRole) && request()->type !='pendingAproval')
                    <td>
                        @if($article->publish_link !=null && $article->publish_link !='')
                        <a target="_blank" href="{{$article->publish_link}}">Link</a>
                        @endif
                    </td>
                    @endif

                    <td>{{$article->word_count}}</td>

                    @if(request()->type =='pendingAproval')
                    <td>
                        @php($log = $article->logs->where('details', 'submitted the article for approval.')->first())
                        {{$log ? $log->created_at->format('d M Y') : '-'}}
                    </td>
                    @endif
                    <td>
                        <span class="@if($article->task && $article->task->due_date->isPast() && $article->writing_status !=2) text-danger @endif">
                            {{isset($article->task->due_date) ? $article->task->due_date->format('d M Y') : '--'}}
                        </span>
                    </td>
                    
                    <td>
                     @if(auth()->id() == $publisher)
                     <span class="@if(\Carbon\Carbon::parse($article->writing_deadline)->isPast() && $article->publishing_status !=1) text-danger @endif">
                        {{\Carbon\Carbon::parse($article->publishing_deadline)->format('d M Y')}}
                    </span>
                    @else
                    <span class="@if(\Carbon\Carbon::parse($article->writing_deadline)->isPast() && $article->writing_status !=2) text-danger @endif">
                        {{\Carbon\Carbon::parse($article->writing_deadline)->format('d M Y')}}
                    </span>
                    @endif
                </td>
                <td class=" text-center">
                    <div class="btn-group dropdown m-r-10">
                        <button aria-expanded="false" data-toggle="dropdown" class="btn dropdown-toggle waves-effect waves-light" type="button"><i class="ti-more"></i></button>
                        <ul role="menu" class="dropdown-menu pull-right">
                            <li><a href="javascript:;" onclick="view('{{$article->id}}')"><i class="fa fa-search" aria-hidden="true"></i> View</a></li>
                            @if (auth()->id() == $writerHeadAssistant || $writerHead == auth()->id() || auth()->user()->hasRole('admin'))
                            <li><a href="javascript:;" onclick="editArticle('{{$article->id}}')"><i class="fa fa-pencil" aria-hidden="true"></i> Edit</a></li>
                            @endif
                        </ul> 
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8">
                    No data found!
                </td>
            </tr>
            @endforelse
        </tbody>
        <tfoot style="border: 0px !important;">
            <tr style="border: 0px !important;">
               <td align="left" colspan="2" style="border: 0px !important; vertical-align: middle;">
                Showing {{($articles->currentPage()-1)*$articles->perPage()+1}} to {{$articles->currentPage()*$articles->perPage() > $articles->total() ? $articles->total() : $articles->currentPage()*$articles->perPage() }} of  {{$articles->total()}} articles
            </td>
            <td align="right" colspan="10" style="border: 0px !important;"> {{$articles->appends(['type' => request('type'), 'category' => request()->category, 'project' => request('project'), 'writer' => request('writer'), 'hide' => request('hide'), 'assignee' => request('assignee'), 'orderBy' => request('orderBy'), 'orderType' => request('orderType'), 'q' => request('q'), 'entries' => request('entries'), 'startDate' => request('startDate'), 'endDate' => request('endDate')])->render()}} </td>
        </tr>
    </tfoot>
</table>

<!-- Modal -->
<div class="modal fade" id="createType" tabindex="-1" role="dialog" aria-labelledby="createTypeModal" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        @lang('article::app.addArticleType')
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
      </button>
  </div>
  <form method="post" id="createTypeForm" action="{{route('member.article.createType')}}">
    @csrf
    <div class="modal-body">
        <label for="name"></label>
        <input type="text" id="name" class="form-control" name="name" placeholder="Name">
        <label for="description"></label>
        <textarea name="description" id="description" class="form-control" placeholder="Description"></textarea>
    </div>
</form>
<div class="modal-footer">
    <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
    <button class="btn btn-success" id="save-form">Save changes</button>
</div>
</div>
</div>
</div>

{{--Ajax Modal--}}
<div class="modal fade bs-modal-md in"  id="subTaskModal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md" id="modal-data-application">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <span class="caption-subject font-red-sunglo bold uppercase" id="subTaskModelHeading">Loading...</span>
            </div>
            <div class="modal-body">
                Loading...
            </div>
            <div class="modal-footer">
                <button type="button" class="btn default" data-dismiss="modal">Close</button>
                <button type="button" class="btn blue">Save changes</button>
            </div>
        </div>
    </div>
</div>
{{--Ajax Modal Ends--}}

@endsection

@push('footer-script')
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script src="{{ asset('plugins/bower_components/custom-select/custom-select.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
<script>

    jQuery('#date-range').datepicker({
        toggleActive: true,
        format: 'yyyy-mm-dd',
        language: '{{ $global->locale }}',
        autoclose: true,
        todayHighlight: true
    });

   $('#entries').on('change', function(){
    var url = '{{route('member.article.index')}}?{{request()->type ? 'type='.request()->type : ''}}{{request()->project ? '&project='.request()->project : ''}}{{request()->writer ? '&writer='.request()->writer : ''}}{{request()->category ? '&category='.request()->category : ''}}{{request()->q ? '&q='.request()->q : ''}}{{request()->hide ? '&hide='.request()->hide : ''}}{{request()->publish ? '&publish='.request()->publish : ''}}{{request()->orderBy ? '&orderBy='.request()->orderBy : ''}}{{request()->orderType ? '&orderType='.request()->orderType : ''}}{{request()->entries ? '&entries=:entries' : ''}}';

    var ent = '{{request()->entries}}';

    var url = url.replace(':entries', $(this).val()).replace(/&amp;/g, '&');
    if (ent.length ===0) {var url = url+'&entries='+$(this).val();}
    window.location.href = url;
})

   function view(id) {
    $(".right-sidebar").slideDown(50).addClass("shw-rside");
    var url = "{{ route('member.article.showModal',':id') }}";
    url = url.replace(':id', id);

    $.easyAjax({
        type: 'GET',
        url: url,
        success: function (response) {
            if (response.status == "success") {
                $('#right-sidebar-content').html(response.view);
            }
        }
    });
}

$('#createArticle').click(function () {
    var url = "{{ route('member.article.create') }}";
    $.ajaxModal('#subTaskModal', url);
})

function editArticle(id) {
    var url = "{{ route('member.article.edit',':id') }}";
    url = url.replace(':id', id);
    $.ajaxModal('#subTaskModal', url);
}

function deleteArticle(id) {
    var buttons = {
        cancel: "Cancel",
        confirm: {
            text: "Yes",
            visible: true,
            className: "danger",
        }
    };
    swal({
        title: "Are you sure want to delete?",
        text: "Please enter your password below:",
        dangerMode: true,
        icon: 'warning',
        buttons: buttons,
        content: "input"
    }).then(function (isConfirm) {
        if (isConfirm !=='' && isConfirm !==null) {
            var url = "{{ route('member.article.delete',':id') }}";
            var url = url.replace(':id', id);
            var token = "{{ csrf_token() }}";
            var dataObject = {'password': isConfirm, '_token': token, '_method': 'DELETE'};
            $.easyAjax({
                type: 'POST',
                url: url,
                data: dataObject,
                success: function (response) {
                    if (response.status == "success") {
                        swal("Success", "The task has been deletd!", "success");
                        $.unblockUI();
                        location.reload(true);
                    } else {
                        swal("Warning!", "The password you entered is incorrect!", "warning");
                    }
                }
            });
        }
        if (isConfirm ==='') {swal("Empty!", "You must enter your password!", "warning");}
    });
}


$( document ).ready(function(){
    var req = '{{request('view-article')}}';
    if (req !='') {
        $(".right-sidebar").slideDown(50).addClass("shw-rside");
        var url = "{{ route('member.article.showModal',':id') }}";
        url = url.replace(':id', req);

        $.easyAjax({
            type: 'GET',
            url: url,
            success: function (response) {
                if (response.status == "success") {
                    $('#right-sidebar-content').html(response.view);
                }
            }
        });
    }
})

     //Copy link
     function copyLink(){
        var button = document.getElementById('copyLink');
        var copyText = document.getElementById("copyText");
        copyText.style.display ='inline';
        copyText.select();
        copyText.setSelectionRange(0, 99999)
        document.execCommand("copy");
        copyText.style.display ='none';
        button.innerHTML = '<i class="fa fa-link"></i> Copied';
    }

    $("#type").select2({
        formatNoMatches: function () {
            return "{{ __('messages.noRecordFound') }}";
        }
    });

    $("#publish").select2({
        formatNoMatches: function () {
            return "{{ __('messages.noRecordFound') }}";
        }
    });

    $("#category").select2({
        formatNoMatches: function () {
            return "{{ __('messages.noRecordFound') }}";
        }
    });

    $("#writer").select2({
        formatNoMatches: function () {
            return "{{ __('messages.noRecordFound') }}";
        }
    });

    $("#project").select2({
        formatNoMatches: function () {
            return "{{ __('messages.noRecordFound') }}";
        }
    });

    $("#orderBy").select2({
        formatNoMatches: function () {
            return "{{ __('messages.noRecordFound') }}";
        }
    });
</script>
@endpush
