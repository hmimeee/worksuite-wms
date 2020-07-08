<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/custom-select/custom-select.css') }}">

<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h4 class="modal-title"><i class="ti-plus"></i> Generate New Payslip</h4>
</div>
<div class="modal-body">
    <div class="portlet-body">

        {!! Form::open(['id'=>'storeInvoice','class'=>'ajax-form','method'=>'POST', 'files' => true]) !!}

        <div class="form-body">
            <div class="row">
                <div id="article-tab">

                </div>

                <div class="col-md-12">
                    <div class="form-group">
                        <label class="control-label required">Select Writers</label>
                        <select class="select2 form-control" data-placeholder="Select Writers" name="writer" id="writer" >
                            <option value=""></option>
                            @foreach ($writers as $writer)
                            <option value="{{$writer->id}}">{{$writer->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="form-group">
                        <label class="control-label required">Unpaid Articles</label>
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr role="row">
                                    <th>#</th>
                                    <th>Title</th>
                                    <th>Project</th>
                                    <th>Word Count</th>
                                    <th>Deadline</th>
                                </tr>
                            </thead>
                            <tbody id="list-data">
                                <tr>
                                    <td colspan="8">
                                        No data found!
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
            <!--/row-->

        </div>
        <div class="form-actions">
            <input type="hidden" name="amount" id="amount" value="0">
            <button type="button" id="generate-invoice" class="btn btn-success"><i class="fa fa-check"></i> Generate Payslip</button>
            <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
        </div>

        {!! Form::close() !!}
    </div>
</div>
<script src="{{ asset('plugins/bower_components/custom-select/custom-select.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.js') }}"></script>

<script>
    //Get Articles
    $('#writer').change(function () {
        var id = $('#writer').val();
        var url = '{{route('member.article.invoiceData', ':id')}}';
        var url = url.replace(':id', id);

        $.easyAjax({
            url: url,
            type: "GET",
            success: function (res) {
                var data = '';
                var amount = 0;

                for (var i = 0; i < res.count; i++) {
                    var data = data+'<tr role="row" class="odd"><td>'+res.articles[i].id+'</td><td>'+res.articles[i].title+'</td><td>'+res.articles[i].project.project_name+'</td><td>'+res.articles[i].word_count+'</td><td>'+res.articles[i].writing_deadline+'</td></tr>';
                    var amount = amount+res.articles[i].rate/1000*res.articles[i].word_count;
                }
                if (data !='') {
                    $('#list-data').html(data);
                } else {
                    $('#list-data').html('<tr><td colspan="8">No data found!</td></tr>');
                }
                $('#amount').val(amount);
            }
        });
    });

    //Save Invoice
    $('#generate-invoice').click(function () {
        var amount = $('#amount').val();
        var writer = $('#writer').val();
        var token = "{{csrf_token()}}";
        var url = "{{route('member.article.invoiceGenerate', ':writer')}}";
        var url = url.replace(':writer', writer);

        if (amount !=0) {
            $.easyAjax({
                url: url,
                type: "POST",
                data: {'writer': writer, '_token': token},
                success: function (res) {
                    if (res.status ==='success') {
                        location.reload(true);
                    }
                }
            });
        } else {
            $.showToastr('No unpaid articles to generate invoice', 'error');
        }
    });

    jQuery('#writing_deadline').datepicker({
        format: 'yyyy-mm-dd',
        autoclose: true,
        todayHighlight: true
    });

    $("#writer").select2({
        formatNoMatches: function () {
            return "{{ __('messages.noRecordFound') }}";
        }
    });
</script>

