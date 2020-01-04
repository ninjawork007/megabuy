<select name="brand_id" >
    @foreach($brandList as $item)
        <option value = "{{$item['id']}}"  @if(isset($product) && $product['brand_id'] == $item['id'])  selected @endif  img = "{{correctImgPath($item['log_img'])}}">{{$item['title']}}</option>
    @endforeach
</select>
<a href = "javascript:void(0)" onclick = "addBrandModal()" style = "float: right;margin-top: 10px; margin-bottom: 10px;" >Add</a>
<script>
    function formatState (state) {
        if (!state.id) { return state.text; }
        var $state = $(
            '<span><img src="' + $(state.element).attr("img") +'" class="img-flag" width="20px" height="20px" onerror="noExitImg()" /> ' + state.text + '</span>'
        );
        return $state;


    }
    $(function(){
        $("select[name='brand_id']").select2({
            templateResult: formatState,
            templateSelection: formatState,
            placeholder: "select",
            theme:"bootstrap"
        });
    })

</script>
