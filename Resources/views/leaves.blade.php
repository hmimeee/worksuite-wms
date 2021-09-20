@extends('article::layouts.member-app')

@section('page-title')
<div class="row bg-title">
    <!-- .page title -->
    <div class="col-lg-8 col-md-4 col-sm-4 col-xs-12">
        <h4 class="page-title"><i class="{{ $pageIcon }}"></i> {{ $pageTitle }} </h4>
    </div>
    <!-- /.page title -->
    <!-- .breadcrumb -->
    <div class="col-lg-4 col-sm-8 col-md-8 col-xs-12 text-right">
        @if($user->id == $writerHead || $user->hasRole('admin') || $user->hasRole($writerRole))
        <button class="btn btn-outline btn-success btn-sm pull-right" data-toggle="modal" data-target="#applyLeaveModal"><i class="fa fa-plus"></i> Apply Leave</button>
        @endif
    </div>
    <!-- /.breadcrumb -->
</div>
@endsection

@push('head-script')
<link rel="stylesheet" href="{{ asset('plugins/bower_components/summernote/dist/summernote.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/custom-select/custom-select.css') }}">
<style type="text/css">
    .swal-footer {
        text-align: center !important;
    }
</style>
@endpush

@section('content')

<div class="row dashboard-stats">
    <div class="col-md-12 m-b-30">
        <div class="white-box">
            @section('filter-section')
            <div class="row">
                <form>
                    @if(request()->entries != null)
                    <input type="hidden" name="entries" value="{{request()->entries}}">
                    @endif
                    
                    <div class="col-md-12">
                        <label>Date Range</label>
                        <div class="form-group">
                            <div class="input-daterange input-group" id="date-range">
                                <input type="text" name="startDate" class="form-control" id="start-date" placeholder="@lang('app.startDate')"
                                value="{{$startDate}}"/>
                                <span class="input-group-addon bg-info b-0 text-white">@lang('app.to')</span>
                                <input type="text" name="endDate" class="form-control" id="end-date" placeholder="@lang('app.endDate')"
                                value="{{$endDate}}"/>
                            </div>
                            <!-- <input type="text" name="start_date" class="form-control" id="start_date" autocomplete="off"> -->
                        </div>
                    </div>
                    @if(!auth()->user()->hasRole($writerRole))
                    <div class="col-md-12">
                        <label>Writer</label>
                        <div class="form-group">
                            <select class="form-control select2" name="writer" id="writer">
                                <option value="">Select Writer</option>
                                @foreach($writers as $writer)
                                <option value="{{$writer->id}}" {{request()->writer == $writer->id ? 'selected' : ''}}>{{$writer->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    @endif
                    <div class="col-md-12">
                        <div class="form-group">
                            <button class="btn btn-success">Submit</button>
                        </div>
                    </div>
                </form>
            </div>
            @endsection
            <div class="row el-element-overlay">
                <div class="col-md-2"> 
                    Show 
                    <select id="entries" class="form-control" style="width: 50%; display: inline;">
                        <option selected>{{request()->entries ? request()->entries : '...'}}</option>
                        <option {{request()->entries == 10 ? 'selected' : ''}}>10</option>
                        <option {{request()->entries == 30 ? 'selected' : ''}}>30</option>
                        <option {{request()->entries == 50 ? 'selected' : ''}}>50</option>
                        <option {{request()->entries == 100 ? 'selected' : ''}}>100</option>
                    </select>
                    entries
                </div>
            </div>

            <table class="table table-bordered table-hover m-l-10">
                <thead>
                    <tr role="row">
                        <th>#</th>
                        <th>Writer</th>
                        <th>Reason</th>
                        <th>Leave Dates</th>
                        <th>Status</th>
                        <th>Applied</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="list">
                    @forelse ($applications as $application)
                    <tr role="row" class="odd">
                        <td>{{$application->id}}</td>
                        <td>{{$application->writer->name}}</td>
                        <td>
                            {{ strlen(strip_tags($application->reason)) > 50 ? substr(strip_tags($application->reason), 0, 50).'...' : strip_tags($application->reason) }}
                        </td>
                        <td>
                            @php($dates = explode(',', $application->leave_dates))
                            @php($leaveDates = '')
                            @foreach($dates as $key => $date)
                            @php($leaveDates .= $date == end($dates) ? count($dates) > 1 ? 'and '.\Carbon\Carbon::create($date)->format('d M Y') : \Carbon\Carbon::create($date)->format('d M Y') : \Carbon\Carbon::create($date)->format('d M').', ')
                            @endforeach
                            {{strlen($leaveDates) > 50 ? substr($leaveDates, 0, 50) .'...' : $leaveDates}}
                        </td>
                        <td>
                            @if($application->status == 1) 
                            <label class="label label-success">Granted</label>
                            @else
                            <label class="label label-warning">Pending</label>
                            @endif
                        </td>
                        <td>{{$application->created_at->diffForHumans()}}</td>
                        <td>
                            <div class="btn-group dropdown m-r-10  text-center">
                                <a href="javascript:;" onclick="viewLeave('{{$application->id}}')" class="btn btn-sm btn-info"><i class="fa fa-search"></i></a>
                                @if(auth()->user()->hasRole('admin'))
                                <a href="javascript:;" onclick="deleteLeave('{{$application->id}}')" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></a>
                                @endif
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
                    <tr align="right" style="border: 0px !important;">
                        <td colspan="8" style="border: 0px !important;"> {{$applications->appends(['entries' => request()->entries, 'writer' => request()->writer, 'startDate' => request()->startDate, 'endDate' => request()->endDate])->render()}} </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
</div>

{{--Ajax Modal--}}
<div class="modal"  id="subTaskModal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md" id="modal-data-application">
        <div class="modal-content">
            <div class="modal-body">
                Loading...
            </div>
        </div>
    </div>
</div>
{{--Ajax Modal Ends--}}

<!-- Modal -->
<div class="modal fade" id="applyLeaveModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h4 class="modal-title"><i class="ti-plus"></i> Apply Leave</h4>
    </div>
    <div class="modal-body">
        <form id="applyLeaveForm" method="post">
            @csrf
            @if(auth()->user()->hasRole('admin') || auth()->id() == $writerHead)
            <div class="form-group">
                <label>Writer</label>
                <select name="writer" class="form-control" id="user">
                    @foreach($writers as $writer)
                    <option value="{{$writer->id}}">{{$writer->name}}</option>
                    @endforeach
                </select>
            </div>
            @endif
            <div class="form-group">
                <label>Date</label>
                <input type="text" name="leaveDates" class="form-control" placeholder="Select leave dates" id="leaveDates" autocomplete="off">
            </div>
            <div class="form-group">
                <label>Reason</label>
                <textarea name="reason" class="summernote" id="reason" rows="4"></textarea>
            </div>
        </form>
    </div>
    <div class="modal-footer">
        <button class="btn btn-info" id="applyButton">Apply</button>
        <button type="button" class="btn btn-inverse" data-dismiss="modal">Cancel</button>
    </div>
</div>
</div>
</div>

@endsection

@push('footer-script')
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script src="{{ asset('plugins/bower_components/summernote/dist/summernote.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/custom-select/custom-select.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
<script type="text/javascript">

    @if(request('application') != null)
    viewLeave({{request('application')}});
    @endif

    $('#applyButton').click(function(){
        var dates = $('#leaveDates').val();
        var reason = $('#reason').val();
        var writer = $('#user').val();
        var url = '{{route('member.article.leaveApply')}}';
        $.easyAjax({
            type: 'POST',
            url: url,
            data: {'leaveDates': dates, 'reason': reason, 'writer': writer, '_token': '{{csrf_token()}}'},
            success: function(response){
                if (response.status == 'success') {
                    document.location.reload(true);
                }
            }
        });
    })

    $("#writer").select2({
        formatNoMatches: function () {
            return "{{ __('messages.noRecordFound') }}";
        }
    });

    $("#user").select2({
        formatNoMatches: function () {
            return "{{ __('messages.noRecordFound') }}";
        }
    });

    $('#entries').on('change', function(){

        var url = '{{route('member.article.leaves')}}?{{request()->entries ? 'entries=:entries' : ''}}';

        var ent = '{{request()->entries}}';
        var writer = '{{request()->writer}}';
        var startDate = '{{request()->startDate}}';
        var endDate = '{{request()->endDate}}';

        var url = url.replace(':entries', $(this).val());
        if (ent.length ===0) {var url = url+'entries='+$(this).val();}

        if (startDate != '' && endDate != '') {
            var url = url + '&startDate='+startDate+'&endDate='+endDate;
        }

        if (writer != '') {
            var url = url + '&writer='+writer;
        }

        window.location.href = url;
    });

    function viewLeave(id){
        var url = "{{ route('member.article.leaveView', ':id') }}";
        var url = url.replace(':id', id);
        $.ajaxModal('#subTaskModal', url);
    }

    @if(auth()->user()->hasRole('admin'))
    function deleteLeave(id) {
        var buttons = {
            cancel: "Cancel",
            confirm: {
                text: "Yes",
                value: 'confirm',
                visible: true,
                className: "danger",
            }
        };
        swal({
            title: "Are you sure?",
            text: "You will not be able to recover deleted application!",
            dangerMode: true,
            icon: 'warning',
            buttons: buttons,
        }).then(function (isConfirm) {
            if (isConfirm ==='confirm') {
                var token = "{{csrf_token()}}";
                var url = '{{route('member.article.leaveDelete', ':id')}}';
                var url = url.replace(':id', id);
                $.easyAjax({
                    type: 'POST',
                    url: url,
                    data: {'_token': token, '_method': 'DELETE'},
                    success: function (response) {
                        if (response.status == "success") {
                            $.unblockUI();
                            location.reload(true);
                        }
                    }
                });
            }
        });
    }
    @endif

    jQuery('#leaveDates').datepicker({
        format: 'yyyy-mm-dd',
        multidate: true,
        todayHighlight: true,
    // datesDisabled: disabledDates
});

    jQuery('#date-range').datepicker({
        toggleActive: true,
        format: 'yyyy-mm-dd',
        autoclose: true
    });

    $('.summernote').summernote({
        height: 100,                 // set editor height
        minHeight: null,             // set minimum height of editor
        maxHeight: null,             // set maximum height of editor
        focus: false,
        toolbar: [
            // [groupName, [list of button]]
            ['style', ['bold', 'italic', 'underline', 'clear']],
            ['font', ['strikethrough', 'superscript', 'subscript']],
            ['fontsize', ['fontsize']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['height', ['height']]
            ]
        });
    </script>
    @endpush
