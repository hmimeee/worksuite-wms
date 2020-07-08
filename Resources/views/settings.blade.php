@extends('layouts.member-app')

@section('page-title')
<div class="row bg-title">
    <!-- .page title -->
    <div class="col-lg-8 col-md-5 col-sm-6 col-xs-12">
        <h4 class="page-title"><i class="{{ $pageIcon ?? '' }}"></i> {{ $pageTitle ?? '' }}
        </h4>
    </div>
    <div class="col-lg-4 col-sm-6 col-md-7 col-xs-12 text-right">
        <ol class="breadcrumb">
            <li><a href="{{ route('admin.dashboard') }}">@lang('app.menu.home')</a></li>
            <li class="active">{{ $pageTitle ?? '' }}</li>
        </ol>
    </div>
    <!-- /.breadcrumb -->
</div>
@endsection

@push('head-script')
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/custom-select/custom-select.css') }}">
<link rel="stylesheet"
href="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.css') }}">
<style type="text/css">
    .pull-left{
        display: none;
    }
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
            <div class="col-12 m-b-10 m-t-10">
                <h3 style="display: inline;">Article Types:</h3>
                @if(auth()->user()->hasRole('admin') || auth()->id() == $writerHead)
                <a href="javascript:;" id="createType" class="btn btn-outline btn-success btn-sm pull-right"><i class="fa fa-plus" aria-hidden="true"></i> Add New Type</a>
                @endif
            </div>

            <table class="table table-bordered table-hover">
                <thead>
                    <tr role="row">
                        <th>#</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody id="list">
                    @forelse ($types as $type)
                    <tr role="row" class="odd">
                        <td>{{$type->id}}</td>
                        <td>{{$type->name}}</td>
                        <td>{{strip_tags($type->description)}}</td>
                        <td class=" text-center">
                            <div class="btn-group dropdown m-r-10">
                                @if(auth()->user()->hasRole('admin') || auth()->id() == $writerHead)
                                <a href="javascript:;" onclick="deleteType('{{$type->id}}')" class="label label-danger"><i class="fa fa-times" aria-hidden="true"></i> Delete</a>
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
                        <td colspan="8" style="border: 0px !important;"> {{$types->render()}} </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection

@push('footer-script')
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script src="{{ asset('plugins/bower_components/custom-select/custom-select.min.js') }}"></script>
<script type="text/javascript">

 $("#writers").select2({
    formatNoMatches: function () {
        return "{{ __('messages.noRecordFound') }}";
    }
});
 $("#inhouse_writers").select2({
    formatNoMatches: function () {
        return "{{ __('messages.noRecordFound') }}";
    }
});

 $("#publisher").select2({
    formatNoMatches: function () {
        return "{{ __('messages.noRecordFound') }}";
    }
});
 $("#writerHead").select2({
    formatNoMatches: function () {
        return "{{ __('messages.noRecordFound') }}";
    }
});

 $('#createType').click(function () {
    var url = "{{ route('member.article.createType') }}";
    $.ajaxModal('#subTaskModal', url);
})

     //Delete Type
     function deleteType(id) {
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
            text: "You will not be able to recover the deleted type!",
            dangerMode: true,
            icon: 'warning',
            buttons: buttons,
        }).then(function (isConfirm) {
            if (isConfirm ==='confirm') {
                var token = "{{csrf_token()}}";
                var url = '{{route('member.article.deleteType', ':id')}}';
                var url = url.replace(':id', id);
                $.easyAjax({
                    type: 'POST',
                    url: url,
                    data: {'_token': token},
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
</script>
@endpush
