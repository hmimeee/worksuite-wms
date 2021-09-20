
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h4 class="modal-title"><i class="ti-plus"></i> Assign New Articles</h4>
</div>
<div class="modal-body">
    <div class="portlet-body">
        <div class="row">
            <div class="col-xs-12 panel-body p-t-15">
                <div class="steamline">
                    @foreach ($logs->sortByDesc('id') as $log)
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

