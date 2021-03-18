@extends('layouts.app')

@section('page-title')
<div class="row bg-title">
    <!-- .page title -->
    <div class="col-lg-8 col-md-5 col-sm-6 col-xs-12">
        <h4 class="page-title"><i class="{{ $pageIcon ?? '' }}"></i> {{ $pageTitle ?? '' }} 
        </h4>
    </div>

    <!-- /.page title -->
</div>
@endsection

@push('head-script')
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/custom-select/custom-select.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.css') }}">
<style type="text/css">
    @media print {
   .navbar-default, .filter-section, #mobile-filter-toggle {
       display: none;
    }
}
</style>
@endpush

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="white-box">

            <div class="col-sm-2">
                <h4>
                    <span class="font-12 text-muted m-l-5">Total Articles: </span>
                    <span class="text-info btn btn-light">{{ $articles ? count($articles) : '0' }}</span>
                </h4>
            </div>

            <div class="col-sm-2">
                <h4>
                    <span class="font-12 text-muted m-l-5">Total Words: </span>
                    <span class="text-info btn btn-light">{{ $words ?? '0' }}</span>
                </h4>
            </div>

            <div class="col-sm-3">
                <h4>
                    <span class="font-12 text-muted m-l-5">Total Cost (BDT): </span>
                    <span class="text-info btn btn-light">{{ number_format($cost, 2) ?? '0' }}</span>
                </h4>
            </div>

            <div class="col-sm-4">
                <h4>
                    <span class="font-12 text-muted m-l-5">Date Between: </span>
                    <span class="text-info btn btn-light">{{\Carbon\Carbon::create($startDate)->format('d M Y')}} - {{\Carbon\Carbon::create($endDate)->format('d M Y')}}</span>
                </h4>
            </div>

            <div class="col-sm-1" align="right">
                <h4>
                    <form action="{{route('admin.article.reportPrint')}}" target="_blank">
                        <input type="hidden" name="start_date" value="{{request()->start_date}}">
                        <input type="hidden" name="end_date" value="{{request()->end_date}}">
                        <input type="hidden" name="project" value="{{request()->project}}">
                        <input type="hidden" name="writer" value="{{request()->writer}}">
                        <input type="hidden" name="assignee" value="{{request()->assignee}}">
                        <button class="btn btn-info btn-sm"><i class="fa fa-print"></i></button>
                    </form>
                </h4>
            </div>

            @section('filter-section')
            <div class="row">
                <form>
                    <div class="col-md-12">
                        <label>Date Range</label>
                        <div class="form-group">
                            <div class="input-daterange input-group" id="date-range">
                                <input type="text" name="start_date" class="form-control" id="start-date" placeholder="@lang('app.startDate')"
                                value="{{$startDate}}"/>
                                <span class="input-group-addon bg-info b-0 text-white">@lang('app.to')</span>
                                <input type="text" name="end_date" class="form-control" id="end-date" placeholder="@lang('app.endDate')"
                                value="{{$endDate}}"/>
                            </div>
                            <!-- <input type="text" name="start_date" class="form-control" id="start_date" autocomplete="off"> -->
                        </div>
                    </div>

                    <div class="col-md-12">
                        <label>Project</label>
                        <div class="form-group">
                            <select class="form-control select2" name="project" id="project">
                                <option value="">Select Project</option>
                                @foreach($projects as $project)
                                <option value="{{$project->id}}" @if(request()->project == $project->id) selected @endif>{{$project->project_name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <label>Writer Role</label>
                        <div class="form-group">
                            <select class="form-control select2" name="writer" id="writer">
                                <option value="">Select Writer Role</option>
                                <option value="{{$inhouseWriterRole}}" @if($inhouseWriterRole == request()->writer) selected @endif>Inhouse Writers</option>
                                <option value="{{$writerRole}}" @if($writerRole == request()->writer) selected @endif>Remote Writers</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <label>Writer</label>
                        <div class="form-group">
                            <select class="form-control select2" name="assignee" id="writer">
                                <option value="">Select Writer</option>
                                @foreach($writers as $writer)
                                <option value="{{$writer->id}}" {{request()->assignee == $writer->id ? 'selected' : ''}}>{{$writer->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="form-group">
                            <button class="btn btn-success">Submit</button>
                        </div>
                    </div>
                </form>
            </div>
            @endsection

            <table class="table table-bordered table-hover">
                <thead>
                    <tr role="row">
                        <th>#</th>
                        <th>Title</th>
                        <th>Project</th>
                        <th>Writer</th>
                        <th>Published Link</th>
                        <th>Word Count</th>
                        <th>Deadline</th>
                        <th>Completed</th>
                    </tr>
                </thead>
                <tbody id="list">
                    @forelse ($articles as $article)
                    <tr role="row" class="odd">
                        <td>{{$article->id}}</td>
                        <td>
                            <a target="_blank" href="{{route('admin.article.show',$article->id)}}">{{$article->title}}
                            </a>
                        </td>
                        <td>
                            <a target="_blank" href="{{route('member.projects.show', $article->project_id)}}">{{$article->project->project_name}}</a>
                        </td>
                        <td>{{App\User::find($article->assignee)->name}}</td>
                        <td>
                            @if($article->publish_link !=null && $article->publish_link !='')
                            <a target="_blank" href="{{$article->publish_link}}">Link</a>
                            @else
                            --
                            @endif
                        </td>
                        <td>{{$article->word_count}}</td>
                        <td>
                            <span>
                                {{\Carbon\Carbon::parse($article->writing_deadline)->format('d M Y')}}
                            </span>
                        </td>
                        <td>
                            <span>
                                {{$article->completedLog->first()->created_at->format('d M Y')}}
                            </span>
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
<script src="{{ asset('plugins/bower_components/custom-select/custom-select.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
<script type="text/javascript">
    jQuery('#date-range').datepicker({
        toggleActive: true,
        format: 'yyyy-mm-dd',
        autoclose: true
    });

    $("#project.select2").select2({
        formatNoMatches: function () {
            return "{{ __('messages.noRecordFound') }}";
        }
    });

    $("#writer.select2").select2({
        formatNoMatches: function () {
            return "{{ __('messages.noRecordFound') }}";
        }
    });
</script>
@endpush
