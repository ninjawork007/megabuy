@extends('admin/layouts/default')

{{-- Web site Title --}}
@section('title')
@lang('blogcategory/title.management')
@parent
@stop
@section('header_styles')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/datatables/css/dataTables.bootstrap.css') }}" />
    <link href="{{ asset('assets/css/pages/tables.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{asset("assets/tree/dtree.css")}}">
    <script src="{{asset("assets/tree/dtree.js")}}"></script>
    <link href="{{ asset('assets/vendors/jasny-bootstrap/css/jasny-bootstrap.css') }}"  rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/vendors/iCheck/css/all.css') }}"  rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/vendors/select2/css/select2.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/vendors/select2/css/select2-bootstrap.css') }}" rel="stylesheet" />
@stop

{{-- Montent --}}
@section('content')
<section class="content-header">
    <h1>Product</h1>
    <ol class="breadcrumb">
        <li>
            <a href="{{ route('admin.dashboard') }}"> <i class="livicon" data-name="home" data-size="16" data-color="#000"></i>
                @lang('general.dashboard')
            </a>
        </li>
        <li><a href="#">Product Manage</a></li>
        <li class="active">Product</li>
    </ol>
</section>

<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-4">
            <div class="panel panel-primary" id="hidepanel6">
                <div class="panel-heading">
                    <h3 class="panel-title">
                        Category List
                    </h3>
                    <span class="pull-right">
                        <i class="glyphicon glyphicon-chevron-up clickable"></i>
                    </span>
                </div>
                <div class="panel-body">
                    <div class="form-group">
                        <div class = "row">
                         <div class = "col-md-12">
                            <label>Categories</label>
                             <span class = "pull-right">
                                <input class = "form-control inline-block w-120" placeholder="search" name = "search"/>
                            </span>
                        </div>
                        </div>
                        <div class = "form-control" style = "min-height:500px !important; overflow: auto;">
                            <ul class = "admin-ul-list" id = "category-wrapper">
                                @include("admin/product/category_list")
                            </ul>
                        </div>
                        <p class="help-block">Product category view here.</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="panel panel-success" id="hidepanel1">
                <div class="panel-heading">
                    <h3 class="panel-title">
                        <i class="livicon" data-name="save" data-size="16" data-loop="true" data-c="#fff" data-hc="white"></i>
                        Product List
                    </h3>
                    <span class="pull-right">
                        <i class="glyphicon glyphicon-chevron-up clickable"></i>
                    </span>
                </div>
                <div class="panel-body">
                    <div class = "row">
                        <div class = "col-sm-4 text-left">
                            <input class = "form-control" id = "searchKey" placeholder="Search" />
                        </div>
                        <div class = "col-sm-4 text-left">
                            <div id = "seller_id_wrapper">
                                <select name = "seller_id" class = "form-control">
                                    <option img = "" value = "0" >All</option>
                                    <?php foreach($sellerList as $seller){?>
                                    <option img = "{!! url('/uploads/users').'/'.$seller['user']['pic'] !!}" value = "<?php echo $seller['user']['id'] ?>" <?php if(isset($product['seller_id']) && ($product['seller_id']==$seller['user']['id'])) echo "selected"; ?>><?php echo $seller['user']->getFullNameAttribute()?></option>
                                    <?php }?>
                                </select>
                                <script>
                                    function formatState (state) {
                                        if (!state.id) { return state.text; }
                                        if($(state.element).attr("img") == '') return state.text;
                                        var $state = $(
                                            '<span><img src="' + $(state.element).attr("img") +'" class="img-flag" width="20px" height="20px" onerror="noExitImg(this)" /> ' + state.text + '</span>'
                                        );
                                        return $state;


                                    }
                                    $(function(){
                                        $("select[name='seller_id']").select2({
                                            templateResult: formatState,
                                            templateSelection: formatState,
                                            placeholder: "select",
                                            theme:"bootstrap"
                                        });
                                    })

                                </script>
                            </div>
                        </div>
                        <div class = "col-sm-4 text-right">
                            {{-- <button type="button" class="btn btn-responsive button-alignment btn-primary" onclick = "exportTableToCSV1()" style="margin-bottom:7px;" data-toggle="button">CSV</button> --}}
                            <a type="button" href="{{url('admin/productexport')}}" class="btn btn-responsive button-alignment btn-primary" style="margin-bottom:7px;" >CSV</a>
                            <button type="button" class="btn btn-responsive button-alignment btn-success" onclick = "editProduct(0)" style="margin-bottom:7px;" data-toggle="button">Add</button>
                        </div>
                    </div>
                    <div id = "product-wrapper">
                        @include("admin.product.product_list")
                    </div>
                </div>
            </div>
        </div>
    </div>



</section>

@stop
{{-- Body Bottom confirm modal --}}
@section('footer_scripts')
    <script src="{{ asset('assets/vendors/jasny-bootstrap/js/jasny-bootstrap.js') }}" ></script>
    <script src="{{ asset('assets/vendors/iCheck/js/icheck.js') }}"></script>
    <script language="javascript" type="text/javascript" src="{{ asset('assets/vendors/select2/js/select2.js') }}"></script>
    <script>
        var currentCategoryId = 0;
        $("input[name='search']").keypress(function(e) {
            if ( e.keyCode == 13){
                initCategory();
            }
        });
        $("#searchKey").keypress(function(e) {
            if ( e.keyCode == 13){
                initProduct();
            }
        });
        $("select[name='seller_id']").change(function(){
            initProduct();
        });

        $(document).ready(function() {
            $('input[type="checkbox"].custom-checkbox, input[type="radio"].custom-radio').iCheck({
                checkboxClass: 'icheckbox_minimal-blue',
                radioClass: 'iradio_minimal-blue',
                increaseArea: '20%'
            });
            initCategory();
        });

        function initCategory(){
            var param = new Object();
            param._token = _token;
            param.search = $("input[name='search']").val();
            $.post("{{url("admin/product/ajaxCategoryList")}}", param, function(html){
                $("#category-wrapper").html(html);
                $("#category-wrapper li").removeClass("active");
                $("#category-wrapper li").each(function(){
                    if($(this).attr("data-category-id") == currentCategoryId){
                        $(this).addClass("active");
                    }
                });
                initProduct();
            })
        }



        function initProduct(pageNo){
            var obj =$("#category-wrapper li.active");
            if(obj.length == 0) return;
            var param = new Object();
            param._token = _token;
            param.categoryId = obj.attr("data-category-id");
            param.search = $("#searchKey").val();
            param.seller_id = $("select[name='seller_id']").val();
            if(pageNo == undefined){
                param.page = "1";
            }else{
                param.page = pageNo;
            }
            currentCategoryId = param.categoryId;
            $.post("{{url("admin/product/ajaxProductList")}}", param, function(html){
                $("#product-wrapper").html(html);
            })
        }



        function deleteProduct(id){
            confirmMsg('Do you sure delete this item!', function(){
                setTimeout(function(){
                    var param = new Object();
                    param._token = _token;
                    param.id = id;
                    $.post("{{url("admin/product/ajaxDeleteProduct")}}", param, function(data){
                        if(data.status == "1"){
                            successMsg(data.msg, function(){
                                window.location.reload();
                            });
                        }else{
                            errorMsg();
                        }
                    }, "json");
                }, 300);
            })
        }


        function editProduct(id, categoryId){
            if(id == "0"){
                if(parseInt(currentCategoryId) == 0){
                    errorMsg("Please choose category!");
                    return;
                }
            }
            if(categoryId == undefined){
                categoryId = currentCategoryId;
            }


            window.location.href = "{{url("admin/product/")}}"+"/"+id+"/"+categoryId;

        }

    </script>
    <div class="modal fade" id="delete_confirm" tabindex="-1" role="dialog" aria-labelledby="blogcategory_delete_confirm_title" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
            </div>
        </div>
    </div>
    <div class="modal fade" id="blogcategory_exists" tabindex="-2" role="dialog" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Modal title</h4>
                </div>
                <div class="modal-body">
                    @lang('blogcategory/message.blogcategory_have_blog')
                </div>
            </div>
        </div>
    </div>
    <script>
        $(function () {$('body').on('hidden.bs.modal', '.modal', function () {$(this).removeData('bs.modal');});});
        $(document).on("click", ".blogcategory_exists", function () {

            var group_name = $(this).data('name');
            $(".modal-header h4").text( group_name+" blog category" );
        });</script>
@stop

