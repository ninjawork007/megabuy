<div id="add-brand-dialog" class="modal fade in"  tabindex="-1" role="dialog" aria-hidden="false" >
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title">Add Customize Brand</h4>
            </div>
            <form class="form-horizontal" id = "addBrandForm" method = "post" action = "{{url("front/sell/ajaxAddBrand")}}">
            <div class="modal-body">
                <div class="form-group">
                    <label class="col-md-3 control-label" for="name">Title</label>
                    <div class="col-md-9">
                        <input name="title"  placeholder="Title" class="form-control" value = "">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-3 control-label" for="name">Image</label>
                    <div class="col-md-9">
                        <button type="button" class="btn " onclick = "onClickFilgDlg('#brandImg');" >Select File</button>
                    </div>
                </div>
                <div class = "form-group " id = "img_rect">
                    <label class="col-md-3 control-label" for="name"></label>
                    <div class="col-md-9">
                        <img id = "brandImg_img"   style = "width:100px;" />
                        <input type = "hidden"  id = "brandImg_val"/>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" data-dismiss="modal" class="btn btn-default" style = "margin-bottom:0px;">Close</button>
                <button type="submit" class="btn btn-primary" onclick = "" >Save changes</button>
            </div>
            </form>
        </div>
    </div>
</div>
<script>
    $("#addBrandForm").validate({
        rules: {
            title: "required",
            log_img: "required",
        },
        messages: {
        },
        errorPlacement: function (error, element) {
            if($(element).closest('div').children().filter("div.error-div").length < 1)
                $(element).closest('div').append("<div class='error-div'></div>");
            $(element).closest('div').children().filter("div.error-div").append(error);
        },
        submitHandler: function(form){
            var datas = new FormData();
            datas.append('_token', _token);
            datas.append("log_img_val", $("#brandImg_val").val());

            var url = $(form).attr("action");
            url += "?"+$(form).serialize();
            $.ajax({
                url: url,
                data: datas,
                cache: false,
                contentType: false,
                processData: false,
                dataType: 'html',
                type: 'POST',
                beforeSend: function (data, status) {
                },
                success: function (html) {
                    $("#add-brand-dialog").modal("hide");
                    $("#brand_wrapper").html(html);
                },
                error: function (data, status, e) {
                    errorMsg("errors happens");
                    return false;
                }
            });
            return false;
        }
    });

</script>

