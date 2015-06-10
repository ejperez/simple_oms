@extends('app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <table id="tbl_history" class="table table-condensed display" cellspacing="0">
                <thead>
                    <tr>
                        <th>PO Number</th>
                        <th>Order Date</th>
                        <th>Pickup Date</th>
                        <th>Customer</th>
                        <th>Total Amount ({{ Config::get('constants.PESO_SYMBOL') }})</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th>PO Number</th>
                        <th>Order Date</th>
                        <th>Pickup Date</th>
                        <th>Customer</th>
                        <th>Total Amount ({{ Config::get('constants.PESO_SYMBOL') }})</th>
                        <th>Status</th>
                    </tr>
                </tfoot>
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

@section('css')
    <style>
        #tbl_history tr.shown td
        {
            background:#76b4ff !important;
        }
    </style>
@endsection

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
        @{{#hasClass status 'Pending'}}
        <table class="table">
            <caption><strong>Options</strong></caption>
            <tr>
                <td>
                    @if ($role == 'Approver')
                        <p><label>Customer Credits ({{ Config::get('constants.PESO_SYMBOL') }}) : </label> <strong>@{{ credits }}</strong></p>
                        <a data-toggle="modal" data-target="#update_order_status_modal" class="btn btn-default btn-approve" data-order-id="@{{ id }}" data-po-number="@{{ po_number }}" data-status="Approved" href="#" title="Approve"><span class="glyphicon glyphicon-ok"></span> Approve</a>
                        <a data-toggle="modal" data-target="#update_order_status_modal" class="btn btn-default btn-disapprove" data-order-id="@{{ id }}" data-po-number="@{{ po_number }}" data-status="Disapproved" href="#" title="Disapprove"><span class="glyphicon glyphicon-remove"></span> Disapprove</a>
                    @endif

                    @if ($role == 'Sales' || $role == 'Administrator')
                        <a class="btn btn-default" href="{{ url('orders') }}/@{{ id }}/edit" id="btn_edit" title="Edit"><span class="glyphicon glyphicon-edit"></span> Edit</a>
                        <a data-toggle="modal" data-target="#update_order_status_modal" class="btn btn-default btn-cancel" data-order-id="@{{ id }}" data-po-number="@{{ po_number }}" data-status="Cancelled" href="#" title="Cancel"><span class="glyphicon glyphicon-remove"></span> Cancel</a>
                    @endif
                </td>
            </tr>
        </table>
        @{{/hasClass}}
    </script>

    <script>
        var template = Handlebars.compile($("#details-template").html());

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
                    {data: "po_number"},
                    {data: "order_date"},
                    {data: "pickup_date"},
                    {data: "customer"},
                    {data: "total_amount"},
                    {data: "status"}
                ],
                order: [[1, 'desc']],
                initComplete: function () {
                    // Add search box per column
                    this.api().columns().every(function () {
                        var column = this,
                                $input = $(document.createElement('input')),
                                $footer = $(column.footer());


                        $('input').css('width', '100%');
                        $footer.css('padding', '10px');

                        $input.appendTo($footer)
                                .on('change', function () {
                                    var val = $.fn.dataTable.util.escapeRegex($(this).val());
                                    column.search(val ? val : '', true, false).draw();
                                });

                    // Hide top search box
                    $('#tbl_history_filter').remove();
                });
            }
            });

            // Add option buttons
            dt_history.on( 'draw.dt', function () {
                // Format total amount
                $('#tbl_history tbody td:nth-child(5)').each(function(){
                    $(this).html( parseInt($(this).html()).format(2, 3, ',', '.') );
                    $(this).addClass('text-right');
                });
            });

            // Add event listener for opening and closing details
            $tbl_history.find('tbody').on('click', 'tr', function () {
                var tr = $(this).closest('tr');
                var row = dt_history.row( tr );

                if ( row.child.isShown() ) {
                    // This row is already open - close it
                    row.child.hide();
                    tr.removeClass('shown');
                }
                else {
                    if (row.data().status != 'Pending'){
                        // Check if extra is not null
                        row.data().extra = row.data().extra || 'No comment provided.';
                    }

                    // Format unit price, price, credits
                    $.each(row.data().details, function(index, value){
                        row.data().details[index].unit_price = parseInt( value.unit_price ).format(2, 3, ',', '.');
                        row.data().details[index].price = parseInt( value.price ).format(2, 3, ',', '.');
                    });

                    row.data().credits = parseInt( row.data().credits ).format(2, 3, ',', '.');

                    // Open this row
                    row.child( template(row.data()) ).show();
                    tr.addClass('shown');
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