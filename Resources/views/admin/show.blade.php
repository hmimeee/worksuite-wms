@extends('layouts.app')

@section('page-title')
<div class="row bg-title">
    <!-- .page title -->
    <div class="col-lg-8 col-md-5 col-sm-6 col-xs-12">
        <a href="{{route('admin.article.index')}}" class="btn btn-sm btn-rounded btn-outline m-b-10" style="background: grey; color: white; margin-left: -15px;"><i class="fa fa-arrow-left"></i> Return to Articles</a>

        <h4 class="page-title"><i class="{{ $pageIcon ?? '' }}"></i> Article #{{ $article->id ?? '' }}
        </h4>
    </div>

    <!-- /.page title -->
    <!-- .breadcrumb -->
    <div class="col-lg-4 col-sm-6 col-md-7 col-xs-12 text-right">

    </div>
    <!-- /.breadcrumb -->
</div>
@endsection

@push('head-script')
<style type="text/css">
    .rate{float:left;height:46px;padding:0 10px}.rate:not(:checked)>input{position:absolute;top:-9999px}.rate:not(:checked)>label{float:right;width:1em;overflow:hidden;white-space:nowrap;cursor:pointer;font-size:30px;color:#ccc}.rate:not(:checked)>label:before{content:'★ '}.rate>input:checked~label{color:#ffc700}.rate:not(:checked)>label:hover,.rate:not(:checked)>label:hover~label{color:#deb217}.rate>input:checked+label:hover,.rate>input:checked+label:hover~label,.rate>input:checked~label:hover,.rate>input:checked~label:hover~label,.rate>label:hover~input:checked~label{color:#c59b08}.upload-btn-wrapper{position:relative;overflow:hidden;display:inline-block}.btn-upload{border:1px dotted gray;color:gray;background-color:#fff;padding:25px;border-radius:8px;font-weight:700;display:block;min-width:500px}.upload-btn-wrapper input[type=file]{font-size:100px;position:absolute;left:0;top:0;opacity:0}.sl-item:nth-child(n+6){display:none}.checked{color:#deb217}
</style>
<link rel="stylesheet" href="{{ asset('plugins/bower_components/summernote/dist/summernote.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/custom-select/custom-select.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/custom-select/custom-select.css') }}">
<style>
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
    <div class="col-xs-12">
        <div class="white-box" style="padding: 10px;"> 
            @if(auth()->user()->hasRole('admin'))
            <a href="javascript:;" class="btn btn-danger btn-sm m-b-10 btn-rounded btn-outline pull-right m-l-5" onclick="deleteArticle('{{$article->id}}')"> <i class="fa fa-trash"></i> Delete</a>
            @endif
            @if (auth()->user()->id == $article->creator || auth()->user()->hasRole('admin'))
            <a href="javascript:;" class="btn btn-info btn-sm m-b-10 btn-rounded btn-outline pull-right m-l-5" onclick="editArticle('{{$article->id}}')"> <i class="fa fa-edit"></i> Edit</a>
            @endif
            <!-- ## Copy Link Starts ## -->
            <textarea id="copyText" style="display: none;">{{route('member.article.show', $article->id)}}</textarea>
            <a href="javascript:;" id="copyLink" onclick="copyLink()" class="btn btn-info btn-sm m-b-10 btn-rounded btn-outline pull-right" title="Copy Link" style="margin-left: 5px;"><i class="fa fa-link"></i> Copy Link</a>
            <!-- ## Copy Link Ends ## -->

            @if(($article->writing_status ==0 || ($article->publisher !=null && $article->publishing_status !=1)) && ($article->creator == auth()->id() || auth()->user()->hasRole('admin')))
            <a href="javascript:;" id="reminderButton" onclick="sendReminder('{{$article->id}}')" class="btn btn-info btn-sm m-b-10 btn-rounded btn-outline pull-right" title="@lang('messages.remindToAssignedEmployee')"><i class="fa fa-envelope"></i> @lang('modules.tasks.reminder')</a>
            @endif

            @if($article->writing_status == 0 && $article->working_status == null) 
            <label class="btn-sm btn-outline btn-default m-r-5 btn-primary pull-right">Not Started</label>
            @elseif($article->writing_status == 0 && $article->working_status == 1)
            <label class="btn-sm btn-outline btn-default m-r-5 btn-info pull-right">Working</label>
            @elseif($article->writing_status == 1)

            @if(!is_null($article->reviewWriter) && (is_null($article->reviewStatus) || $article->reviewStatus->value != 'completed'))
            <label class="btn-sm btn-outline btn-default m-r-5 btn-primary pull-right">Under Review</label>
            @elseif(!is_null($article->reviewStatus) && $article->reviewStatus->value == 'completed')
            <label class="btn-sm btn-outline btn-default m-r-5 btn-success pull-right">Review Complete</label>
            @else
            <label class="btn-sm btn-outline btn-default m-r-5 btn-warning pull-right">Pending for Aproval</label> 
            @endif

            @elseif($article->writing_status == 2)
            @if($article->publishing == 1 && !auth()->user()->hasRole($writerRole))

            @if($article->publishing_status == 1)
            <label class="btn-sm btn-outline btn-default m-r-5 btn-default pull-right" style="color: grey;">Completed Publishing</label>
            @elseif($article->publishing_status != 1 && $article->publish != null)
            <label class="btn-sm btn-outline btn-default m-r-5 btn-success pull-right">Started Publishing</label>
            @else
            <label class="btn-sm btn-outline btn-default m-r-5 btn-danger pull-right">Waiting for Publish</label>
            @endif

            @else
            <label class="btn-sm btn-outline btn-default m-r-5 btn-default pull-right" style="color: grey;">Completed Writing</label>
            @endif
            @endif

            @if(($article->publisher == auth()->id() || $article->creator == auth()->id() || auth()->user()->hasRole('admin')) && $article->writing_status == 2 && $article->publishing == 1 && $article->publish == null)
            <a href="javascript:;" id="startPublishing" class="btn btn-info btn-sm m-b-10 btn-rounded"  onclick="startPublishing('start')"><i class="fa fa-hourglass-start"></i> Start Publishing</a>
            @endif

            @if($article->writing_status ==2 && ($writerHead == auth()->user()->id || auth()->user()->hasRole('admin')) && $article->publishing_status ==1)
            <a href="javascript:;" id="publishButton" class="btn btn-danger btn-sm m-b-10 btn-rounded btn-outline"  onclick="completePublish('incomplete')" ><i class="fa fa-arrow-left"></i> Return to Publisher</a>
            @endif

            @if($article->writing_status ==2 && ($article->publisher == auth()->user()->id || $writerHead == auth()->user()->id || auth()->user()->hasRole('admin') == 1) && ($article->publishing_status ==null || $article->publishing_status ==0) && $article->publishing == 1 && $article->publish != null)
            <a href="javascript:;" id="publishButton" class="btn btn-success btn-sm m-b-10 btn-rounded"  onclick="completePublish('complete')" ><i class="fa fa-check"></i> Complete Publishing</a>
            @endif

            @if(($article->assignee == auth()->id() || $article->creator == auth()->id() || auth()->user()->hasRole('admin')) && $article->writing_status == 0 && $article->working_status == null)
            <a href="javascript:;" id="startWork" class="btn btn-info btn-sm m-b-10 btn-rounded"  onclick="startWork('start')"><i class="fa fa-hourglass-start"></i> Start Working</a>
            @endif

            @if($article->working_status !=null && $article->writing_status ==0 && ($article->assignee == auth()->user()->id || $writerHead == auth()->user()->id || auth()->user()->hasRole('admin')))
            <a href="javascript:;" id="completedButton" class="btn btn-success btn-sm m-b-10 btn-rounded"  onclick="markComplete('complete')" ><i class="fa fa-check"></i> Submit for Approval</a>
            @endif

            @if($article->writing_status ==1 && (is_null($article->reviewStatus) || $article->reviewStatus->value != 'completed') && (!is_null($article->reviewWriter) && ($article->reviewWriter->value == auth()->id() || $writerHead == auth()->id() || auth()->user()->hasRole('admin'))))
            <a href="javascript:;" class="btn btn-success btn-sm m-b-10 btn-rounded pull-left m-r-5"  onclick="reviewStatus('completed')"><i class="fa fa-check"></i> Review Complete</a>

            @if(is_null($article->reviewStatus) || $article->reviewStatus->value !='completed')
            <div class="form-group row m-l-5">
                <div class="col-md-2 col-sm-12">
                    <label class="control-label required" style="display: block;">Rate This Article</label>
                    <label class="control-label" for="rating"></label>
                    <div class="rate" id="rating">
                        <input type="radio" id="star5" name="rate" value="5" />
                        <label for="star5" title="5">5 stars</label>
                        <input type="radio" id="star4" name="rate" value="4" />
                        <label for="star4" title="4">4 stars</label>
                        <input type="radio" id="star3" name="rate" value="3" />
                        <label for="star3" title="3">3 stars</label>
                        <input type="radio" id="star2" name="rate" value="2" />
                        <label for="star2" title="2">2 stars</label>
                        <input type="radio" id="star1" name="rate" value="1" />
                        <label for="star1" title="1">1 star</label>
                    </div>
                </div>
                <div class="col-md-2 col-sm-12">
                    <label class="control-label required">Word Count</label>
                    <input type="number" name="wordCount" id="wordCount" class="form-control">
                </div>

                @if($article->publishing ==1)
                <div class="col-md-2">
                    <label class="control-label required">Publishing Due Date</label>
                    <input type="text" name="publishing_deadline" id="publishing_deadline" class="form-control" autocomplete="off">
                </div>
                <div class="col-md-2">
                    <label class="control-label">Website to Publish</label>
                    <input type="text" name="website" id="website" class="form-control">
                </div>
                @endif
            </div>
            @endif
            @endif

            @if(($article->writing_status ==1 || $article->writing_status ==2) && ($writerHead == auth()->user()->id || auth()->user()->hasRole('admin')))
            <a href="javascript:;" id="inCompletedButton" class="btn btn-danger btn-outline btn-sm m-b-10 btn-rounded"  onclick="markComplete('incomplete')"><i class="fa fa-arrow-left"></i> Return to Writer</a>
            @endif

            @if($article->writing_status ==2 && ($article->publisher == auth()->user()->id || $writerHead == auth()->user()->id || auth()->user()->hasRole('admin') == 1) && ($article->publishing_status ==null || $article->publishing_status ==0) && $article->publishing == 1 && $article->publish != null)
            <div class="form-group row m-t-10">
                <div class="col-md-6">
                    <label class="control-label required" for="publishLink">Publish Link</label>
                    <input type="text" name="publishLink" id="publishLink" placeholder="Enter your publish link here" class="form-control col-md-12 m-b-10">
                </div>
            </div>
            @endif

            @if(is_null($article->reviewWriter))
            @if($article->writing_status ==1 && ($writerHead == auth()->id() || auth()->user()->hasRole('admin')))
            <a href="javascript:;" id="finishButton" class="btn btn-success btn-sm m-b-10 m-r-5 btn-rounded pull-left"  onclick="markComplete('finish')"><i class="fa fa-check"></i> Accept and Finish</a>

            <div class="form-group row m-l-5">
                <div class="col-md-2">
                    <label class="control-label required" style="display: block;">Rate This Article</label>
                    <label class="control-label" for="rating"></label>
                    <div class="rate" id="rating">
                        <input type="radio" id="star5" name="rate" value="5" />
                        <label for="star5" title="5">5 stars</label>
                        <input type="radio" id="star4" name="rate" value="4" />
                        <label for="star4" title="4">4 stars</label>
                        <input type="radio" id="star3" name="rate" value="3" />
                        <label for="star3" title="3">3 stars</label>
                        <input type="radio" id="star2" name="rate" value="2" />
                        <label for="star2" title="2">2 stars</label>
                        <input type="radio" id="star1" name="rate" value="1" />
                        <label for="star1" title="1">1 star</label>
                    </div>
                </div>
                <div class="col-md-2">
                    <label class="control-label required">Word Count</label>
                    <input type="number" name="wordCount" id="wordCount" class="form-control">
                </div>

                @if($article->publishing ==1)
                <div class="col-md-2">
                    <label class="control-label required">Publishing Due Date</label>
                    <input type="text" name="publishing_deadline" id="publishing_deadline" class="form-control" autocomplete="off">
                </div>
                <div class="col-md-3">
                    <label class="control-label">Website to Publish</label>
                    <input type="text" name="website" id="website" class="form-control">
                </div>
                @endif
            </div>
            @endif
            @endif
        </div>
    </div>

    @if($article->writing_status ==1 && ($writerHead == auth()->user()->id || auth()->user()->hasRole('admin')))
    <div class="col-xs-12">
        <div class="white-box" style="padding: 10px;">
            <div class="form-group row m-l-5">
                <div class="col-md-2">
                    <h4>Article Review Section:</h4>
                </div>
                @if(is_null($article->reviewWriter))
                <div class="col-md-2">
                    <label class="control-label">Transfer to Editor</label>
                    <select class="form-control" id="editor" name="editor">
                        @foreach($editors as $editor)
                        <option value="{{$editor->id}}">{{$editor->name}}</option>
                        @endforeach
                    </select>
                </div> 
                <div class="col-md-2">
                    <button class="btn btn-sm btn-info" style="margin-top: 23px !important;" id="editorTransfer">Transfer</button>
                </div>
                @elseif(is_null($article->reviewStatus) || $article->reviewStatus->value != 'completed')
                <div class="col-md-10">
                    <h4>
                        <i class="fa fa-spinner"></i> Article review is in progress to the writer, <b>{{\App\User::find($article->reviewWriter->value)->name}}</b>
                    </h4>
                </div>
                @elseif(!is_null($article->reviewStatus) || $article->reviewStatus->value == 'completed')
                <div class="col-md-10">
                    <h4>
                        <i class="fa fa-check"></i> Article review has been completed by <b>{{\App\User::find($article->reviewWriter->value)->name}}</b>

                        <button class="btn btn-sm btn-danger btn-outline m-l-5" onclick="reviewStatus('incomplete')">Return for Review Again</button>
                    </h4>
                </div>
                @endif
            </div>
        </div>
    </div>
    @endif

    <div class="col-xs-12">
        <div class="white-box"  style="padding: 1% 5%;"> 
            <h5 style="font-weight: 500; color: #337ab7;">{{$article->title}} <label class="label label-default text-dark m-l-5 font-light">{{$article->type}}</label> <label class="label @if($article->priority =='low') label-success @elseif($article->priority =='medium') label-warning @else label-danger @endif"><span class="text-dark">Priority > </span>@if($article->priority =='low') Low @elseif($article->priority =='medium') Medium @else High @endif</label>
            </h5>
            @if(!auth()->user()->hasRole($writerRole))
            <p><i class="icon-layers"></i> Project: <a style="font-weight: 400;" target="_blank" href="@if(auth()->user()->hasRole('admin')){{ route('admin.projects.show', $article->project->id) }}@else{{ route('member.projects.show', $article->project->id) }}@endif"> {{$article->project->project_name}} </a> </p>
            @endif
            @if (($writerHead == auth()->user()->id || auth()->user()->hasRole('admin')) && $article->task !=null)
            <p><i class="fa fa-tasks"></i> Parent Task: <a style="font-weight: 400;" href="javascript:;" onclick="parentTask('{{$article->parent_task}}')">{{$article->task->heading}} </a> </p>
            @endif

            <p><i class="ti-write"></i> @if($article->writing_status ==2) Word Count: @else Assigned Word: @endif {{$article->word_count}}</p>

            @if($article->writing_status != 0)
            <p><i class="icon-clock"></i> Writing Deadline: {{\Carbon\Carbon::create($article->writing_deadline)->format('d M Y')}} </p>
            @endif

            @if ($article->rating !=null && (!auth()->user()->hasRole($writerRole) || !auth()->user()->hasRole($inhouseWriterRole)))
            <p><i class="fa fa-thumbs-up"></i> Rating: 
                <span class="fa fa-star @if ($article->rating > 0) checked @endif"></span>
                <span class="fa fa-star @if ($article->rating > 1) checked @endif"></span>
                <span class="fa fa-star @if ($article->rating > 2) checked @endif"></span>
                <span class="fa fa-star @if ($article->rating > 3) checked @endif"></span>
                <span class="fa fa-star @if ($article->rating > 4) checked @endif"></span>
            </p>
            @endif

            @if ($article->publishing ==1 && $article->writing_status ==2 && $article->publisher !=null && !auth()->user()->hasRole($writerRole) && $article->publish_website !=null)
            <p><i class="fa fa-globe"></i> Publish Website:
                <b>{{$article->publish_website->value}}</b>
            </p>
            @endif

            @if ($article->publishing_status ==1 && !auth()->user()->hasRole($writerRole) && !auth()->user()->hasRole($inhouseWriterRole))
            <p><i class="fa fa-link"></i> Published Link: <a style="font-weight: 400;" target="_blank" href="{{$article->publish_link}}"> Click Here to Visit</a></p>
            @endif
        </div>
    </div>

    <div class="col-xs-12" id="task-detail-section">
        <div class="white-box"  style="padding: 1% 5%;"> 
            <div class="row">
                <div class="col-xs-6 col-md-3 font-12 m-t-10">
                    <label class="font-12" for="">@lang('modules.tasks.assignBy')</label><br>
                    <img src="@if (App\User::find($article->creator)->image ==null){{url('/img/default-profile-2.png')}} @else {{url('/user-uploads/avatar/'.App\User::find($article->creator)->image)}} @endif" class="img-circle" width="25" height="25" alt="">

                    {{App\User::find($article->creator)->name}}
                </div>
                <div class="col-xs-6 col-md-3 font-12 m-t-10">
                    <label class="font-12" for="">@lang('modules.tasks.assignTo') Writer</label><br>
                    <img src="@if (App\User::find($article->assignee)->image ==null){{url('/img/default-profile-2.png')}} @else {{url('/user-uploads/avatar/'.App\User::find($article->assignee)->image)}} @endif" data-toggle="tooltip" data-original-title="{{App\User::find($article->assignee)->name}}" data-placement="right" class="img-circle" width="25" height="25" alt="">
                    {{App\User::find($article->assignee)->name}}
                </div>
                @if ($article->publishing ==1 && $article->writing_status ==2 && $article->publisher !=null && !auth()->user()->hasRole($writerRole))
                <div class="col-xs-6 col-md-3 font-12 m-t-10">
                    <label class="font-12" for="">@lang('modules.tasks.assignTo') Publisher</label><br>
                    <img src="@if ($article->getPublisher->image ==null){{url('/img/default-profile-2.png')}} @else {{url('/user-uploads/avatar/'.$article->getPublisher->image)}} @endif" class="img-circle" width="25" height="25" alt="">

                    {{$article->getPublisher->name}}
                </div>
                @endif

                @if($article->writing_status ==0)
                <div class="col-xs-6 col-md-3 font-12 m-t-10">
                    <label class="font-12" for="">Writing Due Date</label><br>
                    <span class="@if(\Carbon\Carbon::parse($article->writing_deadline)->isPast()) text-danger @else text-info @endif">
                        {{$article->writing_deadline}}
                    </span>
                </div>
                @endif

                @if($article->publishing ==1 && $article->writing_status ==2 && !auth()->user()->hasRole($writerRole))
                <div class="col-xs-6 col-md-3 font-12 m-t-10">
                    <label class="font-12" for="">Publishing Due Date</label><br>
                    <span class="@if(\Carbon\Carbon::parse($article->writing_deadline)->isPast()) text-danger @else text-info @endif">
                        {{$article->publishing_deadline}}
                    </span>
                </div>
                @endif

                <div class="col-lg-9 col-md-9 col-sm-12 col-xs-12">

                    <div class="col-xs-12 col-md-12 m-t-10 p-10">
                        <ul class="nav customtab nav-tabs" role="tablist">
                            <li role="presentation" class="active"><a href="#main" aria-controls="main" role="tab" data-toggle="tab" aria-expanded="true">Description</a></li>
                            <li role="presentation" class=""><a href="#comments" aria-controls="comments" role="tab" data-toggle="tab" aria-expanded="false">Comments ({{$article->comments ? count($article->comments) : 0}})</a></li>
                        </ul>
                    </div>
                    <div class="tab-content" id="task-detail-section">
                        <div role="tabpanel" class="tab-pane fade active in" id="main">
                            <div class="col-xs-12 col-md-12 task-description b-all p-10">
                                <div style="font-size: 14px;">Files:</div> 
                                @forelse ($article->files as $file)
                                @php
                                $size = file_exists(public_path('/user-uploads/article-files/').$file->hashname) ? filesize(public_path('/user-uploads/article-files/').$file->hashname)/1024 : 0
                                @endphp
                                <div class="col-md-12 col-xs-12 m-t-5" id="file-{{$file->id}}">
                                   <a href="javascript:;" onclick="downloadFile('{{$file->id}}')" class="btn btn-default btn-sm btn-rounded btn-outline"><i class="fa fa-paperclip"></i> {{$file->filename}} 
                                       @if($size < 1024)
                                       ({{number_format($size, 2)}} KB)
                                       @elseif($size > 1024)
                                       ({{number_format($size/1024, 2)}} MB)
                                       @endif
                                   </a> 
                                   @if($article->creator == auth()->user()->id || auth()->user()->hasRole('admin'))
                                   <a href="javascript:;" class="btn btn-danger btn-sm btn-rounded btn-outline" onclick="deleteFile('{{$file->id}}')" id="btn-{{$file->id}}"><i class="fa fa-trash"></i></a>
                                   @endif
                               </div>
                               @empty
                               No attachments.
                               @endforelse
                           </div>
                           <div class="col-xs-12 col-md-12 task-description b-all p-10 m-t-5">
                            {!! $article->description !!}
                        </div>
                    </div>

                    <div role="tabpanel" class="tab-pane" id="comments">

                        <div class="col-xs-12 m-b-10" id="comments-list">
                            @foreach ($article->comments as $comment)
                            <div class="row m-t-10" style="background: rgba(0,0,0,0.03); padding: 5px;">
                                <div class="col-xs-10 m-b-10">
                                    <a href="javascript:;"><b>{{$comment->user->name}}</b></a> {{$comment->created_at->format('d M Y')}}
                                </div>
                                @if($writerHead == auth()->user()->id || auth()->user()->hasRole('admin'))
                                <div class="col-xs-2 text-right">
                                    <a href="javascript:;" onclick="delComment('{{$comment->id}}')">@lang('app.delete')</a>
                                </div>
                                @endif
                                <label class="col-xs-12 m-b-10 font-12" for=""> {!!$comment->comment!!} </label>
                                <div class="col-xs-12">
                                    @if ($comment->files !=null)
                                    Files: <hr/>
                                    @php
                                    $file = explode(',', $comment->files);
                                    $count = count($file);
                                    @endphp

                                    @for($i=0; $i < $count; $i++)
                                    <a style='font-weight: 500;' href='javascript:;' onclick='commentDownload("{{$file[$i]}}")' class='m-t-5'><i class="fa fa-paperclip"></i> {{$file[$i]}}</a>
                                    @php($size = filesize(public_path('/user-uploads/article-comment-files/').$file[$i])/1024)
                                    @if($size < 1024)
                                    ({{number_format($size, 2)}} KB)
                                    @elseif($size > 1024)
                                    ({{number_format($size/1024, 2)}} MB)
                                    @endif
                                    <br/>
                                    @endfor
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>

                        @if(auth()->id() == $article->assignee || (!is_null($article->reviewWriter) && auth()->id() == $article->reviewWriter->value) || auth()->id() == $article->creator || auth()->id() == $article->publisher || auth()->user()->hasRole('admin') || auth()->id() == $writerHead)
                        <div class="form-group" id="comment-box">
                            <form id='Comment' method='POST' enctype="multipart/form-data">
                                @csrf
                                <div class="col-xs-12">
                                    <textarea name="comment" id="article-comment" class="summernote" placeholder="@lang('modules.tasks.comment')"></textarea>
                                </div>
                                <div class="col-xs-12 m-b-10" id="commentFiles">
                                    <div class="upload-btn-wrapper">
                                      <button class="btn-upload" style="display: block;width: 100%;"><i class="fa fa-paperclip"></i> Drag and Drop Your Files
                                        <br>
                                        <small>(Maximum file size 5MB)</small>
                                    </button>
                                    <input type="file" name="files[]" id="comment-file" multiple />
                                    <input type="hidden" name="uploadedFiles" id="uploaded-files" value="" />
                                </div>
                            </div>
                            <div class="col-xs-12">
                                <button type="button" id="submit-comment" class="btn btn-info btn-sm"><i class="fa fa-send"></i> @lang('app.submit')</button>
                            </div>
                        </form>
                    </div>
                    @endif

                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12 m-t-20">
            <div class="p-10 text-center">Activity Log</div>
            <div class="steamline">
                @foreach ($article->logs->sortByDesc('id') as $log)
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
            <div id="activity-action" style="background-image: linear-gradient(white, silver); padding: 10px; margin-top: -70px;">
                <div id="activity-show" class="m-t-30 text-center" style="cursor: pointer; color: white; font-weight: bold;">Show More</div>
            </div>
        </div>

    </div>
</div>

<div class="col-xs-12" id="task-history-section">
</div>

</div>

</div>
{{--Ajax Modal--}}
<div class="modal fade bs-modal-lg in"  id="subTaskModal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" id="modal-data-application">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <span class="caption-subject font-red-sunglo bold uppercase" id="subTaskModelHeading">Loading...</span>
            </div>
            <div class="modal-body">
                Loading...
            </div>
            <div class="modal-footer">
                <button type="button" class="btn default" data-dismiss="modal">Close</button>
                <button type="button" class="btn blue">Save changes</button>
            </div>
        </div>
    </div>
</div>
{{--Ajax Modal Ends--}}

@endsection

@push('footer-script')
<script src="{{ asset('plugins/bower_components/custom-select/custom-select.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/summernote/dist/summernote.min.js') }}"></script>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

<script src="{{ asset('plugins/bower_components/peity/jquery.peity.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/peity/jquery.peity.init.js') }}"></script>

<script>
    $('#activity-show').click(function(){
        $('.sl-item').show(300);
        $(this).hide();
        $('#activity-action').hide();
    });
    
     //Copy link
     function copyLink(){
        var button = document.getElementById('copyLink');
        var copyText = document.getElementById("copyText");
        copyText.style.display ='inline';
        copyText.select();
        copyText.setSelectionRange(0, 99999)
        document.execCommand("copy");
        copyText.style.display ='none';
        button.innerHTML = '<i class="fa fa-link"></i> Copied';
    }

 //Download comment files
 function sendReminder(id) {
    var url = "{{ route('member.article.sendReminder',':id') }}";
    var CSRF_TOKEN = '{{ csrf_token() }}';
    url = url.replace(':id', id);
    $.easyAjax({
        url:  url,
        type: 'POST',
        data: {'_token': CSRF_TOKEN},
        success: function (result) {
            if (result.status == 'success') {
            }
        }
    });
}

//Start publishing
function startPublishing(status) {
    var url = "{{ route('member.article.startPublishing', $article->id) }}";
    $.easyAjax({
        url:  url,
        type: 'GET',
        success: function (result) {
            if (result.status == 'success') {
                var location = "{{auth()->user()->hasRole('admin') ? route('admin.article.show', ':id') : route('member.article.show', ':id')}}";
                var location = location.replace(':id', '{{$article->id}}');
                document.location.href = location;
            }
        }
    });
}

//Comment Post
$('#submit-comment').click(function(){
    var url = "{{route('member.article.storeComment')}}";
    var CSRF_TOKEN = '{{ csrf_token() }}';
    var formData = {
        'comment': $('#article-comment').val(),
        'article_id': '{{$article->id}}',
        'uploadedFiles': $('#uploaded-files').val(),
        '_token': "{{csrf_token()}}"
    };
    $.easyAjax({
        url:  url,
        type: 'POST',
        data: formData,
        success: function (result) {
            if (result.status == 'success') {
                var location = "{{auth()->user()->hasRole('admin') ? route('admin.article.show', ':id') : route('member.article.show', ':id')}}";
                var location = location.replace(':id', '{{$article->id}}');
                document.location.href = location;
            }
        }
    });
});

//Download comment files
function commentDownload(file) {
    var url = "{{ route('member.article.commentDownload',':file') }}";
    url = url.replace(':file', file);
    window.location.replace(url);
}

//Delete Comment
function delComment(id) {
    var url = "{{ route('member.article.delComment',':id') }}";
    url = url.replace(':id', id);
    $.easyAjax({
        type: 'POST',
        url: url,
        data: {'_token': '{{csrf_token()}}'},
        success: function (response) {
            if (response.status == 'success') {
                var location = "{{auth()->user()->hasRole('admin') ? route('admin.article.show', ':id') : route('member.article.show', ':id')}}";
                var location = location.replace(':id', '{{$article->id}}');
                document.location.href = location;
            }
        }
    });
}

function delete_comment_file(file){
    if ($('#uploaded-files').val().indexOf(file+',') !== -1) {
        $('#uploaded-files').val($('#uploaded-files').val().replace(file+',', ''));
        $("h5[data|='"+file+"']").hide();
    } else if ($('#uploaded-files').val().indexOf(file) !== -1) {
        $('#uploaded-files').val($('#uploaded-files').val().replace(file, ''));
        $("h5[data|='"+file+"']").hide();
    }
}

//Upload File
$('#comment-file').change(function() {
    var url = "{{route('member.article.storeCommentFiles')}}";
    var CSRF_TOKEN = '{{ csrf_token() }}';
    var formData = new FormData();
    for (var i = 0; i < $(this).get(0).files.length; ++i) {
        formData.append('files[]', $(this).get(0).files[i]);
    }
    formData.append('_token', "{{csrf_token()}}");
    $('.btn-upload').html('Uploading...');
    $.ajax({
        url:  url,
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        success: function (result) {
            if (result.status == 'success') {
                var files = $('#uploaded-files').val()+','+result.files.toString();
                var files = files.indexOf(',') == 0 ? files.substring(1) : files;
                $('#uploaded-files').val(files);
                for (var i = 0; i < result.count; i++) {
                   $('#commentFiles').append('<h5 data="'+result.files[i]+'"><a href="javascript:;" style="padding: 10px;"><i class="fa fa-paperclip"></i> '+result.files[i]+'</a> <a href="javascript:;" onclick="delete_comment_file(\''+result.files[i]+'\')"><i class="fa fa-trash"></i></a></h5>');
               }

               $('#comment-file').val('');
               $('.btn-upload').html('<i class="fa fa-paperclip"></i> Drag and Drop Your Files');

               $.showToastr(result.message, 'success');
           }
       }
   })
})

function downloadFile(id) {
    var url = '{{route('member.article.downloadFile',':id')}}';
    var url = url.replace(':id', id);
    window.location.href = url;
}

//Delete Article File
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

function startWork(status) {
    var id = '{{$article->id}}';
    var status = 1;
    var url = "{{ route('member.article.workStatus',['id' => ':id', 'status' => ':status']) }}";
    var url = url.replace(':id', id).replace(':status', status);
    $.easyAjax({
        type: 'POST',
        url: url,
        data: {'_token': '{{csrf_token()}}'},
        success: function(response){
          var location = "{{auth()->user()->hasRole('admin') ? route('admin.article.show', ':id') : route('member.article.show', ':id')}}";
          var location = location.replace(':id', id);
          document.location.href = location;
      }
  });
}

$('#editorTransfer').on('click', function(){
    var editor = $('#editor').val();
    var url = '{{route('member.article.review', $article->id)}}';
    var id = '{{$article->id}}';
    $.easyAjax({
        type: 'POST',
        url: url,
        data: {'editor': editor, '_token': '{{csrf_token()}}'},
        success: function(response){
          var location = "{{auth()->user()->hasRole('admin') ? route('admin.article.show', ':id') : route('member.article.show', ':id')}}";
          var location = location.replace(':id', id);
          document.location.href = location;
      }
  });
});

function reviewStatus(status) {
    var url = '{{route('member.article.review', $article->id)}}';
    var id = '{{$article->id}}';
    var one = $('#star1:checked').val();
    var two = $('#star2:checked').val();
    var three = $('#star3:checked').val();
    var four = $('#star4:checked').val();
    var five = $('#star5:checked').val();
    if (typeof one !='undefined') {var rating = 1;}
    if (typeof two !='undefined') {var rating = 2;}
    if (typeof three !='undefined') {var rating = 3;}
    if (typeof four !='undefined') {var rating = 4;}
    if (typeof five !='undefined') {var rating = 5;}
    var wordCount = $('#wordCount').val();
    var deadline = $('#publishing_deadline').val();
    var website = $('#website').val();
    if (status =='completed' && (wordCount == '' || typeof rating == 'undefined')) {
        $.showToastr('Please check rating and enter word count!', 'error');
        return false;
    }

    $.easyAjax({
        type: 'POST',
        url: url,
        data: {'status': status, 'rating': rating, 'word_count': wordCount, 'deadline': deadline, 'website': website, '_token': '{{csrf_token()}}'},
        success: function(response){
          var location = "{{route('member.article.show', ':id')}}";
          var location = location.replace(':id', id);
          document.location.href = location;
      }
  });

}

function markComplete(status) {
    var id = '{{$article->id}}';
    if (status ==='complete') {var status = 1;} else if (status ==='incomplete') {var status = 0;} else if (status ==='finish') {var status = 2;}

    if (status === 2) {
        var one = $('#star1:checked').val();
        var two = $('#star2:checked').val();
        var three = $('#star3:checked').val();
        var four = $('#star4:checked').val();
        var five = $('#star5:checked').val();

        if (typeof one !='undefined') {var rating = 1;}
        if (typeof two !='undefined') {var rating = 2;}
        if (typeof three !='undefined') {var rating = 3;}
        if (typeof four !='undefined') {var rating = 4;}
        if (typeof five !='undefined') {var rating = 5;}

        @if (!is_null($article->reviewStatus) && $article->reviewStatus->value =='completed')
        var rating = '{{$article->rating}}';
        @endif

        if (typeof rating =='undefined') {
            $.showToastr('Please check rating!', 'error');
            return false;
        }

        var publisher = $('#publishers').val();
        var deadline = $('#publishing_deadline').val();
        var wordCount = $('#wordCount').val();
        var website = $('#website').val();

        @if (!is_null($article->reviewStatus) && $article->reviewStatus->value =='completed')
        var wordCount = '{{$article->word_count}}';
        @endif

        if ($.isNumeric(wordCount) == false) {
            $.showToastr('Please enter word count!', 'error');
            return false;
        }

        var url = "{{ route('member.article.updateStatus',['id' => ':id', 'status' => ':status']) }}";
        var url = url.replace(':id', id).replace(':status', status);
        $.easyAjax({
            type: 'POST',
            url: url,
            data: {'wordCount': wordCount, 'publisher': publisher, 'deadline': deadline,'rating': rating, 'website': website, '_token': '{{csrf_token()}}'},
            success: function (response) {
                if (response.status ==='success') {
                   var location = "{{auth()->user()->hasRole('admin') ? route('admin.article.show', ':id') : route('member.article.show', ':id')}}";
                   var location = location.replace(':id', id);
                   document.location.href = location;
               }
           }
       });

    } else {

        var url = "{{ route('member.article.updateStatus',['id' => ':id', 'status' => ':status']) }}";
        var url = url.replace(':id', id).replace(':status', status);
        $.easyAjax({
            type: 'POST',
            url: url,
            data: {'_token': "{{csrf_token()}}"},
            success: function (response) {
                var location = "{{auth()->user()->hasRole('admin') ? route('admin.article.show', ':id') : route('member.article.show', ':id')}}";
                var location = location.replace(':id', id);
                document.location.href = location;
            }
        });
    }
}

function completePublish(status) {
    var id = '{{$article->id}}';
    if (status ==='complete') {var status = 1;} else { var status = null;}
    var link = $('#publishLink').val();
    if (status === 1 && link ==='') {$.showToastr('Please anter your publish link!', 'error')} else {

        var url = "{{ route('member.article.updatePublishStatus',['id' => ':id', 'status' => ':status']) }}?link="+link+"&_token={{csrf_token()}}";
        var url = url.replace(':id', id).replace(':status', status);
        $.easyAjax({
            type: 'GET',
            url: url,
            success: function (response) {
                var location = "{{auth()->user()->hasRole('admin') ? route('admin.article.show', ':id') : route('member.article.show', ':id')}}";
                var location = location.replace(':id', id);
                document.location.href = location;
            }
        });
    }
}



$("#editor").select2({
    formatNoMatches: function () {
        return "{{ __('messages.noRecordFound') }}";
    }
});

jQuery('#publishing_deadline').datepicker({
    format: 'yyyy-mm-dd',
    autoclose: true,
    todayHighlight: true
});

function editArticle(id) {
    var url = "{{ route('member.article.edit',':id') }}?ref=show";
    url = url.replace(':id', id);
    $.ajaxModal('#subTaskModal', url);
}

function deleteArticle(id) {
    var buttons = {
        cancel: "Cancel",
        confirm: {
            text: "Yes",
            visible: true,
            className: "danger",
        }
    };
    swal({
        title: "Are you sure want to delete?",
        text: "Please enter your password below:",
        dangerMode: true,
        icon: 'warning',
        buttons: buttons,
        content: "input"
    }).then(function (isConfirm) {
        if (isConfirm !=='' && isConfirm !==null) {
            var url = "{{ route('member.article.delete',':id') }}";
            var url = url.replace(':id', id);
            var token = "{{ csrf_token() }}";
            var dataObject = {'password': isConfirm, '_token': token, '_method': 'DELETE'};
            $.easyAjax({
                type: 'POST',
                url: url,
                data: dataObject,
                success: function (response) {
                    if (response.status == "success") {
                        swal("Success", "The task has been deletd!", "success");
                        $.unblockUI();
                        var location = "{{auth()->user()->hasRole('admin') ? route('admin.article.index') : route('member.article.index')}}";
                        document.location.href = location;
                    } else {
                        swal("Warning!", "The password you entered is incorrect!", "warning");
                    }
                }
            });
        }
        if (isConfirm ==='') {swal("Empty!", "You must enter your password!", "warning");}
    });
}

function parentTask(id) {
    
    $(".right-sidebar").slideDown(50).addClass("shw-rside");
    var url = "{{ auth()->user()->hasRole('admin') ? route('admin.all-tasks.show',':id') : route('member.all-tasks.show',':id') }}";
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

// if ($("#subTaskModal").modal('toggle')) {location.reload(true);}
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
@endpush