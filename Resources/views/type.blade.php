<link rel="stylesheet" href="{{ asset('plugins/bower_components/summernote/dist/summernote.css') }}">


<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h4 class="modal-title"><i class="ti-plus"></i> Add New Type</h4>
</div>
<div class="modal-body">
    <div class="portlet-body">

        {!! Form::open(['id'=>'storeType','class'=>'ajax-form','method'=>'POST']) !!}

        <div class="form-body">
            <div class="row">
                <div id="article-tab">

                </div>
                <div class="col-md-12">
                    <div class="form-group" id="file-upload-tab">
                        <label>Name</label>
                        <input id="name" type="text" name="name" class="form-control">
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <label class="control-label required">@lang('app.description')</label>
                        <textarea rows="4" name="description" class="summernote"></textarea>
                    </div>
                </div>

            </div>
            <!--/row-->

        </div>
        <div class="form-actions">
            <input type="hidden" name="amount" id="amount" value="0">
            <button type="button" id="store-type" class="btn btn-success"><i class="fa fa-check"></i> Save</button>
            <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
        </div>

        {!! Form::close() !!}
    </div>
</div>

<script src="{{ asset('plugins/bower_components/summernote/dist/summernote.min.js') }}"></script>
<script type="text/javascript">
    $('#store-type').click(function(){
        $.easyAjax({
            url: "{{route('member.article.storeType')}}",
            container: '#storeType',
            type: "POST",
            data: $('#storeType').serialize(),
            success: function (res) {
                location.reload(true);
            }
        });
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