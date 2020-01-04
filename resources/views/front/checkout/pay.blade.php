<form method="POST" action="{{url('front/checkout/payunpaid')}}">
    <input type="hidden" name="_token" value="{{csrf_token()}}" />
    <input type="hidden" name="orderIds" id="orderIds"/>
    <input type="hidden" name="trans_id" id="trans_id"/>
    <input type="hidden" name="trans_money" id="trans_money"/>
    <button type = "submit" class="hidden" id = "payforunpaid" style = "width:90%; height:40px; line-height:40px;"></button>
</form>
<script>
    function payOrder(orderIds, payType){
        var param = new Object();
        param._token = _token;
        param.order_ids = orderIds;
        param.trans_type = payType;
        sweetAlert.close();
        setTimeout(function(){
            $.post("{{url("front/checkout/createTransLog")}}", param, function(data){
                if(data.status == "1"){
                    var trans_id = data.transId;
                    var trans_money = data.transMoney;
                    // pay module
                    alert("call pay module trans_id:" + trans_id + "  money:" + trans_money);
                    document.getElementById('orderIds').value = orderIds;
                    document.getElementById('trans_money').value = trans_money;
                    document.getElementById('trans_id').value = trans_id;
                    document.getElementById('payforunpaid').click();
                    // alert("call pay success callback");
                    // // really call back url;
                    // param = new Object();
                    // param._token = _token;
                    // param.transId = trans_id;
                    // $.post("{{url("/pay/success/callback")}}", param, function(){
                    //     window.location.href = "{{url("front/my/activity_purchase_history")}}";
                    // })
                }
            },"json");
        }, 1000);

    }

    $(function(){
        var userInfo = $("input[name='user_info_json']").val();
        userInfo = JSON.parse(userInfo);
        console.log(userInfo);
    })
</script>