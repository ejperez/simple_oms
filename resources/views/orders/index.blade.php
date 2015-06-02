@extends('app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h3>List of Orders</h3>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <table id="tbl_history" class="table display" cellspacing="0">
                <thead>
                <tr>
                    <th>PO Number</th>
                    <th>Order Date</th>
                    <th>Pickup Date</th>
                    <th>Customer</th>
                    <th>Total Amount ({{ Session::get('PESO_SYMBOL') }})</th>
                    <th>Status</th>
                    <th width="100">Options</th>
                </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

@section('css')
    <style>
        table.dataTable tr
        {
            cursor: pointer;
        }
    </style>
@endsection('css')

@section('js')
    <script id="options-template"  type="text/x-handlebars-template">
        <a class="btn btn-default btn-xs" href="{{ url('orders') }}/@{{ id }}" id="btn_edit" title="View"><span class="glyphicon glyphicon-eye-open"></span></a>
        @{{#hasClass class 'Pending'}}
        <a class="btn btn-default btn-xs" href="#" title="Approve"><span class="glyphicon glyphicon-ok"></span></a>
        <a class="btn btn-default btn-xs" href="#" title="Disapprove"><span class="glyphicon glyphicon-remove"></span></a>
        <a class="btn btn-default btn-xs" href="{{ url('orders') }}/@{{ id }}/edit" id="btn_edit" title="Edit"><span class="glyphicon glyphicon-edit"></span></a>
        @{{/hasClass}}
    </script>

    <script>
        var options = Handlebars.compile($("#options-template").html());

        var $tbl_history = $('#tbl_history'),
                dt_history = null;

        $(document).ready(function() {
            // Initialize history datatable
            dt_history = $tbl_history.DataTable({
                processing: true,
                serverSide: true,
                searchDelay: 400,
                ajax: "{{ url('get-orders-datatable') }}",
                scrollX: true,
                columns: [
                    {data: "po_number"},
                    {data: "order_date"},
                    {data: "pickup_date"},
                    {data: "customer"},
                    {data: "total_amount"},
                    {data: "status"},
                    {
                        "className": 'options-control',
                        "orderable": false,
                        "data": null,
                        "defaultContent": ''
                    }
                ]
            });

            dt_history.on( 'draw.dt', function () {
                // Add options
                $('#tbl_history tbody td.options-control').each(function(){
                    var td = $(this),
                            tr = td.closest('tr');

                    td.html(options({
                        id: tr.attr('id'),
                        class: tr.attr('class')
                    }));
                });
            } );
        });

    </script>
@endsection('js')
@endsection('content')