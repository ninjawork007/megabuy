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
    <h1>Gallery</h1>
    <ol class="breadcrumb">
        <li>
            <a href="{{ route('admin.dashboard') }}"> <i class="livicon" data-name="home" data-size="16" data-color="#000"></i>
                @lang('gallery.dashboard')
            </a>
        </li>
        <li><a href="#">Home Manage</a></li>
        <li class="active">Gallery</li>
    </ol>
</section>

<!-- Main content -->
<section class="content">
    <div class="row">
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
                        <form id = "searchForm" action = "{{url("admin/gallery")}}" method = "post">
                            {!! csrf_field() !!}
                            <input type = "hidden" name = "page" value = "{{$pageParam['pageNo']}}"/>
                            <div class = "col-sm-6 text-left">
                                <input class = "form-control " id = "searchKey" name = "search" placeholder="Search" value = "{{$search}}" />
                            </div>
                            <div class = "col-sm-6 text-right">
                                <button type="button" class="btn btn-responsive button-alignment btn-success" onclick = "searchData(0)" style="margin-bottom:7px;" data-toggle="button">Search</button>
                            </div>
                        </form>
                    </div>
                    <div id = "product-wrapper">
                        <div class="table-scrollable ">
                            <table class="table table-bordered table-hover table-last-bottom">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Image </th>
                                    <th>Title</th>
                                    <th>SubTitle</th>
                                    <th>ImgCount</th>
                                    <th width="200px;"></th>
                                </tr>
                                </thead>
                                <tbody >
                                @if(isset($productList))
                                    @foreach($productList as $key=>$item)
                                        <tr>
                                            <td>{{$key+1+$pageParam['startNumber']}}</td>
                                            <td>
                                                <img src = "{{correctImgPath($item['img'])}}" style = "width:80px;" onerror = "noExitImg(obj)"/>
                                            </td>
                                            <td>{{$item['title']}}</td>
                                            <td>{{$item['subtitle']}}</td>
                                            <td>{{$item->getProductImgCount()}}</td>
                                            <td>
                                                <a href="javascript:void(0)" class="btn default btn-xs purple" data-id = "{{$item['id']}}" data-count = "{{$item->getProductImgCount()}}" onclick = "downloadImgFile(this)">
                                                    <i class="livicon" data-name="angle-down" data-loop="true" data-color="#000" data-hovercolor="black" data-size="14"></i>
                                                    Download
                                                </a>
                                            </td>
                                            </td>
                                        </tr>
                                    @endforeach
                                    @if(count($productList) == 0)
                                        <tr>
                                            <td colspan = "9">There is not data</td>
                                        </tr>
                                    @endif
                                @else
                                    <tr>
                                        <td colspan = "9">There is not data</td>
                                    </tr>
                                @endif
                                </tbody>
                            </table>
                        </div>
                        <div class = "text-center">
                            @include("admin.layouts.pagination")
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <form id = "downloadForm" action = "{{url("admin/gallery/downloadImgZip")}}" method = "post">
        {!! csrf_field() !!}
        <input type = "hidden" name = "id"/>
    </form>


</section>

@stop
{{-- Body Bottom confirm modal --}}
@section('footer_scripts')
    <script src="{{ asset('assets/vendors/jasny-bootstrap/js/jasny-bootstrap.js') }}" ></script>
    <script src="{{ asset('assets/vendors/iCheck/js/icheck.js') }}"></script>

    <script>
        function downloadImgFile(obj){
            var count = $(obj).attr("data-count");
            if(parseInt(count) < 0){
                errorMsg("you can not download Images!");
                return;
            }
            var id = $(obj).attr("data-id");
            $("#downloadForm input[name='id']").val(id);
            $("#downloadForm").submit();
        }
        $("input[name='search']").keypress(function(e) {
            if ( e.keyCode == 13){
                searchData(0);
            }
        });
        $("#searchKey").keypress(function(e) {
            if ( e.keyCode == 13){
                searchData(0);
            }
        });
        $(document).ready(function() {
            $('input[type="checkbox"].custom-checkbox, input[type="radio"].custom-radio').iCheck({
                checkboxClass: 'icheckbox_minimal-blue',
                radioClass: 'iradio_minimal-blue',
                increaseArea: '20%'
            });

        });


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

