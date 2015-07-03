@extends('layout.private')

@section('inner-content')
<div class="row">
    <div class="col-md-12">
        <h3>Dashboard</h3>
    </div>
</div>

<div class="row">
	<div class="col-md-4">
		<div class="panel panel-default">
			<div class="panel-heading">Order Count by Status</div>
			<div class="panel-body">
                <div class="row">
                    <div class="col-md-12">
                        <div id="canvas-holder">
                            <canvas id="orders-count-by-status-chart-area" style="width:100%;height:300px"/>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div id="legend"></div>
                    </div>
                </div>
			</div>
		</div>
	</div>
    <div class="col-md-8">
        <div class="panel panel-default">
            <div class="panel-heading">Pending Orders</div>
            <div class="panel-body">
                <div class="col-md-6" id="near-expiration-orders">

                </div>
                <div class="col-md-6" id="expired-orders">

                </div>
            </div>
        </div>
    </div>
</div>

@section('js')

    @include('partials.modal')
    @include('partials.order_details_status')

    <script id="legends-template" type="text/x-handlebars-template">
        @{{#each dataset}}
        <div class="row">
            <div class="col-md-3" style="color:@{{ color }}">
                <strong>@{{ label }}</strong>
            </div>
            <div class="col-md-9">
                <input class="text-right" style="width:100%;border:none" type="text" readonly value="@{{ count }}"/>
            </div>
        </div>
        @{{/each  }}
        <div class="row">
            <div class="col-md-3">
                <strong>Total</strong>
            </div>
            <div class="col-md-9">
                <input class="text-right" style="width:100%;font-weight:bold;border:none" type="text" readonly value="@{{ total }}"/>
            </div>
        </div>
    </script>

    <script id="orders-list-template" type="text/x-handlebars-template">
        <h5>@{{ title }}</h5>
        <table class="table table-condensed table-striped table-selectable table-order-view">
            <thead>
                <th>PO Number</th>
                <th>Order Date</th>
                <th>@{{ days_label }}</th>
            </thead>
            <tbody>
                @{{#each dataset}}
                <tr data-order-id="@{{ id }}">
                    <td>@{{ po_number }}</td>
                    <td>@{{ order_date }}</td>
                    <td>@{{ days }}</td>
                </tr>
                @{{ else }}
                <tr>
                    <td colspan="3">No data to display.</td>
                </tr>
                @{{/each  }}
            </tbody>
        </table>
    </script>

    <script>
        var legends = Handlebars.compile($("#legends-template").html()),
                orders_list = Handlebars.compile($("#orders-list-template").html());

        var colors = {
            Pending:        '#5DA5DA',
            Cancelled:      '#FAA43A',
            Approved:       '#60BD68',
            Disapproved:    '#FF5A5E'
        };

        delay_load = true;

        $(document).ready(function(){
            $.when(getUserOrderPendingCount(), getUserOrderCountStatus()).done(function(){
                showLoadingScreen(false);
            });
        });

        function getUserOrderPendingCount(){
            return $.get('get-user-order-pending-count/{{ SimpleOMS\Helpers\Helpers::hash(Auth::user()->id) }}', {}, function(data){
                data = JSON.parse(data);

                $("#near-expiration-orders").html(orders_list({
                    dataset: data.near_expired,
                    title: 'Top ' + data.limit + ' Orders Near Expiration',
                    days_label: 'Days Left'
                }));

                $("#expired-orders").html(orders_list({
                    dataset: data.expired,
                    title: 'Top Expired ' + data.limit + ' Orders',
                    days_label: 'Days Passed'
                }));
            }).fail(function(){
                alert('Failed to get details.');
            });
        }

        function getUserOrderCountStatus(){
            return $.get('get-user-order-count-status/{{ SimpleOMS\Helpers\Helpers::hash(Auth::user()->id) }}', {}, function(data){
                var ctr = 0,
                        dataset = JSON.parse(data);

                if (dataset.length > 0){
                    $.each(dataset, function(index, item){
                        dataset[index]['color'] = colors[item.label];
                        ctr++;
                    });

                    var ctx = document.getElementById("orders-count-by-status-chart-area").getContext("2d");

                    window.myPie = new Chart(ctx).Pie(dataset, {
                        legendTemplate : legends({
                            dataset: dataset,
                            total: dataset[0].total
                        }),
                        percentageInnerCutout: 50,
                        tooltipTemplate: "<%= label %>: <%= value %>%"
                    });

                    //then you just need to generate the legend
                    var legend = window.myPie.generateLegend();

                    //and append it to your page somewhere
                    $('#legend').append(legend);
                } else {
                    $('#legend').html('No data to display.');
                }
            }).fail(function(){
                alert('Failed to get details.');
            });
        }
    </script>
@endsection('js')

@endsection