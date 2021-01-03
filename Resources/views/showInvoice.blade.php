 <!DOCTYPE html>
 <html>
 <head>
   <title>{{$invoice->name}} ({{$invoice->user->name}})</title>
   <link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
   <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
</head>
<body>
    <div class="row" id="invoice">
        <div class="col-1"></div>
        <div class="col-3">
            <img src="{{ asset('favicon.png') }}" height="100px">
        </div>
        <div class="col-3 text-center">
            <u><h3>PAYSLIP</h3></u>
        </div>
        <div class="col-2 text-right">
            Payslip No:
            <br>
                Generated On:
                <br>
            @if($invoice->status !=0)
                Payment Date:
            @endif
            <br>
            Payment Status:
        </div>
        <div class="col-2 text-center">
            <div class="border"><b>{{$invoice->name}}</b></div>
            <div class="m-b-5"><b>{{date("Y-m-d",strtotime($invoice->created_at))}}</b></div>
            @if($invoice->status !=0)
            <div class="col-12 m-b-5 text-success"><b>{{date("Y-m-d",strtotime($invoice->paid_date))}}</b></div>
            @endif
            <div>
                <b>
                    @if($invoice->status == 1) <div class='border border-success text-success'>Paid</div>
                    @else
                    <div class='border border-danger text-danger'>Unpaid</div>
                    @endif
                </b>
            </div>
        </div>
        <div class="col-1"></div>


        <div class="col-1"></div>
        <div class="col-3">
            {!! $address ? $address->value : '<span class="text-danger"> Please update company address from <br> "Article Management >> Settings" </span>' !!}
        </div>
        <div class="col-3 text-right">
            Payment To:
            <br>
            E-mail:
            <br>
            Phone No.:
            <br>
            Payment Method:
            <br>
            Payment Details:
        </div>
        <div class="col-3 text-left">
            <b>{{$invoice->user->name}}</b>
            <br>
            {{$invoice->user->email}}
            <br>
            {{$invoice->user->phone}}
            <br>
            {!! $invoice->user->paymentDetails ? $invoice->user->paymentDetails->title : '<span class="text-danger">Empty!</span>' !!}
            <br>
            {!! $invoice->user->paymentDetails ? $invoice->user->paymentDetails->details : '<span class="text-danger">Please select '.$invoice->user->name."'s default payment details!</span>" !!}
            <br>
        </div>

        <div class="col-2"></div>
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>

        <div class="col-1"></div>
        <div class="col-10">
            <table class="table table-sm table-bordered">
                <thead>
                    <tr align="center">
                        <th scope="col">ID</th>
                        <th scope="col">Title</th>
                        <th scope="col">Words</th>
                        <th scope="col">Bill</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($invoice->articles as $article)
                    <tr>
                        <th scope="row" class="text-center">#{{$article->id}}</th>
                        <td><a style="text-decoration: none;" target="_blank" href="@if(auth()->user()->hasRole('admin')) {{route('admin.article.show', $article->id)}} @else {{route('member.article.show', $article->id)}} @endif">{{$article->title}}</a></td>
                        <td class="text-center">{{$article->word_count}}</td>
                        <td class="text-center">{{$article->rate/1000*$article->word_count}} /=</td> 
                    </tr>
                    @endforeach
                    @php($count = count($articles))
                    @while ($count<25)
                    <tr style="height: 30px">
                        <th scope="row" class="text-center"></th>
                        <td></td>
                        <td class="text-center"></td>
                        <td class="text-right"></td>    
                    </tr>
                    @php($count++)
                    @endwhile

                    <tr>
                        <th scope="row" class="text-right" colspan="2">Total=</th>
                        <th scope="row" class="text-center">{{$words}} words</th>
                        <th scope="row" class="text-center">{{$amount}} /=</th>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="col-1"></div>

        <div class="col-10 offset-1" style="height: 50px;"><b>In Words:</b> @php($f = new NumberFormatter("en", NumberFormatter::SPELLOUT)) {{ucfirst(numfmt_format($f, round($amount)))}} taka only.</div>
        
        <div class="col-12" style="height: 50px;"></div>

        <div class="col-1"></div>
        <div class="col-4 text-center">
            <hr>
            Signature of Account Manager
        </div>
        <div class="col-2"></div>
        <div class="col-4 text-center">
            <hr>
            Signature of Project Manager
        </div>
        <div class="col-1"></div>

        <div class="col-1"></div>
        <div class="col-10"><hr style="border-top: 1px dashed grey"/></div>
        <div class="col-1"></div>

        <div class="col-12 text-center"><b>For Office Use Only</b></div>

        <div class="col-1"></div>
        <div class="col-2 text-center" style="background: rgba(0,0,0,0.2); border: 2px solid grey;">Approved/Not Approved</div>
        <div class="col-3" style="border: 2px solid grey;"></div>
        <div class="col-2 text-center" style="background: rgba(0,0,0,0.2); border: 2px solid grey;">Signature & Seal of Authority</div>
        <div class="col-3" style="border: 2px solid grey;"></div>
        <div class="col-1"></div>

    </div>

    <div class="row">
        <div class="col-12" style="height: 30px;"></div>
    </div>

    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

    <script type="text/javascript">
        window.print();
    </script>
</body>
</html>