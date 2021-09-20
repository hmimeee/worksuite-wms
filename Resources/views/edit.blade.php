<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/custom-select/custom-select.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.css') }}">
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
    <h4 class="modal-title"><i class="ti-plus"></i> Edit Article</h4>
</div>
<div class="modal-body">
    <div class="portlet-body">

        {!! Form::open(['id'=>'updateArticle','class'=>'ajax-form','method'=>'POST', 'files' => true]) !!}

        <div class="form-body">
            <div class="row">
                <div id="article-tab">
                </div>
                <div class="form-group row">
                    <div id="article-tab"> 
                        <div class="col-md-12"> 
                            <div class="form-group"> 
                                <label class="control-label required">@lang("app.title")
                                </label> 
                                <input type="text" id="title" name="title" class="form-control" value="{{$article->title}}"> 
                            </div> 
                        </div> 
                        <div class="col-md-4"> 
                            <div class="form-group"> 
                                <label class="control-label required">Type
                                </label> 
                                <select class="select2 form-control" data-placeholder="Type" name="type" id="types" > 
                                    <option value="{{$article->type}}">{{$article->type}}</option> 
                                    @foreach ($articleTypes as $articleType) 
                                    <option value="{{$articleType->name}}">{{$articleType->name}}</option> @endforeach 
                                </select> 
                            </div> 
                        </div> 
                        <div class="col-md-4"> 
                            <div class="form-group"> 
                                <label class="control-label required">Word Count
                                </label> 
                                <input type="number" name="word_count" class="form-control" value="{{$article->word_count}}"> 
                            </div> 
                        </div>
                        <div class="col-md-2"> 
                            <div class="form-group align-middle"> 
                                <label>Publishing
                                </label> 
                                <div class="checkbox checkbox-info"> 
                                    <input id="publishing" name="publishing" value="@if ($article->publishing ==1) false @else true @endif" type="checkbox" @if ($article->publishing ==1) checked @endif> 
                                    <label for="publishing">Yes
                                    </label> 
                                </div> 
                            </div> 
                        </div> 
                        <div class="col-md-12">
                            <hr>
                        </div> 
                    </div>
                </div>
                <!--/span-->
                <div class="col-md-12">
                    <div class="form-group">
                        <label class="control-label required">@lang('app.description')</label>
                        <textarea rows="4" name="description" class="summernote">{{$article->description}}</textarea>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group" id="file-upload-tab">
                        <div class="upload-btn-wrapper">
                          <button class="btn-upload"><i class="fa fa-paperclip"></i> Drag and Drop Your Files
                            <br>
                            <small>(Maximum file size 5MB)</small>
                        </button>
                        <input type="file" name="files[]" id="file-upload" multiple />
                    </div>
                        <!-- <label>Attachment</label>
                            <input id="file-upload" type="file" name="file" class="form-control"> -->
                        </div>
                    </div>
                    <div class="col-xs-12 m-t-10" id="fileList">Files: <br>
                        @foreach ($article->files as $file)
                        <a href="javascript:;" onclick="downloadFile('{{$file->id}}')" class="btn btn-primary btn-sm btn-rounded btn-outline m-t-10" id="file-{{$file->id}}"><i class="fa fa-file"></i> {{$file->filename}}</a> <a href="javascript:;" class="btn btn-danger btn-sm btn-rounded btn-outline m-t-10" onclick="deleteFile('{{$file->id}}')" id="btn-{{$file->id}}"><i class="fa fa-trash"></i></a><br/>
                        @endforeach
                    </div>

                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="control-label required">Project</label>
                            <select class="select2 form-control" data-placeholder="Select Project" name="project" id="projects" >
                                <option value=""></option>
                                @foreach ($projects as $project)
                                <option value="{{$project->id}}" @if($article->project->id == $project->id) selected @endif>#{{$project->id}} - {{$project->project_name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="form-group">
                            <div class="checkbox checkbox-info">
                                <input id="parent" name="parent" value="yes"
                                type="checkbox" @if($article->task !=null) checked @endif>
                                <label for="parent" class="control-label">Parent Task</label>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12" id="parent-field" style="display: none">
                        <div class="form-group">
                            <select class="select2 form-control" data-placeholder="Select Parent Task" name="parent_task" id="parent_task" >
                                <option value="{{$article->task ? $article->task->id : ''}}">#{{$article->task ? $article->task->id : ''}} - {{$article->task ? $article->task->heading : ''}}</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="control-label required">@lang('app.dueDate')</label>
                            <input type="text" name="writing_deadline" id="writing_deadline" class="form-control" autocomplete="off" value="{{$article->writing_deadline}}">
                        </div>
                    </div>
                    <!--/span-->
                    <div class="col-md-12" id="assigneeBlock">
                        <div class="form-group">
                            <label class="control-label required">@lang('modules.tasks.assignTo') Writer</label>
                            <select class="select2 form-control" data-placeholder="@lang('modules.tasks.chooseAssignee')" name="assignee" id="assignee" >
                                <option value="{{$article->assignee}}">{{$article->getAssignee->name}}</option>
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
                                type="checkbox" @if($article->assignee == auth()->id()) checked @endif>
                                <label for="self" class="control-label">Assign To Self</label>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="control-label required">@lang('modules.tasks.priority')</label>

                            <div class="radio radio-danger">
                                <input type="radio" name="priority" id="radio13"
                                value="high" @if ($article->priority =='high') checked @endif>
                                <label for="radio13" class="text-danger">
                                @lang('modules.tasks.high') </label>
                            </div>
                            <div class="radio radio-warning">
                                <input type="radio" name="priority"
                                id="radio14" value="medium" @if ($article->priority =='medium') checked @endif>
                                <label for="radio14" class="text-warning">
                                @lang('modules.tasks.medium') </label>
                            </div>
                            <div class="radio radio-success">
                                <input type="radio" name="priority" id="radio15"
                                value="low" @if ($article->priority =='low') checked @endif>
                                <label for="radio15" class="text-success">
                                @lang('modules.tasks.low') </label>
                            </div>
                        </div>
                    </div>
                    <!--/span-->
                </div>
                <!--/row-->

            </div>
            <div class="form-actions">
                <button type="button" id="edit-task" class="btn btn-success"><i class="fa fa-check"></i> @lang('app.save')</button>
                <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
            </div>

            {!! Form::close() !!}
        </div>
    </div>
    <script src="{{ asset('plugins/bower_components/summernote/dist/summernote.min.js') }}"></script>
    <script src="{{ asset('plugins/bower_components/custom-select/custom-select.min.js') }}"></script>
    <script src="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.js') }}"></script>
    <script src="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>

    <script>
    //Delete File
    function deleteFile(id) {
        var url = "{{route('member.article.removeArticle', ':id')}}?_token={{csrf_token()}}";
        var url = url.replace(':id', id);
        $.easyAjax({
            type: 'POST',
            url: url,
            success: function (response) {
                $('#file-'+id).hide();
                $('#btn-'+id).hide();
            }
        });
    }

        //Delete File
        $('#file-upload').change(function() {
            $('.btn-upload').html('Uploading...');
            var url = "{{route('member.article.storeFiles')}}?_token={{csrf_token()}}";
            var CSRF_TOKEN = '{{ csrf_token() }}';
            var formData = new FormData();
            // formData.append('file', $('#file-upload')[0].files[0]);
            for (var i = 0; i < $('#file-upload').get(0).files.length; ++i) {
                formData.append('files[]', $('#file-upload').get(0).files[i]);
            }

            formData.append('articles', "{{$article->id}}");
            $('#file-upload').hide();

            $.ajax({
                url:  url,
                type: 'POST',
                data: formData,
                contentType: false, 
                processData: false,
                success: function (result) {
                    if (result.status == 'success') {
                        for (var i = 0; i < result.file.length; i++) {
                            $('#fileList').append('<a href="javascript:;" onclick="downloadFile('+result.fileId[i]+')" class="btn btn-primary btn-sm btn-rounded btn-outline m-t-10" id="file-'+result.fileId[i]+'"><i class="fa fa-file"></i> '+result.file[i]+'</a> <a href="javascript:;" class="btn btn-danger btn-sm btn-rounded btn-outline m-t-10" onclick="deleteFile('+result.fileId[i]+')" id="btn-'+result.fileId[i]+'"><i class="fa fa-trash"></i></a><br>');
                        }
                        $('#file-upload').val('');
                        $('#file-upload').show();
                        $('.btn-upload').html('<i class="fa fa-paperclip"></i> Drag and Drop Your Files');


                        $.showToastr(result.message, 'success');
                    }
                }
            })
        })

        if($('#parent').is(':checked')){
            var url = "{{route('member.article.projectData', ':id')}}";
            var url = url.replace(':id', $('#projects').val());
            $.easyAjax({
                url: url,
                type: "GET",
                success: function (res) {
                    var data = '';
                    for(var i = 0; i < res.tasks.length; i++){
                        var data = data+'<option value="'+res.tasks[i].id+'">#'+res.tasks[i].id+' - '+res.tasks[i].heading+'</option>';
                    }
                    $('#parent_task').append(data);
                }
            });
        }

        $('#projects').change(function(){
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
    $('#edit-task').click(function () {
        var id = "{{$article->id}}";
        var url = "{{route('member.article.update', ':id')}}";
        var url = url.replace(':id', id);
        $.easyAjax({
            url: url,
            container: '#updateArticle',
            type: "POST",
            data: $('#updateArticle').serialize(),
            success: function (res) {
                $("#subTaskModal").modal('toggle');

                var location = "{{auth()->user()->hasRole('admin') ? route('admin.article.show', ':id') : route('member.article.show', ':id')}}";
                var location = location.replace(':id', id);
                
                if ('{{request()->ref}}' === 'show') { 
                    document.location.href = location
                } else {
                    var url = "{{ route('member.article.showModal',':id') }}";
                    url = url.replace(':id', id);
                    $.easyAjax({
                        type: 'GET',
                        url: url,
                        success: function (response) {
                            if (response.status == "success") {
                                $('#right-sidebar-content').html(response.view);
                            }
                        }
                    });
                }
                
            }
        })
    });

    jQuery('#writing_deadline').datepicker({
        format: 'yyyy-mm-dd',
        autoclose: true,
        todayHighlight: true
    });

    $("#projects").select2({
        formatNoMatches: function () {
            return "{{ __('messages.noRecordFound') }}";
        }
    })

    $("#parent_task").select2({
        formatNoMatches: function () {
            return "{{ __('messages.noRecordFound') }}";
        }
    });

    $("#types").select2({
        formatNoMatches: function () {
            return "{{ __('messages.noRecordFound') }}";
        }
    });

    $("#assignee").select2({
        formatNoMatches: function () {
            return "{{ __('messages.noRecordFound') }}";
        }
    });

    $('#parent').change(function () {
        if($(this).is(':checked')){
            $('#parent-field').show();
        }
        else{
            $('#parent-field').hide();
        }
    })

    if($('#parent').is(':checked')){
        $('#parent-field').show();
    }
    else{
        $('#parent-field').hide();
    }

    $('#self').change(function () {
        if($(this).is(':checked')){
            $('#assigneeBlock').hide();
        }
        else{
            $('#assigneeBlock').show();
        }
    });

    if($('#self').is(':checked')){
        $('#assigneeBlock').hide();
    }
    else{
        $('#assigneeBlock').show();
    }
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
        })
    </script>

