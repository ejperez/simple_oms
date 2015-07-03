@extends('layout.private')

@section('inner-content')

<div class="row">
    <div class="col-md-12">
        <h3>List of Orders</h3>
    </div>
</div>

<div class="row">
    <div class="col-md-9">
        <table class="table table-condensed table-striped table-selectable table-order-view" cellspacing="0">
            <thead>
                <tr>
                    <th>PO Number
                        <a title="{{ ($sort_column == 'po_number' && $sort_direction == 'desc' ? 'Sort ascending' : 'Sort descending') }}" href="{{ URL::action('OrdersController@index', ['s' => 'po_number', 'd' => ($sort_column == 'po_number' && $sort_direction == 'desc' ? 'asc' : 'desc'), 'f' => Input::get('f') ]) }}"><img src="../build/images/sort_both.png" alt=""/></a>
                    </th>
                    <th>Created Date
                        <a title="{{ ($sort_column == 'created_at' && $sort_direction == 'desc' ? 'Sort ascending' : 'Sort descending') }}" href="{{ URL::action('OrdersController@index', ['s' => 'created_at', 'd' => ($sort_column == 'created_at' && $sort_direction == 'desc' ? 'asc' : 'desc'), 'f' => Input::get('f') ]) }}"><img src="../build/images/sort_both.png" alt=""/></a>
                    </th>
                    <th>Order Date
                        <a title="{{ ($sort_column == 'order_date' && $sort_direction == 'desc' ? 'Sort ascending' : 'Sort descending') }}" href="{{ URL::action('OrdersController@index', ['s' => 'order_date', 'd' => ($sort_column == 'order_date' && $sort_direction == 'desc' ? 'asc' : 'desc'), 'f' => Input::get('f') ]) }}"><img src="../build/images/sort_both.png" alt=""/></a>
                    </th>
                    <th>Pickup Date
                        <a title="{{ ($sort_column == 'pickup_date' && $sort_direction == 'desc' ? 'Sort ascending' : 'Sort descending') }}" href="{{ URL::action('OrdersController@index', ['s' => 'pickup_date', 'd' => ($sort_column == 'pickup_date' && $sort_direction == 'desc' ? 'asc' : 'desc'), 'f' => Input::get('f') ]) }}"><img src="../build/images/sort_both.png" alt=""/></a>
                    </th>
                    <th>Customer
                        <a title="{{ ($sort_column == 'customer' && $sort_direction == 'desc' ? 'Sort ascending' : 'Sort descending') }}" href="{{ URL::action('OrdersController@index', ['s' => 'customer', 'd' => ($sort_column == 'customer' && $sort_direction == 'desc' ? 'asc' : 'desc'), 'f' => Input::get('f') ]) }}"><img src="../build/images/sort_both.png" alt=""/></a>
                    </th>
                    <th>Total Amount ({{ PESO_SYMBOL }})
                        <a title="{{ ($sort_column == 'total_amount' && $sort_direction == 'desc' ? 'Sort ascending' : 'Sort descending') }}" href="{{ URL::action('OrdersController@index', ['s' => 'total_amount', 'd' => ($sort_column == 'total_amount' && $sort_direction == 'desc' ? 'asc' : 'desc'), 'f' => Input::get('f') ]) }}"><img src="../build/images/sort_both.png" alt=""/></a>
                    </th>
                    <th>Status
                        <a title="{{ ($sort_column == 'status' && $sort_direction == 'desc' ? 'Sort ascending' : 'Sort descending') }}" href="{{ URL::action('OrdersController@index', ['s' => 'status', 'd' => ($sort_column == 'status' && $sort_direction == 'desc' ? 'asc' : 'desc'), 'f' => Input::get('f') ]) }}"><img src="../build/images/sort_both.png" alt=""/></a>
                    </th>
                </tr>
            </thead>
            <tfoot>
                <tr>
                    <th colspan="7" class="text-right">
                        {!! $orders->appends(['s' => Input::get('s'), 'd' => Input::get('d'), 'f' => Input::get('f'),])->render() !!}
                        <div>{{ 'Shown: '.(count($orders)) . ' ' . 'Total: '.$orders->total() }}</div>
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
    <div class="col-md-3">
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

@section('js')

    @include('partials.modal')
    @include('partials.order_details_status')

    <script>
        var base_url = document.location.href.split('?')[0];

        // Selectors
        var $apply_filter = $('#apply_filter');

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
        });
    </script>

@endsection('js')

@endsection('content')