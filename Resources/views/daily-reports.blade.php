@extends('article::layouts.member-app')

@section('page-title')
<div class="row bg-title">
    <!-- .page title -->
    <div class="col-lg-8 col-md-5 col-sm-6 col-xs-12">
        <h4 class="page-title"><i class="{{ $pageIcon ?? '' }}"></i> {{ $pageTitle ?? '' }} 
        </h4>
    </div>

    <!-- /.page title -->
</div>
@endsection

@push('head-script')
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.css') }}">
@endpush

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="white-box">

            <div class="col-sm-2">
                <h4>
                    <span class="font-12 text-muted m-l-5">Assigned Articles: </span>
                    <span class="text-info btn btn-light">{{ $assignedArticles ? count($assignedArticles) : '0' }}</span>
                    <br>
                    <span class="font-12 text-muted m-l-5">Words:</span>
                    <span class="text-info btn btn-light">{{$assignedArticlesWords}} </span>
                </h4>
            </div>
            <div class="col-sm-2">
                <h4>
                    <span class="font-12 text-muted m-l-5">Submitted Articles: </span>
                    <span class="text-info btn btn-light">{{ $submittedArticles ? count($submittedArticles) : '0' }}</span>
                    <br>
                    <span class="font-12 text-muted m-l-5">Words:</span>
                    <span class="text-info btn btn-light">{{$submittedArticlesWords}} </span>
                </h4>
            </div>
            <div class="col-sm-2">
                <h4>
                    <span class="font-12 text-muted m-l-5">Approved Articles: </span>
                    <span class="text-info btn btn-light">{{ $approvedArticles ? count($approvedArticles) : '0' }}</span>
                    <br>
                    <span class="font-12 text-muted m-l-5">Words:</span>
                    <span class="text-info btn btn-light">{{$approvedArticlesWords}} </span>
                </h4>
            </div>
            <div class="col-sm-4">
                <h4>
                    <span class="font-12 text-muted m-l-5">Date: </span>
                    <span class="text-info btn btn-light">{{\Carbon\Carbon::create($date)->format('d M Y')}}</span>
                </h4>
            </div>

            @section('filter-section')
            <div class="row">
                <form>
                    <div class="col-md-12">
                        <label>Date</label>
                        <div class="form-group">
                            <input type="text" name="date" id="date" value="{{$date}}" class="form-control">
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="form-group">
                            <button class="btn btn-success">Submit</button>
                        </div>
                    </div>
                </form>
            </div>
            @endsection
            <div class="row">
                <div class="white-box">

                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header alert alert-inverse text-center">
                                Assigned Articles ({{ $assignedArticles ? count($assignedArticles) : '0' }})
                            </div>
                            <div class="car-body">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                        <tr role="row">
                                            <th>#</th>
                                            <th>Title</th>
                                            <th>Project</th>
                                            <th>Writer</th>
                                            <th>Word Count</th>
                                            <th>Deadline</th>
                                        </tr>
                                    </thead>
                                    <tbody id="list">
                                        @forelse($assignedArticles as $article)
                                            <tr role="row" class="odd">
                                                <td>{{ $article->id }}</td>
                                                <td>
                                                    <a target="_blank"
                                                        href="{{ route('admin.article.show',$article->id) }}">{{ $article->title }}
                                                    </a>
                                                </td>
                                                <td>
                                                    <a target="_blank"
                                                        href="{{ route('member.projects.show', $article->project_id) }}">{{ $article->project->project_name }}</a>
                                                </td>
                                                <td>{{ $article->getAssignee->name }}</td>
                                                <td>{{ $article->word_count }}</td>
                                                <td>
                                                    <span>
                                                        {{ \Carbon\Carbon::parse($article->writing_deadline)->format('d M Y') }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8">
                                                    No data found!
                                                </td>
                                            </tr>
                                        @endforelse
                                        @if($assignedArticles->count() > 0)
                                            <tr>
                                                <td colspan="4" class="text-right font-bold">Word Count: </td>
                                                <td colspan="2" class="font-bold text-info">{{ $assignedArticlesWords }}
                                                </td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header alert alert-inverse text-center">
                                Submitted Articles ({{ $submittedArticles ? count($submittedArticles) : '0' }})
                            </div>
                            <div class="car-body">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                        <tr role="row">
                                            <th>#</th>
                                            <th>Title</th>
                                            <th>Project</th>
                                            <th>Writer</th>
                                            <th>Word Count</th>
                                            <th>Deadline</th>
                                        </tr>
                                    </thead>
                                    <tbody id="list">
                                        @forelse($submittedArticles as $article)
                                            <tr role="row" class="odd">
                                                <td>{{ $article->id }}</td>
                                                <td>
                                                    <a target="_blank"
                                                        href="{{ route('admin.article.show',$article->id) }}">{{ $article->title }}
                                                    </a>
                                                </td>
                                                <td>
                                                    <a target="_blank"
                                                        href="{{ route('member.projects.show', $article->project_id) }}">{{ $article->project->project_name }}</a>
                                                </td>
                                                <td>{{ $article->getAssignee->name }}</td>
                                                <td>{{ $article->word_count }}</td>
                                                <td>
                                                    <span>
                                                        {{ \Carbon\Carbon::parse($article->writing_deadline)->format('d M Y') }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8">
                                                    No data found!
                                                </td>
                                            </tr>
                                        @endforelse
                                        @if($submittedArticles->count() > 0)
                                            <tr>
                                                <td colspan="4" class="text-right font-bold">Word Count: </td>
                                                <td colspan="2" class="font-bold text-info">{{ $submittedArticlesWords }}
                                                </td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header alert alert-inverse text-center">
                                Approved Articles ({{ $approvedArticles ? count($approvedArticles) : '0' }})
                            </div>
                            <div class="car-body">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                        <tr role="row">
                                            <th>#</th>
                                            <th>Title</th>
                                            <th>Project</th>
                                            <th>Writer</th>
                                            <th>Reviewed By</th>
                                            <th>Word Count</th>
                                            <th>Deadline</th>
                                        </tr>
                                    </thead>
                                    <tbody id="list">
                                        @forelse($approvedArticles as $article)
                                            <tr role="row" class="odd">
                                                <td>{{ $article->id }}</td>
                                                <td>
                                                    <a target="_blank"
                                                        href="{{ route('admin.article.show',$article->id) }}">{{ $article->title }}
                                                    </a>
                                                </td>
                                                <td>
                                                    <a target="_blank"
                                                        href="{{ route('admin.projects.show', $article->project_id) }}">{{ $article->project->project_name }}</a>
                                                </td>
                                                <td>{{ $article->getAssignee->name }}</td>
                                                <td>{{ \App\User::find($article->reviewWriter->details)->name }}</td>
                                                <td>{{ $article->word_count }}</td>
                                                <td>
                                                    <span>
                                                        {{ \Carbon\Carbon::parse($article->writing_deadline)->format('d M Y') }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8">
                                                    No data found!
                                                </td>
                                            </tr>
                                        @endforelse
                                        @if($approvedArticles->count() > 0)
                                            <tr>
                                                <td colspan="4" class="text-right font-bold">Word Count: </td>
                                                <td colspan="2" class="font-bold text-info">{{ $approvedArticlesWords }}
                                                </td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
@endsection

@push('footer-script')
<script src="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
<script type="text/javascript">
    jQuery('#date').datepicker({
        toggleActive: true,
        format: 'yyyy-mm-dd',
        autoclose: true
    });
</script>
@endpush
