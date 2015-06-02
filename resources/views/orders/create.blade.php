@extends('app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h3>Create Order</h3>
        </div>
    </div>

    @include('alerts')

    {!! Form::open(['url' => url('orders'), 'name' => 'create_order_form', 'id' => 'create_order_form']) !!}
    <div class="row">
        <div class="col-md-3">
            <fieldset>
                <legend>Details</legend>

                <div class="row">
                    <div class="col-md-12">
                        <label for="po_number" class="required">PO Number</label>
                        <input class="form-control" maxlength="50" type="text" name="po_number" id="po_number" value="{{ Form::old('po_number') }}" required/>
                    </div>
                    <div class="col-md-12">
                        <label for="order_date" class="required">Order Date</label>
                        <div class="input-group date">
                            <input class="form-control" maxlength="10" type="text" name="order_date" id="order_date" value="{{ Form::old('order_date') }}" required/><span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <label for="pickup_date" class="required">Pickup Date</label>
                        <div class="input-group date">
                            <input class="form-control" maxlength="10" type="text" name="pickup_date" id="pickup_date" value="{{ Form::old('pickup_date') }}" required readonly/><span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
                        </div>
                    </div>
                    <div class="col-md-3">&nbsp;</div>
                </div>
            </fieldset>
        </div>
        <div class="col-md-9">
            <fieldset>
                <legend>Items</legend>

                <div class="panel panel-default">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-4">
                                <label for="category">Category</label>
                                <select class="form-control"id="category">
                                    <option value=""></option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="product">Product</label>
                                <select class="form-control" id="product"></select>
                            </div>
                            <div class="col-md-4">
                                <br/>
                                <a class="btn btn-default btn-md" id="btn_add_product"><span class="glyphicon glyphicon-plus"></span> Add</a>
                                <a class="btn btn-default btn-md" id="btn_remove_product"><span class="glyphicon glyphicon-remove"></span> Remove</a>
                            </div>
                        </div>
                    </div>
                </div>

                <table id="tbl_cart" class="display" cellspacing="0">
                    <thead>
                    <tr>
                        <th>DESCRIPTION</th>
                        <th>CATEGORY</th>
                        <th>U/M</th>
                        <th>UNIT PRICE ({{ Session::get('PESO_SYMBOL') }})</th>
                        <th>QUANTITY</th>
                        <th>PRICE ({{ Session::get('PESO_SYMBOL') }})</th>
                    </tr>
                    </thead>
                    <tbody></tbody>
                </table>

                <div id="div_total_amount" class="panel panel-info">
                    <div class="panel-heading text-right">
                        <label for="">Total: {{ Session::get('PESO_SYMBOL') }}</label>
                        <label for="" id="lbl_total_amount">0.00</label>
                    </div>
                </div>
            </fieldset>
        </div>
    </div>





        <div class="row">
            <div class="col-md-12 text-right">
                <input class="btn btn-primary" type="submit"/>
            </div>
        </div>

    <input type="hidden" name="cart_table_data" id="cart_table_data" value="{{ Form::old('cart_table_data') }}"/>
    {!! Form::close() !!}
</div>

    @section('css')
    <style>
        table.dataTable tr{
            cursor: pointer;
        }
        fieldset{
            margin-top:2em;
        }
        div#div_total_amount label{
            font-size:1.5em;
            font-weight:bold;
        }
    </style>
    @endsection('css')

    @section('js')
    <script id="options-template"  type="text/x-handlebars-template">
        @{{#each products }}
        <option value="@{{ id }}">@{{ name }}</option>
        @{{/each }}
    </script>

    <script id="cart-row-template"  type="text/x-handlebars-template">
        @{{#each items }}
        <tr id="@{{ id }}" class="@{{#oddEven @index}}@{{/oddEven}}">
            <td>@{{ name }}</td>
            <td>@{{ category }}</td>
            <td>@{{ uom }}</td>
            <td>@{{ unit_price }}</td>
            <td>
                <input data-unit-price="@{{ unit_price }}" type="number" name="quantity[]" class="quantity" value="@{{ quantity }}"/>
                <input type="hidden" name="product[]" value="@{{ id }}"/>
                <input type="hidden" name="unit_price[]" value="@{{ unit_price }}"/>
            </td>
            <td class="price">0.00</td>
        </tr>
        @{{/each }}
    </script>
        
    <script>
        // Handlebars templates
        var options_template = Handlebars.compile($("#options-template").html()),
                cart_row_template = Handlebars.compile($("#cart-row-template").html());
        
        var dt_cart = null,
                products_data = {},
                cart_data = {},

                // jQuery selectors
                $order_date = $('input#order_date'),
                $pickup_date = $('input#pickup_date'),

                $tbl_cart = $('#tbl_cart'),

                $product = $('select#product'),
                $category = $('select#category'),

                $btn_add_product = $('a#btn_add_product'),
                $btn_remove_product = $('a#btn_remove_product'),

                $lbl_total_amount = $('label#lbl_total_amount'),

                $create_order_form = $('form#create_order_form');

        $(document).ready(function(){
            // Initialize datepicker, set minimum date to today
            $order_date.datepicker({
                startDate:              "0d",
                disableTouchKeyboard:   true,
                format:                 '{{ Session::get('DATE_FORMAT') }}'
            }).on('change', function(){
                var pickup_date = new Date($(this).val());
                pickup_date = pickup_date.addDays({{ Session::get('PICKUP_DAYS_COUNT') }});
                $pickup_date.val(pickup_date.format('{{ Session::get('DATE_FORMAT_PHP') }}'));
            });

            // Initialize products select
            $product.select2();

            // Initialize cart datatable
            dt_cart = $tbl_cart.DataTable({
                scrollX: true,
                paging: false,
                searching: false
            });

            // Changing of category
            $category.on('change', function(){
                if ($(this).val() != ''){
                    $.get('{{ url('search-products-by-category') }}/' + $(this).val(), {},
                            function(data){
                                var category = JSON.parse(data);

                                // Store product details in memory
                                $(category.products).each(function(index, value){
                                    if (!products_data.hasOwnProperty(value.id)){
                                        products_data[value.id] = {
                                            name: value.name,
                                            id: value.id,
                                            uom: value.uom,
                                            unit_price: value.unit_price,
                                            category: category.name,
                                            quantity: 0
                                        };
                                    }
                                });

                                $product.html(options_template(category));
                            }).fail(function(){
                                alert('Failed to get products.');
                            });
                }
            });

            // Add product click event
            $btn_add_product.click(function(){
                var curr_product = products_data[$product.select2('data')[0].id];

                // Add to cart data
                if (curr_product != null){
                    // Check if product is already added
                    if (cart_data.hasOwnProperty(curr_product.id)){
                        alert('Product already added.');
                    } else {
                        cart_data[curr_product.id] = {
                            name: curr_product.name,
                            id: curr_product.id,
                            uom: curr_product.uom,
                            unit_price: curr_product.unit_price,
                            category: curr_product.category,
                            quantity: curr_product.quantity
                        };
                    }
                }

                drawCartTable();
            });

            // Update of quantity
            $tbl_cart.on('focusout', 'input.quantity', function(){
                // Parse value to integer
                var value = $(this).val();
                value = isNaN(parseInt(value)) ? 0 : parseInt(value);
                $(this).val(value);

                computeAndDisplayTotal();
            });

            // Highlight product selected by user
            $tbl_cart.on( 'click', 'tbody tr', function () {
                if ( $(this).hasClass('selected') ) {
                    $(this).removeClass('selected');
                }
                else {
                    $(this).addClass('selected');
                }
            });

            // Delete product click event
            $btn_remove_product.click(function(){
                // Delete selected products from cart
                $tbl_cart.find('tr.selected').each(function(){
                    delete cart_data[$(this).attr('id')];
                });

                drawCartTable();
            });

            // Store cart data to hidden field
            $create_order_form.submit(function(){
                $('input#cart_table_data').val(JSON.stringify(cart_data));
            });

            @if (Form::old('cart_table_data'))
            cart_data = {!! Form::old('cart_table_data') !!};
            drawCartTable();
            computeAndDisplayTotal();
            @endif
        });

        // Display cart items to table
        function drawCartTable(){
            // Convert to array
            var items = [];

            $.map(cart_data, function(value, index){
                items.push(value);
            });

            $tbl_cart.find('tbody').html(cart_row_template({items: items}));
        }

        function computeAndDisplayTotal(){
            var total = 0;

            // Compute subtotal for each row of cart table
            $tbl_cart.children('tbody').children('tr').each(function(){
                var $quantity = $(this).find('input.quantity:first'),
                        $price = $(this).find('td.price:first'),
                        quantity = parseInt($quantity.val()),
                        unit_price = parseFloat($quantity.attr('data-unit-price')),
                        price = quantity * unit_price;

                // Add to grand total
                total += price;

                $price.html(price.toFixed(2));
            });

            $lbl_total_amount.html(total.toFixed(2));
        }
    </script>
    @endsection('js')
@endsection('content')