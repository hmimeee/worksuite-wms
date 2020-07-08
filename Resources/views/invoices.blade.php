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
        @if($user->id == $writerHead || $user->hasRole('admin'))
        <a href="#" class="btn btn-outline btn-success btn-sm pull-right" id="createInvoice"><i class="fa fa-plus" aria-hidden="true"></i> Add New Payslip</a>
        @endif
    </div>
    <!-- /.breadcrumb -->
</div>
@endsection

@push('head-script')
<style type="text/css">
    .filter-section>h5{
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
    <div class="col-md-12 m-b-30">
        <div class="white-box">
            <div class="row el-element-overlay">
                <div class="col-md-2"> 
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
                    <div class="col-md-2 checkbox checkbox-info">
                        <input type="checkbox" name="hide" value="true" id="hide" @if(request()->hide =='on') checked @endif>
                       <label for="hide">Hide Paid Payslips</label> 
                    </div>

                <table class="table table-bordered table-hover m-l-10">
                    <thead>
                        <tr role="row">
                            <th>#</th>
                            <th>Name</th>
                            <th>Payment For</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="list">
                        @forelse ($invoices as $invoice)
                        <tr role="row" class="odd">
                            <td>{{$invoice->id}}</td>
                            <td>
                                <a href="javascript:;" onclick="viewInvoice('{{$invoice->id}}')">{{$invoice->name}}</a>
                            </td>
                            <td>{{$invoice->user->name}}</td>
                            <td>{{$invoice->amount}} BDT</td>
                            <td>
                                @if($invoice->status == 1) 
                                <label class="label label-success">Paid</label>
                                @else
                                <label class="label label-danger">Unpaid</label>
                                @endif
                            </td>
                            <td>{{$invoice->created_at->diffForHumans()}}</td>
                            <td>
                                <div class="btn-group dropdown m-r-10  text-center">
                                    <button aria-expanded="false" data-toggle="dropdown" class="btn dropdown-toggle waves-effect waves-light" type="button"><i class="ti-more"></i></button>
                                    <ul role="menu" class="dropdown-menu pull-right">
                                        <li><a target="_blank" href="{{route('member.article.invoice', $invoice->id)}}"><i class="fa fa-print" aria-hidden="true"></i> Print</a></li>
                                        @if($user->hasRole('admin'))
                                        <li><a href="javascript:;"  onclick="deleteInvoice('{{$invoice->id}}')"><i class="fa fa-times" aria-hidden="true"></i> Delete</a></li>
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
                        <tr align="right" style="border: 0px !important;">
                            <td colspan="8" style="border: 0px !important;"> {{$invoices->appends(['hide' => request()->hide, 'entries' => request()->entries])->render()}} </td>
                        </tr>
                    </tfoot>
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
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script type="text/javascript">

    @if(request('view-invoice'))
    viewInvoice('{{request('view-invoice')}}');
    @endif

    $('#entries').on('change', function(){

        var url = '{{route('member.article.invoices')}}?{{request()->entries ? 'entries=:entries' : ''}}';

        var ent = '{{request()->entries}}';

        var url = url.replace(':entries', $(this).val());
        if (ent.length ===0) {var url = url+'entries='+$(this).val();}

        if($('#hide').is(':checked')){
            var hide = 'on';
        } else {
            var hide = 'off';
        }
        var url = url+'&hide='+hide;

        window.location.href = url;
    });

    $('#createInvoice').click(function () {
        var url = "{{ route('member.article.createInvoice') }}";
        $.ajaxModal('#subTaskModal', url);
    })

    function viewInvoice(id){
        var url = "{{ route('member.article.modalInvoice', ':id') }}";
        var url = url.replace(':id', id);
        $.ajaxModal('#subTaskModal', url);
    }

    // function deleteInvoice(id){
    //     var token = "{{csrf_token()}}";
    //     var url = '{{route('member.article.invoiceDelete', ':id')}}';
    //     var url = url.replace(':id', id);
    //     $.easyAjax({
    //         url: url,
    //         type: "POST",
    //         data: {'_token': token},
    //         success: function (res) {
    //             if (res.status ==='success') {
    //                 location.reload(true);
    //             }
    //         }
    //     });
    // }

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
            text: "You will not be able to recover the deleted invoice!",
            dangerMode: true,
            icon: 'warning',
            buttons: buttons,
        }).then(function (isConfirm) {
            if (isConfirm ==='confirm') {
                var token = "{{csrf_token()}}";
                var url = '{{route('member.article.invoiceDelete', ':id')}}';
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

    $('#hide').on('click', function(){
        if($(this).is(':checked')){
            var hide = 'on';
        } else {
            var hide = 'off';
        }
        var url = '{{route("member.article.invoices")}}?hide='+hide;
        window.location.href = url;
    })
</script>
@endpush
