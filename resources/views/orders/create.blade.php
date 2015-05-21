@extends('app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h3>
                    Create Order
                    <button style="float:right" type="button" class="btn btn-default" data-toggle="modal" data-target="#myModal">
                        <span class="glyphicon glyphicon-plus"></span>
                        Add item(s)
                    </button>
                </h3>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-4">
                                <label for="">PO Number</label>
                                <p>
                                    <input class="form-control" type="text" id="po_number" value="{{ $po_number }}"/>
                                </p>
                            </div>
                            <div class="col-md-4">
                                <label for="">Order Date</label>
                                <div class="input-group date">
                                    <input id="order_date" type="text" class="form-control" value="{{ $order_date }}"><span class="input-group-addon"><i class="glyphicon glyphicon-th"></i></span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label for="">Pick-Up Date</label>
                                <div class="input-group date">
                                    <input id="pickup_date" type="text" class="form-control" disabled="disabled" value="{{ $pickup_date  }}"/><span class="input-group-addon"><i class="glyphicon glyphicon-th"></i></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading">Shopping Cart</div>
                    <div class="panel-body">
                        <table id="cart_table" class="display" cellspacing="0">
                            <thead>
                            <tr>
                                <th>Category</th>
                                <th>Code</th>
                                <th>Description</th>
                                <th>Price ({{ Session::get('CURRENCY_SYMBOL') }})</th>
                                <th>UOM</th>
                                <th>Available</th>
                                <th>Quantity</th>
                            </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                        <br>
                        <div class="panel panel-info">
                            <div class="panel-heading text-right">
                                <label for="">Total: {{ Session::get('CURRENCY_SYMBOL') }}</label>
                                <label for="" id="total_amount">0.00</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="btn-group" style="float:right">
                    <button id="submit_order" class="btn btn-primary"><span class="glyphicon glyphicon-ok"></span> Submit</button>
                    <button id="cancel_order" class="btn btn-secondary"><span class="glyphicon glyphicon-remove"></span> Cancel</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade bs-example-modal-lg" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Add item(s)</h4>
                </div>
                <div class="modal-body">
                     <table id="products_table" class="display" cellspacing="0">
                         <thead>
                         <tr>
                             <th>Category</th>
                             <th>Code</th>
                             <th>Description</th>
                             <th>Price ({{ Session::get('CURRENCY_SYMBOL') }})</th>
                             <th>UOM</th>
                             <th>Available</th>
                         </tr>
                         </thead>
                         <tbody></tbody>
                     </table>
                </div>
                <div class="modal-footer">
                    <div class="btn-group">
                        <button type="button" class="btn btn-primary" id="add_product"><span class="glyphicon glyphicon-plus"></span> Add</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @section('css')
    {!! Html::style('plugins/bootstrap-datepicker/datepicker.min.css') !!}
    {!! Html::style('plugins/datatables/media/css/jquery.dataTables.min.css') !!}
    <style>
        #cart_table tr, #products_table tr{
            cursor: pointer;
        }
    </style>
    @endsection('css')

    @section('js')
    {!! Html::script('plugins/bootstrap-datepicker/bootstrap-datepicker.min.js') !!}
    {!! Html::script('plugins/datatables/media/js/jquery.dataTables.min.js') !!}
    <script>
        // map order model to controls
        var order = {
            po_number: $('#po_number'),
            order_date: $('#order_date'),
            pickup_date: $('#pickup_date'),
            init: function() {
                // Initialize datepicker, set minimum date to today
                this.order_date.datepicker({
                    startDate: new Date(),
                    disableTouchKeyboard: true,
                    format: '{{ Session::get('DATE_FORMAT') }}'
                }).on('changeDate', function(e){
                    var order_date = new Date(e.target.value);
                    var pickup_date = order_date.addDays({{ Session::get('PICKUP_DAYS_COUNT') }});
                    order.pickup_date.val(pickup_date.format('{{ Session::get('DATE_FORMAT_PHP') }}'));
                });
            },
            toJSON: function(){
                return {
                    po_number: this.po_number.val(),
                    order_date: this.order_date.val(),
                    pickup_date: this.pickup_date.val()
                };
            }
        };

        // handles searching and adding of products to shopping cart
        var products = {
            selected_products: null,
            datatable: null,
            init: function(){
                // Initialize products table
                this.datatable = $('#products_table').DataTable( {
                    "processing": true,
                    "serverSide": true,
                    "ajax": "{{ url('get-products') }}",
                    "scrollX": true,
                    "scrollY": 300,
                    "columns": [
                        {"data": "category"},
                        {"data": "code"},
                        {"data": "desc"},
                        {"data": "price"},
                        {"data": "uom"},
                        {"data": "available"}
                    ]
                } );

                // Disable automatic searching every keypress, wait for ENTER key instead
                $('#products_table_filter input').unbind().bind('keyup', function(e) {
                    if(e.keyCode == 13) {
                        products.datatable.search(this.value).draw();
                    }
                });

                // Highlight product selected by user
                $(document).on( 'click', '#products_table tbody tr', function () {
                    if ( $(this).hasClass('selected') ) {
                        $(this).removeClass('selected');
                    }
                    else {
                        $(this).addClass('selected');
                    }
                } );

                // Add product to shopping cart (server)
                $('#add_product').click(function(){
                    if (products.updateSelectedProducts()){
                        $.post('{{ url('store-product-item') }}',{
                            "_token":   "{{ csrf_token() }}",
                            "order":    order.toJSON(),
                            "products":  products.toJSON()
                        }, function (data, status){
                            console.log("Data: " + data + "\nStatus: " + status);

                            // Refresh cart table
                            cart.datatable.ajax.reload();
                        });
                    } else {
                        alert('No selected item');
                    }
                });
            },
            updateSelectedProducts: function(){
                this.clearSelected();
                var selected = $('#products_table tbody tr.selected');
                if (selected.length > 0){
                    selected.each(function(index, value){
                        var details = $('tr#' + $(value).attr('id')).children('td');
                        products.selected_products.push({
                            id:         $(value).attr('id'),
                            category:   $(details[0]).html(),
                            code:       $(details[1]).html(),
                            desc:       $(details[2]).html(),
                            price:      $(details[3]).html(),
                            uom:        $(details[4]).html(),
                            available:  $(details[5]).html(),
                            quantity:   1
                        });
                    });

                    return true;
                } else {
                    return false;
                }
            },
            clearSelected: function(){
                products.selected_products = [];
            },
            toJSON: function(){
                return this.selected_products;
            }
        };

        var cart = {
            datatable: null,
            init: function(){
                // Initialize products table
                this.datatable = $('#cart_table').DataTable( {
                    "processing": true,
                    "serverSide": true,
                    "ajax": "{{ url('get-cart') }}",
                    "scrollX": true,
                    "scrollY": 300,
                    "columns": [
                        {"data": "category"},
                        {"data": "code"},
                        {"data": "desc"},
                        {"data": "price"},
                        {"data": "uom"},
                        {"data": "available"},
                        {"data": "quantity"}
                    ]
                } );

                // Disable automatic searching every keypress, wait for ENTER key instead
                $('#cart_table_filter input').unbind().bind('keyup', function(e) {
                    if(e.keyCode == 13) {
                        cart.datatable.search(this.value).draw();
                    }
                });

                // Highlight product selected by user
                $(document).on( 'click', '#cart_table tbody tr', function () {
                    if ( $(this).hasClass('selected') ) {
                        $(this).removeClass('selected');
                    }
                    else {
                        $(this).addClass('selected');
                    }
                } );
            }
        };

        $(document).ready(function(){
            order.init();
            products.init();
            cart.init();
        });
    </script>
    @endsection('js')
@endsection('content')