@extends('app')

@section('content')
<div class="row">
    <div class="col-md-9">
        <table id="tbl_history" class="table table-condensed display" cellspacing="0">
            <thead>
                <tr>
                    <th class="text">PO Number</th>
                    <th class="date">Created Date</th>
                    <th class="date">Order Date</th>
                    <th class="date">Pickup Date</th>
                    <th class="text">Customer</th>
                    <th class="number">Total Amount ({{ Config::get('constants.PESO_SYMBOL') }})</th>
                    <th class="status">Status</th>
                </tr>
            </thead>
        </table>
    </div>
    <div class="col-md-3">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">Filters</h3>
            </div>
            <div class="panel-body" id="filters">
            </div>
        </div>
    </div>
</div>


<!-- Modal -->
<div id="update_order_status_modal" class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            {!! Form::open(['url' => '', 'name' => 'update_order_status_form', 'id' => 'update_order_status_form', 'method' => 'PUT']) !!}
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Confirm Change of Order Status</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <label>PO Number:</label>
                            <label id="lbl_po_number" style="font-weight:bold"></label><br/>
                            <label>Change status to:</label>
                            <label id="lbl_status" style="font-weight:bold"></label><br/>
                            <label id="lbl_extra" for="extra">Optional message:</label>
                            <textarea class="form-control" name="extra" id="extra" cols="30" rows="5"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="submit" class="btn btn-default" id="btn_confirm" value="Confirm">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            {!! Form::close() !!}
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
        <div role="alert" class="alert alert-default">
            <p>Updated by:<br/><strong>@{{ updated_by }}</strong></p>
            <p>Date:<br/><strong>@{{ updated_date }}</strong></p>
            <p>Comment:<br/><em>@{{ update_remarks }}</em></p>
        </div>
        @{{#ifCond status '==' 'Cancelled'}}
        <div role="alert" class="alert alert-warning">
            <p>Date:<br/><strong>@{{ change_status_date }}</strong></p>
            <p>Reason:<br/><em>@{{ extra }}</em></p>
        </div>
        @{{/ifCond}}
        @{{#ifCond status '==' 'Disapproved'}}
        <div role="alert" class="alert alert-danger">
            <p>Disapproved by:<br/><strong>@{{ user }}</strong></p>
            <p>Date:<br/><strong>@{{ change_status_date }}</strong></p>
            <p>Comment:<br/><em>@{{ extra }}</em></p>
        </div>
        @{{/ifCond}}
        @{{#ifCond status '==' 'Approved'}}
        <div role="alert" class="alert alert-success">
            <p>Approved by:<br/><strong>@{{ user }}</strong></p>
            <p>Date:<br/><strong>@{{ change_status_date }}</strong></p>
            <p>Comment:<br/><em>@{{ extra }}</em></p>
        </div>
        @{{/ifCond}}
        @{{#ifCond status '==' 'Pending'}}
        <table class="table">
            <caption><strong>Options</strong></caption>
            <tr>
                <td>
                    @if ($role == 'Approver')
                        <p><label>Customer Credits ({{ Config::get('constants.PESO_SYMBOL') }}) : </label> <strong>@{{ credits_formatted }}</strong></p>
                        <a class="btn btn-default" href="{{ url('orders') }}/@{{ id }}/edit/approver" id="btn_edit" title="Edit"><span class="glyphicon glyphicon-edit"></span> Edit</a>
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
        @{{/ifCond}}
    </script>

    <script>
        var template = Handlebars.compile($("#details-template").html());

        var $tbl_history = $('#tbl_history'),
                $update_order_status_form = $('form#update_order_status_form'),

                $sel_status = null,

                $update_order_status_modal = $('div#update_order_status_modal'),
                $lbl_status = $('label#lbl_status'),
                $lbl_po_number = $('label#lbl_po_number'),
                $lbl_extra = $('label#lbl_extra'),
                $extra = $('#extra'),

                dt_history = null,
                base_dt_ajax = "{{ url('get-orders-datatable') }}";

        $(document).ready(function() {
            // Initialize history datatable
            dt_history = $tbl_history.DataTable({
                processing: true,
                serverSide: true,
                displayLength: 10,
                @if ($role == 'Approver')
                    ajax: base_dt_ajax + '?status[]=Pending',
                @else
                    ajax: base_dt_ajax,
                @endif
                lengthChange: false,
                searchDelay: 400,
                scrollX: true,
                columns: [
                    {data: "po_number"},
                    {data: "created_date"},
                    {data: "order_date"},
                    {data: "pickup_date"},
                    {data: "customer"},
                    {data: "total_amount"},
                    {data: "status"}
                ],
                order: [[1, 'desc']],
                initComplete: function () {
                    var $filters = $('div#filters');

                    // Add search box per column
                    this.api().columns().every(function () {
                        var column = this,
                                $input = null,
                                $header = $(column.header());

                        // Create corresponding input type for each column
                        if ($header.hasClass('text')){
                            $input = $(document.createElement('input'));

                            $filters.append($header.html());
                            $input.appendTo($filters)
                                    .on('change', function () {
                                        var val = $.fn.dataTable.util.escapeRegex($(this).val());
                                        column.search(val ? val : '', true, false).draw();
                                    });
                        } else if ($header.hasClass('number')){
                            $input = $(document.createElement('input'));

                            $filters.append($header.html());
                            $input.appendTo($filters)
                                    .on('change', function () {
                                        var val = $(this).val();

                                        if (val != ''){
                                            if (val <= 0){
                                                alert('Amount must be more than zero.');
                                                return false;
                                            }

                                            if (isNaN(val)){
                                                alert('Please enter numeric values only.');
                                                return false;
                                            }
                                        }

                                        column.search(val ? val : '', true, false).draw();
                                    });
                        } else if ($header.hasClass('date')){
                            $input = $(document.createElement('input'));

                            $filters.append($header.html());
                            $input.appendTo($filters)
                                    .on('change', function () {
                                        var val = $(this).val();

                                        column.search(val ? val : '', true, false).draw();
                                    });

                            $input.datepicker({
                                disableTouchKeyboard:   true,
                                format:                 '{{ Config::get('constants.DATE_FORMAT') }}'
                            }).on('changeDate', function(){
                                $(this).datepicker('hide');
                            });
                        } else if ($header.hasClass('status')){
                            $input = $(document.createElement('select'));
                            $input.attr('multiple', 'multiple');
                            $input.css('width', '100%');

                            @foreach ($status as $value)
                                var option = document.createElement("option");
                                option.text = "{{ $value->name }}";
                                @if ($role == 'Approver')
                                    @if ($value->name == 'Pending')
                                        // For approver, pending orders are displayed first
                                        option.selected = true;
                                    @endif
                                @endif
                                $input.append(option);
                            @endforeach

                            $filters.append($header.html());
                            $input.appendTo($filters)
                                    .on('change', function (e) {
                                        // Modify ajaxurl of datatable
                                        var url = base_dt_ajax + '?';

                                        $($(this).val()).each(function(index, value){
                                            url += 'status[]=' + value + '&';
                                        });

                                        dt_history.ajax.url(url);
                                        dt_history.ajax.reload();
                                    });

                            // Initialize drop down
                            $input.select2();
                        }

                        // Adjust input width
                        $input.addClass('form-control');
                    });
                }
            });

            // Add option buttons
            dt_history.on( 'draw.dt', function () {
                // Format total amount
                $('#tbl_history tbody td:nth-child(6)').each(function(){
                    $(this).html( parseInt($(this).html()).format(2, 3, ',', '.') );
                    $(this).addClass('text-right');
                });

                // Hide top search box
                $('#tbl_history_filter').remove();
            });

            // Add event listener for opening and closing details
            $tbl_history.find('tbody').on('click', 'tr[id]', function () {
                var tr = $(this).closest('tr');
                var row = dt_history.row( tr );

                if ( row.child.isShown() ) {
                    // This row is already open - close it
                    row.child.hide();
                    tr.removeClass('shown');
                }
                else {
                    // Format unit price, price, credits
                    $.each(row.data().details, function(index, value){
                        row.data().details[index].unit_price = parseInt( value.unit_price ).format(2, 3, ',', '.');
                        row.data().details[index].price = parseInt( value.price ).format(2, 3, ',', '.');
                    });

                    // Format credits
                    row.data().credits_formatted = parseInt( row.data().credits ).format(2, 3, ',', '.');

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

                // If disapproval/cancellation, require reason
                if ($(this).attr('data-status') == 'Cancelled' || $(this).attr('data-status') == 'Disapproved'){
                    $lbl_extra.html('Reason:').addClass('required');
                    $extra.attr('required', 'required');
                } else {
                    $lbl_extra.html('Optional message:').removeClass('required');
                    $extra.removeAttr('required');
                }
            });
    });

    </script>
@endsection('js')
@endsection('content')