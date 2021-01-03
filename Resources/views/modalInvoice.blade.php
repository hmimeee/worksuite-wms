<style>
    .btn-pref .btn {
        -webkit-border-radius:0 !important;
    }

    .swal-footer {
        text-align: center !important;
    }
</style>

<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h4 class="modal-title"><i class="ti-receipt"></i> Payslip #{{$invoice->id}} <a target="_blank" href="{{route('member.article.invoice', $invoice->id)}}" class="btn btn-default btn-sm"><i class="fa fa-print"></i></a></h4>
</div>
<div class="modal-body">
    <div class="btn-pref btn-group btn-group-justified btn-group-lg" role="group" aria-label="...">
        <div class="btn-group" role="group">
            <button type="button" id="invoice" class="btn btn-info" href="#tab1" data-toggle="tab"><span class="ti-receipt" aria-hidden="true"></span>
                <div class="hidden-xs">Payslip</div>
            </button>
        </div>
        <div class="btn-group" role="group">
            <button type="button" id="receipt" class="btn btn-default" href="#tab2" data-toggle="tab"><span class="fa fa-file" aria-hidden="true"></span>
                <div class="hidden-xs">Receipt</div>
            </button>
        </div>
        <div class="btn-group" role="group">
            <button type="button" id="activity" class="btn btn-default" href="#tab3" data-toggle="tab"><span class="fa fa-history" aria-hidden="true"></span>
                <div class="hidden-xs">Activity</div>
            </button>
        </div>
    </div>
    <div class="tab-content">
        <div class="tab-pane fade in active" id="tab1">
            <table class="table table-sm">
                <tr>
                    <td style="border: 0px !important;"></td>
                    <td style="border: 0px !important;">
                        <img src="{{ asset('favicon.png') }}" height="100px">
                    </td>
                    <td style="border: 0px !important;" class="text-left">
                        <u><h3><b>PAYSLIP</b></h3></u>
                    </td>
                    <td style="border: 0px !important;" class="text-right row">
                        <div class="col-12 m-b-5">
                            Payslip No:
                        </div>
                        <div class="col-12 m-b-5">
                            Generated On:
                        </div>
                        @if($invoice->paid_date !=null)
                        <div class="col-12 m-b-5">
                            Payment Date :
                        </div>
                        @endif
                        <div class="col-12 m-b-5">
                            Payment Status:
                        </div>
                    </td>
                    <td style="border: 0px !important;" class="text-right row">
                        <div class="col-12 m-b-5 text-left"><b>{{$invoice->name}}</b></div>
                        <div class="col-12 m-b-5 text-left"><b>{{date("Y-m-d",strtotime($invoice->created_at))}}</b></div>
                        @if($invoice->paid_date !=null)
                        <div class="col-12 m-b-5 text-success text-left"><b>{{date("Y-m-d",strtotime($invoice->paid_date))}}</b></div>
                        @endif
                        <div class="col-12 m-b-5 text-center">
                            <b>
                                @if($invoice->status == 1) 
                                <div class='text-success' style="border: 1px solid #02C293; padding: 2px;">Paid</div>
                                @if($user->hasRole('admin'))
                                <button class="btn btn-sm bg-danger text-white m-t-10" onclick="invoiceStatus(0)">Change to Unpaid</button>
                                @endif
                                @else
                                <div class='text-danger' style="border: 1px solid #FB9A7D; padding: 2px;">Unpaid </div>
                                @if($user->hasRole('admin'))
                                <button class="btn btn-sm bg-success text-white m-t-10" onclick="invoiceStatus(1)">Change to Paid</button>
                                @endif
                                @endif
                            </b>
                        </div>
                    </td>
                    <td style="border: 0px !important;"></td>
                </tr>
                <tr>
                    <td style="border: 0px !important;"></td>
                    <td style="border: 0px !important;">
                        {!! $address ? $address->value : '<span class="text-danger"> Please update company address from <br> "Article Management >> Settings" </span>' !!}
                    </td>
                    <td style="border: 0px !important;" class="text-right">
                        Payment To:
                        <br>
                        E-mail:
                        <br>
                        Phone No.:
                        <br>
                        Payment Method:
                        <br>
                        Payment Details:
                    </td>
                    <td style="border: 0px !important;" class="text-left">
                        <b>{{$invoice->user->name}}</b>
                        <br>
                        {{$invoice->user->email}}
                        <br>
                        {{$invoice->user->phone}}
                        <br>
                        {!! $invoice->user->paymentDetails ? $invoice->user->paymentDetails->title : '<span class="text-danger">Empty!</span>' !!}
                        <br>
                        {!! $invoice->user->paymentDetails ? $invoice->user->paymentDetails->details : '<span class="text-danger">Please select '.$invoice->user->name."'s default payment details!</span>" !!}
                    </td>
                    <td style="border: 0px !important;"></td>
                    <td style="border: 0px !important;"></td>
                </tr>
            </table>
            <br>
            <br>
            <table class="table table-sm table-bordered">
                <tbody>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Words</th>
                        <th>Bill</th>
                    </tr>
                    @foreach ($articles as $article)
                    <tr>
                        <th scope="row">#{{$article->id}}</th>
                        <td>
                            <a target="_blank" href="@if(auth()->user()->hasRole('admin')) {{route('admin.article.show', $article->id)}} @else {{route('member.article.show', $article->id)}} @endif"> {{$article->title}} </a>
                        </td>
                        <th>{{$article->word_count}}</th>
                        <th>{{$article->rate/1000*$article->word_count}}/=</th> 
                    </tr>
                    @endforeach
                    @php($count = count($articles))
                    @while ($count<25)
                    <tr style="height: 30px">
                        <th scope="row"></th>
                        <th></th>
                        <th></th>
                        <th></th>    
                    </tr>
                    @php($count++)
                    @endwhile

                    <tr>
                        <th scope="row" class="text-right" colspan="2">Total=</th>
                        <th scope="row">{{$words}} words</th>
                        <th scope="row">{{$amount}} /=</th>
                    </tr>
                </tbody>
            </table>

            <div style="height: 50px; margin: 10px;"><b>In Words:</b> @php($f = new NumberFormatter("en", NumberFormatter::SPELLOUT)) {{ucfirst(numfmt_format($f, round($amount)))}} taka only.</div>

            <table class="table table-sm" style="border: 0px !important;">
                <tr>
                    <td colspan="4" class="text-center" style="border: 0px !important;" width="40%">
                        <hr>
                        Signature of Account Manager
                    </td>
                    <td colspan="2" style="border: 0px !important;" width="10%"></td>
                    <td colspan="2" style="border: 0px !important;" width="10%"></td>
                    <td colspan="4" class="text-center" style="border: 0px !important;" width="40%">
                        <hr>
                        Signature of Project Manager
                    </td>
                </tr>
                <tr>
                    <td colspan="12" style="border: 0px !important;"><hr style="border-top: 1px dashed grey"/></td>
                </tr>
                <tr>
                    <td colspan="12" class="text-center" style="border: 0px !important;"><b>For Office Use Only</b></td>
                </tr>
                <tr>
                    <td colspan="3" class="text-center" style="background: rgba(0,0,0,0.2); border: 2px solid grey;">Approved/Not Approved</td>
                    <td colspan="3" style="border: 2px solid grey;" width="30%"></td>
                    <td colspan="3" class="text-center" style="background: rgba(0,0,0,0.2); border: 2px solid grey;">Signature & Seal of Authority</td>
                    <td colspan="3" style="border: 2px solid grey;" width="30%"></td>

                </tr>
            </table>

            <div class="row">
                <div class="col-12" style="height: 30px;"></div>
            </div>
        </div>
        <div class="tab-pane fade in" id="tab2">
            <div class="row">
                @if(auth()->user()->hasRole('admin'))
                <div class="col-xs-4 panel-body p-t-15">
                    <form method="post" id="receiptUploadForm">
                        @csrf
                        <div class="form-group">
                            <input type="file" name="receipt" class="form-control" id="receiptFile" accept=".jpg,.jpeg,.png,.gif">
                        </div>
                        <div class="form-group">
                            <button class="btn btn-info btn-sm">Upload File</button>
                        </div>
                    </form>
                </div>
                @endif
                <div class="{{auth()->user()->hasRole('admin') ? 'col-xs-8' : 'col-xs-12'}} panel-body p-t-15">
                    @foreach($invoice->receipts as $receipt)
                    <div class="col-xs-6" align="center">
                        <a href="javascript:;" data-toggle="modal" id="previewReceipt" data-target="#previewModal" data-id="{{$receipt->id}}">
                            <img class="card-img-top" src="{{route('member.article.receiptDownload', [$invoice->id, $receipt->id])}}" style="width: 250px; border: 2px solid rgba(0,0,0,0.5); position: relative;" alt="Receipt">
                            @if(auth()->user()->hasRole('admin'))
                            <a href="javascript:;" data-id="{{$receipt->id}}" id="deleteReceipt" class="label label-danger" style="position: absolute; top: 3%; right: 10%;">X</a>
                            @endif
                        </a>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="tab-pane fade in" id="tab3">
            <div class="row">
                <div class="col-xs-12 panel-body p-t-15">
                    <div class="steamline">
                        @foreach ($invoice->logs->sortByDesc('id') as $log)
                        <div class="sl-item">
                            <div class="sl-left" style="margin-left: -13px !important;"><img class="img-circle" src="@if($log->user->image !=null) /user-uploads/avatar/{{$log->user->image}} @else /img/default-profile-2.png @endif" width="25" height="25" alt="">
                            </div>
                            <div class="sl-right">
                                <div>
                                    <h6><b>{{$log->user->name}}</b> {{$log->details}}</h6>


                                    <span class="sl-date">{{$log->created_at->format('d-m-Y H:s a')}}</span>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $(".btn-pref .btn").click(function () {
            $(".btn-pref .btn").removeClass("btn-info").addClass("btn-default");
            // $(".tab").addClass("active"); // instead of this do the below 
            $(this).removeClass("btn-default").addClass("btn-info");
        });
    })

    function invoiceStatus(status) {
        var buttons = {
            cancel: "Cancel",
            confirm: {
                text: "Confirm",
                visible: true,
                className: "success",
            }
        };
        swal({
            title: "Are you sure want to change status?",
            text: "Please enter your password below:",
            dangerMode: true,
            icon: 'warning',
            buttons: buttons,
            content: 'input'
        }).then(function (isConfirm) {
            if (isConfirm !=='' && isConfirm !==null) {
                var url = "{{ route('member.article.invoiceStatus',':id') }}";
                var url = url.replace(':id', '{{$invoice->id}}');
                var token = "{{ csrf_token() }}";
                var dataObject = {'password': isConfirm, '_token': token, 'status': status};
                $.easyAjax({
                    type: 'POST',
                    url: url,
                    data: dataObject,
                    success: function (response) {
                        if (response.status == "success") {
                            swal("Success", "Status has been updated!", "success");
                            var url = "{{ route('member.article.modalInvoice', ':id') }}";
                            var url = url.replace(':id', '{{$invoice->id}}');
                            $("#subTaskModal").modal('toggle');
                            $.ajaxModal('#subTaskModal', url);
                            $("#subTaskModal").modal('show');
                        } else {
                            swal("Error!", "The password you entered is incorrect!", "error");
                        }
                    }
                });
            }
            if (isConfirm ==='') {swal("Empty!", "You must enter your password!", "warning");}
        });
    }

    @if(auth()->user()->hasRole('admin'))

    $('#receiptUploadForm').submit(function(e){
        e.preventDefault();

        url = '{{route('member.article.receiptUpload', $invoice->id)}}';

        $.easyAjax({
            type: 'POST',
            url: url,
            container: '#receiptUploadForm',
            data: $(this).serialize(),
            file: true,
            success: function(response){
                viewInvoice({{$invoice->id}});
            }
        })
    });

    $('body #deleteReceipt').click(function(){
        var id = $(this).data('id');
        var block = $(this).parent();
        var token = "{{csrf_token()}}";
        var url = '{{route('member.article.receiptDelete', [$invoice->id, ':id'])}}';
        var url = url.replace(':id', id);

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
            if (isConfirm ==='') {swal("Empty!", "You must enter your password!", "warning"); return false;}
            if (isConfirm === null) {return false; }
            $.easyAjax({
                type: 'POST',
                url: url,
                data: {'password': isConfirm, '_token': token, '_method': 'delete'},
                success: function (response) {
                    if (response.status == "success") {
                        block.hide();
                    }
                }
            });
        });
    })
    @endif

    $('#previewReceipt').click(function(){
        url = '{{route('member.article.receiptDownload', [$invoice->id, ':id'])}}';
        url = url.replace(':id', $(this).data('id'));

        $('#previewModal').find('#previewImage').attr('src', url);
    })
</script>