@extends('app')

@section('content')
<div class="row">
    <div class="col-md-10">
        <table id="tbl_history" class="table table-condensed table-striped" cellspacing="0">
            <thead>
                <tr>
                    <th>PO Number
                        <a title="Ascending sort" href="{{ URL::action('OrdersController@index', ['s' => 'po_number', 'd' => 'asc', 'f' => Input::get('f') ]) }}"><span class="glyphicon glyphicon-arrow-up"></span></a>
                        <a title="Descending sort" href="{{ URL::action('OrdersController@index', ['s' => 'po_number', 'd' => 'desc', 'f' => Input::get('f')]) }}"><span class="glyphicon glyphicon-arrow-down"></span></a>
                    </th>
                    <th>Created Date
                        <a title="Ascending sort" href="{{ URL::action('OrdersController@index', ['s' => 'created_at', 'd' => 'asc', 'f' => Input::get('f') ]) }}"><span class="glyphicon glyphicon-arrow-up"></span></a>
                        <a title="Descending sort" href="{{ URL::action('OrdersController@index', ['s' => 'created_at', 'd' => 'desc', 'f' => Input::get('f')]) }}"><span class="glyphicon glyphicon-arrow-down"></span></a>
                    </th>
                    <th>Order Date
                        <a title="Ascending sort" href="{{ URL::action('OrdersController@index', ['s' => 'order_date', 'd' => 'asc', 'f' => Input::get('f') ]) }}"><span class="glyphicon glyphicon-arrow-up"></span></a>
                        <a title="Descending sort" href="{{ URL::action('OrdersController@index', ['s' => 'order_date', 'd' => 'desc', 'f' => Input::get('f')]) }}"><span class="glyphicon glyphicon-arrow-down"></span></a>
                    </th>
                    <th>Pickup Date
                        <a title="Ascending sort" href="{{ URL::action('OrdersController@index', ['s' => 'pickup_date', 'd' => 'asc', 'f' => Input::get('f') ]) }}"><span class="glyphicon glyphicon-arrow-up"></span></a>
                        <a title="Descending sort" href="{{ URL::action('OrdersController@index', ['s' => 'pickup_date', 'd' => 'desc', 'f' => Input::get('f')]) }}"><span class="glyphicon glyphicon-arrow-down"></span></a>
                    </th>
                    <th>Customer
                        <a title="Ascending sort" href="{{ URL::action('OrdersController@index', ['s' => 'customer', 'd' => 'asc', 'f' => Input::get('f') ]) }}"><span class="glyphicon glyphicon-arrow-up"></span></a>
                        <a title="Descending sort" href="{{ URL::action('OrdersController@index', ['s' => 'customer', 'd' => 'desc', 'f' => Input::get('f')]) }}"><span class="glyphicon glyphicon-arrow-down"></span></a>
                    </th>
                    <th>Total Amount ({{ Config::get('constants.PESO_SYMBOL') }})
                        <a title="Ascending sort" href="{{ URL::action('OrdersController@index', ['s' => 'total_amount', 'd' => 'asc', 'f' => Input::get('f') ]) }}"><span class="glyphicon glyphicon-arrow-up"></span></a>
                        <a title="Descending sort" href="{{ URL::action('OrdersController@index', ['s' => 'total_amount', 'd' => 'desc', 'f' => Input::get('f')]) }}"><span class="glyphicon glyphicon-arrow-down"></span></a>
                    </th>
                    <th>Status
                        <a title="Ascending sort" href="{{ URL::action('OrdersController@index', ['s' => 'status', 'd' => 'asc', 'f' => Input::get('f') ]) }}"><span class="glyphicon glyphicon-arrow-up"></span></a>
                        <a title="Descending sort" href="{{ URL::action('OrdersController@index', ['s' => 'status', 'd' => 'desc', 'f' => Input::get('f')]) }}"><span class="glyphicon glyphicon-arrow-down"></span></a>
                    </th>
                    <th>Options</th>
                </tr>
            </thead>
            <tfoot>
                <tr>
                    <th colspan="8">
                        <p>
                            {!! $orders->appends(['s' => Input::get('s'), 'd' => Input::get('d'), 'f' => Input::get('f'),])->render() !!}
                            {{ 'Page '.$orders->currentPage().' of '.$orders->lastPage().' ('.$orders->total().' records)' }}
                        </p>
                    </th>
                </tr>
            </tfoot>
            <tbody>
            @foreach ($orders as $order)
                <tr data-order-id="{{ $hashids->encode($order->id) }}" data-po-number="{{ $order->po_number }}">
                    <td>{{ $order->po_number }}</td>
                    <td>{{ $order->created_at }}</td>
                    <td>{{ $order->order_date }}</td>
                    <td>{{ $order->pickup_date }}</td>
                    <td>{{ $order->customer }}</td>
                    <td class="text-right">{{ $order->total_amount }}</td>
                    <td>{{ $order->status }}</td>
                    <td>
                        @if ($order->status == 'Pending')
                            @if ($role == 'Approver')
                                <a href="{{ url('orders/'.$hashids->encode($order->id).'/edit/approver') }}" id="btn_edit" title="Edit"><span class="glyphicon glyphicon-edit"></span></a>
                                <a data-toggle="modal" data-target="#update_order_status_modal" class="btn-approve" data-status="Approved" href="#" title="Approve"><span class="glyphicon glyphicon-ok"></span></a>
                                <a data-toggle="modal" data-target="#update_order_status_modal" class="btn-disapprove" data-status="Disapproved" href="#" title="Disapprove"><span class="glyphicon glyphicon-remove"></span></a>
                            @endif

                            @if ($role == 'Sales' || $role == 'Administrator')
                                <a href="{{ url('orders/'.$hashids->encode($order->id).'/edit') }}" id="btn_edit" title="Edit"><span class="glyphicon glyphicon-edit"></span></a>
                                <a data-toggle="modal" data-target="#update_order_status_modal" class="btn-cancel" data-status="Cancelled" href="#" title="Cancel"><span class="glyphicon glyphicon-remove"></span></a>
                            @endif
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <div class="col-md-2">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">Filters</h3>
            </div>
            <div class="panel-body">
                <label for="">PO Number</label><br/>
                <input type="text" class="form-control" id="po_number" value="{{ isset($filters->po_number) ? $filters->po_number : '' }}"/>
                <label for="">Created Date</label><br/>
                <input type="text" class="form-control" id="created_at" value="{{ isset($filters->created_at) ? $filters->created_at : '' }}"/>
                <label for="">Order Date</label><br/>
                <input type="text" class="form-control" id="order_date" value="{{ isset($filters->order_date) ? $filters->order_date : '' }}"/>
                <label for="">Pickup Date</label><br/>
                <input type="text" class="form-control" id="pickup_date" value="{{ isset($filters->pickup_date) ? $filters->pickup_date : '' }}"/>
                <label for="">Customer</label><br/>
                <input type="text" class="form-control" id="customer" value="{{ isset($filters->customer) ? $filters->customer : '' }}"/>
                <label for="">Status</label><br/>
                <select name="" id="status" multiple class="form-control">
                    @foreach ($status as $value)
                        @if ($value->name == 'Pending')
                            @if ($role == 'Approver' && !isset($filters)))
                                <option selected>{{ $value->name }}</option>
                            @else
                                <option {{ isset($filters->status) && in_array($value->name, $filters->status) ? 'selected' : '' }}>{{ $value->name }}</option>
                            @endif
                        @else
                            <option {{ isset($filters->status) && in_array($value->name, $filters->status) ? 'selected' : '' }}>{{ $value->name }}</option>
                        @endif
                    @endforeach
                </select>
                <br/>
                <a href="" id="apply_filter">Apply filter</a>
                <a href="{{ URL::action('OrdersController@index') }}" id="apply_filter">Clear filter</a>
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
                        <div class="col-md-3"><label>PO Number:</label></div>
                        <div class="col-md-3"><label id="lbl_po_number" style="font-weight:bold"></label></div>
                        <div class="col-md-3"><label>Change status to:</label></div>
                        <div class="col-md-3"><label id="lbl_status" style="font-weight:bold"></label></div>
                        <div class="col-md-12">
                            <label id="lbl_extra" for="extra">Optional message:</label>
                            <textarea class="form-control" name="extra" id="extra" cols="30" rows="10"></textarea>
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
        #tbl_history tr
        {
            cursor:pointer;
        }
        #tbl_history tbody tr:hover
        {
            outline:solid thin #0063dc;
        }
        #tbl_history thead tr
        {
            background:#f5f5f5;
            border:solid thin #ddd;
        }
        #tbl_history thead span.glyphicon
        {
            font-size:0.75em;
        }
        #tbl_history tr.shown td
        {
            background:#76b4ff !important;
        }
        .pagination{
            margin:0;
        }
    </style>
@endsection

@section('js')
    <script>
        var base_url = document.location.href.split('?')[0],
                $update_order_status_form = $('form#update_order_status_form'),
                $apply_filter = $('#apply_filter'),
                $lbl_status = $('label#lbl_status'),
                $lbl_po_number = $('label#lbl_po_number'),
                $lbl_extra = $('label#lbl_extra'),
                $extra = $('#extra');

        $(document).ready(function(){
            // Initialize drop down
            $('#status').select2();

            // Initialize date pickers
            $('#created_at, #order_date, #pickup_date').datepicker({
                disableTouchKeyboard:   true,
                format:                 '{{ Config::get('constants.DATE_FORMAT') }}'
            }).on('changeDate', function(){
                $(this).datepicker('hide');
            });

            // Apply filter link
            $apply_filter.on('click', function(e){
                var f = {
                    po_number: $('#po_number').val(),
                    created_at: $('#created_at').val(),
                    order_date: $('#order_date').val(),
                    pickup_date: $('#pickup_date').val(),
                    customer: $('#customer').val(),
                    status: $('#status').val()
                }
                var url = base_url + '?f=' + encodeURIComponent(JSON.stringify(f));
                $(this).attr('href', url);
            });

            // Update status buttons click event
            $('#tbl_history tbody tr').on('click', 'a.btn-approve, a.btn-disapprove, a.btn-cancel', function(){
                // Update url of form
                var url = '{{ url('orders')  }}/' + $(this).closest('tr').attr('data-order-id') + '/update-{{ Auth::user()->role->name == 'Approver' ? 'approver' : 'customer' }}-status/' + $(this).attr('data-status');
                $update_order_status_form.attr('action', url);

                // Update labels
                $lbl_status.html($(this).attr('data-status'));
                $lbl_po_number.html($(this).closest('tr').attr('data-po-number'));

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
        /*
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
    });
*/
    </script>
@endsection('js')

{{ var_dump(\DB::getQueryLog()) }}

@endsection('content')