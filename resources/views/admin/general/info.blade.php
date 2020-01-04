@extends('admin/layouts/default')

{{-- Web site Title --}}
@section('title')
    @lang('blogcategory/title.management')
    @parent
@stop
@section('header_styles')
    <link href="{{ asset('assets/vendors/summernote/summernote.css') }}" rel="stylesheet" type="text/css">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/datatables/css/dataTables.bootstrap.css') }}" />
    <link href="{{ asset('assets/css/pages/tables.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{asset("assets/tree/dtree.css")}}">
    <script src="{{asset("assets/tree/dtree.js")}}"></script>
    <link href="{{ asset('assets/vendors/jasny-bootstrap/css/jasny-bootstrap.css') }}"  rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/vendors/iCheck/css/all.css') }}"  rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/pages/editor.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('assets/css/pages/buttons.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('assets/css/pages/advbuttons.css') }}" rel="stylesheet" type="text/css"/>
@stop

{{-- Montent --}}
@section('content')
    <section class="content-header">
        <h1>General Information</h1>
        <ol class="breadcrumb">
            <li>
                <a href="{{ route('admin.dashboard') }}"> <i class="livicon" data-name="home" data-size="16" data-color="#000"></i>
                    @lang('general.dashboard')
                </a>
            </li>
            <li><a href="#">Home Manage</a></li>
            <li class="active">General</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-primary" id="hidepanel6">
                    <div class="panel-heading">
                        <h3 class="panel-title">
                            General Information
                        </h3>
                        <span class="pull-right">
                        <i class="glyphicon glyphicon-chevron-up clickable"></i>
                    </span>
                    </div>
                    <div class="panel-body">
                        <form class="form-horizontal" id = "infoForm" action="{{url("admin/general/ajaxSaveCategory")}}" method = "post">
                            <input type = "hidden" name = "_token" value = "{!! csrf_token() !!}"/>
                            <input type = "hidden" name = "id" value = "{{$id}}"/>

                            @if(isset($noneChildList))
                                <div class="form-group">
                                    <label class="col-md-3 control-label" for="name">ChildList</label>
                                    <div class="col-md-9">
                                        <select name = "none_child_list" class = "form-control" onchange="getChildInfo()">
                                            @foreach($noneChildList as $item)
                                                <option value = "{{$item['id']}}">{{$item['title']}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            @endif
                            <div id = "info_wrapper">
                            @include("admin/general/other_info")
                            </div>
                            <div class="form-position">
                                <div class="col-md-12 text-right">
                                    <button type="submit" class="btn btn-responsive btn-primary btn-sm">Submit</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @include("dlg/crop_dlg")
    </section>

@stop
{{-- Body Bottom confirm modal --}}
@section('footer_scripts')
    <script src="{{ asset('assets/vendors/jasny-bootstrap/js/jasny-bootstrap.js') }}" ></script>
    <script src="{{ asset('assets/vendors/iCheck/js/icheck.js') }}"></script>
    <script>
        $(document).ready(function() {
            getChildInfo();
            $(".logImg").hover(function(){
                $(this).parent().next().removeClass("hidden");
            }, function(e){
                if(!$(e.relatedTarget).hasClass("delBtnWrapper")){
                    $(this).parent().next().addClass("hidden");
                }
            })

        });

        function getChildInfo(){
            if($("select[name='none_child_list']").length == 0){
                return;
            }

            var id = $("select[name='none_child_list']").val();
            $("input[name='id']").val(id);
            var param = new Object();
            param._token = _token;
            var url = "{{url("admin/general/ajaxGetCategoryInfo/")}}"+"/"+id;
            $.post(url, param, function(html){
                $("#info_wrapper").html(html);
            });
        }

        $("#infoForm").validate({
            rules: {
                title: "required",
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
                datas.append("log_img_val", $("#logImg_val").val());
                var is_hot = 0;
                if($("#is_hot").prop("checked")){
                    is_hot = 1;
                }

                $("input[name='is_hot']").val(is_hot);
                var url = $(form).attr("action");
                url += "?"+$(form).serialize();
                $.ajax({
                    url: url,
                    data: datas,
                    cache: false,
                    contentType: false,
                    processData: false,
                    dataType: 'json',
                    type: 'POST',
                    beforeSend: function (data, status) {
                    },
                    success: function (data) {
                        if (data.status == 1) {
                            successMsg(data.msg, function(){
                                goBack();
                            });
                        } else {
                            errorMsg(data.msg);
                            return false
                        }
                    },
                    error: function (data, status, e) {
                        errorMsg("errors happens");
                        return false;
                    }
                });
                return false;
            }
        });

        function delImage(obj){
            $("#logImg_img").attr("src", "");
            $("#logImg_val").val("");
            $(obj).addClass("hidden");

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
        });
    </script>
@stop

