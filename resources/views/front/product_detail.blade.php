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
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/slick/slick.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/slick/slick-theme.css') }}">
    <!--end of page level css-->
@stop

{{-- slider --}}
@section('top')

@stop

{{-- content --}}
@section('content')
    <div class="container">
        <div class="row ml-0 mr-0">
            <ol class="breadcrumb breadcrumb-path">
                <li class="hidden-xs"><a href = "javascript:void(0);" href1="/front/index">MegaBuy</a></li>
                @foreach($parentPath as $parent)
                    <li class="hidden-xs"><i class="fa fa-chevron-right" style="font-size: 10px;"></i><a href = "javascript:void(0);" href1="{{url("/front/category/")}}{{$parent['id']}}/0">{{$parent["title"]}}</a></li>
                @endforeach
            </ol>
            <div class="pull-right hidden">
                <a class="color-blue font-14 bold" href="javascript:void(0);">Share</a>
            </div>
        </div>
        <div class="row ml-0 mr-0">
            <p class="product-title">
                {{$productInfo['title']}}
            </p>
        </div>
        @if($productInfo['seller'])
        <div class="row ml-0 mr-0">
            <div class="product-rate font-14">
                <?php $avg_mark = $productInfo['seller']->getSellerProductAvgMark($productInfo['id']);?>
                @for($i=1; $i<=5; $i++)
                <i class="fa <?php if(number_format($avg_mark*1) >= $i ) echo "fa-star" ; else echo "fa-star-o"; ?> text-brown"></i>
                @endfor
                <a class="color-blue pl-10">{{$countRating}} Product Ratings</a>
                <span class="pl-10 color-blue hidden">|</span>
                <a class="color-blue pl-10 hidden">About this product</a>
            </div>
        </div>
        @endif
        <div class="row ml-0 mr-0 mt-10 product-brand">
            <div class="tabbable-line">
                <ul class="nav nav-tabs hidden">
                    <li class="active brand-tab">
                        <a href="#brand1" class="bg-white brand-link" data-toggle="tab"> Your Pick AUD ${{$productInfo->getProductPagePrice()}}</a>
                    </li>
                    <li class="brand-tab hidden">
                        <a href="#brand2" class="bg-white brand-link" data-toggle="tab"> Brand new RMB 838.46 </a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane active" id="brand1">
                        <div class="col-md-5 col-xs-12">
                            <div class="product-small-image-wrapper" id="gal1">
                                <div class="product-small-image">
                                    @if($productInfo['img'] == "")
                                        <a href="javascript:void(0);" data-image="{{asset("assets/images/default_no_image.jpg")}}" data-zoom-image="{{asset("assets/images/default_no_image.jpg")}}">
                                            <img id="img_01" src="{{asset("assets/images/default_no_image.jpg")}}" class="img-responsive"  onerror = "noExitImg(this)"/>
                                        </a>
                                    @else
                                        <a href="javascript:void(0);" data-image="{{asset($productInfo['img'])}}" data-zoom-image="{{asset($productInfo['img'])}}">
                                            <img id="img_01" src="{{asset($productInfo['img'])}}" class="img-responsive" />
                                        </a>
                                    @endif
                                </div>
                                @foreach($productImgInfo as $productImg)
                                    <div class="product-small-image">
                                        @if($productImg['img'] == "")
                                            <a href="javascript:void(0);" onclick="" data-image="{{asset("assets/images/default_no_image.jpg")}}" data-zoom-image="{{asset("assets/images/default_no_image.jpg")}}">
                                                <img id="img_02" src="{{asset("assets/images/default_no_image.jpg")}}" class="img-responsive" onerror = "noExitImg(this)" />
                                            </a>
                                        @else
                                            <a href="javascript:void(0);" onclick="" data-image="{{asset($productImg['img'])}}" data-zoom-image="{{asset($productImg['img'])}}">
                                                <img id="img_02" src="{{asset($productImg['img'])}}" class="img-responsive"  onerror = "noExitImg(this)" />
                                            </a>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                            <div class="product_wrapper product-image-wrapper">
                                <div class="product-hint hidden">11 watches</div>
                                @if($productInfo['img'] == "")
                                    <img id="zoom_09" src="{{asset("assets/images/default-product.png")}}" data-zoom-image="{{asset("assets/images/default-product.png")}}" class="img-responsive " onerror = "noExitImg(this)" />
                                @else
                                    <img id="zoom_09" src="{{asset($productInfo['img'])}}" data-zoom-image="{{asset($productInfo['img'])}}" class="img-responsive " onerror = "noExitImg(this)" />
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4 col-xs-12 mt-10">
                            <div class="row ml-0 mr-0"><span class="font-16 bold">Your Pick</span></div>
                            <div class="row ml-0 mr-0"><span class="font-30 bold color-black" id="product-price">{{$productInfo->getProductPagePrice()}}</span> @if($productInfo['retail_price']*1 !=0)<del style="margin-left: 30px;color: darkred;" class="font-16">$ {{$productInfo['retail_price']}}</del>@endif</div>
                            <div class="row ml-0 mr-0"><span class="font-16 bold ">Shipped: {{$productInfo['sell_count']}}</span></div>
                            <div class="row ml-0 mr-0"><span class="font-16 bold ">Remain: {{$productInfo['quantity']-$productInfo['sell_count']}}</span></div>
                            <div class="row ml-0 mr-0"><span class="font-14 bold hidden">US $119.00</span></div><br>
                            <div class="row ml-0 mr-0"><span class="font-14 bold hidden">Get it by Thu, Sep 19-Tue, Oct 29 from Huntington, New York</span></div>
                            <ul class="product-condition-wrapper " >
                                <li style = "list-style: none;">
                                    <a href="{{url('front/product_list').'/'.$productInfo['brand_id']}}">{!! $productInfo->getBrandHtml() !!}</a>
                                </li>
                                <br/>
                                <li @if($productInfo["condition"] == 0) style = "list-style: none;" @endif>
                                    <span class="font-12 bold">
                                        @if($productInfo["condition"] == 1) Condition: New @endif
                                        @if($productInfo["condition"] == 2) Condition: Used @endif
                                    </span>
                                </li>
                                <li @if($returnCondition == '') style = "list-style: none;" @endif>
                                    <span class="font-12 bold">
                                        {{$returnCondition}}
                                    </span>
                                </li>
                                <li class = "hidden"><span class="font-12 bold" >No returns, but backed by </span><a href="#"><span class="color-blue bold">MegaBuy Moeny back guarantee</span></a></li>
                            </ul>
                            <div class="row ml-0 mr-0 hidden">
                                <span class="font-14 bold font-italic" style="word-break:break-all;">
                                    "{{$commentInfo}}"
                                </span>
                            </div>
                            <div class="row ml-0 mr-0 variant-wrapper">
                                @foreach($productVariant as $key => $variant)
                                    <button price="{{$variant['variantInfo']['price']}}" data-id="{{$variant['variantInfo']['id']}}" class="variant-btn mt-10
                                        @if($key == 0)
                                            active
                                        @endif">{{$variant['attrVal']}}</button>
                                @endforeach
                            </div>
                        </div>
                        <div class="col-md-3 col-xs-12 mt-10 pr-0">
                            <form id="cart_form">
                                <input type="text" class="hidden" name="_token" value="{{ csrf_token() }}">
                                <input type="hidden" name="product_id" value="{{$productInfo['id']}}">
                                <input type="hidden" name="sku_id"
                                       @if(isset($productVariant[0])))
                                        value="{{$productVariant[0]['variantInfo']['id']}}"
                                       @else
                                        value="0"
                                       @endif
                                       id="sku_id">
                                <input type="hidden" name="supplier_id" value="{{$productInfo['seller_id']}}">
                                <input type="hidden" name="immeditaly_type" value="0" id="immeditaly_type">
                                <div class="row ml-0 mr-0"><span class="font-16 bold pull-right">Quantity</span></div>
                                <input type="number" class="form-control mt-10" name="product_count" id="product_count" value = "1">
                                <input type="button" class="product-act-btn mt-20" onclick = "onBuyProduct()" value="Buy It Now">
                                <input type="button" class="product-act-btn mt-20" value="Add to Cart" onclick="onAddCart()">
                                <div class="row ml-0 mr-0 mt-20 hidden"><span class="font-14 bold">Sold by</span></div>
                                <div class="row ml-0 mr-0 hidden"><span class="font-14 bold color-blue">{{$userInfo['full_name']}} ({{$orderCount}})</span></div>
                                <div class="row ml-0 mr-0 hidden"><span class="font-14 bold">100.0% Positive feedback</span></div>
                                <div class="row ml-0 mr-0 hidden"><a href="#"><span class="font-14 bold color-blue">Contact seller</span></a></div>
                            </form>
                        </div>
                    </div>
                    <div class="tab-pane active" id="brand2">

                    </div>
                </div>
            </div>
        </div>
        <div class="standard-wrapper mt-20">
            <div class="row ml-0 mr-0 pb-10">
                <span class="font-18 color-black bold">Similar Sponsored Items</span>
            </div>
            <div class="row ml-0 mr-0 mt-10">
                <div id = "relationSlick" style = "height:260px;">
                    @foreach($productInfo->getSimilarProductList() as $item)
                        <div class="float-left pr-10">
                            <a href = "{{url("front/product/".$item['id'])}}" class = "none-underline"  >
                                <div class = "row ml-0 mr-0">
                                    <img src="{{correctImgProductPath($item['img'])}}" class="product-img-md">
                                </div>
                                <div class = "row ml-10 mr-0 font-bold font-15 cursor">
                                    <a style = "word-break: break-all;" href = "{{url("front/product/".$item['id'])}}">{{str_limit($item['title'],30)}}</a>
                                </div>
                                <div class = "row ml-10 mr-0 font-bold font-13">
                                    <span>{{$item['subtitle']}}</span>
                                </div>
                                <div class = "row ml-10 mr-0 font-bold font-13">
                                    @if($item->getBrandTitle() != '')
                                    <span>brand:{{$item->getBrandTitle()}}</span>
                                    @endif
                                </div>
                                <div class = "row ml-10 mr-0 font-bold font-13">
                                    <span class = "color-black">${{$item['price']}}</span>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="row ml-0 mr-0 mt-10 product-brand">
            <div class="tabbable-line">
                <ul class="nav nav-tabs">
                    <li class="active">
                        <a href="#description-tab" class="bg-white color-blue font-16" data-toggle="tab"> Description </a>
                    </li>
                    <li class="color-blue">
                        <a href="#shipping-tab" class="bg-white color-blue font-16" data-toggle="tab"> Attributes </a>
                    </li>
                    <li class="color-blue">
                        <a href="#review-tab" class="bg-white color-blue font-16" data-toggle="tab">Review</a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane active" id="description-tab">
                        {!!  $productDescription["description"] !!}
                    </div>
                    <div class="tab-pane" id="shipping-tab">
                        <div class="row ml-0 mr-0 hidden">
                            {{$commentInfo}}
                        </div>
                        <div class="row ml-0 mr-0">
                            <ul class="hidden-xs detail-content" style = "border-bottom:1px solid transparent;">
                                {!! $productInfo->getCommonPropertierHtml() !!}
                            </ul>
                        </div>
                    </div>
                    <div class="tab-pane" id="review-tab">
                        <div class="row ml-0 mr-0">
                           <div id = "review-wrpper">

                           </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <input type = "hidden" id = "remain_count" value = "{{$productInfo['quantity']-$productInfo['sell_count']}}"/>
@stop


@section('footer_scripts')
    <!-- page level js starts-->
    {{--<script type="text/javascript" src="{{ asset('assets/js/jquery.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/bootstrap.min.js') }}"></script>--}}
    <script type="text/javascript" src="{{ asset('assets/js/frontend/jquery.circliful.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/vendors/wow/js/wow.min.js') }}" ></script>
    <script type="text/javascript" src="{{ asset('assets/vendors/owl_carousel/js/owl.carousel.min.js') }}"></script>
    <script type="text/javascript" src="{{asset('assets/slick/slick.js')}}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/frontend/carousel.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/frontend/index.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/common.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/frontend/elevatezoom.js') }}"></script>
    <!--page level js ends-->
    <script>
        $(document).ready(function(){
            var width = $(window).width();
            if(width > 600){
                $("#zoom_09").elevateZoom();
                //initiate the plugin and pass the id of the div containing gallery images
                $("#zoom_09").elevateZoom({gallery:'gal1', cursor: 'pointer', galleryActiveClass: 'active', imageCrossfade: true});
                //pass the images to Fancybox
                $("#zoom_09").bind("click", function(e) {
                    var ez =   $('#zoom_09').data('elevateZoom');
                    return false;
                });
            }


            searchOrderReviewList(1);
        });

        $(function(){
            $('#relationSlick')
                .slick({
                    autoplay: true,
                    autoplaySpeed: 1000,
                    dots: false,
                    arrows: false,
                    slidesToShow: 8,
                    slidesToScroll: 2,
                    infinite:false,
                    listHeight:150,
                });
        })

        function searchOrderReviewList(page){
            var param = new Object();
            param._token = _token;
            param.page = page;
            param.seller_id = "{{$productInfo['seller_id']}}";
            param.product_id = "{{$productInfo['id']}}";
            var url = "{{url("front/product/ajax_order_review_list")}}";
            $.post(url, param, function(html){
               $("#review-wrpper").html(html);
            });
        }

        $("input[name='product_count']").change(function(){
            var product_count = $("input[name='product_count']").val();
            product_count = parseInt(product_count);
            if(isNaN(product_count)){
                $("input[name='product_count']").val("1");
                return;
            }
            if(product_count <= 0){
                $("input[name='product_count']").val("1");
                return;
            }
        })
        $(".variant-btn").on('click', function(){
            $(".variant-btn").removeClass("active");
            $(this).addClass("active");
            var price = $(this).attr("price");
            $("#product-price").html("$ " + price);
            var data_id = $(this).attr("data-id");
            $("#sku_id").val(data_id);
        });

        function onAddCart(){
            if($("#product_count").val() == ""){
                errorMsg("Please input product count");
                return;
            }
            var remain_count = $("#remain_count").val();
            remain_count = parseInt(remain_count);
            if(isNaN(remain_count)){
                errorMsg("Product remain quantity undefined!");
                return;
            }
            var product_count = $("#product_count").val();
            product_count = parseInt(product_count);
            if(isNaN(product_count)){
                errorMsg("quantity only number!");
                return;
            }

            if(product_count > remain_count){
                errorMsg("quantity number is small then product remain number!");
                return;
            }

            $("#immeditaly_type").val(0);
            var param = $("#cart_form").serialize();
            $.post("{{url("front/saveCart")}}", param, function(data){
                if(data.status == "1"){
                    successMsg("The product is added to cart successfully", function(){
                        window.location.reload();
                    });
                }else{
                    window.location.href = "{{url("/login")}}";
                }

            }, "json");
        }
        function onBuyProduct(){
            if($("#product_count").val() == ""){
                errorMsg("Please input product count");
                return;
            }
            var remain_count = $("#remain_count").val();
            remain_count = parseInt(remain_count);
            if(isNaN(remain_count)){
                errorMsg("Product remain quantity undefined!");
                return;
            }
            var product_count = $("#product_count").val();
            product_count = parseInt(product_count);
            if(isNaN(product_count)){
                errorMsg("quantity only number!");
                return;
            }

            if(product_count > remain_count){
                errorMsg("quantity number is small then product remain number!");
                return;
            }
            $("#immeditaly_type").val(1);
            var param = $("#cart_form").serialize();
            $.post("{{url("front/saveCart")}}", param, function(data){
                if(data.status == "1")
                    window.location.href = "{{url("front/basket/index")}}?immeditaly_type=1";
                else{
                    window.location.href = "{{url("/login")}}";
                }
            }, "json");
        }
    </script>
@stop