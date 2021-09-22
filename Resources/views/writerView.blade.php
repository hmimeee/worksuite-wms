<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/custom-select/custom-select.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.css') }}">
<style type="text/css">
    /* USER PROFILE PAGE */
    .card {
        padding: 30px;
        background-color: rgba(214, 224, 226, 0.2);
        -webkit-box-sizing: border-box;
        -moz-box-sizing: border-box;
        box-sizing: border-box;
    }
    .card.hovercard {
        position: relative;
        padding-top: 0;
        overflow: hidden;
        text-align: center;
        background-color: #fff;
        background-color: rgba(255, 255, 255, 1);
    }
    .card.hovercard .card-background {
        height: 130px;
    }
    .card-background img {
        -webkit-filter: blur(25px);
        -moz-filter: blur(25px);
        -o-filter: blur(25px);
        -ms-filter: blur(25px);
        filter: blur(25px);
        margin-left: -100px;
        margin-top: -200px;
        min-width: 130%;
    }
    .card.hovercard .useravatar {
        position: absolute;
        top: 15px;
        left: 0;
        right: 0;
    }
    .card.hovercard .useravatar img {
        width: 100px;
        height: 100px;
        max-width: 100px;
        max-height: 100px;
        -webkit-border-radius: 50%;
        -moz-border-radius: 50%;
        border-radius: 50%;
        border: 5px solid rgba(255, 255, 255, 0.5);
    }
    .card.hovercard .card-info {
        position: absolute;
        bottom: 14px;
        left: 0;
        right: 0;
    }
    .card.hovercard .card-info .card-title {
        padding:0 5px;
        font-size: 20px;
        line-height: 1;
        color: #262626;
        background-color: rgba(255, 255, 255, 0.1);
        -webkit-border-radius: 4px;
        -moz-border-radius: 4px;
        border-radius: 4px;
    }
    .card.hovercard .card-info {
        overflow: hidden;
        font-size: 12px;
        line-height: 20px;
        color: #737373;
        text-overflow: ellipsis;
    }
    .card.hovercard .bottom {
        padding: 0 20px;
    }
    .btn-pref .btn {
        -webkit-border-radius:0 !important;
    }


    .circle-tile {
        margin-bottom: 15px;
        text-align: center;
    }
    .circle-tile-heading {
        border: 3px solid rgba(255, 255, 255, 0.3);
        border-radius: 100%;
        color: #FFFFFF;
        height: 80px;
        margin: 0 auto -40px;
        position: relative;
        transition: all 0.3s ease-in-out 0s;
        width: 80px;
    }
    .circle-tile-heading .fa {
        line-height: 80px;
    }
    .circle-tile-content {
        padding-top: 50px;
    }
    .circle-tile-number {
        font-size: 26px;
        font-weight: 700;
        line-height: 1;
        padding: 5px 0 15px;
    }
    .circle-tile-description {
        text-transform: uppercase;
    }
    .circle-tile-footer {
        background-color: rgba(0, 0, 0, 0.1);
        color: rgba(255, 255, 255, 0.5);
        display: block;
        padding: 5px;
        transition: all 0.3s ease-in-out 0s;
    }
    .circle-tile-footer:hover {
        background-color: rgba(0, 0, 0, 0.2);
        color: rgba(255, 255, 255, 0.5);
        text-decoration: none;
    }
    .circle-tile-heading.dark-blue:hover {
        background-color: #2E4154;
    }
    .circle-tile-heading.green:hover {
        background-color: #138F77;
    }
    .circle-tile-heading.orange:hover {
        background-color: #DA8C10;
    }
    .circle-tile-heading.blue:hover {
        background-color: #2473A6;
    }
    .circle-tile-heading.red:hover {
        background-color: #CF4435;
    }
    .circle-tile-heading.purple:hover {
        background-color: #7F3D9B;
    }
    .tile-img {
        text-shadow: 2px 2px 3px rgba(0, 0, 0, 0.9);
    }

    .dark-blue {
        background-color: #34495E;
    }
    .green {
        background-color: #16A085;
    }
    .blue {
        background-color: #2980B9;
    }
    .orange {
        background-color: #F39C12;
    }
    .red {
        background-color: #E74C3C;
    }
    .purple {
        background-color: #8E44AD;
    }
    .dark-gray {
        background-color: #7F8C8D;
    }
    .gray {
        background-color: #95A5A6;
    }
    .light-gray {
        background-color: #BDC3C7;
    }
    .yellow {
        background-color: #F1C40F;
    }
    .text-dark-blue {
        color: #34495E;
    }
    .text-green {
        color: #16A085;
    }
    .text-blue {
        color: #2980B9;
    }
    .text-orange {
        color: #F39C12;
    }
    .text-red {
        color: #E74C3C;
    }
    .text-purple {
        color: #8E44AD;
    }
    .text-faded {
        color: rgba(255, 255, 255, 0.7);
    }

    .rating-block{
        background-color:#FAFAFA;
        border:1px solid #EFEFEF;
        padding:15px 15px 20px 15px;
        border-radius:3px;
    }
    .bold{
        font-weight:700;
    }
    .padding-bottom-7{
        padding-bottom:7px;
    }

    .review-block{
        background-color:#FAFAFA;
        border:1px solid #EFEFEF;
        padding:15px;
        border-radius:3px;
        margin-bottom:15px;
    }
    .review-block-name{
        font-size:12px;
        margin:10px 0;
    }
    .review-block-date{
        font-size:12px;
    }
    .review-block-rate{
        font-size:13px;
        margin-bottom:15px;
    }
    .review-block-title{
        font-size:15px;
        font-weight:700;
        margin-bottom:10px;
    }
    .review-block-description{
        font-size:13px;
    }

    .swal-footer {
        text-align: center !important;
    }

    .switch {
      position: relative;
      display: inline-block;
      width: 45px;
      height: 25px;
  }

  .switch input { 
      opacity: 0;
      width: 0;
      height: 0;
  }

  .slider {
      position: absolute;
      cursor: pointer;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background-color: #47bb50;
      -webkit-transition: .4s;
      transition: .4s;
  }

  .slider:before {
      position: absolute;
      content: "";
      height: 15px;
      width: 15px;
      left: 2px;
      bottom: 5px;
      background-color: white;
      -webkit-transition: .4s;
      transition: .4s;
  }

  input:checked + .slider {
      background-color: #ff6b6b;
  }

  input:focus + .slider {
      box-shadow: 0 0 1px #ff6b6b;
  }

  input:checked + .slider:before {
      -webkit-transform: translateX(26px);
      -ms-transform: translateX(26px);
      transform: translateX(26px);
  }

  /* Rounded sliders */
  .slider.round {
      border-radius: 34px;
  }

  .slider.round:before {
      border-radius: 50%;
  }


</style>
<div class="modal-header bg-info">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
</div>
<div class="card hovercard" id="writerView">
    <div class="card-background">
        <img class="card-bkimg" alt="" src="{{url('/img/writer_cover.jpg')}}">
    </div>
    <div class="useravatar">
        <img alt="" src="@if ($writer->image ==null){{url('/img/default-profile-2.png')}} @else {{url('/user-uploads/avatar/'.$writer->image)}} @endif">
    </div>
    <div class="card-info"> <span class="card-title">{{$writer->name}}</span>
    </div>
</div>
<div class="btn-pref btn-group btn-group-justified btn-group-lg" role="group" aria-label="...">
    <div class="btn-group" role="group">
        <button type="button" id="stats" class="btn btn-info" href="#tab1" data-toggle="tab"><span class="glyphicon glyphicon-stats" aria-hidden="true"></span>
            <div class="hidden-xs">Statistics</div>
        </button>
    </div>
    <div class="btn-group" role="group">
        <button type="button" id="info" class="btn btn-default" href="#tab2" data-toggle="tab"><span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span>
            <div class="hidden-xs">Information</div>
        </button>
    </div>
    <div class="btn-group" role="group">
        <button type="button" id="articles" class="btn btn-default" href="#tab3" data-toggle="tab"><span class="glyphicon glyphicon-tasks" aria-hidden="true"></span>
            <div class="hidden-xs">Incomplete Articles</div>
        </button>
    </div>
    <div class="btn-group" role="group">
        <button type="button" id="activity" class="btn btn-default" href="#tab4" data-toggle="tab"><span class="fa fa-history" aria-hidden="true"></span>
            <div class="hidden-xs">Activity</div>
        </button>
    </div>
</div>

<div class="well">
    <div class="tab-content">
        <div class="tab-pane fade in active" id="tab1">
            <div class="row">
                <div class="col-md-12 m-b-10">
                    <div class="rating-block">
                        <h4>Average rating</h4>
                        <h2 class="bold padding-bottom-7">{{number_format($rating, 2)}} <small>/ 5</small></h2>
                        <button type="button" class="btn @if (round($rating)+1 > 1)btn-warning @endif btn-sm" aria-label="Left Align">
                            <span class="glyphicon glyphicon-star" aria-hidden="true"></span>
                        </button>
                        <button type="button" class="btn @if (round($rating)+1 > 2)btn-warning @endif btn-sm" aria-label="Left Align">
                            <span class="glyphicon glyphicon-star" aria-hidden="true"></span>
                        </button>
                        <button type="button" class="btn @if (round($rating)+1 > 3)btn-warning @endif btn-sm" aria-label="Left Align">
                            <span class="glyphicon glyphicon-star" aria-hidden="true"></span>
                        </button>
                        <button type="button" class="btn @if (round($rating)+1 > 4)btn-warning @endif btn-grey btn-sm" aria-label="Left Align">
                            <span class="glyphicon glyphicon-star" aria-hidden="true"></span>
                        </button>
                        <button type="button" class="btn @if (round($rating)+1 > 5)btn-warning @endif btn-grey btn-sm" aria-label="Left Align">
                            <span class="glyphicon glyphicon-star" aria-hidden="true"></span>
                        </button>
                    </div>
                </div>
                <div class="col-md-12 m-b-10">
                    <div class="rating-block">
                        <h5 class="box-title">@lang('app.selectDateRange')</h5>
                        <div class="input-daterange input-group" id="date-range">
                            <input type="text" name="startDate" class="form-control" id="startDate"  placeholder="@lang('app.startDate')" value="{{request()->startDate}}" />
                            <span class="input-group-addon bg-info b-0 text-white">@lang('app.to')</span>
                            <input type="text" name="endDate" class="form-control" id="endDate" placeholder="@lang('app.endDate')" value="{{request()->endDate}}" />
                        </div>
                    </div>
                    <div class="rating-block m-t-5">
                        <div class="row">
                            <h4>Written Articles</h4>
                            <div class="col-md-3 p-20">
                                Word Count: <span id="rangeWords" class="badge badge-info badge-lg">{{$range_words}}</span>
                            </div>
                            <div class="col-md-3 p-20">
                                Article Count: <span id="rangeArticles" class="badge badge-info badge-lg">{{count($articles)}}</span>
                            </div>
                            <div class="col-md-3 p-20">
                                Articles/Day (Avg.): <span id="rangeDayArt" class="badge badge-info badge-lg">{{ $articles->count() && $days ? number_format($articles->count()/$days) : 0 }}</span>
                            </div>
                            <div class="col-md-3 p-20">
                                Words/Day (Avg.): <span id="rangeDayWor" class="badge badge-info badge-lg">{{ $range_words && $days ? number_format($range_words/$days) : 0}}</span>
                            </div>
                        </div>
                    </div>
                    @if(isset($edited_articles))
                    <div class="rating-block m-t-5">
                        <div class="row">
                            <h4>Reviewed Articles</h4>
                            <div class="col-md-3 p-20">
                                Word Count: <span id="rangeEwords" class="badge badge-info badge-lg">{{$edited_words}}</span>
                            </div>
                            <div class="col-md-3 p-20">
                                Article Count: <span id="rangeEarticles" class="badge badge-info badge-lg">{{count($edited_articles)}}</span>
                            </div>
                            <div class="col-md-3 p-20">
                                Articles/Day (Avg.): <span id="rangeDayEart" class="badge badge-info badge-lg">{{ $edited_articles->count() && $days ? number_format($edited_articles->count()/$days) : 0}}</span>
                            </div>
                            <div class="col-md-3 p-20">
                                Words/Day (Avg.): <span id="rangeDayEwor" class="badge badge-info badge-lg">{{$edited_articles->count() && $days ? number_format($edited_words/$days) : 0}}</span>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            <div class="row">
                <div class="col-lg-3 col-sm-6">
                    <a style="font-weight: 300;" target="_blank" href="@if(auth()->user()->hasRole('admin')) {{route('admin.article.index')}}@else{{route('member.article.index')}}@endif?type=completedArticles&writer={{$writer->id}}">
                        <div class="circle-tile ">
                            <div class="circle-tile-heading dark-blue"><i class="fa fa-check fa-3x"></i></div>
                            <div class="circle-tile-content dark-blue">
                                <div class="circle-tile-description text-faded"> Completed Articles</div>
                                <div class="circle-tile-number text-faded ">{{count($articles->where('writing_status', 2)->where('article_status', '!=', 1))}}</div>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-lg-3 col-sm-6">
                    <a style="font-weight: 300;" target="_blank" href="@if(auth()->user()->hasRole('admin')) {{route('admin.article.index')}}@else{{route('member.article.index')}}@endif?hide=on&writer={{$writer->id}}">
                        <div class="circle-tile ">
                            <div class="circle-tile-heading red"><i class="fa fa-exclamation-circle fa-3x"></i></div>
                            <div class="circle-tile-content red">
                                <div class="circle-tile-description text-faded"> Incomplete Articles</div>
                                <div class="circle-tile-number text-faded ">{{count($articles->where('writing_status', 0))}}</div>
                            </div>
                        </div>
                    </a>
                </div>

                @if(!$writer->hasRole($inhouseWriterRole))
                <div class="col-lg-3 col-sm-6">
                    <a style="font-weight: 300;" target="_blank" href="@if(auth()->user()->hasRole('admin')) {{route('admin.article.index')}}@else{{route('member.article.index')}}@endif?type=paidUnpaidArticles&writer={{$writer->id}}">
                        <div class="circle-tile ">
                            <div class="circle-tile-heading dark-blue"><i class="fa fa-money fa-3x"></i></div>
                            <div class="circle-tile-content dark-blue">
                                <div class="circle-tile-description text-faded"> Paid Articles</div>
                                <div class="circle-tile-number text-faded ">{{count($articles->where('article_status', 1))}}</div>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-lg-3 col-sm-6">
                    <div class="circle-tile ">
                        <a href="#"><div class="circle-tile-heading red"><i class="fa fa-credit-card fa-3x"></i></div></a>
                        <div class="circle-tile-content red">
                            <div class="circle-tile-description text-faded"> Total Earned</div>
                            <div class="circle-tile-number text-faded ">{{$earning}} BDT</div>
                        </div>
                    </div>
                </div>
                @endif

            </div> 
        </div>
        <div class="tab-pane fade in" id="tab2">
            <table class="table table-bordered">
                <thead>
                    <tr >
                        <th class="info">Rate (BDT Per 1K Words)</th>
                        <td id="showRate">
                            {{$writer->rate ? number_format($writer->rate->rate, 2) : 0.00}} BDT
                            @if(auth()->user()->hasRole('admin') && $writer->hasRole($writerRole))
                            <a href="javascript:;" class="label label-sm label-danger m-l-5" id="changeRateButton">Change</a>
                            @endif
                        </td>
                        @if(auth()->user()->hasRole('admin') && $writer->hasRole($writerRole))
                        <td class="row" id="changeRate" style="display: none;">
                            <div class="col-md-8">
                                <input type="number" name="rate" value="{{$writer->rate ? $writer->rate->rate : 0}}" class="form-control" id="rateValue">
                            </div>
                            <div class="col-md-4" align="right">
                                <button class="btn bg-success text-white" id="UpdateRate">Update Rate</button>
                                <button class="btn bg-inverse text-white" id="cancelUpdateRate">Cancel</button>
                            </div>
                        </td>
                        @endif
                    </tr>
                    <tr >
                        <th class="info">Full Name</th>
                        <td>{{$writer->name}}</td>
                    </tr>
                    <tr>
                        <th class="info">Email</th>
                        <td>{{$writer->email}}</td>
                    </tr>
                    <tr>
                        <th class="info">Phone Number</th>
                        <td>{{$writer->mobile}}</td>
                    </tr>

                    <tr>
                        <th class="info">Gender</th>
                        <td>{{ucfirst($writer->gender)}}</td>
                    </tr>

                    <tr>
                        <th class="info">Account Status</th>
                        <td> {{ucfirst($writer->status)}}</td>
                    </tr>

                    <tr>
                        <th class="info">Availability Status</th>
                        <td>
                            <div class="form-group">
                                @if(user()->is_writer_head() || auth()->user()->hasRole('admin') || user()->is_writer_head_assistant())
                                    <label class="switch">
                                        <input type="checkbox" id="unavailableWriter" {{$writer->unavailable ? 'checked' : ''}}>
                                        <span class="slider round"></span>
                                    </label>
                                @endif
                                <p>
                                    <label class="p-10 text-{{$writer->unavailable ? 'danger' : 'success'}}">
                                        {{$writer->unavailable ? 'Unavailable - ' : 'Available'}}
                                    </label>
                                    @if($writer->unavailable)
                                    {{$writer->unavailable->details}}
                                    @endif
                                </p>
                            </div>

                            @if(user()->is_writer_head() || user()->hasRole('admin') || user()->is_writer_head_assistant())
                            <div class="form-group" id="unavailableNote" style="display: none;">
                                <textarea class="form-control" placeholder="Write a note for unavailability"></textarea>
                                <br>
                                <button class="btn btn-sm btn-danger" id="saveUnavailable">Save</button>
                            </div>
                            @endif
                        </td>
                    </tr>

                    @if($writer->is_writer())
                    <tr>
                        <th class="label-info" style="vertical-align: middle;">
                            Payment Details (Default)
                        </th>
                        <td id="defaultPayment">
                            <div class="label-info p-10">
                                @if(auth()->user()->hasRole('admin'))
                                <a href="javascript:;" class="label label-sm label-danger m-l-5" id="changeDefaultPaymentButton" style="float: right;">Change</a>
                                @endif
                                @if($writer->paymentDetails !=null)
                                <b><u>{{$writer->paymentDetails->title}}</u></b> <br/>
                                {!! $writer->paymentDetails->details !!}
                                @endif
                            </div>
                        </td>

                        @if(user()->is_writer() || user()->hasRole('admin'))
                        <td id="changeDefaultPayment" class="row" style="display: none;">
                            <div class="col-md-8">
                                @if($writer->paymentDetails ==null || user()->hasRole('admin'))
                                <select name="payment_method" id="payment_method" class="form-control">
                                    @foreach ($writer->paymentInfos as $payInfo)
                                    <option value="{{$payInfo->id}}">#{{$payInfo->id}}: {{$payInfo->payment_method}} ({{strip_tags(str_replace('<br/>', ', ', $payInfo->payment_details))}})</option>
                                    @endforeach
                                </select>
                                @else
                                <div class="text-danger">
                                    Please contact the authority to change the default payment details!
                                </div>
                                @endif
                            </div>
                            <div class="col-md-4">
                                <div class="col-md-6" align="left">
                                    @if($writer->paymentDetails ==null || auth()->user()->hasRole('admin'))
                                    <button class="btn bg-success text-white btn-sm" id="update-payment">Set As Default</button>
                                    @endif
                                </div>
                                <div class="col-md-6" align="right">
                                    <button class="btn btn-sm bg-inverse text-white" id="cancelDefaultPaymentChange">Cancel</button>
                                </div>
                            </div>
                        </td>
                        @endif
                    </tr>

                    @if(user()->hasRole('admin') || user()->is_writer())
                    <tr>
                        <th class="info" style="vertical-align: middle;">
                            Added Payment Information
                        </th>
                        <td>
                            @foreach ($writer->paymentInfos as $payInfo)
                            <div class="p-10 label-default m-b-5" id="paymentDetails-{{$payInfo->id}}">
                                #{{$payInfo->id}}: {{$payInfo->payment_method}} ({{strip_tags(str_replace('<br/>', ', ', $payInfo->payment_details))}})

                                @if(user()->hasRole('admin'))
                                <a href="javascript:;" onclick="deleteDetails('{{$payInfo->id}}')" class="label label-danger">Delete</a>
                                @endif
                            </div>
                            @endforeach
                            <a class="btn bg-info text-white btn-sm" href="javascript:;" onclick="addPaymentDetails()">Add New Details</a>
                        </td>
                    </tr>
                    @endif
                    @endif
                </thead>
            </table>
        </div>
        <div class="tab-pane fade in" id="tab3">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr role="row">
                        <th>#</th>
                        <th>Title</th>
                        <th>Assignee</th>
                        <th>Creator</th>
                        <th>Word Count</th>
                        <th>Priority</th>
                        <th>Deadline</th>
                    </tr>
                </thead>
                <tbody id="list">
                    @forelse ($incompleteArticles as $article)
                    <tr role="row" class="odd">
                        <td>{{$article->id}}</td>
                        <td>
                            <a target="_blank" href="@if(auth()->user()->hasRole('admin')) {{route('admin.article.show', $article->id)}} @else {{route('member.article.show', $article->id)}} @endif" class="@if($article->writing_status == 1) text-warning @elseif($article->writing_status == 2) text-dark @endif" style="@if($article->writing_status == 2 && $article->publisher != $user->id) text-decoration: line-through; @endif @if ($article->publishing_status == 1) text-decoration: line-through; @endif">{{$article->title}}</a>
                        </td>
                        <td>{{App\User::find($article->assignee)->name}}</td>
                        <td>{{App\User::find($article->creator)->name}}</td>
                        <td>{{$article->word_count}}</td>
                        <td><div class="label @if($article->priority =='low') label-success @elseif($article->priority =='medium') label-warning @else label-danger @endif">@if($article->priority =='low') Low @elseif($article->priority =='medium') Medium @else High @endif</div></td>
                        <td>{{$article->writing_deadline}}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8">
                            No data found!
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="tab-pane fade in" id="tab4">
            <div class="row">
                <div class="col-xs-12 panel-body p-t-15">
                    <div class="steamline">
                        @foreach ($writer->logs->sortByDesc('id') as $log)
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
</div>


<script src="{{ asset('plugins/bower_components/custom-select/custom-select.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.js') }}"></script>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script src="{{ asset('plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>

<script type="text/javascript">
    $('#unavailableWriter').change(function () {
        if ($(this).prop('checked')) {
            $('#unavailableNote').toggle('show');
        } else {
            $('#unavailableNote').hide();
            changeUnavailablity(false);
        }
    });

    $('#saveUnavailable').click(function () {
        note = $('#unavailableNote').find('textarea').val();
        changeUnavailablity(true, note);
    })

    function changeUnavailablity(status, note = null) {
        $.easyAjax({
            url: '{{ route('member.article.writerAvailability', $writer->id) }}',
            type: "POST",
            data: {
                'status': status,
                'note': note,
                '_token': '{{ csrf_token() }}'
            },
            success: function(res){
                viewWriter('{{$writer->id}}');
            }
        })
    }
    
    $(document).ready(function() {
        $(".btn-pref .btn").click(function () {
            $(".btn-pref .btn").removeClass("btn-info").addClass("btn-default");
            $(this).removeClass("btn-default").addClass("btn-info");   
        });
    });

    $('#UpdateRate').click(function(){
        var url = '{{route('member.article.writerRateUpdate', $writer->id)}}';
        var rate = $('#rateValue').val();
        $.easyAjax({
            url: url,
            type: "POST",
            data: {'rate': rate, '_token': '{{csrf_token()}}'},
            success: function(){

            }
        })
    })

    @if(!$writer->is_inhouse_writer())
    $('#update-payment').click(function(){

     var buttons = {
        cancel: "Cancel",
        confirm: {
            text: "Yes",
            visible: true,
            className: "danger",
        }
    };
    swal({
        title: "Are you sure want to update default details?",
        text: "Please enter your password below:",
        dangerMode: true,
        icon: 'warning',
        buttons: buttons,
        content: "input"
    }).then(function (isConfirm) {
        if (isConfirm !=='' && isConfirm !==null) {
            var url = "{{route('member.article.writerPaymentUpdate', $writer->id)}}";
            var token = "{{ csrf_token() }}";
            var dataObject = {'password': isConfirm, 'details_id': $('#payment_method').val(), '_token': '{{csrf_token()}}'};
            $.easyAjax({
                type: 'POST',
                url: url,
                data: dataObject,
                success: function (response) {
                    if (response.status == "success") {
                        swal("Success", response.message, "success");
                        viewWriter('{{$writer->id}}');
                    } else {
                        swal("Error!", response.message, "error");
                    }
                }
            });
        }
        if (isConfirm ==='') {swal("Empty!", "You must enter your password!", "error");}
    });
})

    function deleteDetails(id){
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
            var url = "{{ route('member.article.writerPaymentDetailsDelete',':id') }}";
            url = url.replace(':id', id);
            var token = "{{ csrf_token() }}";
            var dataObject = {'password': isConfirm, '_token': token};
            $.easyAjax({
                type: 'POST',
                url: url,
                data: dataObject,
                success: function (response) {
                    if (response.status == "success") {
                        swal("Success", "The task has been deletd!", "success");
                        $('#paymentDetails-'+id).hide();
                    } else {
                        swal("Error!", "The password you entered is incorrect!", "error");
                    }
                }
            });
        }
        if (isConfirm ==='') {swal("Empty!", "You must enter your password!", "error");}
    });
}

function addPaymentDetails(){
    $(".well").html('<div class="cssload-speeding-wheel"></div>');
    var url = "{{ route('member.article.writerPaymentDetails',':id') }}";
    url = url.replace(':id', '{{$writer->id}}');
    $("#subTaskModal").modal('hide');
    $.ajaxModal('#subTaskModal', url);
    $("#subTaskModal").modal('show');
}

@endif

function viewWriter(id) {
    var url = "{{ route('member.article.writer', ':id')}}";
    var url = url.replace(':id', id);
    $("#subTaskModal").modal('hide');
    $.ajaxModal('#subTaskModal', url);
    $("#subTaskModal").modal('show');
}

$('#changeRateButton').click(function(){
    $('#changeRate').show();
    $('#showRate').hide();
})

$('#cancelUpdateRate').click(function(){
    $('#changeRate').hide();
    $('#showRate').show();
})

$('#changeDefaultPaymentButton').click(function(){
    $('#changeDefaultPayment').show();
    $('#defaultPayment').hide();
})

$('#cancelDefaultPaymentChange').click(function(){
    $('#changeDefaultPayment').hide();
    $('#defaultPayment').show();
})

$("#payment_method").select2({
    formatNoMatches: function () {
        return "{{ __('messages.noRecordFound') }}";
    }
})

jQuery('#date-range').datepicker({
    toggleActive: true,
    format: 'yyyy-mm-dd',
    language: '{{ $global->locale }}',
    autoclose: true,
    todayHighlight: true
});

$('.input-daterange').change(function(){
    var start = $('#startDate').val();
    var end = $('#endDate').val();
    var url = "{{route('member.article.writerStats', $writer->id)}}";
    $.easyAjax({
        type: 'POST',
        url: url,
        data: {'startDate': start, 'endDate': end, '_token': '{{csrf_token()}}'},
        success: function (response) {
            $('#rangeWords').html(response.words);
            $('#rangeArticles').html(response.articles.length);
            $('#rangeDayArt').html((response.articles.length/response.days).toFixed(1));
            $('#rangeDayWor').html((response.words/response.days).toFixed(1));

            //For edited articles
            if (response.earticles !== 'undefined') {
                $('#rangeEwords').html(response.ewords);
                $('#rangeEarticles').html(response.earticles.length);
                $('#rangeDayEart').html((response.earticles.length/response.days).toFixed(1));
                $('#rangeDayEwor').html((response.ewords/response.days).toFixed(1));
            }
        }
    });
})
</script>