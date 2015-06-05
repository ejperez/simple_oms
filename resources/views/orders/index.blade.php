@extends('app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h3>List of Orders</h3>
        </div>
    </div>

    @include('alerts')

    <div class="row">
        <div class="col-md-12">
            <table id="tbl_history" class="table display" cellspacing="0">
                <thead>
                <tr>
                    <th></th>
                    <th>PO Number</th>
                    <th>Order Date</th>
                    <th>Pickup Date</th>
                    <th>Customer</th>
                    <th>Total Amount ({{ $CS }})</th>
                    <th>Status</th>
                    <th width="100">Options</th>
                </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

{!! Form::open(['url' => '', 'name' => 'update_order_status_form', 'id' => 'update_order_status_form', 'method' => 'PUT']) !!}{!! Form::close() !!}
@section('css')
    <style>
        table.dataTable tr
        {
            cursor: pointer;
        }
    </style>
@endsection('css')

@section('js')
    <script id="details-template" type="text/x-handlebars-template">
        <table class="table">
            <caption><strong>Order Items</strong></caption>
            <thead>
            <th>Product</th>
            <th>Category</th>
            <th>UOM</th>
            <th>Unit Price ({{ $CS }})</th>
            <th>Quantity</th>
            <th>Price ({{ $CS }})</th>
            </thead>
            <tbody>
            @{{#each details}}
            <tr class="@{{#oddEven @index}}@{{/oddEven}}">
                <td>@{{ product }}</td>
                <td>@{{ category }}</td>
                <td>@{{ uom }}</td>
                <td>@{{ unit_price }}</td>
                <td>@{{ quantity }}</td>
                <td>@{{ price }}</td>
            </tr>
            @{{/each}}
            </tbody>
        </table>
    </script>

    <script id="order-options-template"  type="text/x-handlebars-template">
        @{{#hasClass class 'Pending'}}

        @if ($role == 'Approver')
        <a class="btn btn-default btn-xs btn-approve" data-order-id="@{{ id }}" data-status="Approved" href="#" title="Approve"><span class="glyphicon glyphicon-ok"></span></a>
        <a class="btn btn-default btn-xs btn-disapprove" data-order-id="@{{ id }}" data-status="Disapproved" href="#" title="Disapprove"><span class="glyphicon glyphicon-remove"></span></a>
        @endif

        @if ($role == 'Sales' || $role == 'Administrator')
        <a class="btn btn-default btn-xs" href="{{ url('orders') }}/@{{ id }}/edit" id="btn_edit" title="Edit"><span class="glyphicon glyphicon-edit"></span></a>
        <a class="btn btn-default btn-xs btn-cancel" data-order-id="@{{ id }}" data-status="Cancelled" href="#" title="Cancel"><span class="glyphicon glyphicon-remove"></span></a>
        @endif
        @{{/hasClass}}
    </script>

    <script>
        var options = Handlebars.compile($("#order-options-template").html()),
                template = Handlebars.compile($("#details-template").html());

        var $tbl_history = $('#tbl_history'),
                $update_order_status_form = $('form#update_order_status_form'),
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
                    {
                        "className":      'details-control',
                        "orderable":      false,
                        "data":           null,
                        "searchable":     false,
                        "defaultContent": '<span class="glyphicon glyphicon-collapse-down"></span>'
                    },
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
                        "searchable":     false,
                        "defaultContent": ''
                    }
                ],
                order: [[2, 'desc']]
            });

            // Disable automatic searching every keypress, wait for ENTER key instead
            $('#tbl_history_filter input').unbind().bind('keyup', function(e) {
                if(e.keyCode == 13) {
                    dt_history.search(this.value).draw();
                }
            });

            // Add option buttons
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

            // Add event listener for opening and closing details
            $tbl_history.find('tbody').on('click', 'td.details-control', function () {
                var tr = $(this).closest('tr');
                var row = dt_history.row( tr );

                if ( row.child.isShown() ) {
                    // This row is already open - close it
                    row.child.hide();
                    tr.removeClass('shown');
                    tr.find('td.details-control').html('<span class="glyphicon glyphicon-collapse-down"></span>');
                }
                else {
                    // Open this row
                    row.child( template(row.data()) ).show();
                    tr.addClass('shown');
                    tr.find('td.details-control').html('<span class="glyphicon glyphicon-collapse-up"></span>');
                }
            });

            // Update status buttons click event
            $tbl_history.on('click', 'a.btn-approve, a.btn-disapprove, a.btn-cancel', function(){
                // Update url of hidden form
                var url = '{{ url('orders')  }}/' + $(this).attr('data-order-id') + '/update-{{ Auth::user()->hasRole('approver') ? 'approver' : 'customer' }}-status/' + $(this).attr('data-status');
                $update_order_status_form.attr('action', url).submit();
            });
        });

    </script>
@endsection('js')
@endsection('content')