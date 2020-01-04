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
@stop

{{-- Montent --}}
@section('content')
<section class="content-header">
    <h1>General</h1>
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
        <div class="col-md-8">
            <div class="panel panel-primary" id="hidepanel1">
                <div class="panel-heading">
                    <h3 class="panel-title">
                        <i class="livicon" data-name="save" data-size="16" data-loop="true" data-c="#fff" data-hc="white"></i>
                        Main Category List
                    </h3>
                    <span class="pull-right">
                        <i class="glyphicon glyphicon-chevron-up clickable"></i>
                    </span>
                </div>
                <div class="panel-body">
                    <form id = "searchForm" action = "{{url("admin/general")}}" method = "post">
                    <input type = "hidden" name = "_token" value = "{!! csrf_token() !!}"/>
                    <div class = "row">
                        <div class = "col-sm-6 text-left">
                            <select class = "form-control searchInput" name = "category_id" onchange="getLeafCategoryList()" >
                                @foreach($rootCategoryList as $item)
                                    <option value = "{{$item['id']}}">{{$item['title']}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class = "col-sm-6 text-right">
                            <button type="button" class="btn btn-responsive button-alignment btn-success" onclick = "editItem(0)" style="margin-bottom:7px;" data-toggle="button">Add</button>
                        </div>
                    </div>
                    </form>
                    <div class="table-scrollable" id = "listWrapper">

                    </div>

                </div>

            </div>
        </div>
        <div class = "col-md-4">
            <div class="panel panel-success" id="hidepanel2">
                <div class="panel-heading">
                    <h3 class="panel-title">
                        <i class="livicon" data-name="setting" data-size="16" data-loop="true" data-c="#fff" data-hc="white"></i>
                        Config
                    </h3>
                    <span class="pull-right">
                        <i class="glyphicon glyphicon-chevron-up clickable"></i>
                    </span>
                </div>
                <div class="panel-body">
                    <div class = "row hidden">
                        <div class = "col-sm-6 text-left">
                            <select class = "form-control searchInput" name = "category_id" onchange="getLeafCategoryList()" >
                                @foreach($rootCategoryList as $item)
                                    <option value = "{{$item['id']}}">{{$item['title']}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class = "col-sm-6 text-right">
                            <button type="button" class="btn btn-responsive button-alignment btn-success" onclick = "editItem(0)" style="margin-bottom:7px;" data-toggle="button">Add</button>
                        </div>
                    </div>
                    <div class = "row">
                        <div class = "col-sm-12 text-left">
                            <table class="table table-bordered table-hover table-last-bottom">
                                <thead>
                                <tr>
                                    <th >#</th>
                                    <th style = "width:40%;">Title </th>
                                    <th style = "width:40%;">Value</th>
                                    <th width="200px;"></th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($config_list as $key => $item)
                                <tr>
                                    <td>{{$key+1}}</td>
                                    <td>{{$item['conf_title']}}</td>
                                    <td><input class = "form-control" value = "{{$item['conf_val']}}"/></td>
                                    <td>
                                        <a href="javascript:void(0);" onclick="editConfig(this)" data-id = "{{$item['id']}}"  class="btn default btn-xs purple">
                                            <i class="livicon" data-name="pen" data-loop="true" data-color="#000" data-hovercolor="black" data-size="14"></i>
                                            Edit
                                        </a>
                                    </td>
                                </tr>
                                @endforeach

                                </tbody>
                            </table>
                        </div>
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

    <script>
        $(document).ready(function(){
            getLeafCategoryList();
        });

        function editConfig(obj){
            var val = $(obj).parent().prev().find("input").val();
            if(val == ""){
                errorMsg("please input the value!");
                return;
            }
            var param = new Object();
            param._token = _token;
            param.conf_val = val
            param.id = $(obj).attr("data-id");
            var url = "{{url("admin/general/ajaxSaveConfigs")}}";
            $.post(url, param, function(data){
                if(data.status == "1"){
                    successMsg(data.msg);
                }else{
                    errorMsg(data.msg)
                }
            }, "json");
        }


        function getLeafCategoryList(){
            var param = new Object();
            param._token = _token;
            param.root_id = $("select[name='category_id']").val();
            var url = "{{url("admin/general/getLeafCategoryList")}}";
            $.post(url, param, function(html){
                $("#listWrapper").html(html);
            })

        }

        function editItem(id){
            if(id=='0'){
                var length = $("#listWrapper table tbody tr").length;
                if(length >= 5){
                    errorMsg("You can not add the main show categories over  5 ");
                    return;
                }
            }
            var root_id = $("select[name='category_id']").val();
            window.location.href = "{{url("admin/general/getInfo")}}"+"/"+id+"/"+root_id;
        }

        function deleteItem(id){
            confirmMsg('Do you really want to delete this item?', function(){
                setTimeout(function(){
                    var param = new Object();
                    param._token = _token;
                    param.id = id;
                    $.post("{{url("admin/general/ajaxDeleteCategory")}}", param, function(data){
                        if(data.status == "1"){
                            successMsg(data.msg, function(){
                                getLeafCategoryList();
                            });
                        }else{
                            errorMsg(data.msg);
                        }
                    }, "json");
                }, 300);
            });
        }
    </script>

@stop

