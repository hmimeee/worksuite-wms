<!DOCTYPE html>

<html lang="en">
<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <link rel="icon" type="image/png" href="{{asset('favicon.png')}}">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="theme-color" content="#ffffff">

    <title>@if($userRole) {{ $userRole->display_name }} @lang("app.panel")  @else @lang("app.employeePanel") @endif | {{ $pageTitle }}</title>
    <!-- Bootstrap Core CSS -->
    <link href="{{ asset('plugins/bootstrap/dist/css/bootstrap.min.css') }}" rel="stylesheet">
    <link rel='stylesheet prefetch'
          href='https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/0.8.2/css/flag-icon.min.css'>
    <link rel='stylesheet prefetch'
          href='https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.2/css/bootstrap-select.min.css'>

    <!-- This is Sidebar menu CSS -->
    <link href="{{ asset('plugins/bower_components/sidebar-nav/dist/sidebar-nav.min.css') }}" rel="stylesheet">

    <link href="{{ asset('plugins/bower_components/toast-master/css/jquery.toast.css') }}" rel="stylesheet">
    <link href="{{ asset('plugins/bower_components/sweetalert/sweetalert.css') }}" rel="stylesheet">

    <!-- This is a Animation CSS -->
    <link href="{{ asset('css/animate.css') }}" rel="stylesheet">

    @stack('head-script')

            <!-- This is a Custom CSS -->
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <!-- color CSS you can use different color css from css/colors folder -->
    <!-- We have chosen the skin-blue (default.css) for this starter
       page. However, you can choose any other skin from folder css / colors .
       -->
    <link href="{{ asset('css/colors/default.css') }}" id="theme" rel="stylesheet">
    <link href="{{ asset('plugins/froiden-helper/helper.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/magnific-popup.css') }}">
    <link href="{{ asset('css/custom-new.css') }}" rel="stylesheet">

    @if($global->rounded_theme)
    <link href="{{ asset('css/rounded.css') }}" rel="stylesheet">
    @endif

    @if(file_exists(public_path().'/css/member-custom.css'))
    <link href="{{ asset('css/member-custom.css') }}" rel="stylesheet">
    @endif


    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

    @if($pushSetting->status == 'active')
    <link rel="manifest" href="{{ asset('manifest.json') }}" />
    <script src="https://cdn.onesignal.com/sdks/OneSignalSDK.js" async='async'></script>
    <script>
        var OneSignal = window.OneSignal || [];
        OneSignal.push(function() {
            OneSignal.init({
                appId: "{{ $pushSetting->onesignal_app_id }}",
                autoRegister: false,
                notifyButton: {
                    enable: false,
                },
                promptOptions: {
                    /* actionMessage limited to 90 characters */
                    actionMessage: "We'd like to show you notifications for the latest news and updates.",
                    /* acceptButtonText limited to 15 characters */
                    acceptButtonText: "ALLOW",
                    /* cancelButtonText limited to 15 characters */
                    cancelButtonText: "NO THANKS"
                }
            });
            OneSignal.on('subscriptionChange', function (isSubscribed) {
                console.log("The user's subscription state is now:", isSubscribed);
            });


            if (Notification.permission === "granted") {
                // Automatically subscribe user if deleted cookies and browser shows "Allow"
                OneSignal.getUserId()
                    .then(function(userId) {
                        if (!userId) {
                            OneSignal.registerForPushNotifications();
                        }
                        else{
                            let db_onesignal_id = '{{ $user->onesignal_player_id }}';

                            if(db_onesignal_id == null || db_onesignal_id !== userId){ //update onesignal ID if it is new
                                updateOnesignalPlayerId(userId);
                            }
                        }
                    })
            } else {
                OneSignal.isPushNotificationsEnabled(function(isEnabled) {
                    if (isEnabled){
                        console.log("Push notifications are enabled! - 2    ");
                        // console.log("unsubscribe");
                        // OneSignal.setSubscription(false);
                    }
                    else{
                        console.log("Push notifications are not enabled yet. - 2");
                        // OneSignal.showHttpPrompt();
                        // OneSignal.registerForPushNotifications({
                        //         modalPrompt: true
                        // });
                    }

                    OneSignal.getUserId(function(userId) {
                        console.log("OneSignal User ID:", userId);
                        // (Output) OneSignal User ID: 270a35cd-4dda-4b3f-b04e-41d7463a2316
                        let db_onesignal_id = '{{ $user->onesignal_player_id }}';
                        console.log('database id : '+db_onesignal_id);

                        if(db_onesignal_id == null || db_onesignal_id !== userId){ //update onesignal ID if it is new
                            updateOnesignalPlayerId(userId);
                        }


                    });


                    OneSignal.showHttpPrompt();
                });

            }
        });
    </script>
    @endif

    {{--Custom theme styles--}}
    @if($global->active_theme == 'custom')
    <style>

        :root {
            --header_color: {{ $employeeTheme->header_color }};
            --sidebar_color: {{ $employeeTheme->sidebar_color }};
            --link_color: {{ $employeeTheme->link_color }};
            --sidebar_text_color: {{ $employeeTheme->sidebar_text_color }};
        }
        .navbar-header {
            background: {{ $employeeTheme->header_color }};
        }
        .sidebar-nav .notify {
            margin: 0 !important;
        }
        .sidebar .notify .heartbit {
            border: 5px solid {{ $employeeTheme->header_color }} !important;
            top: -23px !important;
            right: -15px !important;
        }
        .sidebar .notify .point {
            background-color: {{ $employeeTheme->header_color }} !important;
            top: -13px !important;
        }
        .navbar-top-links > li > a {
            color: {{ $employeeTheme->link_color }};
        }
        /*Right panel*/
        .right-sidebar .rpanel-title {
            background: {{ $employeeTheme->header_color }};
        }
        /*Bread Crumb*/
        .bg-title .breadcrumb .active {
            color: {{ $employeeTheme->header_color }};
        }
        /*Sidebar*/
        .sidebar {
            background: {{ $employeeTheme->sidebar_color }};
            box-shadow: 1px 0px 20px rgba(0, 0, 0, 0.08);
        }
        .sidebar .label-custom {
            background: {{ $employeeTheme->header_color }};
        }
        #side-menu li a, #side-menu > li:not(.user-pro) > a {
            color: var(--sidebar_text_color);
            border-left: 0 solid var(--sidebar_color);
        }
        #side-menu > li > a:hover,
        #side-menu > li > a:focus {
            background: rgba(0, 0, 0, 0.07);
        }
        #side-menu > li > a.active {
            /* border-left: 3px solid var(--header_color); */
            color: var(--link_color);
            background: var(--header_color);
        }
        #side-menu > li > a.active i {
            color: var(--link_color);
        }
        #side-menu ul > li > a:hover {
            color: var(--link_color);
        }
        #side-menu ul > li > a.active, #side-menu ul > li > a:hover {
            color: var(--header_color);
        }
        .sidebar #side-menu .user-pro .nav-second-level a:hover {
            color: {{ $employeeTheme->header_color }};
        }
        .nav-small-cap {
            color: {{ $employeeTheme->sidebar_text_color }};
        }
        /* .content-wrapper .sidebar .nav-second-level li {
            background: #444859;
        }
        @media (min-width: 768px) {
            .content-wrapper #side-menu ul,
            .content-wrapper .sidebar #side-menu > li:hover,
            .content-wrapper .sidebar .nav-second-level > li > a {
                background: #444859;
            }
        } */

        /*themecolor*/
        .bg-theme {
            background-color: {{ $employeeTheme->header_color }} !important;
        }
        .bg-theme-dark {
            background-color: {{ $employeeTheme->sidebar_color }} !important;
        }
        /*Chat widget*/
        .chat-list .odd .chat-text {
            background: {{ $employeeTheme->header_color }};
        }
        /*Button*/
        .btn-custom {
            background: {{ $employeeTheme->header_color }};
            border: 1px solid {{ $employeeTheme->header_color }};
            color: {{ $employeeTheme->link_color }};
        }
        .btn-custom:hover {
            background: {{ $employeeTheme->header_color }};
            border: 1px solid {{ $employeeTheme->header_color }};
        }
        /*Custom tab*/
        .customtab li.active a,
        .customtab li.active a:hover,
        .customtab li.active a:focus {
            border-bottom: 2px solid {{ $employeeTheme->header_color }};
            color: {{ $employeeTheme->header_color }};
        }
        .tabs-vertical li.active a,
        .tabs-vertical li.active a:hover,
        .tabs-vertical li.active a:focus {
            background: {{ $employeeTheme->header_color }};
            border-right: 2px solid {{ $employeeTheme->header_color }};
        }
        /*Nav-pills*/
        .nav-pills > li.active > a,
        .nav-pills > li.active > a:focus,
        .nav-pills > li.active > a:hover {
            background: {{ $employeeTheme->header_color }};
            color: {{ $employeeTheme->link_color }};
        }

        .member-panel-name{
            background: {{ $employeeTheme->header_color }};
        }

        /*fullcalendar css*/
        .fc th.fc-widget-header{
            background: {{ $employeeTheme->sidebar_color }};
        }

        .fc-button{
            background: {{ $employeeTheme->header_color }};
            color: {{ $employeeTheme->link_color }};
            margin-left: 2px !important;
        }

        .fc-unthemed .fc-today{
            color: #757575 !important;
        }

        .user-pro{
            background-color: {{ $employeeTheme->sidebar_color }};
        }

        .top-left-part{
            background: {{ $employeeTheme->sidebar_color }};
        }

        .notify .heartbit{
            border: 5px solid {{ $employeeTheme->sidebar_color }};
        }

        .notify .point{
            background-color: {{ $employeeTheme->sidebar_color }};
        }
    </style>

    <style>
        {!! $employeeTheme->user_css !!}
    </style>
    @endif
    {{--Custom theme styles end--}}

    <style>
        .sidebar .notify  {
        margin: 0 !important;
        }
        .sidebar .notify .heartbit {
        top: -23px !important;
        right: -15px !important;
        }
        .sidebar .notify .point {
        top: -13px !important;
        }
        .top-notifications .message-center .user-img{
            margin: 0 0 0 0 !important;
        }
    </style>


</head>
<body class="fix-sidebar">
<!-- Preloader -->
<div class="preloader">
    <div class="cssload-speeding-wheel"></div>
</div>
<div id="wrapper">
    @if(!auth()->user()->hasRole(\Modules\Article\Entities\ArticleSetting::where('type', 'writer')->first()->value))
    <!-- Left navbar-header -->
    @include('sections.member_left_sidebar')
            <!-- Left navbar-header end -->
    @else
    @include('article::sections.writer_left_sidebar')
    @endif
    <!-- Page Content -->
    <div id="page-wrapper" class="row">
        <div class="container-fluid">

            @if (!empty($__env->yieldContent('filter-section')))
                <div class="col-md-3 filter-section">
                    <h5><i class="fa fa-sliders"></i> @lang('app.filterResults')</h5>
                    @yield('filter-section')
                </div>
             @endif

             @if (!empty($__env->yieldContent('other-section')))
                <div class="col-md-3 filter-section">
                    @yield('other-section')
                </div>
             @endif


            <div class="
            @if (!empty($__env->yieldContent('filter-section')) || !empty($__env->yieldContent('other-section')))
            col-md-9
            @else
            col-md-12
            @endif
            data-section">

                @if (!empty($__env->yieldContent('filter-section')) || !empty($__env->yieldContent('other-section')))
                    <div class="row hidden-md hidden-lg">
                        <div class="col-xs-12 p-l-25 m-t-10">
                            <button class="btn btn-inverse btn-outline" id="mobile-filter-toggle"><i class="fa fa-sliders"></i></button>
                        </div>
                    </div>
                @endif

                @yield('page-title')

                        <!-- .row -->
                @yield('content')

                @include('sections.right_sidebar')

            </div>
        </div>
        <!-- /.container-fluid -->
    </div>
    <!-- /#page-wrapper -->
</div>
<!-- /#wrapper -->


{{--Timer Modal--}}
<div class="modal fade bs-modal-md in" id="projectTimerModal" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
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
    <!-- /.modal-dialog -->
</div>
{{--Timer Modal Ends--}}

{{--Footer sticky notes--}}
<div id="footer-sticky-notes" class="row hidden-xs hidden-sm">
    <div class="col-md-12" id="sticky-note-header">
        <div class="col-xs-10" style="line-height: 30px">
        @lang('app.menu.stickyNotes') <a href="javascript:;" onclick="showCreateNoteModal()" class="btn btn-success btn-outline btn-xs m-l-10"><i class="fa fa-plus"></i> @lang("modules.sticky.addNote")</a>
            </div>
        <div class="col-xs-2">
            <a href="javascript:;" class="btn btn-default btn-circle pull-right" id="open-sticky-bar"><i class="fa fa-chevron-up"></i></a>
            <a style="display: none;" class="btn btn-default btn-circle pull-right" href="javascript:;" id="close-sticky-bar"><i class="fa fa-chevron-down"></i></a>
        </div>

    </div>

    <div id="sticky-note-list" style="display: none">

        @foreach($stickyNotes as $note)
            <div class="col-md-12 sticky-note" id="stickyBox_{{$note->id}}">
            <div class="well
             @if($note->colour == 'red')
                bg-danger
             @endif
             @if($note->colour == 'green')
                bg-success
             @endif
             @if($note->colour == 'yellow')
                bg-warning
             @endif
             @if($note->colour == 'blue')
                bg-info
             @endif
             @if($note->colour == 'purple')
                bg-purple
             @endif
             b-none">
                <p>{!! nl2br($note->note_text)  !!}</p>
                <hr>
                <div class="row font-12">
                    <div class="col-xs-9">
                        @lang("modules.sticky.lastUpdated"): {{ $note->updated_at->diffForHumans() }}
                    </div>
                    <div class="col-xs-3">
                        <a href="javascript:;"  onclick="showEditNoteModal({{$note->id}})"><i class="ti-pencil-alt text-white"></i></a>
                        <a href="javascript:;" class="m-l-5" onclick="deleteSticky({{$note->id}})" ><i class="ti-close text-white"></i></a>
                    </div>
                </div>
            </div>
        </div>
        @endforeach

    </div>
</div>

<a href="javascript:;" id="sticky-note-toggle"><i class="icon-note"></i></a>

{{--sticky note end--}}

{{--sticky note modal--}}
<div id="responsive-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            Loading ...
        </div>
    </div>
</div>
{{--sticky note modal ends--}}

{{--Timer Modal--}}
<div class="modal fade bs-modal-md in" id="projectTimerModal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md" id="modal-data-application">
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
    <!-- /.modal-dialog -->
</div>
{{--Timer Modal Ends--}}

{{--Ajax Modal--}}
<div class="modal fade bs-modal-md in"  id="subTaskModal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md" id="modal-data-application">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <span class="caption-subject font-red-sunglo bold uppercase" id="subTaskModelHeading">Sub Task e</span>
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

<!-- jQuery -->
<script src="{{ asset('plugins/bower_components/jquery/dist/jquery.min.js') }}"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>

<!-- Bootstrap Core JavaScript -->
<script src="{{ asset('plugins/bootstrap/dist/js/bootstrap.min.js') }}"></script>
<script src='//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.2/js/bootstrap-select.min.js'></script>

<!-- Sidebar menu plugin JavaScript -->
<script src="{{ asset('plugins/bower_components/sidebar-nav/dist/sidebar-nav.min.js') }}"></script>
<!--Slimscroll JavaScript For custom scroll-->
<script src="{{ asset('js/jquery.slimscroll.js') }}"></script>
<!--Wave Effects -->
<script src="{{ asset('js/waves.js') }}"></script>
<!-- Custom Theme JavaScript -->
<script src="{{ asset('plugins/bower_components/sweetalert/sweetalert.min.js') }}"></script>
<script src="{{ asset('js/custom.js') }}"></script>
<script src="{{ asset('js/jasny-bootstrap.js') }}"></script>
<script src="{{ asset('plugins/froiden-helper/helper.js') }}"></script>
<script src="{{ asset('plugins/bower_components/toast-master/js/jquery.toast.js') }}"></script>

{{--sticky note script--}}
<script src="{{ asset('js/cbpFWTabs.js') }}"></script>
<script src="{{ asset('plugins/bower_components/icheck/icheck.min.js') }}"></script>
<script src="{{ asset('plugins/bower_components/icheck/icheck.init.js') }}"></script>
<script src="{{ asset('js/jquery.magnific-popup.min.js') }}"></script>
<script src="{{ asset('js/jquery.magnific-popup-init.js') }}"></script>
<script src="https://js.pusher.com/5.0/pusher.min.js"></script>

<script>

    $('body').on('click', '.active-timer-modal', function(){
        var url = '{{ route('member.all-time-logs.show-active-timer')}}';
        $('#modelHeading').html('Active Timer');
        $.ajaxModal('#projectTimerModal',url);
    });

    $('.datepicker, #start-date, #end-date').on('click', function(e) {
        e.preventDefault();
        $(this).attr("autocomplete", "off");
    });

    function updateOnesignalPlayerId(userId) {
        $.easyAjax({
            url: '{{ route("member.profile.updateOneSignalId") }}',
            type: 'POST',
            data:{'userId':userId, '_token':'{{ csrf_token() }}'},
            success: function (response) {
            }
        })
    }

    function addOrEditStickyNote(id)
    {
        var url = '';
        var method = 'POST';
        if(id === undefined || id == "" || id == null) {
            url =  '{{ route('member.sticky-note.store') }}'
        } else{

            url = "{{ route('member.sticky-note.update',':id') }}";
            url = url.replace(':id', id);
            var stickyID = $('#stickyID').val();
            method = 'PUT'
        }

        var noteText = $('#notetext').val();
        var stickyColor = $('#stickyColor').val();
        $.easyAjax({
            url: url,
            container: '#responsive-modal',
            type: method,
            data:{'notetext':noteText,'stickyColor':stickyColor,'_token':'{{ csrf_token() }}'},
            success: function (response) {
                $("#responsive-modal").modal('hide');
                getNoteData();
            }
        })
    }

    // FOR SHOWING FEEDBACK DETAIL IN MODEL
    function showCreateNoteModal(){
        var url = '{{ route('member.sticky-note.create') }}';

        $("#responsive-modal").removeData('bs.modal').modal({
            remote: url,
            show: true
        });

        $('#responsive-modal').on('hidden.bs.modal', function () {
            $(this).find('.modal-body').html('Loading...');
            $(this).data('bs.modal', null);
        });

        return false;
    }

    // FOR SHOWING FEEDBACK DETAIL IN MODEL
    function showEditNoteModal(id){
        var url = '{{ route('member.sticky-note.edit',':id') }}';
        url  = url.replace(':id',id);

        $("#responsive-modal").removeData('bs.modal').modal({
            remote: url,
            show: true
        });

        $('#responsive-modal').on('hidden.bs.modal', function () {
            $(this).find('.modal-body').html('Loading...');
            $(this).data('bs.modal', null);
        });

        return false;
    }

    function selectColor(id){
        $('.icolors li.active ').removeClass('active');
        $('#'+id).addClass('active');
        $('#stickyColor').val(id);

    }


    function deleteSticky(id){

        swal({
            title: "Are you sure?",
            text: "You will not be able to recover the deleted Sticky Note!",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes, delete it!",
            cancelButtonText: "No, cancel please!",
            closeOnConfirm: true,
            closeOnCancel: true
        }, function(isConfirm){
            if (isConfirm) {

                var url = "{{ route('member.sticky-note.destroy',':id') }}";
                url = url.replace(':id', id);

                var token = "{{ csrf_token() }}";

                $.easyAjax({
                    type: 'POST',
                    url: url,
                    data: {'_token': token, '_method': 'DELETE'},
                    success: function (response) {
                        $('#stickyBox_'+id).hide('slow');
                        $("#responsive-modal").modal('hide');
                        getNoteData();
                    }
                });
            }
        });
    }


    //getting all chat data according to user
    function getNoteData(){

        var url = "{{ route('member.sticky-note.index') }}";

        $.easyAjax({
            type: 'GET',
            url: url,
            messagePosition: '',
            data:  {},
            container: ".noteBox",
            error: function (response) {

                //set notes in box
                $('#sticky-note-list').html(response.responseText);
            }
        });
    }

    //    sticky notes script
    var stickyNoteOpen = $('#open-sticky-bar');
    var stickyNoteClose = $('#close-sticky-bar');
    var stickyNotes = $('#footer-sticky-notes');
    var viewportHeight = Math.max(document.documentElement.clientHeight, window.innerHeight || 0);
    var stickyNoteHeaderHeight = stickyNotes.height();

    $('#sticky-note-list').css('max-height', viewportHeight-150);

    stickyNoteOpen.click(function () {
        $('#sticky-note-list').toggle(function () {
            $(this).animate({
                height: (viewportHeight-150)
            })
        });
        stickyNoteClose.toggle();
        stickyNoteOpen.toggle();
    })

    stickyNoteClose.click(function () {
        $('#sticky-note-list').toggle(function () {
            $(this).animate({
                height: 0
            })
        });
        stickyNoteOpen.toggle();
        stickyNoteClose.toggle();
    })

</script>

<script>
    $('body').on('click', '.timer-modal', function(){
        var url = '{{ route('member.time-log.create')}}';
        $('#modelHeading').html('Start Timer For a Project');
        $.ajaxModal('#projectTimerModal',url);
    });

    $('body').on('click', '.stop-timer-modal', function(){
        var url = '{{ route('member.time-log.show', ':id')}}';
        url = url.replace(':id', $(this).data('timer-id'));

        $('#modelHeading').html('Stop Timer');
        $.ajaxModal('#projectTimerModal',url);
    });

    $('.mark-notification-read').click(function () {
        var token = '{{ csrf_token() }}';
        $.easyAjax({
            type: 'POST',
            url: '{{ route("mark-notification-read") }}',
            data: {'_token': token},
            success: function (data) {
                if(data.status == 'success'){
                    $('.top-notifications').remove();
                    $('.top-notification-count').html('0');
                    $('#top-notification-dropdown .notify').remove();
                    $('.notify').remove()
                }
            }
        });

    });

    $('.show-all-notifications').click(function () {
        var url = '{{ route('show-all-member-notifications')}}';
        $('#modelHeading').html('View Unread Notifications');
        $.ajaxModal('#projectTimerModal',url);
    });

    $('#sticky-note-toggle').click(function () {
        $('#footer-sticky-notes').toggle();
        $('#sticky-note-toggle').hide();
    })

    $('body').on('click', '.right-side-toggle', function () {
        $(".right-sidebar").slideDown(50).removeClass("shw-rside");
    })
</script>


@if ($pusherSettings->status)
<script>

// Enable pusher logging - don't include this in production
// Pusher.logToConsole = true;

var pusher = new Pusher("{{ $pusherSettings->pusher_app_key }}", {
  cluster: "{{ $pusherSettings->pusher_cluster }}",
  forceTLS: "{{ $pusherSettings->force_tls }}"
});

</script>

@yield('pusher-event')

@endif


@stack('footer-script')

</body>
</html>
