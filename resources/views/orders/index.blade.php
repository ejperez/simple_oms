@extends('app')

@section('content')
<div class="container">
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
                    <th>Total Amount ({{ Config::get('constants.PESO_SYMBOL') }})</th>
                    <th>Status</th>
                    <th width="100">Options</th>
                </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal -->
<div id="update_order_status_modal" class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Confirm Change of Order Status</h4>
            </div>
            <div class="modal-body">
                {!! Form::open(['url' => '', 'name' => 'update_order_status_form', 'id' => 'update_order_status_form', 'method' => 'PUT']) !!}
                <div class="row">
                    <div class="col-md-12">
                        <label>PO Number:</label>
                        <label id="lbl_po_number" style="font-weight:bold"></label><br/>
                        <label>Change status to:</label>
                        <label id="lbl_status" style="font-weight:bold"></label><br/>
                        <label for="reason">Optional message:</label>
                        <textarea class="form-control" name="extra" id="extra" cols="30" rows="5"></textarea>
                    </div>
                </div>

                {!! Form::close() !!}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" id="btn_confirm">Confirm</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>

    </div>
</div>

@section('js')
    <script id="details-template" type="text/x-handlebars-template">
        <table class="table">
            <caption><strong>Order Items</strong></caption>
            <thead>
            <th>Product</th>
            <th>Category</th>
            <th>UOM</th>
            <th>Unit Price ({{ Config::get('constants.PESO_SYMBOL') }})</th>
            <th>Quantity</th>
            <th>Price ({{ Config::get('constants.PESO_SYMBOL') }})</th>
            </thead>
            <tbody>
            @{{#each details}}
            <tr class="@{{#oddEven @index}}@{{/oddEven}}">
                <td>@{{ product }}</td>
                <td>@{{ category }}</td>
                <td>@{{ uom }}</td>
                <td class="text-right">@{{ unit_price }}</td>
                <td class="text-right">@{{ quantity }}</td>
                <td class="text-right">@{{ price }}</td>
            </tr>
            @{{/each}}
            </tbody>
        </table>
        @{{#hasClass status 'Cancelled'}}
        <div role="alert" class="alert alert-warning">
            <p>Reason:<br/><strong>@{{ extra }}</strong></p>
        </div>
        @{{/hasClass}}
        @{{#hasClass status 'Disapproved'}}
        <div role="alert" class="alert alert-danger">
            <p>Disapproved by:<br/><strong>@{{ user }}</strong></p>
            <p>Comment:<br/><strong>@{{ extra }}</strong></p>
        </div>
        @{{/hasClass}}
        @{{#hasClass status 'Approved'}}
        <div role="alert" class="alert alert-success">
            <p>Approved by:<br/><strong>@{{ user }}</strong></p>
            <p>Comment:<br/><strong>@{{ extra }}</strong></p>
        </div>
        @{{/hasClass}}
    </script>

    <script id="order-options-template"  type="text/x-handlebars-template">
        @{{#hasClass class 'Pending'}}

        @if ($role == 'Approver')
        <a data-toggle="modal" data-target="#update_order_status_modal" class="btn btn-default btn-xs btn-approve" data-order-id="@{{ id }}" data-po-number="@{{ po_number }}" data-status="Approved" href="#" title="Approve"><span class="glyphicon glyphicon-ok"></span></a>
        <a data-toggle="modal" data-target="#update_order_status_modal" class="btn btn-default btn-xs btn-disapprove" data-order-id="@{{ id }}" data-po-number="@{{ po_number }}" data-status="Disapproved" href="#" title="Disapprove"><span class="glyphicon glyphicon-remove"></span></a>
        @endif

        @if ($role == 'Sales' || $role == 'Administrator')
        <a class="btn btn-default btn-xs" href="{{ url('orders') }}/@{{ id }}/edit" id="btn_edit" title="Edit"><span class="glyphicon glyphicon-edit"></span></a>
        <a data-toggle="modal" data-target="#update_order_status_modal" class="btn btn-default btn-xs btn-cancel" data-order-id="@{{ id }}" data-po-number="@{{ po_number }}" data-status="Cancelled" href="#" title="Cancel"><span class="glyphicon glyphicon-remove"></span></a>
        @endif
        @{{/hasClass}}
    </script>

    <script>
        var options = Handlebars.compile($("#order-options-template").html()),
                template = Handlebars.compile($("#details-template").html());

        var $tbl_history = $('#tbl_history'),
                $update_order_status_form = $('form#update_order_status_form'),

                $update_order_status_modal = $('div#update_order_status_modal'),
                $btn_confirm = $('button#btn_confirm'),
                $lbl_status = $('label#lbl_status'),
                $lbl_po_number = $('label#lbl_po_number'),

                dt_history = null;

        $(document).ready(function() {
            // Initialize history datatable
            dt_history = $tbl_history.DataTable({
                processing: true,
                serverSide: true,
                displayLength: 10,
                lengthChange: false,
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
                // Format total amount
                $('#tbl_history tbody td:nth-child(6)').each(function(){
                    $(this).html( parseInt($(this).html()).format(2, 3, ',', '.') );
                    $(this).addClass('text-right');
                });

                // Add options
                $('#tbl_history tbody td.options-control').each(function(){
                    var td = $(this),
                            tr = td.closest('tr');

                    td.html(options({
                        id: tr.attr('id'),
                        class: tr.attr('data-status'),
                        po_number: tr.attr('data-po-number')
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
                    // Check if extra is not null
                    row.data().extra = row.data().extra || 'No comment provided.';

                    // Format unit price and price
                    $.each(row.data().details, function(index, value){
                        row.data().details[index].unit_price = parseInt( value.unit_price ).format(2, 3, ',', '.');
                        row.data().details[index].price = parseInt( value.price ).format(2, 3, ',', '.');
                    });

                    // Open this row
                    row.child( template(row.data()) ).show();
                    tr.addClass('shown');
                    tr.find('td.details-control').html('<span class="glyphicon glyphicon-collapse-up"></span>');
                }
            });

            // Update status buttons click event
            $tbl_history.on('click', 'a.btn-approve, a.btn-disapprove, a.btn-cancel', function(){
                // Update url of form
                var url = '{{ url('orders')  }}/' + $(this).attr('data-order-id') + '/update-{{ Auth::user()->hasRole('approver') ? 'approver' : 'customer' }}-status/' + $(this).attr('data-status');
                $update_order_status_form.attr('action', url);

                // Update labels
                $lbl_status.html($(this).attr('data-status'));
                $lbl_po_number.html($(this).attr('data-po-number'));
            });

            // Confirm button
            $btn_confirm.click(function(){
                $update_order_status_form.submit();
            });
        });

    </script>
@endsection('js')
@endsection('content')