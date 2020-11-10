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

        <a href="javascript:;" class="btn btn-outline btn-success btn-sm pull-right" id="createInvoice"><i class="fa fa-plus" aria-hidden="true"></i> Add New Payslip</a>

    </div>

    <!-- /.breadcrumb -->

</div>

@endsection



@push('head-script')
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.css') }}">
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.13/css/dataTables.bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.1.1/css/responsive.bootstrap.min.css">
<link rel="stylesheet" href="//cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css">
<link rel="stylesheet" href="//cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css">

<style type="text/css">
    .pull-left{
        display: none;
    }
    .checked {
      color: orange;
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



<div class="row dashboard-stats">

    <div class="col-md-12">

        <div class="white-box">

            @section('filter-section')
            <div class="row">
                <form id="filterForm">
                    <div class="form-group col-12">
                        <h5 class="box-title">Status</h5>
                        <select class="form-control select2" name="status" id="status">
                            <option value="">Select</option>
                            <option value="paid" {{request()->status =='paid' ? 'selected' : ''}}>Paid</option>
                            <option value="unpaid" {{request()->status =='unpaid' ? 'selected' : ''}}>Unpaid</option>
                        </select>
                    </div>

                    <div class="form-group col-12">
                        <h5 class="box-title">Date Range</h5>
                        <div class="input-daterange input-group" id="date-range">
                            <input type="text" name="startDate" class="form-control" id="startDate"  placeholder="@lang('app.startDate')" value="{{request()->startDate}}" autocomplete="off" />
                            <span class="input-group-addon bg-info b-0 text-white">@lang('app.to')</span>
                            <input type="text" name="endDate" class="form-control" id="endDate" placeholder="@lang('app.endDate')" value="{{request()->endDate}}" autocomplete="off" />
                        </div>
                    </div>

                    <div class="form-group col-12">
                        <button class="btn btn-info btn-sm btn-block">Apply</button>
                        <button class="btn btn-dark btn-sm btn-block" id="resetFilter">Reset</button>
                    </div>
                </form>
            </div>
            @endsection

            <div class="row">
                <div class="col-md-12">
                    <div class="white-box">
                        <div class="row">
                            <div class="table-responsive p-20">
                                {!! $dataTable->table(['class' => 'table table-bordered table-hover toggle-circle default footable-loaded footable']) !!}
                            </div>
                        </div>
                    </div>
                </div>
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

<div class="modal fade" id="previewModal" tabindex="-1" role="dialog" aria-labelledby="previewModalArea" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="previewModalArea"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <img src="" id="previewImage" style="width: 100%; height: cover;">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@endsection



@push('footer-script')
<script src="{{ asset('plugins/bower_components/datatables/jquery.dataTables.min.js') }}"></script>
<script src="https://cdn.datatables.net/1.10.13/js/dataTables.bootstrap.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.1.1/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.1.1/js/responsive.bootstrap.min.js"></script>
<script src="{{ asset('plugins/bower_components/custom-select/custom-select.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
<script src="https://cdn.datatables.net/buttons/1.0.3/js/dataTables.buttons.min.js"></script>
<script src="{{ asset('js/datatables/buttons.server-side.js') }}"></script>
{!! $dataTable->scripts() !!}

<script type="text/javascript">
    $('#createInvoice').click(function () {

        var url = "{{ route('member.article.createInvoice') }}";

        $.ajaxModal('#subTaskModal', url);

    })



    function viewInvoice(id){

        var url = "{{ route('member.article.modalInvoice', ':id') }}";

        var url = url.replace(':id', id);

        $.ajaxModal('#subTaskModal', url);

    }



    function deleteInvoice(id) {

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

            text: "Please enter your password below:",

            dangerMode: true,

            icon: 'warning',

            buttons: buttons,

            content: "input"

        }).then(function (isConfirm) {

            if (isConfirm ==='') {swal("Empty!", "You must enter your password!", "warning"); return false; }



            var token = "{{csrf_token()}}";

            var url = '{{route('member.article.invoiceDelete', ':id')}}';

            var url = url.replace(':id', id);

            $.easyAjax({

                type: 'POST',

                url: url,

                data: {'password': isConfirm, '_token': token},

                success: function (response) {

                    if (response.status == "success") {

                        $.unblockUI();

                        location.reload(true);

                    }

                }

            });

        });

    }

</script>

@endpush

