@extends('layouts.app')

@section('page-title')
<div class="row bg-title">
    <!-- .page title -->
    <div class="col-lg-8 col-md-5 col-sm-6 col-xs-12">
        <h4 class="page-title"><i class="{{ $pageIcon ?? '' }}"></i> {{ $pageTitle ?? '' }}
        </h4>
    </div>
</div>
@endsection

@push('head-script')
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/custom-select/custom-select.css') }}">
<link rel="stylesheet"
href="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/summernote/dist/summernote.css') }}">
<style type="text/css">
    .pull-left{
        display: none;
    }
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
    <div class="col-md-12">
        <div class="white-box">
            @section('filter-section')
            <div class="row">
                <div class="col-md-12 p-b-10">
                    <h1>Settings</h1>
                </div>
            </div>
            @endsection

            <div class="row">
                @if(count($temp) > 0)
                <div class="col-md-12 m-t-10">
                    <h3>Clear Temp Files:</h3>
                </div>
                <div class="col-lg-12">
                    <form>
                        <div class="form-group">
                            <button class="btn btn-success waves-effect waves-light btn-sm" id="delete-temp">Clear All Temporary Files</button>
                        </div>
                    </form>
                    <hr>
                </div>
                @endif

                <div class="col-lg-12 m-b-10 m-t-10">
                    <h3>Article Settings:</h3>
                </div>
                <div class="col-lg-6">
                    <form method="post" id="settings">
                        @csrf
                        <div class="form-group col-md-12">
                            <label class="control-label col-md-4">Freelance Writer Role</label>
                            <div class="col-md-8">
                                <select class="form-control custom-select" name="writer" id="writers">
                                    @foreach ($roles as $role)
                                    <option value="{{$role->name}}" @if($writerRole == $role->name) selected @endif>{{$role->display_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group col-md-12">
                            <label class="control-label col-md-4">Inhouse Writer Role</label>
                            <div class="col-md-8">
                                <select class="form-control custom-select" name="inhouse_writer" id="inhouse_writers">
                                    @foreach ($roles as $role)
                                    <option value="{{$role->name}}" @if($inhouseWriterRole == $role->name) selected @endif>{{$role->display_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-group col-md-12">
                            <label class="control-label col-md-4">Writer Head</label>
                            <div class="col-md-8">
                                <select class="custom-select custom-multiple" name="writer_head[]" id="writer_head" multiple>
                                    @foreach ($employees as $employee)
                                    <option value="{{$employee->id}}"{{ in_array($employee->id, explode(',', $writerHead)) ? 'selected' : '' }}>{{$employee->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group col-md-12">
                            <label class="control-label col-md-4">Writer Head Assistant</label>
                            <div class="col-md-8">
                                <select class="custom-select custom-multiple" name="writer_head_assistant[]" id="writer_head_assistant" multiple>
                                    @foreach ($employees as $employee)
                                    <option value="{{$employee->id}}"{{ in_array($employee->id, explode(',', $writerHeadAssistant)) ? 'selected' : '' }}>{{$employee->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group col-md-12">
                            <label class="control-label col-md-4">Default Editor</label>
                            <div class="col-md-8">
                                <select class="form-control custom-select" name="default_editor" id="defaultEditor">
                                    @foreach ($employees as $employee)
                                    <option value="{{$employee->id}}" @if($defaulEditor == $employee->id) selected @endif>{{$employee->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group col-md-12">
                            <label class="control-label col-md-4">Publisher Head</label>
                            <div class="col-md-8">
                                <select class="form-control custom-select" name="publisher" id="publisher">
                                    @foreach ($employees as $employee)
                                    <option value="{{$employee->id}}" @if($publisher == $employee->id) selected @endif>{{$employee->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group col-md-12">
                            <label class="control-label col-md-4">Outreach Head</label>
                            <div class="col-md-8">
                                <select class="form-control custom-select" name="outreach_head" id="outreach_head">
                                    @foreach ($employees as $employee)
                                    <option value="{{$employee->id}}" @if($outreachHead == $employee->id) selected @endif>{{$employee->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group col-md-12">
                            <label class="control-label col-md-4">Outreach Assistants</label>
                            <div class="col-md-8">
                                <select class="custom-select custom-multiple" name="outreach_assistants[]" id="outreach_assistants" multiple>
                                    @foreach ($employees as $employee)
                                    <option value="{{$employee->id}}"{{ in_array($employee->id, explode(',', $outreachAssistants)) ? 'selected' : '' }}>{{$employee->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group col-md-12">
                            <label class="control-label col-md-4">Outreach Article Category</label>
                            <div class="col-md-8">
                                <select class="form-control custom-select" name="outreach_category" id="outreach_category">
                                    @foreach ($categories as $category)
                                    <option value="{{$category->id}}" @if($outreachCategory == $category->id) selected @endif>{{$category->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group col-md-12">
                            <label class="control-label col-md-4">Publishers</label>
                            <div class="col-md-8">
                                <select class="custom-select custom-multiple" name="publishers[]" id="publishers" multiple>
                                    @foreach ($employees as $employee)
                                    <option value="{{$employee->id}}"{{ in_array($employee->id, explode(',', $publishers)) ? 'selected' : '' }}>{{$employee->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group col-md-12">
                            <label class="control-label col-md-4">Company Address</label>
                            <div class="col-md-8">
                                <textarea class="summernote" name="address" id="address">
                                    @php($address= $settings->where('type', 'address')->first())
                                    {!! $address ? $address->value : '' !!}
                                </textarea>
                            </div>
                        </div>
                        <div class="col-md-12"></div>
                        <div class="col-md-12">
                            <button class="btn btn-success waves-effect waves-light m-r-10" id="save-form">Save</button>
                        </div>
                    </form>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-md-12 m-t-10">
                    <h3>Update Module:</h3>
                </div>
                <div class="col-lg-6">
                    <form id="update-module">
                        @csrf
                        <div class="form-group row">
                            <label class="control-label text-right col-md-2">Package:</label>
                            <div class="col-md-8">
                                <input type="file" name="package" class="form-control" id="package">
                            </div>
                            <div class="col-lg-2">
                                <button class="btn btn-success waves-effect waves-light btn-sm" id="package-upload">Update</button>
                            </div>
                        </div>
                    </form>
                    <div class="col-md-4">
                        @if($module_update)
                        Current version: <b>{{$module_update->details}}</b>
                    </div>
                    <div class="col-md-4">
                        Updated: {{$module_update->created_at->diffForHumans()}}.
                    </div>
                    <div class="col-md-4">
                        Updated by: <b>{{$module_update->user->name}}</b>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="white-box">
            <div class="col-12 m-b-10 m-t-10">
                <h3 style="display: inline;">Article Types:</h3>
                <a href="javascript:;" id="createType" class="btn btn-outline btn-success btn-sm pull-right"><i class="fa fa-plus" aria-hidden="true"></i> Add New Type</a>
            </div>

            <table class="table table-bordered table-hover">
                <thead>
                    <tr role="row">
                        <th>#</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody id="list">
                    @forelse ($types as $type)
                    <tr role="row" class="odd">
                        <td>{{$type->id}}</td>
                        <td>{{$type->name}}</td>
                        <td>{{strip_tags($type->description)}}</td>
                        <td class=" text-center">
                            <div class="btn-group dropdown m-r-10">
                                <a href="javascript:;" onclick="deleteType('{{$type->id}}')" class="label label-danger"><i class="fa fa-times" aria-hidden="true"></i> Delete</a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8">
                            No data found!
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                <tfoot style="border: 0px !important;">
                    <tr style="border: 0px !important;">
                        <td colspan="8" style="border: 0px !important;"> {{$types->render()}} </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection

@push('footer-script')
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script src="{{ asset('plugins/bower_components/custom-select/custom-select.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/summernote/dist/summernote.min.js') }}"></script>
<script type="text/javascript">

    $('.summernote').summernote({
    height: 100,                 // set editor height
    minHeight: null,             // set minimum height of editor
    maxHeight: null,             // set maximum height of editor
    focus: false,
    toolbar: [
        // [groupName, [list of button]]
        ['style', ['bold', 'italic', 'underline', 'clear']]
        ]
    })

    $('#save-form').click(function (e) {
     e.preventDefault();
     $.easyAjax({
        url: "{{route('admin.article.settings')}}",
        container: '#settings',
        type: "POST",
        data: $('#settings').serialize()
    })
 });

    $(".custom-select").select2({
        formatNoMatches: function () {
            return "{{ __('messages.noRecordFound') }}";
        }
    });

    $('#package-upload').click(function(e){
        e.preventDefault();
        if($('#package').val() !=''){
            $(this).html('Uploading');
            var formData = new FormData();
            formData.append('package', $('#package').get(0).files[0]);
            formData.append('_token', '{{csrf_token()}}');
            $.ajax({
                type: 'POST',
                url: '{{route('admin.article.update-module')}}',
                contentType: false,
                processData: false,
                data: formData,
                success: function (response) {
                    if (response.status == "success") {
                        $('#package-upload').html('<i class="fa fa-check"></i> Updated');
                        swal("Success!", response.message, "success");
                        location.reload(true);
                    } else {
                        $('#package-upload').html('Upload');
                        swal("Warning!", response.message, "warning");
                    }
                }
            })
        } else {
            $.showToastr('Please select package file of Article module!', 'error');
        }
    })

    $('#createType').click(function () {
        var url = "{{ route('member.article.createType') }}";
        $.ajaxModal('#subTaskModal', url);
    })

 //Delete Type
 function deleteType(id) {
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
        text: "You will not be able to recover the deleted type!",
        dangerMode: true,
        icon: 'warning',
        buttons: buttons,
    }).then(function (isConfirm) {
        if (isConfirm ==='confirm') {
            var token = "{{csrf_token()}}";
            var url = '{{route('member.article.deleteType', ':id')}}';
            var url = url.replace(':id', id);
            $.easyAjax({
                type: 'POST',
                url: url,
                data: {'_token': token},
                success: function (response) {
                    if (response.status == "success") {
                        $.unblockUI();
                        location.reload(true);
                    }
                }
            });
        }
    });
}

$('#delete-temp').click(function(e){
    e.preventDefault();
    var url  = '{{route('admin.article.temp')}}';
    $.easyAjax({
        url: url,
        type: 'POST',
        data: {'_token': '{{csrf_token()}}'},
        success: function(response){
            swal("Success!", response.message, "success");
            location.reload(true);
        }
    })
})
</script>
@endpush
