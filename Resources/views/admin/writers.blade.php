@extends('layouts.app')

@section('page-title')
<div class="row bg-title">
    <!-- .page title -->
    <div class="col-lg-8 col-md-4 col-sm-4 col-xs-12">
        <h4 class="page-title"><i class="{{ $pageIcon }}"></i> {{ $pageTitle }} </h4>
    </div>
    <!-- /.page title -->
    <!-- .breadcrumb -->
    <div class="col-lg-4 col-sm-8 col-md-8 col-xs-12 text-right">
        <a href="{{ route('admin.employees.create') }}" class="btn btn-outline btn-success btn-sm pull-right"><i class="fa fa-plus" aria-hidden="true"></i> Add New Writer</a>
        <ol class="breadcrumb">
            <li><a href="{{ route('member.dashboard') }}">@lang('app.menu.home')</a></li>
            <li class="active">{{ $pageTitle }}</li>
        </ol>
    </div>
    <!-- /.breadcrumb -->
</div>
@endsection

@push('head-script')
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/custom-select/custom-select.css') }}">
<style type="text/css">
.checked {
    color: orange;
}
a{
    font-weight: 600;
}

.sorting:after {
    display: none;
}
</style>
@endpush

@section('content')
<div class="row dashboard-stats">
    <div class="col-md-12">
        <div class="white-box">
            @section('filter-section')
            <form>
                <div class="row">
                    <div class="col-md-12 p-t-10">
                        <h5 class="box-title">Status</h5>
                        <select class="select2 form-control" name="status" id="status">
                            <option value="all">Select Status</option>
                            <option value="Available" {{ request()->status == 'Available' ? 'selected' : '' }}>Available</option>
                            <option value="Unavailable" {{ request()->status == 'Unavailable' ? 'selected' : '' }}>Unavailable</option>
                        </select>
                    </div>
                    <div class="col-md-12 p-t-10">
                        <h5 class="box-title">Type</h5>
                        <select class="select2 form-control" name="type" id="type">
                            <option value="all">Select Type</option>
                            <option value="Inhouse" {{ request()->type == 'Inhouse' ? 'selected' : '' }}>Inhouse Writers</option>
                            <option value="Freelance" {{ request()->type == 'Freelance' ? 'selected' : '' }}>Freelance Writers</option>
                        </select>
                    </div>
                    <div class="col-md-12 m-t-15">
                        <button class="btn btn-success btn-sm pull-right">
                            <i class="fa fa-check"></i> Submit
                        </button>
                        <a href="{{ url()->current() }}" class="btn btn-inverse btn-sm pull-right m-r-5">
                            <i class="fa fa-check"></i> Reset
                        </a>
                    </div>
                </div>
            </form>
            @endsection

        <div class="row">
            <div class="col-sm-12">
                <h4>
                    <span class="text-info">{{ $totalWriters ?? '0' }}</span>
                    <span class="font-12 text-muted m-l-5"> @lang('article::app.totalwriters')</span>
                </h4>
            </div>
            {{-- <div class="col-md-3 m-b-5"> 
                Show 
                <select id="entries" class="form-control" style="width: 50%; display: inline;">
                    <option selected>{{request()->entries ? request()->entries : '...'}}</option>
                    <option @if(request()->entries == 10) selected @endif>10</option>
                    <option @if(request()->entries == 30) selected @endif>30</option>
                    <option @if(request()->entries == 50) selected @endif>50</option>
                    <option @if(request()->entries == 100) selected @endif>100</option>
                </select>
                entries
            </div> --}}
        </div>

        <div class="row el-element-overlay">
            <table class="table table-bordered table-hover" id="writers">
                <thead>
                    <tr role="row">
                        <th>#</th>
                        <th>Name</th>
                        <th>Writer Type</th>
                        @if(!auth()->user()->hasRole($writerRole) && !auth()->user()->hasRole($inhouseWriterRole))
                        <th>Rate (1k Words)</th>
                        @endif
                        <th>Pending Articles</th>
                        <th>Completed Articles</th>
                        <th>Words Written</th>
                        {{-- <th>Gender</th> --}}
                        <th>Rating</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="list">
                    @foreach ($writers as $writer)
                    <tr role="row" class="odd">
                        <td>{{$writer->id}}</td>
                        <td>
                            <a href="javascript:;" onclick="viewWriter('{{$writer->id}}')">{{$writer->name}}</a>
                            @if($writer->leaves()->where('leave_dates', 'LIKE', '%'.date('Y-m').'%')->count() > 0)
                                <span class="label label-danger">Leaves taken</span>
                                @endif
                                @if($writer->unavailable)
                                <span class="label label-danger">Unavailable</span>
                                @endif
                        </td>
                        <td>{{App\Role::find($writer->role->last()->role_id)->display_name}}</td>
                        @if(!auth()->user()->hasRole($writerRole) && !auth()->user()->hasRole($inhouseWriterRole))
                        <td>{{$writer->rate ? $writer->rate->rate : '--'}}</td>
                        @endif
                        <td>
                            @php
                            $pending = $writer->articles->where('writing_status', 0);
                            $count = count($pending);
                            @endphp
                            {{count($pending)}}
                        </td>
                        <td>
                            @php
                            $completed = $writer->articles->where('writing_status', 2);
                            $count = count($completed);
                            @endphp
                            {{count($completed)}}
                        </td>
                        <td>
                            @php($words = 0)
                            @foreach ($completed as $article)
                            @php($words += $article->word_count)
                            @endforeach
                            {{$words}}
                        </td>
                        {{-- <td>{{ucfirst($writer->gender)}}</td> --}}
                        <td>
                            @php($rate = 0)

                            @foreach ($completed as $article)
                            @php($rate += $article->rating)
                            @endforeach

                            @if ($rate !=0)
                            ({{number_format($rate/$count, 2)}}) - 
                            @for($i=0; $i < round($rate/$count); $i++)
                            <span class="fa fa-star checked"></span>
                            @endfor

                            @for($i=$i; $i < 5; $i++)
                            <span class="fa fa-star"></span>
                            @endfor

                            @else
                            (0) - 
                            @for($i=0; $i < 5; $i++)
                            <span class="fa fa-star"></span>
                            @endfor
                            @endif
                        </td>
                        <td class=" text-center">
                            <div class="btn-group dropdown m-r-10">
                                <button aria-expanded="false" data-toggle="dropdown" class="btn dropdown-toggle waves-effect waves-light" type="button"><i class="ti-more"></i></button>
                                <ul role="menu" class="dropdown-menu pull-right">
                                    <li><a href="javascript:;" onclick="viewWriter('{{$writer->id}}')"><i class="fa fa-search" aria-hidden="true"></i> View</a></li>
                                    @if(auth()->user()->hasRole('admin') || auth()->id() == $writerHead)
                                    <li><a href="{{ route('member.employees.edit', $writer->id) }}"><i class="fa fa-pencil" aria-hidden="true"></i> Edit</a></li>
                                    @endif
                                    @if(auth()->user()->hasRole('admin'))
                                    <li><a href="javascript:;"  data-user-id="{{$writer->id}}" class="sa-params"><i class="fa fa-times" aria-hidden="true"></i> Delete</a></li>
                                    @endif
                                </ul> 
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
</div>

{{--Ajax Modal--}}
<div class="modal"  id="subTaskModal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" id="modal-data-application">
        <div class="modal-content">
            <div class="modal-body">
                Loading...
            </div>
        </div>
    </div>
</div>
{{--Ajax Modal Ends--}}

@endsection

@push('footer-script')
<script src="{{ asset('plugins/bower_components/custom-select/custom-select.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.js') }}"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.css">
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.js"></script>
<script type="text/javascript">
    $(document).ready(function(){
        $('#writers').addClass('table-striped table-hover table-bordered').DataTable({
            pageLength: 25,
        });

        $(".select2").select2({
            formatNoMatches: function () {
                return "{{ __('messages.noRecordFound') }}";
            }
        });
    })

    $('#entries').on('change', function(){
        var url = '{{route('admin.article.writers')}}?{{request()->entries ? 'entries=:entries' : ''}}';

        var ent = '{{request()->entries}}';

        var url = url.replace(':entries', $(this).val());
        if (ent.length ===0) {var url = url+'entries='+$(this).val();}
        window.location.href = url;
    })

    function viewWriter(id) {
        var url = "{{ route('member.article.writer', ':id')}}";
        var url = url.replace(':id', id);
        $.ajaxModal('#subTaskModal', url);
    }
    $(function() {
        $('body').on('click', '.sa-params', function(){
            var id = $(this).data('user-id');
            swal({
                title: "Are you sure?",
                text: "You will not be able to recover the deleted user!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes, delete it!",
                cancelButtonText: "No, cancel please!",
                closeOnConfirm: true,
                closeOnCancel: true
            }, function(isConfirm){
                if (isConfirm) {

                    var url = "{{ route('admin.employees.destroy',':id') }}";
                    url = url.replace(':id', id);

                    var token = "{{ csrf_token() }}";

                    $.easyAjax({
                        type: 'POST',
                        url: url,
                        data: {'_token': token, '_method': 'DELETE'},
                        success: function (response) {
                            if (response.status == "success") {
                                $.easyBlockUI('#employees-table');
                                window.LaravelDataTables["employees-table"].draw();
                                $.easyUnblockUI('#employees-table');
                            }
                        }
                    });
                }
            });
        });
    });
</script>
@endpush
