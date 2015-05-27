@extends('app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h3>Transactions</h3>
        </div>
    </div>
    <div class="row">
        <div class="col-md-3">
            <div class="panel panel-default">
                <div class="panel-heading">Summary</div>
                <div class="panel-body">
                    <table class="table">
                        <thead>
                        <tr>
                            <th>Status</th>
                            <th>Count</th>
                        </tr>
                        </thead>
                        <tfoot></tfoot>
                        <tbody>
                            <?php $all = 0; ?>
                            @foreach ($summary as $item)
                                <tr>
                                    <td class="{{ $item['css_class'] }}">{{ $item['name'] }}</td>
                                    <td>{{ $item['count'] }}</td>
                                </tr>
                                <?php $all += $item['count']; ?>
                            @endforeach
                            <tr>
                                <td>All</td>
                                <td><?php echo $all; ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <br>
            <a class="btn btn-default" href="{{ url('create-order') }}"><span class="glyphicon glyphicon-pencil"></span> Create Order</a>
        </div>
        <div class="col-md-9">
            <div class="panel panel-default">
                <div class="panel-heading">Order History</div>
                <div class="panel-body">
                    <table id="tbl_history" class="display" cellspacing="0">
                        <thead>
                        <tr>
                            <th></th>
                            <th>PO Number</th>
                            <th>Order Date</th>
                            <th>Pickup Date</th>
                            <th>Total Amount ({{ Session::get('CURRENCY_SYMBOL') }})</th>
                            <th>Status</th>
                            <th>Options</th>
                        </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@section('css')
    {!! Html::style('plugins/datatables/media/css/jquery.dataTables.min.css') !!}
    <style>
        table.dataTable tr
        {
            cursor: pointer;
        }
        @foreach ($summary as $item)
        table tr.{{ $item['css_class'] }} td:nth-last-child(2), table tr td.{{ $item['css_class'] }}
        {
            color:{{ $item['html_color'] }};
        }
        @endforeach
        tr.selected td:nth-last-child(2)
        {
            color:#000 !important;
        }
    </style>
@endsection('css')

@section('js')
    {!! Html::script('plugins/datatables/media/js/jquery.dataTables.min.js') !!}
    {!! Html::script('plugins/handlebars/handlebars.min.js') !!}

    <!-- Option Control template -->
    <script id="options-template"  type="text/x-handlebars-template">
        @{{#hasClass class 'pending'}}
        <a href="#" title="Approve"><span class="glyphicon glyphicon-ok"></span></a>
        <a href="#" title="Disapprove"><span class="glyphicon glyphicon-remove"></span></a>
        <a href="{{ url('orders') }}/@{{ id }}/edit" id="btn_edit" title="Edit"><span class="glyphicon glyphicon-edit"></span></a>
        @{{/hasClass}}
    </script>

    <!-- Order Details template -->
    <script id="details-template" type="text/x-handlebars-template">
        <table class="table">
            <tr>
                <td colspan="4">
                    <table class="table">
                        <caption><strong>Order Details</strong></caption>
                        <thead>
                            <th>Product</th>
                            <th>Category</th>
                            <th>Price ({{ Session::get('CURRENCY_SYMBOL') }})</th>
                            <th>UOM</th>
                            <th>Quantity</th>
                        </thead>
                        <tbody>
                            @{{#each details}}
                                <tr class="@{{#oddEven @index}}@{{/oddEven}}">
                                    <td>@{{ product.desc }}</td>
                                    <td>@{{ product.category.desc }}</td>
                                    <td>@{{ product.price }}</td>
                                    <td>@{{ product.uom }}</td>
                                    <td>@{{ quantity }}</td>
                                </tr>
                            @{{/each}}
                        </tbody>
                    </table>
                </td>
            </tr>
            <tr>
                <td><strong>Created on:</strong></td>
                <td>@{{ created_at }}</td>
            </tr>
            <tr>
                <td><strong>Customer:</strong></td>
                <td>@{{ user.name }}</td>
            </tr>
        </table>
    </script>

    <script>
        var tblHistory = $('#tbl_history'),
            historyDatatable = null,
            selectedRow = null,
            btnEdit = $('#btn_edit');

        // Handlebars templates
        var template = Handlebars.compile($("#details-template").html()),
            options = Handlebars.compile($("#options-template").html());

        // Checking if class name exists
        Handlebars.registerHelper('hasClass', function (subject, testClass, options) {
            return (subject.indexOf(testClass) != -1) ? options.fn(this) : options.inverse(this);
        });

        // For striping table rows
        Handlebars.registerHelper('oddEven', function(index) {
            return index % 2 == 0 ? 'even' : 'odd';
        });

        $(document).ready(function(){
            // Initialize history datatable
            historyDatatable = tblHistory.DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ url('get-transactions-datatable') }}",
                scrollX: true,
                columns: [
                    {
                        "className":      'details-control',
                        "orderable":      false,
                        "data":           null,
                        "defaultContent": '<span class="glyphicon glyphicon-collapse-down"></span>'
                    },
                    {data: "po_number"},
                    {data: "order_date"},
                    {data: "pickup_date"},
                    {data: "total_amount"},
                    {data: "status.name"},
                    {
                        "className":      'options-control',
                        "orderable":      false,
                        "data":           null,
                        "defaultContent": ''
                    }
                ],
                order: [[2, 'desc']]
            });

            // Datatable events
            historyDatatable.on( 'draw.dt', function () {
                console.log('draw');

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

            historyDatatable.on( 'xhr.dt',function( e, settings, json, xhr ) {
                console.log('xhr');
            } );

            historyDatatable.on( 'init.dt', function () {
                console.log('init');
            } );

            // Add event listener for opening and closing details
            $('#tbl_history tbody').on('click', 'td.details-control', function () {
                var tr = $(this).closest('tr');
                var row = historyDatatable.row( tr );

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

            // Disable automatic searching every keypress, wait for ENTER key instead
            $('#tbl_history_filter input').unbind().bind('keyup', function(e) {
                if(e.keyCode == 13) {
                    historyDatatable.search(this.value).draw();
                }
            });

            // Highlight product selected by user and show available options
            tblHistory.on( 'click', 'tbody tr', function () {
                if ( !$(this).hasClass('selected') && $(this).attr('id') != null) {
                    var tr = $(this);

                    // Deselect currently selected row and remove options
                    if (selectedRow != null){
                        selectedRow.removeClass('selected');
                    }

                    tr.addClass('selected');
                    selectedRow = tr;
                }
            });
        });
    </script>
@endsection('js')
@endsection('content')