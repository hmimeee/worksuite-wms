<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/custom-select/custom-select.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/summernote/dist/summernote.css') }}">

<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h4 class="modal-title"><i class="ti-plus"></i> Add New Payment Details</h4>
</div>
<div class="modal-body">
    <div class="portlet-body">

        {!! Form::open(['id'=>'storeWriterDetails','class'=>'ajax-form','method'=>'POST']) !!}

        <div class="form-body">
            <div class="row">
                <div id="article-tab">

                </div>

                <div class="col-md-12">
                    <div class="form-group">
                        <label class="control-label required">Select Method</label>
                        <select class="select2 form-control" data-placeholder="Select Method" name="method" id="method" >
                            <option value="bKash">bKash</option>
                            <option value="Rocket">Rocket</option>
                            <option value="Nagad">Nagad</option>
                            <option value="Bank">Bank</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="form-group">
                        <label class="control-label required">Payment Details</label>
                        <textarea name="details" id="details" class="summernote" rows="5"></textarea>
                    </div>
                </div>

            </div>
            <!--/row-->

        </div>
        <div class="form-actions">
            <input type="hidden" name="amount" id="amount" value="0">
            <button type="button" id="store-writer-details" class="btn btn-success"><i class="fa fa-check"></i> Add Now</button>
            <button type="button" class="btn btn-light" id="cancel">Cancel</button>
        </div>

        {!! Form::close() !!}
    </div>
</div>
<script src="{{ asset('plugins/bower_components/custom-select/custom-select.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/summernote/dist/summernote.min.js') }}"></script>

<script type="text/javascript">
    $("#method").select2({
        formatNoMatches: function () {
            return "{{ __('messages.noRecordFound') }}";
        }
    })

    $('#store-writer-details').click(function(e){
        e.preventDefault();
        var url = "{{ route('member.article.writerPaymentDetailsStore',':id') }}";
        url = url.replace(':id', '{{$writer->id}}');
        $.easyAjax({
            url: url,
            type: "POST",
            data: {'_token': '{{csrf_token()}}', 'method': $('#method').val(), 'details': $('#details').val()},
            success: function(response){
                if (response.status == 'success') {
                    viewWriter('{{$writer->id}}');
                }
            }
        })
    })

    $('#cancel').click(function(){
        viewWriter('{{$writer->id}}');
    })

    function viewWriter(id) {
        var url = "{{ route('member.article.writer', ':id')}}";
        var url = url.replace(':id', id);
        $("#subTaskModal").modal('hide');
        $.ajaxModal('#subTaskModal', url);
        $("#subTaskModal").modal('show');
    }

    $('.summernote').summernote({
        height: 100,                 // set editor height
        minHeight: null,             // set minimum height of editor
        maxHeight: null,             // set maximum height of editor
        focus: false,
        toolbar: [
            // [groupName, [list of button]]
            ['para', ['ul', 'ol', 'paragraph']]
            ]
        })
</script>
