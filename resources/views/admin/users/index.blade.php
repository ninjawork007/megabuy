@extends('admin/layouts/default')

{{-- Page title --}}
@section('title')
Users List
@parent
@stop

{{-- page level styles --}}
@section('header_styles')
<link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/datatables/css/dataTables.bootstrap.css') }}" />
<link href="{{ asset('assets/css/pages/tables.css') }}" rel="stylesheet" type="text/css" />
@stop


{{-- Page content --}}
@section('content')
<section class="content-header">
    <h1>Users</h1>
    <ol class="breadcrumb">
        <li>
            <a href="{{ route('admin.dashboard') }}">
                <i class="livicon" data-name="home" data-size="14" data-color="#000"></i>
                Dashboard
            </a>
        </li>
        <li><a href="#"> Users</a></li>
        <li class="active">Users List</li>
    </ol>
</section>

<!-- Main content -->
<section class="content paddingleft_right15">
    <div class="row">
        <div class="panel panel-primary ">
            <div class="panel-heading">
                <h4 class="panel-title"> <i class="livicon" data-name="user" data-size="16" data-loop="true" data-c="#fff" data-hc="white"></i>
                    Users List
                </h4>
            </div>
            <br />
            <div class="row">
                <div class="col-md-3">
                    {{-- <button type="button" class="btn btn-responsive button-alignment btn-primary" onclick = "exportTableUserToCSV()" style="margin-bottom:7px;" data-toggle="button">CSV</button> --}}
                    <a type="button" id="user_export" href="{{url('admin/userexport')}}" class="btn btn-responsive button-alignment btn-primary" style="margin-bottom:7px;">CSV</a>
                </div>
                <div class="col-md-4">
                    <label class="control-label inline-block"> Show only:
                        <select name = "show_only" class = "form-control inline-block" style = "width:200px;">
                            <option value = "">All</option>
                            <option value = "1">Admin</option>
                            {{-- <option value = "2">Buyer</option> --}}
                            <option value = "3">User</option>
                        </select>
                    </label>

                </div>
            </div>
            <div class="panel-body">
                <div class="table-responsive">
                <table class="table table-bordered " id="table">
                    <thead>
                        <tr class="filters">
                            <th>ID</th>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>User Type</th>
                            <th>User E-mail</th>
                            <th>Sold</th>
                            <th>Purchased</th>
                            <th>Status</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>


                    </tbody>
                </table>
                </div>
            </div>
        </div>
    </div>    <!-- row-->
</section>
@stop

{{-- page level scripts --}}
@section('footer_scripts')
    <script type="text/javascript" src="{{ asset('assets/vendors/datatables/js/jquery.dataTables.js') }}" ></script>
    <script type="text/javascript" src="{{ asset('assets/vendors/datatables/js/dataTables.bootstrap.js') }}" ></script>

<script>
    var show_only ;
    $(function() {



        var table = $('#table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{!! route('admin.users.data') !!}',
                data: function (d) {
                    d.show_only = $("select[name='show_only']").val();
                }
            },
            columns: [
                { data: 'id', name: 'id' },
                { data: 'first_name', name: 'first_name' },
                { data: 'last_name', name: 'last_name' },
                { data: 'user_type', name: 'user_type' },
                { data: 'email', name: 'email' },
                { data: 'purchased_num', name: 'purchased_num'},
                { data: 'sold_num', name: 'sold_num'},
                { data: 'status', name: 'status'},
                { data: 'created_at', name:'created_at'},
                { data: 'actions', name: 'actions', orderable: false, searchable: false }
            ],

        });
        table.on( 'draw', function () {
            $('.livicon').each(function(){
                $(this).updateLivicon();
            });
        } );

        $("select[name='show_only']").change(function () {
            show_only = $(this).val();
            table.draw();
        });
    });

    function exportTableUserToCSV(filename) {
        if(filename == undefined || filename == ''){
            filename = 'export-data.csv';
        }
        var csv = [];
        var rows = document.querySelectorAll("table tr");

        for (var i = 0; i < rows.length; i++) {
            var row = [], cols = rows[i].querySelectorAll("td, th");

            for (var j = 0; j < 7; j++){
                row.push(cols[j].innerText.replace(",",""));
            }
            if(i==0){
                row.push("top seller");
            }else{
                var top_seller = false;
                var innerText = cols[1];
                innerText = $(innerText).html();
                if(innerText.indexOf("blue")>-1){
                    top_seller = true;
                }
                if(top_seller){
                    row.push("top seller");
                }else{
                    row.push("");
                }

            }

            csv.push(row.join(","));
        }
        downloadCSV(csv.join("\n"), filename);
    }

</script>

<div class="modal fade" id="delete_confirm" tabindex="-1" role="dialog" aria-labelledby="user_delete_confirm_title" aria-hidden="true">
	<div class="modal-dialog">
    	<div class="modal-content"></div>
  </div>
</div>
<script>
$(function () {
	$('body').on('hidden.bs.modal', '.modal', function () {
		$(this).removeData('bs.modal');
	});
});
</script>
@stop
