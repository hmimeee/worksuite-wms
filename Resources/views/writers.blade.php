@extends('layouts.member-app')

@section('page-title')
<div class="row bg-title">
    <!-- .page title -->
    <div class="col-lg-8 col-md-5 col-sm-6 col-xs-12">
        <h4 class="page-title"><i class="{{ $pageIcon }}"></i> {{ $pageTitle }}</h4>
    </div>

    <!-- /.page title -->
</div>
@endsection

@push('head-script')
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.13/css/dataTables.bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.1.1/css/responsive.bootstrap.min.css">
<link rel="stylesheet" href="//cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css">
<link rel="stylesheet" href="//cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/custom-select/custom-select.css') }}">
<link rel="stylesheet"
href="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.css') }}">
<style type="text/css">
    .checked {
      color: orange;
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

<<<<<<< HEAD
            <div class="row el-element-overlay">
                <table class="table table-bordered table-hover m-l-10">
                    <thead>
                        <tr role="row">
                            <th>#</th>
                            <th>Name</th>
                            <th>Writer Type</th>
                            @if(!auth()->user()->hasRole($writerRole) && !auth()->user()->hasRole($inhouseWriterRole))
                            <th>Rate (1k Words)</th>
                            @endif
                            <th>Pending Articles</th>
                            <th>Completed Articles</th>
                            <th>Words Written</th>
                            <th>Gender</th>
                            <th>Rating</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="list">
                        @forelse ($writers as $writer)
                        <tr role="row" class="odd">
                            <td>{{$writer->id}}</td>
                            <td>
                                <a href="javascript:;" onclick="viewWriter('{{$writer->id}}')">{{$writer->name}}</a> @if($writer->leaves()->where('leave_dates', 'LIKE', '%'.date('Y-m').'%')->count() > 0)
                                <span class="label label-danger">Leaves taken</span>
                                @endif
                                @if($writer->unavailable)
                                <span class="label label-danger">Unavailable</span>
                                @endif
                            </td>
                            <td>{{App\Role::find($writer->role->last()->role_id)->display_name}}</td>
                            @if(!auth()->user()->hasRole($writerRole) && !auth()->user()->hasRole($inhouseWriterRole))
                            <td>{{$writer->rate ? $writer->rate->rate : '--'}}</td>
                            @endif
                            <td>
                                @php
                                $pending = $writer->articles->where('writing_status', 0);
                                $count = count($pending);
                                @endphp
                                {{count($pending)}}
                            </td>
                            <td>
                                @php
                                $completed = $writer->articles->where('writing_status', 2);
                                $count = count($completed);
                                @endphp
                                {{count($completed)}}
                            </td>
                            <td>
                                @php($words = 0)
                                @foreach ($completed as $article)
                                @php($words += $article->word_count)
                                @endforeach
                                {{$words}}
                            </td>
                            <td>{{ucfirst($writer->gender)}}</td>
                            <td>
                                @php($rate = 0)

                                @foreach ($completed as $article)
                                @php($rate += $article->rating)
                                @endforeach

                                @if ($rate !=0)
                                ({{number_format($rate/$count, 2)}}) - 
                                @for($i=0; $i < round($rate/$count); $i++)
                                <span class="fa fa-star checked"></span>
                                @endfor

                                @for($i=$i; $i < 5; $i++)
                                <span class="fa fa-star"></span>
                                @endfor

                                @else
                                (0) - 
                                @for($i=0; $i < 5; $i++)
                                <span class="fa fa-star"></span>
                                @endfor
                                @endif
                            </td>
                            <td class=" text-center">
                                <div class="btn-group dropdown m-r-10">
                                    <button aria-expanded="false" data-toggle="dropdown" class="btn dropdown-toggle waves-effect waves-light" type="button"><i class="ti-more"></i></button>
                                    <ul role="menu" class="dropdown-menu pull-right">
                                        <li><a href="javascript:;" onclick="viewWriter('{{$writer->id}}')"><i class="fa fa-search" aria-hidden="true"></i> View</a></li>
                                        @if(auth()->user()->hasRole('admin') || auth()->id() == $writerHead)
                                        <li><a href="{{ route('member.employees.edit', $writer->id) }}"><i class="fa fa-pencil" aria-hidden="true"></i> Edit</a></li>
                                        @endif
                                        @if(auth()->user()->hasRole('admin'))
                                        <li><a href="javascript:;"  data-user-id="{{$writer->id}}" class="sa-params"><i class="fa fa-times" aria-hidden="true"></i> Delete</a></li>
                                        @endif
                                    </ul> 
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
                        <tr align="right" style="border: 0px !important;">
                            <td colspan="10" style="border: 0px !important;"> {{$writers->appends(['entries' => request('entries'), 'search' => request('search')])->render()}} </td>
                        </tr>
                    </tfoot>
                </table>
=======
            <div class="row" id="ticket-filters">
                <div class="table-responsive p-20">
                    {!! $dataTable->table(['class' => 'table table-bordered table-hover toggle-circle default footable-loaded footable']) !!}
                </div>
>>>>>>> 1f3322c3c6355e9545e648b0c49b0cc25f11bbd2
            </div>
        </div>
    </div>
    <!-- .row -->
    {{--Ajax Modal--}}
    <div class="modal fade bs-modal-md in" id="writerModal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" id="modal-data-application">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <span class="caption-subject font-red-sunglo bold uppercase" id="modelHeading"></span>
                </div>
                <div class="modal-body">
                    Loading...
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn default" data-dismiss="modal">Close</button>
                    <button type="button" class="btn blue">Save changes</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->.
    </div>
    {{--Ajax Modal Ends--}}
    @endsection

    @push('footer-script')
    <script src="{{ asset('plugins/bower_components/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="https://cdn.datatables.net/1.10.13/js/dataTables.bootstrap.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.1.1/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.1.1/js/responsive.bootstrap.min.js"></script>
    <script src="{{ asset('plugins/bower_components/custom-select/custom-select.min.js') }}"></script>
    <script src="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.js') }}"></script>
    <script src="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ asset('plugins/bower_components/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
    <script src="https://cdn.datatables.net/buttons/1.0.3/js/dataTables.buttons.min.js"></script>
    <script src="{{ asset('js/datatables/buttons.server-side.js') }}"></script>

    {!! $dataTable->scripts() !!}
    <script>
        $(".select2").select2();

        jQuery('#date-range').datepicker({
            toggleActive: true,
            format: '{{ $global->date_picker_format }}',
            language: '{{ $global->locale }}',
            autoclose: true
        });
        var table;

        $('.toggle-filter').click(function () {
            $('#ticket-filters').toggle('slide');
        })

        $('#apply-filters').click(function () {
            $('#writers-table').on('preXhr.dt', function (e, settings, data) {
                var status = $('#status').val();
                data['status'] = status;


            });

            $.easyBlockUI('#writers-table');
            window.LaravelDataTables["writers-table"].draw();
            $.easyUnblockUI('#writers-table');

        });

        $('#reset-filters').click(function () {
            $('#filter-form')[0].reset();
            $('#status').val('all');
            $('.select2').val('all');
            $('#filter-form').find('select').select2();

            $.easyBlockUI('#writers-table');
            window.LaravelDataTables["writers-table"].draw();
            $.easyUnblockUI('#writers-table');
        })

        function viewWriter(id) {
        var url = "{{ route('member.article.writer', ':id')}}";
        var url = url.replace(':id', id);
        $.ajaxModal('#writerModal', url);
    }
    </script>
    @endpush
