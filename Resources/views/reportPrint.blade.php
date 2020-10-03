 <!DOCTYPE html>
 <html>
 <head>
     <title>Articles Report</title>
     <link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
     <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
 </head>
 <body>
    <div class="row">
        <div class="col-sm-6 mb-3 mt-3">
            <h4>
                <i class="fa fa-line-chart"></i> Article Report
            </h4>
        </div>
        <div class="col-sm-6 mb-3 mt-3" align="right">
            <img src="{{ $global->logo_url }}" alt="home" class="Logo" height="50px" />
        </div>

        <div class="col-sm-2 mb-3 mt-3">
            <h5>
                <span class="text-muted m-l-5">Total Articles: </span>
                <span class="text-info">{{ $articles ? count($articles) : '0' }}</span>
            </h5>
        </div>

        <div class="col-sm-2 mb-3 mt-3 text-center">
            <h5>
                <span class="text-muted m-l-5">Total Words: </span>
                <span class="text-info">{{ $words ?? '0' }}</span>
            </h5>
        </div>

        <div class="col-sm-3 mb-3 mt-3 text-center">
            <h5>
                <span class="text-muted m-l-5">Total Cost (BDT): </span>
                <span class="text-info">{{ number_format($cost, 2) ?? '0' }}</span>
            </h5>
        </div>

        <div class="col-sm-5 mb-3 mt-3 text-right">
            <h5>
                <span class="text-muted m-l-5">Date Between: </span>
                <span class="text-info">{{\Carbon\Carbon::create($startDate)->format('d M Y')}} - {{\Carbon\Carbon::create($endDate)->format('d M Y')}}</span>
            </h5>
        </div>
    </div>
    <table class="table table-bordered table-hover">
        <thead>
            <tr role="row">
                <th>#</th>
                <th>Title</th>
                <th>Project</th>
                <th>Assignee</th>
                <th>Publish Link</th>
                <th>Deadline</th>
                <th>Word Count</th>
            </tr>
        </thead>
        <tbody id="list">
            @php($id = 1)
            @forelse ($articles as $article)
            <tr role="row" class="odd">
                <td>{{$id}}</td>
                <td>
                    <a target="_blank" href="{{route('admin.article.show',$article->id)}}">{{$article->title}}
                    </a>
                </td>
                <td>
                    <a target="_blank" href="{{route('member.projects.show', $article->project_id)}}">{{$article->project->project_name}}</a>
                </td>
                <td>{{App\User::find($article->assignee)->name}}</td>
                <td>
                    @if($article->publish_link !=null && $article->publish_link !='')
                    <a target="_blank" href="{{$article->publish_link}}">Link</a>
                    @else
                    --
                    @endif
                </td>
                <td>
                    <span>
                        {{\Carbon\Carbon::parse($article->writing_deadline)->format('d M Y')}}
                    </span>
                </td>
                <td>{{$article->word_count}}</td>
            </tr>
            @php($id++)
            @empty
            <tr>
                <td colspan="8">
                    No data found!
                </td>
            </tr>
            @endforelse
            <tr>
                <td colspan="6" class="text-right">
                    Total = 
                </td>
                <td>{{$words}} Words</td>
            </tr>
        </tbody>
    </table>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

    <script type="text/javascript">
        window.print();
    </script>
</body>
</html>