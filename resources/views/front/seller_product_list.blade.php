@extends('layouts/default')

{{-- Page title --}}
@section('title')
    Deals
    @parent
@stop
{{-- page level styles --}}
@section('header_styles')
    <!--page level css starts-->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/globaldeals.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/frontend/tabbular.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/animate/animate.min.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/frontend/jquery.circliful.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/owl_carousel/css/owl.carousel.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/owl_carousel/css/owl.theme.css') }}">
    <!--end of page level css-->
@stop

{{-- slider --}}
@section('top')

    <!--Carousel Start -->

    <!-- //Carousel End -->
@stop

{{-- content --}}
@section('content')
    <div class="container">
        <div class="navigation-desktop">
            <h1><a href="javascript:void(0);">{{$sellerInfo->getFullNameAttribute()}}'s Products</a></h1>
        </div>
        <div class="sections-container">
            <div class="ebayui-dne-featured-card ebayui-dne-featured-with-padding">
                <div class="ebayui-dne-item-featured-card ebayui-dne-item-featured-card">
                    <div class="row">
                        @foreach($productList as $product)
                            <div class="col" onclick="location.href='{{url("front/product/".$product["id"])}}'">
                                <div class="dne-itemtile dne-itemtile-medium" itemscope="itemscope"
                                     data-listing-id="254352188761" itemtype="https://schema.org/Product">
                                    <div class="dne-itemtile-imagewrapper" aria-hidden="true">
                                        <div class="slashui-image-cntr">
                                            <img src="
                                            @if($product['img'] != "")
                                            {{asset($product['img'])}}
                                            @else
                                            {{asset('assets/images/default_no_image.jpg')}}
                                            @endif">
                                        </div>
                                    </div>
                                    <div class="dne-itemtile-detail" role="text">
                                        <h3 class="dne-itemtile-title ellipse-2">
                                            <a href=""><span class="ebayui-ellipsis-2">{{$product['title']}}</span></a>
                                        </h3>
                                        <div class="dne-itemtile-price">
                                            <span class="first">AUD ${{$product['price']}}</span>
                                        </div>
                                        <div class="dne-itemtile-original-price">
                                        <span>
                                            <span class="itemtile-price-strikethrough">{{$product['retail_price'] !=0 ? 'AUD $'.$product['retail_price']:''}} </span>
                                            <span class="aria-hide">Previous price: US $29.99</span>
                                            <span class="itemtile-price-bold">@if($product['retail_price'] > $product['price'])| {{floor(($product['retail_price'] - $product['price'])/$product['retail_price']*100)}}% Off @endif</span>
                                        </span>
                                        </div>
                                        @foreach ($brands as $brand)
                                            @if($brand['id'] == $product['brand_id'])
                                            <a href="{{url('front/product_list/').'/'.$brand['id']}}">
                                                <span>
                                                    {!! $product->getBrandHtml() !!}
                                                </span>
                                            </a>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

{{-- footer scripts --}}
@section('footer_scripts')
    <!-- page level js starts-->
    <script type="text/javascript" src="{{ asset('assets/js/frontend/jquery.circliful.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/vendors/wow/js/wow.min.js') }}" ></script>
    <script type="text/javascript" src="{{ asset('assets/vendors/owl_carousel/js/owl.carousel.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/frontend/carousel.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/frontend/index.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/common.js') }}"></script>
    <!--page level js ends-->
@stop