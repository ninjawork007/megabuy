@extends('layouts/default')

{{-- Page title --}}
@section('title')
    Home
    @parent
@stop
{{-- page level styles --}}
@section('header_styles')
    <!--page level css starts-->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/frontend/tabbular.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/animate/animate.min.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/jasny-bootstrap/css/jasny-bootstrap.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/iCheck/css/minimal/blue.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/frontend/jquery.circliful.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/owl_carousel/css/owl.carousel.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/owl_carousel/css/owl.theme.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/select2/css/select2.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/select2/css/select2-bootstrap.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/datetimepicker/css/bootstrap-datetimepicker.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/frontend/user_account.css') }}">
    <!--end of page level css-->
@stop

{{-- slider --}}
@section('top')

@stop

{{-- content --}}
@section('content')
    <div class="container">
        <div class="row ml-0 mr-0">
            <span class="font-24 color-black bold">MegaBuy Selling Overview</span>
        </div>
        <div class="row ml-0 mr-0">
            @include("front/front_seller/top_menu")
            <div class="tab-content mt-10">
                <div class="tab-pane fade in active" id="messages">
                    <div class="col-md-4 col-xs-12 mt-20 border-rect pt-20" id="memo_user_list">
                        <div class="row ml-0 mr-0 border-bottom memo-saerch-wrapper pb-10 mb-10 memo-search">
                            <form method="post" action="{{url('/front/my/messages')}}">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <input type="text" name="search_key" id="memo_search_key" value="{{$search_key}}">
                                <input type="submit" class="memo-search-btn" value="Search">
                            </form>
                        </div>
                        <div class="memo-search-wrapper">
                            @foreach($userList as $user)
                                <div class="row ml-0 mr-0 memo-user-wrapper border-bottom"
                                     @if($user['sender_id'] == $myId)
                                     senderId = "{{$user['receiver_id']}}"
                                     @else
                                     senderId = "{{$user['sender_id']}}"
                                     @endif
                                >
                                    <img src="{{ asset('uploads/users/'.$user['pic']) }}" onerror="noExitImg(this)" class="memo-user-avatar">
                                    <div class="display-inline memo-user-detail">
                                        <p class="mb-0 font-16 bold">{{$user['first_name']}}
                                            (<span
                                                @if($user['sender_id'] == $myId)
                                                id="memo_count_{{$user['receiver_id']}}"
                                                @else
                                                id="memo_count_{{$user['sender_id']}}"
                                                @endif
                                            >{{$user['memo_count']}}</span>)
                                        </p>
                                        <p class="mb-0 font-14"><span class="pull-right">{{$user['last_send_time']}}</span></p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="col-md-8 col-xs-12 mt-20 border-rect" id="memo-content">
                        <div class="message-content-wrapper">
                        </div>
                        <div class="type-wrapper row">
                            <input type="hidden" name="receiver_id" value="" id="receiver_id">
                            <input type="text" class="type-input" name="content" placeholder="Type message" id="message">
                            <input class="btn send-btn" type="button" value="Send" onclick="sendMessage()">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="clone-wrapper hidden">
        <div class="row ml-0 mr-0 msg-left-wrapper">
            <div class="message-left">
                <div class="message-content">
                </div>
            </div>
        </div>
        <div class="row ml-0 mr-0 msg-right-wrapper">
            <div class="message-right">
                <div class="message-content">
                </div>
            </div>
        </div>
    </div>
@stop

@section('footer_scripts')
    <script type="text/javascript" src="{{ asset('assets/vendors/moment/js/moment.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/frontend/jquery.circliful.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/vendors/wow/js/wow.min.js') }}" ></script>
    <script type="text/javascript" src="{{ asset('assets/vendors/owl_carousel/js/owl.carousel.min.js') }}"></script>
    <script type="text/javascript" src="{{asset('assets/slick/slick.js')}}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/frontend/carousel.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/frontend/index.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/vendors/iCheck/js/icheck.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/frontend/elevatezoom.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/vendors/datetimepicker/js/bootstrap-datetimepicker.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/frontend/user_account.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/common.js') }}"></script>
    <script>
        $(".memo-user-wrapper").on('click', function(){
            $(".memo-user-wrapper").removeClass("active");
            $(this).addClass("active");
            $("#receiver_id").val($(this).attr("senderId"));
            $(".message-content-wrapper").html("");
            getMessage($(this).attr("senderId"));
        });

        function getMessage(id) {
            param = Object();
            param._token = _token;
            param.id = id;
            $.post("{{url("/front/my/getMessage")}}", param, function(data){
                if(data.status == "1") {
                    var msgList = data.msgList;
                    for(var i = 0; i < msgList.length; i++) {
                        if(msgList[i].sender_id == data.myId) {
                            $(".clone-wrapper .msg-right-wrapper .message-content").html(msgList[i].content);
                            var rightMsg = $(".clone-wrapper .msg-right-wrapper").clone();
                            $(".message-content-wrapper").append(rightMsg);
                        } else {
                            $(".clone-wrapper .msg-left-wrapper .message-content").html(msgList[i].content);
                            var leftMsg = $(".clone-wrapper .msg-left-wrapper").clone();
                            $(".message-content-wrapper").append(leftMsg);
                        }
                    }
                } else {
                }
            }, "json");
        }

        function sendMessage(){
            var content = $("#message").val();
            var param = Object();
            param._token = _token;
            param.receiver_id = $("#receiver_id").val();
            param.content = content;
            $.post("{{url("/front/my/sendMessage")}}", param, function(data){
                if(data.status == "1"){
                    $(".clone-wrapper .msg-right-wrapper .message-content").html(content);
                    var rightMsg = $(".clone-wrapper .msg-right-wrapper").clone();
                    $(".message-content-wrapper").append(rightMsg);
                    $("#message").val("");
                    var prevCount = $("#memo_count_" + param.receiver_id).html() * 1;
                    $("#memo_count_" + param.receiver_id).html(prevCount + 1);
                }else{
                    // error
                }
            }, "json");
            return true;
        }
    </script>
@stop