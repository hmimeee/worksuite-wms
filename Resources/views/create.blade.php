<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/custom-select/custom-select.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/dropzone-master/dist/dropzone.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/summernote/dist/summernote.css') }}">
<style type="text/css">
    .upload-btn-wrapper {
      position: relative;
      overflow: hidden;
      display: inline-block;
  }

  .btn-upload {
      border: 1px dotted gray;
      color: gray;
      background-color: white;
      padding: 20px;
      border-radius: 8px;
      font-weight: bold;
      width: 400px;
  }

  .upload-btn-wrapper input[type=file] {
      font-size: 100px;
      position: absolute;
      left: 0;
      top: 0;
      opacity: 0;
  }
</style>

<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h4 class="modal-title"><i class="ti-plus"></i> Assign New Articles</h4>
</div>
<div class="modal-body">
    <div class="portlet-body">

        {!! Form::open(['id'=>'storeArticle','class'=>'ajax-form','method'=>'POST', 'files' => true]) !!}

        <div class="form-body">
            <div class="row">
                <div id="article-tab">

                </div>
                <div class="form-group row">
                    <div class="col-md-4">
                        <input type="number" name="article_count" class="form-control" id="article_count" placeholder="Number of articles">
                    </div>
                    <div class="col-md-8">
                        <button type="button" class="btn btn-success btn-sm" id="add-more"><i class="fa fa-plus"></i> Add Article Details</button>
                    </div>
                </div>

                <!-- <div class="col-md-12">
                    <div class="form-group">
                        <label class="control-label required">Description</label>
                        <textarea rows="4" name="description" class="summernote"></textarea>
                    </div>
                </div> -->

                <div class="col-md-12">
                    <div class="form-group" id="file-upload-tab">
                        <div id="selected-files" class="m-b-10"></div>
                        <div class="upload-btn-wrapper">
                          <button class="btn-upload"><i class="fa fa-paperclip"></i> Drag and Drop Your Files
                            <br>
                            <small>(Maximum file size 5MB)</small>
                        </button>
                        <input type="file" name="files[]" id="file-upload" multiple />
                    </div>
                </div>
            </div>

            <div class="col-md-12">
                <div class="form-group">
                    <label class="control-label required">Project</label>
                    <select class="select2 form-control" data-placeholder="Select Project" name="project" id="projectTab" >
                        <option value=""></option>
                        @foreach ($projects as $project)
                        <option value="{{$project->id}}" {{request()->project_id == $project->id ? 'selected' : ''}}>#{{$project->id}} - {{$project->project_name}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group m-t-0" id="parentBlock" style="display: none;">
                    <div class="checkbox checkbox-info">
                        <input id="parent" name="parent" value="yes"
                        type="checkbox">
                        <label for="parent" class="control-label">Parent Task</label>
                    </div>
                </div>
            </div>
            
            <div class="col-md-12" id="parent-field" style="display: none">
                <div class="form-group">
                    <select class="select2 form-control" data-placeholder="Select Parent Task" name="parent_task" id="parent_task" >
                        <option value=""></option>
                    </select>
                </div>
            </div>

            <div class="col-md-12" id="assigneeBlock">
                <div class="form-group">
                    <label class="control-label required">Assign To</label>
                    <select class="select2 form-control" data-placeholder="@lang('modules.tasks.chooseAssignee')" name="assignee" id="assignee" >
                        <option value=""></option>
                        @foreach ($writers as $writer)
                        <option value="{{$writer->id}}">{{$writer->name}}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="col-md-12">
                <div class="form-group">
                    <div class="checkbox checkbox-info">
                        <input id="self" name="self" value="{{auth()->id()}}"
                        type="checkbox">
                        <label for="self" class="control-label">Assign To Self</label>
                    </div>
                </div>
            </div>

            <div class="col-md-12">
                <div class="form-group">
                    <label class="control-label required">Priority</label>

                    <div class="radio radio-danger">
                        <input type="radio" name="priority" id="radio13"
                        value="high">
                        <label for="radio13" class="text-danger">
                        High </label>
                    </div>
                    <div class="radio radio-warning">
                        <input type="radio" name="priority"
                        id="radio14" checked value="medium">
                        <label for="radio14" class="text-warning">
                        Medium </label>
                    </div>
                    <div class="radio radio-success">
                        <input type="radio" name="priority" id="radio15"
                        value="low">
                        <label for="radio15" class="text-success">
                        Low </label>
                    </div>
                </div>
            </div>
            <!--/span-->
            <input type="hidden" id="articlesID" name="articles" value="">

        </div>
        <!--/row-->

    </div>
    <div class="form-actions">
        <button type="button" id="store-task" class="btn btn-success"><i class="fa fa-check"></i> Save</button>
        <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
    </div>

    {!! Form::close() !!}
</div>
</div>
<script src="{{ asset('plugins/bower_components/summernote/dist/summernote.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/custom-select/custom-select.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
<script>
    $('#file-upload').change(function(){
        var data = '';
        $('.btn-upload').html('Uploading...');
        for (var i = 0; i < $('#file-upload').get(0).files.length; ++i) {
            var data = data + "<a href='javascript:;' class='m-b-5'><i class='fa fa-paperclip'></i> " + $('#file-upload').get(0).files[i].name + "</a><br/>";
        }
        $('.btn-upload').html('<i class="fa fa-paperclip"></i> Drag and Drop Your Files');
        $('#selected-files').html(data);
    })

    $('#add-more').click(function(){
        var article_count = $('#article_count').val();
        for (var i = 0; i < article_count; i++) {
            var color = '#'+(Math.random()*0xFFFFFF<<0).toString(16);
            var article_tab = '<div id="article-tab"> <div class="col-md-12"> <div class="form-group"> <label class="control-label required"><span style="background:'+color+'; padding: 3px; border-radius: 2px; color: white;">#'+(i+1)+'</span> Title</label> <input type="text" id="title" name="title[]" class="form-control" > </div> </div> <div class="col-md-4"> <div class="form-group"> <label class="control-label required">Type</label> <select class="select2 form-control" data-placeholder="Type" name="type[]" id="type_'+i+'" > <option value=""></option> @foreach ($articleTypes as $articleType) <option value="{{$articleType->name}}">{{$articleType->name}}</option> @endforeach </select> </div> </div> <div class="col-md-3"> <div class="form-group"> <label class="control-label required">Word Count</label> <input type="number" name="word_count[]" class="form-control"> </div> </div> <div class="col-md-3"> <div class="form-group"> <label class="control-label required">Due Date</label> <input type="text" name="writing_deadline[]" class="form-control" id="writing_deadline_'+i+'" autocomplete="off"> </div> </div> <div class="col-md-2"> <div class="form-group align-middle"> <label>Publishing</label> <div class="checkbox checkbox-info"> <input id="'+i+'" name="publishing[]" value="true" type="checkbox"> <label for="'+i+'">Yes</label> </div> </div> </div> <div class="col-md-12"><hr></div> </div> <div class="col-md-12"> <div class="form-group"><label class="control-label required">Description</label><textarea rows="4" name="description[]" class="summernote"></textarea></div></div> <script> jQuery("#writing_deadline_'+i+'").datepicker({format: "yyyy-mm-dd",autoclose: true,todayHighlight: true}); $("#type_'+i+'").select2({formatNoMatches: function () {return "{{ __("messages.noRecordFound") }}";}}); $(".summernote").summernote({height: 100, minHeight: null, maxHeight: null,focus: false, toolbar: [ ["style", ["bold", "italic", "underline", "clear"]], ["font", ["strikethrough", "superscript", "subscript"]], ["fontsize", ["fontsize"]], ["color", ["color"]], ["para", ["ul", "ol", "paragraph"]], ["height", ["height"]]]});';

            $('#article-tab').append(article_tab);
            $('#add-more').hide();
            $('#article_count').hide();
        }
    })

    $(document).ready(function(){
        var project = $('#projectTab').val();
        var task = '{{request()->task_id}}';
        if (project =='') { return false;}

        $('#parentBlock').show();
        var url = "{{route('member.article.projectData', ':id')}}";
        var url = url.replace(':id', $('#projectTab').val());
        $.easyAjax({
            url: url,
            type: "GET",
            success: function (res) {
                var data = '';
                for(var i = 0; i < res.tasks.length; i++){
                    var data = data+'<option value="'+res.tasks[i].id+'">#'+res.tasks[i].id+' - '+res.tasks[i].heading+'</option>';
                }
                $('#parent_task').html(data);
                 $('option:selected', this).attr('value="'+task+'"');
            }
        });
    })


    $('#projectTab').change(function(){
        var url = "{{route('member.article.projectData', ':id')}}";
        var url = url.replace(':id', $(this).val());
        $.easyAjax({
            url: url,
            type: "GET",
            success: function (res) {
                var data = '';
                for(var i = 0; i < res.tasks.length; i++){
                    var data = data+'<option value="'+res.tasks[i].id+'">#'+res.tasks[i].id+' - '+res.tasks[i].heading+'</option>';
                }
                $('#parent_task').html(data);
            }
        });

    })



    //    update task
    $('#store-task').click(function () {
        $.easyAjax({
            url: "{{route('member.article.store')}}",
            container: '#storeArticle',
            type: "POST",
            data: $('#storeArticle').serialize(),
            success: function (res) {
                if (res.status ==='success') {
                    var CSRF_TOKEN = '{{ csrf_token() }}';
                    var formData = new FormData();
                    for (var i = 0; i < $('#file-upload').get(0).files.length; ++i) {
                        formData.append('files[]', $('#file-upload').get(0).files[i]);

                    }

                    $.ajax({
                        url:  "{{route('member.article.storeFiles')}}?articles="+res.articles+"&_token="+CSRF_TOKEN,
                        type: 'POST',
                        data: formData,
                        contentType: false, 
                        processData: false,
                        success: function (result) {
                            if (result.status == 'success') {
                                location.reload(true);
                                $.showToastr(result.message, 'success');
                            }
                        }
                    })
                }
            }
        });
    });

    jQuery('#writing_deadline').datepicker({
        format: 'yyyy-mm-dd',
        autoclose: true,
        todayHighlight: true
    });

    $("#parent_task").select2({
        formatNoMatches: function () {
            return "{{ __('messages.noRecordFound') }}";
        }
    });

    $("#projectTab").select2({
        formatNoMatches: function () {
            return "{{ __('messages.noRecordFound') }}";
        }
    });

    $("#assignee").select2({
        formatNoMatches: function () {
            return "{{ __('messages.noRecordFound') }}";
        }
    });

    $('#projectTab').change(function () {
        $('#parentBlock').show();
    });

    $('#parent').change(function () {
        if($(this).is(':checked')){
            $('#parent-field').show();
        }
        else{
            $('#parent-field').hide();
        }
    });

    $('#self').change(function () {
        if($(this).is(':checked')){
            $('#assigneeBlock').hide();
        }
        else{
            $('#assigneeBlock').show();
        }
    });
</script>
<script type="text/javascript">
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

