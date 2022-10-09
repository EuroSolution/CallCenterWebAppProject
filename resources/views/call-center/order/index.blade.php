@extends('call-center.layout')
@section('title', 'Orders')
@section('css')
    <link href="https://cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css" rel="stylesheet">
    <style>
        .addBtn{
            float: right;
            /*margin-top: 10px;*/
        }
        td{
            text-align: center;
        }
    </style>
@endsection

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Orders</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('callCenter.dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item active">Orders</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">

                        <div class="card">
                            <div class="card-header">
                                <a class="btn btn-sm btn-primary addBtn" href="{{ route('callCenter.addOrder') }}">Add Order</a>
                            </div>
                            <div class="card-body">
                                <table id="example1" class="table table-bordered table-striped">
                                    <thead>
                                    <tr>
                                        <th>S.No</th>
                                        <th>Order No</th>
                                        <th>Restaurant</th>
                                        <th>Customer</th>
                                        <th>Phone</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                        <th>Order Date</th>
                                        <th>Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>
                            </div>
                            <!-- /.card-body -->
                        </div>
                        <!-- /.card -->
                    </div>
                    <!-- /.col -->
                </div>
                <!-- /.row -->
            </div>
            <!-- /.container-fluid -->
        </section>
        <div id="confirmModal" class="modal fade" role="dialog">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header"  style="background-color: #343a40;
            color: #fff;">
                        <h2 class="modal-title">Confirmation</h2>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <h4 align="center" style="margin: 0;">Are you sure you want to delete this ?</h4>
                    </div>
                    <div class="modal-footer">
                        <button type="button" id="ok_delete" name="ok_delete" class="btn btn-danger">Delete</button>
                        <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="{{ asset('admin/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script>
        $(document).ready(function () {
            var DataTable = $("#example1").DataTable({
                dom: "Blfrtip",
                buttons: [{
                    extend: "copy",
                    className: "btn-sm"
                }, {
                    extend: "csv",
                    className: "btn-sm"
                }, {
                    extend: "excel",
                    className: "btn-sm"
                }, {
                    extend: "pdfHtml5",
                    className: "btn-sm"
                }, {
                    extend: "print",
                    className: "btn-sm"
                }],
                responsive: true,
                processing: true,
                serverSide: true,
                pageLength: 10,
                ajax: {
                    url: `{{route('callCenter.orders')}}`,
                },
                order: [ [0, 'asc'] ],
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                    {data: 'order_number', name: 'order_number'},
                    {data: 'restaurant', name: 'restaurant'},
                    {data: 'customer', name: 'customer'},
                    {data: 'phone', name: 'phone'},
                    {data: 'total_amount', name: 'total_amount'},
                    {data: 'status', name: 'status'},
                    {data: 'order_date', name: 'order_date'},
                    {data: 'action', name: 'action', orderable: false}
                ]

            });

            var delete_id;
            $(document,this).on('click','.delete',function(){
                delete_id = $(this).attr('id');
                $('#confirmModal').modal('show');
            });

            $(document).on('click','#ok_delete',function(){
                $.ajax({
                    type:"delete",
                    url:`{{route('callCenter.destroyOrder', '')}}/${delete_id}`,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    beforeSend: function(){
                        $('#ok_delete').text('Deleting...');
                        $('#ok_delete').attr("disabled",true);
                    },
                    success: function (data) {
                        DataTable.ajax.reload();
                        $('#ok_delete').text('Delete');
                        $('#ok_delete').attr("disabled",false);
                        $('#confirmModal').modal('hide');
                        if(data === false) {
                            toastr.error('Something Went Wrong..!!');
                        }else{
                            toastr.success('Record Delete Successfully');
                        }
                    }
                })
            });
        })
    </script>
@endsection
