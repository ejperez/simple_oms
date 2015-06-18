@extends('app')

@section('content')
<div class="row">
    <div class="col-md-10">
        <table id="tbl_history" class="table table-condensed table-striped table-selectable" cellspacing="0">
            <thead>
                <tr>
                    <th>PO Number
                        <a title="Sort ascending" href="{{ URL::action('OrdersController@index', ['s' => 'po_number', 'd' => 'asc', 'f' => Input::get('f') ]) }}"><span class="glyphicon glyphicon-arrow-up"></span></a>
                        <a title="Sort descending" href="{{ URL::action('OrdersController@index', ['s' => 'po_number', 'd' => 'desc', 'f' => Input::get('f')]) }}"><span class="glyphicon glyphicon-arrow-down"></span></a>
                    </th>
                    <th>Created Date
                        <a title="Sort ascending" href="{{ URL::action('OrdersController@index', ['s' => 'created_at', 'd' => 'asc', 'f' => Input::get('f') ]) }}"><span class="glyphicon glyphicon-arrow-up"></span></a>
                        <a title="Sort descending" href="{{ URL::action('OrdersController@index', ['s' => 'created_at', 'd' => 'desc', 'f' => Input::get('f')]) }}"><span class="glyphicon glyphicon-arrow-down"></span></a>
                    </th>
                    <th>Order Date
                        <a title="Sort ascending" href="{{ URL::action('OrdersController@index', ['s' => 'order_date', 'd' => 'asc', 'f' => Input::get('f') ]) }}"><span class="glyphicon glyphicon-arrow-up"></span></a>
                        <a title="Sort descending" href="{{ URL::action('OrdersController@index', ['s' => 'order_date', 'd' => 'desc', 'f' => Input::get('f')]) }}"><span class="glyphicon glyphicon-arrow-down"></span></a>
                    </th>
                    <th>Pickup Date
                        <a title="Sort ascending" href="{{ URL::action('OrdersController@index', ['s' => 'pickup_date', 'd' => 'asc', 'f' => Input::get('f') ]) }}"><span class="glyphicon glyphicon-arrow-up"></span></a>
                        <a title="Sort descending" href="{{ URL::action('OrdersController@index', ['s' => 'pickup_date', 'd' => 'desc', 'f' => Input::get('f')]) }}"><span class="glyphicon glyphicon-arrow-down"></span></a>
                    </th>
                    <th>Customer
                        <a title="Sort ascending" href="{{ URL::action('OrdersController@index', ['s' => 'customer', 'd' => 'asc', 'f' => Input::get('f') ]) }}"><span class="glyphicon glyphicon-arrow-up"></span></a>
                        <a title="Sort descending" href="{{ URL::action('OrdersController@index', ['s' => 'customer', 'd' => 'desc', 'f' => Input::get('f')]) }}"><span class="glyphicon glyphicon-arrow-down"></span></a>
                    </th>
                    <th>Total Amount ({{ PESO_SYMBOL }})
                        <a title="Sort ascending" href="{{ URL::action('OrdersController@index', ['s' => 'total_amount', 'd' => 'asc', 'f' => Input::get('f') ]) }}"><span class="glyphicon glyphicon-arrow-up"></span></a>
                        <a title="Sort descending" href="{{ URL::action('OrdersController@index', ['s' => 'total_amount', 'd' => 'desc', 'f' => Input::get('f')]) }}"><span class="glyphicon glyphicon-arrow-down"></span></a>
                    </th>
                    <th>Status
                        <a title="Sort ascending" href="{{ URL::action('OrdersController@index', ['s' => 'status', 'd' => 'asc', 'f' => Input::get('f') ]) }}"><span class="glyphicon glyphicon-arrow-up"></span></a>
                        <a title="Sort descending" href="{{ URL::action('OrdersController@index', ['s' => 'status', 'd' => 'desc', 'f' => Input::get('f')]) }}"><span class="glyphicon glyphicon-arrow-down"></span></a>
                    </th>
                </tr>
            </thead>
            <tfoot>
                <tr>
                    <th colspan="7" class="text-right">
                        {!! $orders->appends(['s' => Input::get('s'), 'd' => Input::get('d'), 'f' => Input::get('f'),])->render() !!}
                    </th>
                </tr>
                <tr>
                    <th colspan="7" class="text-right">
                        {{ 'Shown: '.(count($orders)) }}
                        {{ 'Total: '.$orders->total() }}
                    </th>
                </tr>
            </tfoot>
            <tbody>
            @foreach ($orders as $order)
                <tr title="Click to see details" data-order-id="{{ SimpleOMS\Helpers\Helpers::hash($order->id) }}">
                    <td>{{ $order->po_number }}</td>
                    <td>{{ $order->created_at }}</td>
                    <td>{{ $order->order_date }}</td>
                    <td>{{ $order->pickup_date }}</td>
                    <td>{{ $order->customer }}</td>
                    <td class="text-right">{{ number_format($order->total_amount,2) }}</td>
                    <td>{{ $order->status }}</td>
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
                <br/><br/>
                <div class="btn-group-justified" role="group">
                    <a class="btn btn-primary btn-sm" href="#" id="apply_filter">Apply filter</a>
                    <a class="btn btn-default btn-sm" href="{{ URL::action('OrdersController@index') }}" id="apply_filter">Clear filter</a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- The Modal -->
<div id="modal" class="modal fade" role="dialog" data-keyboard="false">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content"></div>
    </div>
</div>

@section('js')
    <script id="update-order-status-template" type="text/x-handlebars-template">
        {!! Form::open(['url' => '', 'name' => 'update_order_status_form', 'id' => 'update_order_status_form', 'method' => 'PUT']) !!}
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">Update Order Status</h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-3">
                    <div class="row">
                        <div class="col-md-12"><label>PO Number:</label></div>
                        <div class="col-md-12"><label style="font-weight:bold">@{{ po_number }}</label></div>
                        <div class="col-md-12"><label>Change Status To:</label></div>
                        <div class="col-md-12"><label style="font-weight:bold">@{{ status }}</label></div>
                    </div>
                </div>
                <div class="col-md-9">
                    <label id="lbl_extra" for="extra">Optional Message:</label>
                    <textarea id="extra" name="extra" class="form-control" cols="30" rows="10"></textarea>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <input type="button" class="btn btn-default btn-back" data-order-id="@{{ id }}" value="Back">
            <input type="submit" class="btn btn-default" value="Confirm">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
        {!! Form::close() !!}
    </script>

    <script id="details-template" type="text/x-handlebars-template">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">Order Details</h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-3">
                    <div class="row">
                        <div class="col-md-12"><label>PO Number:</label></div>
                        <div class="col-md-12"><label style="font-weight:bold">@{{ po_number }}</label></div>
                        <div class="col-md-12"><label>Customer:</label></div>
                        <div class="col-md-12"><label style="font-weight:bold">@{{ customer }}</label></div>
                        <div class="col-md-12"><label>Remaing Credits:</label></div>
                        <div class="col-md-12"><label style="font-weight:bold">{{ PESO_SYMBOL }} @{{ credits }}</label></div>
                        @{{#ifCond updated_by '!=' null}}
                            <div class="col-md-12"><label>Updated By:</label></div>
                            <div class="col-md-12"><label style="font-weight:bold">@{{ updated_by }}</label></div>
                            <div class="col-md-12"><label>Date:</label></div>
                            <div class="col-md-12"><label style="font-weight:bold">@{{ updated_at }}</label></div>
                            <div class="col-md-12"><label>Remarks:</label></div>
                            <div class="col-md-12"><textarea readonly style="resize:none;width:100%" rows="5">@{{ update_remarks }}</textarea></div>
                        @{{/ifCond}}
                    </div>
                </div>
                <div class="col-md-9">
                    <label for="">Order Items:</label>
                    <table class="table">
                        <thead>
                        <th>Product</th>
                        <th>Category</th>
                        <th>UOM</th>
                        <th>Unit Price ({{ PESO_SYMBOL }})</th>
                        <th>Quantity</th>
                        <th>Price ({{ PESO_SYMBOL }})</th>
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
                        <tfoot>
                        <th colspan="6">
                            <p class="text-right" style="font-weight:bold">Total: {{ PESO_SYMBOL }} @{{ total }}</p>
                        </th>
                        </tfoot>
                    </table>
                </div>
            </div>

            <br/>

            @{{#ifCond status '==' 'Cancelled'}}
            <div role="alert" class="alert alert-warning">
                <p>Reason:<br/><strong>@{{ extra }}</strong></p>
            </div>
            @{{/ifCond}}
            @{{#ifCond status '==' 'Disapproved'}}
            <div role="alert" class="alert alert-danger">
                <p>Disapproved by:<br/><strong>@{{ user }}</strong></p>
                <p>Comment:<br/><strong>@{{ extra }}</strong></p>
            </div>
            @{{/ifCond}}
            @{{#ifCond status '==' 'Approved'}}
            <div role="alert" class="alert alert-success">
                <p>Approved by:<br/><strong>@{{ user }}</strong></p>
                <p>Comment:<br/><strong>@{{ extra }}</strong></p>
            </div>
            @{{/ifCond}}
        </div>
        <div class="modal-footer">
            @{{#ifCond status '==' 'Pending'}}
            @if ($role == 'Approver')
                <a href="{{ url('orders') }}/@{{ id }}/edit/approver" id="btn_edit" class="btn btn-default"><span class="glyphicon glyphicon-edit"></span> Edit</a>
                <a class="btn btn-default btn-approve" data-order-id="@{{ id }}" data-po-number="@{{ po_number }}" data-status="Approved" href="#" title="Approve"><span class="glyphicon glyphicon-ok"></span> Approve</a>
                <a class="btn btn-default btn-disapprove" data-order-id="@{{ id }}" data-po-number="@{{ po_number }}" data-status="Disapproved" href="#" title="Disapprove"><span class="glyphicon glyphicon-remove"></span> Disapprove</a>
            @endif

            @if ($role == 'Sales' || $role == 'Administrator')
                <a class="btn btn-default" href="{{ url('orders') }}/@{{ id }}/edit" id="btn_edit" title="Edit"><span class="glyphicon glyphicon-edit"></span> Edit</a>
                <a class="btn btn-default btn-cancel" data-order-id="@{{ id }}" data-po-number="@{{ po_number }}" data-status="Cancelled" href="#" title="Cancel"><span class="glyphicon glyphicon-remove"></span> Cancel</a>
            @endif
            @{{/ifCond}}
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
    </script>

    <script>
        var base_url = document.location.href.split('?')[0],

                // Selectors
                $apply_filter = $('#apply_filter'),

                // Modal
                $modal = $('#modal'),

                // Handlebars templates
                details_template = Handlebars.compile($("#details-template").html()),
                update_order_status_template = Handlebars.compile($("#update-order-status-template").html());

        $(document).ready(function(){
            // Initialize drop down
            $('#status').select2();

            // Initialize date pickers
            $('#created_at, #order_date, #pickup_date').datepicker({
                disableTouchKeyboard:   true,
                format:                 '{{ DATE_FORMAT }}'
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
            $modal.on('click', 'a.btn-approve, a.btn-disapprove, a.btn-cancel', function(){
                var status = $(this).attr('data-status');

                $($modal.find('div.modal-content')[0]).html(update_order_status_template({
                        po_number: $(this).attr('data-po-number'),
                        id: $(this).attr('data-order-id'),
                        status: status
                }));

                var $lbl_extra = $('#lbl_extra'),
                        $extra = $('#extra'),
                        url = '{{ url('orders')  }}/' + $(this).attr('data-order-id') + '/update-{{ $role == 'Approver' ? 'approver' : 'customer' }}-status/' + status;

                // Update url of form
                $('form#update_order_status_form').attr('action', url);

                // If disapproval/cancellation, require reason
                if (status == 'Cancelled' || status == 'Disapproved'){
                    $lbl_extra.html('Reason:').addClass('required');
                    $extra.attr('required', 'required');
                } else {
                    $lbl_extra.html('Optional message:').removeClass('required');
                    $extra.removeAttr('required');
                }
            });

            // Back button
            $modal.on('click', 'input.btn-back', function(){
                showDetailsModal($(this).attr('data-order-id'));
            });

            // View details click event
            $('#tbl_history tbody').on('click', 'tr', function() {
                showDetailsModal($(this).attr('data-order-id'));

                $('.selected').removeClass('selected');
                $(this).addClass('selected');
            });
        });

        function showDetailsModal(id) {
            $.get('{{ url('get-order-details') }}/' + id, {},
                function(data){
                    // Display order details to modal
                    var details = JSON.parse(data);
                    // Add order id of selected tr
                    details.id = id;
                    $($modal.find('div.modal-content')[0]).html(details_template(details));
                    $modal.modal('show');
                }).fail(function(){
                    alert('Failed to get details.');
                });
        }
    </script>
@endsection('js')

@endsection('content')