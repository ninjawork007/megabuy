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
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/frontend/jquery.circliful.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/owl_carousel/css/owl.carousel.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/owl_carousel/css/owl.theme.css') }}">
    <!--end of page level css-->
@stop

{{-- slider --}}
@section('top')

@stop

{{-- content --}}
@section('content')
    <div class="container">
        <div class="row ml-0 mr-0">
            <span class="font-24 color-black bold">MegaBuy Selling Sold</span>
        </div>
        <div class="row ml-0 mr-0">
            @include("front/front_seller/top_menu")
            <div class="tab-content mt-10">
                <div class="tab-pane fade active in" id="activity">
                    @include("front/front_seller/left_menu")
                    <div class="col-md-10 my-ebay-content col-xs-12 pl-0 pr-0 active">
                        <form id = "searchForm" action = "{{url("front/my/seller/activity_selling_drafts")}}">
                            <div class="row ml-0 mr-0 mt-20">
                                <div class="col-md-8">

                                    <input type = "hidden" name = "page" value = "{{$pageParam['pageNo']}}"/>
                                    <input class = "form-control" placeholder="search" name = "search" value ="{{$search}}"/>

                                </div>
                                <div class="col-md-4">
                                    <button class="visit-btn"  >Search</button>
                                </div>
                            </div>
                        </form>
                        @foreach($list as $item)
                        <div class="row ml-0 mr-0 mt-20">
                            <div class="col-md-6 pl-0 display-flex">
                                <div class="display-inline top-align">
                                    <input type="checkbox" class="checkbox-inline mt-0 hidden">
                                </div>
                                <div class="display-inline ml-20">
                                    <img class="border-rect size-150 cursor" src="{{correctImgPath($item['img'])}}">
                                </div>
                                <div class="display-inline top-align ml-20">
                                    <p class="color-blue font-bold font-16 mb-0"><div class = "inline-block color-blue font-16 font-bold text-right" style = "width:60px;">Title : </div> <span class = "font-16 color-blue font-bold">{{$item['title']}}</span></p>
                                    <p class="font-12 mb-0"><div class = "inline-block text-right" style = "width:60px;">Category : </div> {{str_limit($item['category']['path'],30)}} </p>
                                    <span class="color-blue font-12"><div class = "inline-block text-right" style = "width:60px;">Subtitle : </div>{{$item['subtitle']}}</span>
                                    <p class="font-12 mb-0"><div class = "inline-block text-right" style = "width:60px;">Remains :</div>{{$item['quantity']}}</p>
                                    @if($item->getVariantCount($item['id']) > 0)
                                        <p class="font-10 mb-0"><div class = "inline-block text-right" style = "width:60px;">Variants :</div> {{$item->getVariantCount($item['id'])}}</p>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6 text-right">
                                <div class="display-inline ml-10 top-align">
                                    <p class="color-black font-12 mb-0 text-right">{{$item['brand']['title']}}</p>
                                    <p class="font-12 mb-0 text-right">{{getProductConditionTitle($item['condition'])}}</p>
                                </div>
                                <div class="display-inline ml-20 top-align">
                                    <p class="font-14 bold color-green mb-0 text-right">$ {{$item['price']}}</p>
                                    <p class="font-10 text-right">Buy It Now or Best offer</p>
                                    <p class="font-10 mb-0 text-right">Free shipping</p>
                                </div>
                                <div class="display-inline ml-20 top-align">
                                    <div class="row ml-0 mr-0">
                                        <button class="visit-btn" type = "button" data-id = "{{$item['id']}}" onclick = "setOnlineProduct(this)">Online It Now</button>
                                    </div>
                                    <div class="row ml-0 mr-0 mt-10">
                                        <a href = "{{url("front/sell?id=")}}{{$item['id']}}" class="font-15 color-blue">Edit item</a>
                                        | <a href = "javascript:void(0);" data-id = "{{$item['id']}}" onclick = "deleteProduct(this)" class="font-15  color-blue">Delete item</a>
                                    </div>
                                    <div class="row ml-0 mr-0 hidden">
                                        <select class="font-10 border-none pr-0">
                                            <option>More actions</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                        <div class = "text-right" style = "margin-top:10px;">
                            @include("layouts/pagination")
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <form method="POST" action="{{url('front/product/ajaxUpdateProductInfo')}}">
        <input type="hidden" name="_token" value="{{csrf_token()}}" />
        <input type="hidden" name="id" id="product_id" />
        <input type="hidden" name="state" id="state" />
        <button type = "submit" class="hidden" id="online" style = "width:90%; height:40px; line-height:40px;"><i class = "fa fa-lock"></i> </button>
    </form>
    <script>
        function setOnlineProduct(obj){
            confirmMsg("Do you really want to online this product? You will have to pay AUD 1 per item.", function(){
                setTimeout(function(){
                    document.getElementById('product_id').value = $(obj).attr("data-id");
                    document.getElementById('state').value = 1;
                    document.getElementById('online').click();
                    console.log("ok");
                    // var url = "{{url("front/product/ajaxUpdateProductInfo")}}";
                    // var param = new Object();
                    // param._token = _token;
                    // param.id = $(obj).attr("data-id");
                    // param.state = 1;
                    // $.post(url, param, function(data){
                    //     if(data.status == "1"){
                    //         // successMsg(data.msg, function(){
                    //         //     window.location.reload();
                    //         // });
                    //     }else{
                    //         errorMsg(data.msg);
                    //     }
                    // }, "json");
                }, 1000);
            })
        }

        function deleteProduct(obj){
            confirmMsg("Are you sure to delete this product?", function(){
                setTimeout(function(){
                    var url = "{{url("front/product/ajaxDeleteProductInfo")}}";
                    var param = new Object();
                    param._token = _token;
                    param.id = $(obj).attr("data-id");
                    $.post(url, param, function(data){
                        if(data.status == "1"){
                            successMsg(data.msg, function(){
                                window.location.reload();
                            });
                        }else{
                            errorMsg(data.msg);
                        }
                    }, "json");
                }, 1000);
            })
        }
    </script>
@stop

@section('footer_scripts')
    <!-- page level js starts-->
    <script type="text/javascript" src="{{ asset('assets/js/bootstrap.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/frontend/jquery.circliful.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/vendors/wow/js/wow.min.js') }}" ></script>
    <script type="text/javascript" src="{{ asset('assets/vendors/owl_carousel/js/owl.carousel.min.js') }}"></script>
    <script type="text/javascript" src="{{asset('assets/slick/slick.js')}}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/frontend/carousel.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/frontend/index.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/common.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/frontend/elevatezoom.js') }}"></script>

@stop