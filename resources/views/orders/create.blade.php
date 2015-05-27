@extends('app')

@section('content')
    <div class="container">
        <div role="alert" id="alr-order" style="display:none"></div>
        <div class="row">
            <div class="col-md-12">
                <h3>Create Order</h3>
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
                                    <input class="form-control" type="text" id="txt_po_number" maxlength="50"/>
                                </p>
                            </div>
                            <div class="col-md-4">
                                <label for="">Order Date</label>
                                <div class="input-group date">
                                    <input id="txt_order_date" type="text" class="form-control" maxlength="10"><span class="input-group-addon"><i class="glyphicon glyphicon-th"></i></span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label for="">Pick-Up Date</label>
                                <div class="input-group date">
                                    <input id="txt_pickup_date" type="text" class="form-control" disabled="disabled" maxlength="10"/><span class="input-group-addon"><i class="glyphicon glyphicon-th"></i></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading">
                        Shopping Cart
                        <a style="float:right;cursor:pointer" type="button" data-toggle="modal" data-target="#mdl_products">
                            <span class="glyphicon glyphicon-plus"></span>
                            Add item(s)
                        </a>
                    </div>
                    <div class="panel-body">
                        <table id="tbl_cart" class="display" cellspacing="0">
                            <thead>
                            <tr>
                                <th>#</th>
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
                                <label for="" id="lbl_total_amount">0.00</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="btn-group" style="float:right">
                    <button id="btn_submit" class="btn btn-primary"><span class="glyphicon glyphicon-ok"></span> Submit</button>
                    <button id="btn_cancel" class="btn btn-default"><span class="glyphicon glyphicon-remove"></span> Cancel</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="mdl_products" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Add item(s)</h4>
                </div>
                <div class="modal-body">
                     <table id="tbl_products" class="display" cellspacing="0">
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
                        <button type="button" class="btn btn-primary" id="btn_add_product"><span class="glyphicon glyphicon-plus"></span> Add</button>
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
        table.dataTable tr{
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
            txtPONumber:        $('#txt_po_number'),
            txtOrderDate:       $('#txt_order_date'),
            txtPickupDate:      $('#txt_pickup_date'),
            btnSubmit:          $('#btn_submit'),
            btnCancel:          $('#btn_cancel'),
            lblTotalAmount:     $('#lbl_total_amount'),
            altOrder:           $('div#alr-order'),
            init: function() {
                // Initialize datepicker, set minimum date to today
                this.txtOrderDate.datepicker({
                    startDate:              "0d",
                    disableTouchKeyboard:   true,
                    format:                 '{{ Session::get('DATE_FORMAT') }}'
                }).on('changeDate', function(e){
                    var orderDate = new Date(e.target.value);
                    var pickupDate = orderDate.addDays({{ Session::get('PICKUP_DAYS_COUNT') }});
                    order.txtPickupDate.val(pickupDate.format('{{ Session::get('DATE_FORMAT_PHP') }}'));
                });

                // Submit order event
                this.btnSubmit.click(function(){
                    order.altOrder.hide();

                    // Submit order data
                    $.post('{{ url('store-order') }}',{
                        _token:         "{{ csrf_token() }}",
                        po_number:      order.txtPONumber.val(),
                        order_date:     order.txtOrderDate.val(),
                        pickup_date:    order.txtPickupDate.val(),
                        total_amount:   order.lblTotalAmount.html(),
                        items:          cart.items
                    }).done(function (data, status){
                        console.log(data);
                    }).fail(function(jqXHR){
                        console.log(jqXHR);
                        console.log(jqXHR.responseText);
                        if (jqXHR.status == 422)
                        {
                            var message = ''
                            $.each(jqXHR.responseJSON, function (key, value) {
                                message += value + '<br>';
                            });
                        }
                        order.altOrder.attr('class', 'alert alert-warning').html(message).fadeIn();
                    });
                });

                // Cancel order event
                this.btnCancel.click(function(){
                    // Reload page
                    window.location.reload();
                });
            },
            setTotalAmount: function(value){
                this.lblTotalAmount.html(value.toFixed(2));
            }
        };

        // handles searching and adding of products to shopping cart
        var products = {
            datatable:              null,
            mdlProducts:            $('#mdl_products'),
            tblProducts:            $('#tbl_products'),
            btnAddProduct:          $('#btn_add_product'),
            init: function(){
                // Initialize datatable on first appearance of modal, to fix width issue
                this.mdlProducts.on('shown.bs.modal', function() {
                    if (products.datatable == null){
                        products.initDatatable();
                    } else {
                        products.datatable.ajax.reload();
                    }
                })
            },
            initDatatable: function(){
                // Initialize products table
                this.datatable = $(this.tblProducts).DataTable( {
                    processing: true,
                    serverSide: true,
                    ajax: "{{ url('get-products-datatable') }}",
                    scrollX: true,
                    scrollY: 300,
                    columns: [
                        {data: "category.desc"},
                        {data: "code"},
                        {data: "desc"},
                        {data: "price"},
                        {data: "uom"},
                        {data: "available"}
                    ]
                } );

                // Disable automatic searching every keypress, wait for ENTER key instead
                $('#tbl_products_filter input').unbind().bind('keyup', function(e) {
                    if(e.keyCode == 13) {
                        products.datatable.search(this.value).draw();
                    }
                });

                // Highlight product selected by user
                this.tblProducts.on( 'click', 'tbody tr', function () {
                    if ( $(this).hasClass('selected') ) {
                        $(this).removeClass('selected');
                    }
                    else {
                        $(this).addClass('selected');
                    }
                } );

                // Add product to shopping cart
                this.btnAddProduct.click(function(){
                    var selectedItems = products.getSelected();
                    if (selectedItems.length > 0){
                        cart.addItems(selectedItems);
                        cart.updateDatatable();
                        products.clearSelected();
                    } else {
                        alert('Please select at least one item!');
                    }
                });
            },
            getSelected: function(){
                var outputArray = [],
                    selected = $('#tbl_products tbody tr.selected');

                selected.each(function(index, value){
                    var details = $('tr#' + $(value).attr('id')).children('td');
                    outputArray.push({
                        id:         parseInt($(value).attr('id')),
                        category:   $(details[0]).html(),
                        code:       $(details[1]).html(),
                        desc:       $(details[2]).html(),
                        price:      parseFloat($(details[3]).html()),
                        uom:        $(details[4]).html(),
                        available:  parseInt($(details[5]).html()),
                        quantity:   0
                    });
                });

                return outputArray;
            },
            clearSelected: function(){
                var selected = $('#tbl_products tbody tr.selected');
                $(selected).each(function(){
                    $(this).removeClass('selected');
                });
            }
        };

        var cart = {
            datatable: null,
            items: {},
            tblCart: $('#tbl_cart'),
            init: function(){
                // Initialize products table
                this.datatable = this.tblCart.DataTable({
                    scrollX: true,
                    paging: false,
                    searching: false
                });

                // Disable automatic searching every keypress, wait for ENTER key instead
                $('#tbl_cart_filter input').unbind().bind('keyup', function(e) {
                    if(e.keyCode == 13) {
                        cart.datatable.search(this.value).draw();
                    }
                });

                // Highlight product selected by user
                this.tblCart.on( 'click', 'tbody tr', function () {
                    if ( $(this).hasClass('selected') ) {
                        $(this).removeClass('selected');
                    }
                    else {
                        $(this).addClass('selected');
                    }
                } );

                // Quantity update event
                this.tblCart.on('focusout', 'input.num-quantity', function(){
                    var quantity = $(this).val(),
                        available = $(this).attr('data-available'),
                        id = $(this).attr('data-id');

                    // Remove non-numeric characters
                    quantity = parseValue('integer-unsigned', quantity);

                    // Limit ordered quantity to available quantity
                    if (quantity > available){
                        quantity = available;
                    }

                    // Update item quantity
                    cart.items[id].quantity = quantity;

                    // Reload datatable
                    cart.updateDatatable();

                    // Update total amount
                    order.setTotalAmount(cart.computeTotal());
                });
            },
            computeTotal: function(){
                var keys = Object.keys(cart.items),
                    total = 0;
                $(keys).each(function(index, key) {
                    var item = cart.items[key];
                    total += item.quantity * item.price;
                });

                return total;
            },
            addItems: function(newItems){
                $(newItems).each(function(index, value){
                    // Ignore repeated items
                    if (cart.items[value['id']] == undefined){
                        cart.items[value['id']] = value;
                    }
                });
            },
            updateDatatable: function(){
                cart.datatable.clear();
                var keys = Object.keys(cart.items),
                    ctr = 1;
                $(keys).each(function(index, key){
                    var item = cart.items[key];
                    cart.datatable.row.add([
                        ctr++,
                        item.category,
                        item.code,
                        item.desc,
                        item.price,
                        item.uom,
                        item.available,
                        '<input type="number" class="num-quantity" data-id="' + item.id + '" data-available="' + item.available + '" value="' + item.quantity + '"/>'
                    ]);
                });

                cart.datatable.draw();
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