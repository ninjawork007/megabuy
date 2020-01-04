<div class="row ml-0 mr-0 border-bottom pb-10">
    <div class="col-md-12 text-right">
        <span>Status</span>
        <select class="select-small color-blue ml-10 ebay-control" id = "unpaidDiscountState" onchange="searchUnpaidOrderList(0)" style="width:200px">
            <option value = "0" @if($discountState == "0") selected @endif>All</option>
            <option value = "1" @if($discountState == "1") selected @endif>Discount Request</option>
            <option value = "3" @if($discountState == "3") selected @endif>Discount Proposal</option>
            <option value = "2" @if($discountState == "2") selected @endif>Discount Passed</option>
            <option value = "4" @if($discountState == "4") selected @endif>Discount Canceled</option>
        </select>
        <span class="ml-10">Sort</span>
        <select class="select-small color-blue ml-10 ebay-control" id = "unpaidSort" onchange="searchUnpaidOrderList(0)" style="width:200px">
            <option value = "1" @if($unpaidSort == "1") selected @endif >Time : Down</option>
            <option value = "2" @if($unpaidSort == "2") selected @endif>Time : Up</option>
        </select>
    </div>
</div>

@foreach($list as $order)
    <div class = "order-wrapper border-bottom">
        <div class="row ml-0 mr-0 mt-20">
            <div class="col-md-6 pl-0 display-flex">
                <div class="display-inline top-align">
                    <input type="checkbox" class="checkbox-inline mt-0 order_check" value = "{{$order['id']}}">
                </div>
                <div class="display-inline top-align ml-20">
                    <p class="color-blue font-14 mb-0">{{$order['order_number']}}</p>
                    <span class="color-blue font-14">{{$countries[$order['receive_region_id']]}}</span>
                    <span class="font-14 mlr-5">|</span>
                    <span class="color-blue font-14">{{$order['receive_address']}}</span>
                    <span class="font-10"></span>
                    <p class="font-12 mb-0">
                        {{$order['receive_user_name']}}: {{$order['receive_phone_num']}}
                    </p>
                    <p class="font-12 color-green mb-0">
                        @if($order['discount_price_req']*1 !=0)
                            Requested: $ {{$order['discount_price_req']}}&nbsp;
                        @endif    
                        @if($order['discount_price_response']*1 !=0)
                            Answer: $ {{$order['discount_price_response']}}
                        @endif
                    </p>
                    @if($order['discount_state']*1 !=0)
                        <p class="font-12 color-red mb-0">
                            Offer Status: {{getOrderDiscountState($order['discount_state'])}}
                        </p>
                    @endif
                </div>
            </div>
            <div class="col-md-6 text-right">
                <div class="display-inline ml-10 top-align">
                    <div class="display-inline ml-10 top-align">
                        <p class="bold color-black font-15 mb-0 text-right">Order time:{{$order['order_time']}} </p>
                        @if($order['state']*1 >=1 )
                        <?php $transLog = $order->transLog();?>
                        <p class="font-12 mb-0 text-right">Pay Time:{{$transLog['pay_time']}}</p>
                        @endif
                        @if($order['state']*1 >=2 )
                            <p class="font-12 mb-0 text-right">Delivery Time: {{$order['delivery_time']}}</p>
                        @endif
                        @if($order['state']*1 >=3 )
                            <p class="font-12 mb-0 text-right">Check Time : {{$order['receive_time']}}</p>
                        @endif
                        @if($order['state']*1 >=4 )
                            <p class="font-12 mb-0 text-right">Complete Time : {{$order['complete_time']}}</p>
                        @endif
                    </div>
                </div>
                <div class="display-inline ml-20 top-align">
                    <p class="font-16 bold color-green mb-0 text-right">
                        @if($order['org_price']*1 !=0)
                            <del>$  {{$order['org_price']}}</del>
                        @endif
                        $ {{$order['total_price']}}
                    </p>
                    <p class="font-10 color-blue mb-0 text-right">
                        Number of Products: {{$order['quantity']}}&nbsp;
                        {{-- @if($order['discount_price_req']*1 !=0)
                            Requested: $ {{$order['discount_price_req']}}
                        @endif     --}}
                    </p>
                    {{-- @if($order['discount_price_req']*1 !=0)
                    <p class="font-10 color-blue mb-0 text-right">Requested: $ {{$order['discount_price_req']}}</p>
                    @endif
                    @if($order['discount_price_response']*1 !=0)
                    <p class="font-10 color-blue mb-0 text-right">Answer: $ {{$order['discount_price_response']}}</p>
                    @endif
                    @if($order['discount_state']*1 !=0)
                    <p class="font-10 color-red mb-0 text-right">{{getOrderDiscountState($order['discount_state'])}}</p>
                    @endif --}}
                </div>
                <div class="display-inline ml-20 top-align">
                    <div class="row ml-0 mr-0">
                        <button class="visit-btn" data-order-id = "{{$order['id']}}" onclick = "discountBuyerProposal(this)">Offer</button>
                    </div>
                    <div class="row ml-0 mr-0">
                        <a class="font-10 color-blue">Cancel</a>
                    </div>
                </div>
            </div>
        </div>
        <p class="font-11 color-green mt-10">If you want to get discount, you may consult the seller by clicking Offer.</p>
        @foreach($order['orderDetailList'] as $order_detail)
        <div class="row ml-0 mr-0">
            <div class="col-md-3 border-rect pt-10 pb-10">
            <div class="row ml-0 mr-0 text-center mt-10"><a href="{{url('front/product').'/'.$order_detail['product']['id']}}"><img src="{{correctImgPath($order_detail['product']['img'])}}" href="{{url('front/product').'/'.$order_detail['product']['id']}}" class="size-150"></a></div>
                <p class="font-12 color-black bold mt-10">{{$order_detail['product']['title']}}</p>
            </div>
            <div class="col-md-9 border-rect similar-item pt-10 pb-10" style="height:219px; overflow: hidden;">
                <p class="font-14 color-black bold"> Count: &nbsp; &nbsp; {{$order_detail['product_count']}} &nbsp; &nbsp; Unit Price: ${{$order_detail['product_price']}} &nbsp; &nbsp; Total Price : $ {{$order_detail['product_total_price']}}</p>
                <p class="font-12 bold">Product Info</p>
                <a href="{{url('front/product_list').'/'.$order_detail['product']['category_id']}}">
                    <p class="font-12 bold" style = "word-break: break-all;">
                        {{$categories[$order_detail['product']['category_id']]['title']}}
                    </p>
                </a>
                <a href="{{url('front/brand').'/'.$order_detail['product']['brand_id']}}">
                    <p class="font-12 bold" style = "word-break: break-all;">
                        @foreach ($brands as $brand)
                            @if($brand['id'] == $order_detail['product']['brand_id'])
                                {{$brand['title']}}
                            @endif
                        @endforeach
                    </p>
                </a>
                <p class="font-12 bold color-black" style = "word-break: break-all;">
                    Sold:{{$order_detail['product']['sell_count']}}
                </p>
                {{-- @if($order_detail['sku_id']*1 != 0)
                    <p class="font-12 bold">Variant</p>
                    <p class="font-12 bold" style = "word-break: break-all;">
                        {{$order_detail->getVariantAttrStr()}}
                    </p>
                @endif --}}
            </div>
        </div>
        @endforeach
    </div>
@endforeach
<div class = "text-right" style = "margin-top:10px;">
    <?php $searchFun = "searchUnpaidOrderList";?>
    @include("layouts/pagination")
</div>