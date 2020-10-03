<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h4 class="modal-title"><i class="ti-notepad"></i> Leave Application</h4>
</div>
<div class="modal-body">
    <div class="form-group">
        <label>Writer Name</label>
        <p>
            {{$application->writer->name}}
        </p>
    </div>

    <div class="form-group">
        <label>Leave Dates</label>
        <p>
            @php($dates = explode(',', $application->leave_dates))
            @foreach($dates as $key => $date)
            {{ $date == end($dates) ? count($dates) > 1 ? 'and '.\Carbon\Carbon::create($date)->format('d M Y') : \Carbon\Carbon::create($date)->format('d M Y') : \Carbon\Carbon::create($date)->format('d M').', '}} 
            @endforeach
        </p>
    </div>

    <div class="form-group">
        <label>Reason for Leave</label>
        {!! $application->reason !!}
    </div>

    <div class="form-group">
        <label>Status</label>
        @if($application->status == 1) 
        <label class="label label-success">Granted</label>
        @else
        <label class="label label-warning">Pending</label>
        @endif
    </div>

    <div class="form-group">
        @if($application->status != 1 && ($writerHead == auth()->id() || auth()->user()->hasRole('admin')))
        <button class="btn btn-sm btn-success" id="leaveApprove">Approve</button>
        <button type="button" class="btn btn-inverse btn-sm float-sm-right" data-dismiss="modal">Cancel</button>
        @endif
    </div>
</div>

<script type="text/javascript">
    $('#leaveApprove').click(function(){
        var url = "{{route('member.article.leaveApprove', ':id')}}";
        var url = url.replace(':id', '{{$application->id}}');
        $.easyAjax({
            url: url,
            type: "POST",
            data: {'_token': '{{csrf_token()}}', '_method': 'PATCH'},
            success: function (resposne) {
                if (resposne.status == 'success') {
                    viewLeave('{{$application->id}}');
                }
            }
        });
    });

    function viewLeave(id){
        var url = "{{ route('member.article.leaveView', ':id') }}";
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
            ['style', ['bold', 'italic', 'underline', 'clear']],
            ['font', ['strikethrough', 'superscript', 'subscript']],
            ['fontsize', ['fontsize']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['height', ['height']]
            ]
        });
    </script>