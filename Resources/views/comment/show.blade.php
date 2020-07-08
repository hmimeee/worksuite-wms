<div class="row" style="background: rgba(0,0,0,0.03); padding: 5px;">
    <div class="col-xs-10 m-b-10">
        <a href="javascript:;"><b>{{\App\User::find($comment->user_id)->name}}</b></a>
    </div>
    <div class="col-xs-2 text-right">
        <a href="javascript:;" class="btn btn-danger btn-sm btn-rounded btn-outline" onclick="deleteComment('{{$comment->id}}')">@lang('app.delete')</a>
    </div>
    <label class="col-xs-12 m-b-10 font-12" for=""> {!!$comment->comment!!} </label>
    <div class="col-xs-12">
        @if ($comment->files !=null)
        Files: <br/>
        @php
        $file = explode(',', $comment->files);
        $count = count($file);
        @endphp

        @for($i=0; $i < $count; $i++)
        <a href="{{$file[$i]}}" class="btn btn-primary btn-sm btn-rounded btn-outline m-t-5"><i class="fa fa-file"></i> {{$file[$i]}}</a>
        <br/>
        @endfor
        @endif
    </div>
</div>