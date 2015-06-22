@extends('app')

@section('content')
<div class="row">
	<div class="col-md-4">
		<div class="panel panel-default">
			<div class="panel-heading">Order Count by Status</div>
			<div class="panel-body">
                <div class="row">
                    <div class="col-md-12">
                        <div id="canvas-holder">
                            <canvas id="orders-count-by-status-chart-area" width="385" height="300"/>
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
    <script id="legends-template" type="text/x-handlebars-template">
        @{{#each dataset}}
        <div class="row">
            <div class="col-md-3" style="color:@{{ color }}"><strong>@{{ label }}</strong></div>
            <div class="col-md-9">@{{ count }}</div>
        </div>
        @{{/each  }}
        <div class="row">
            <div class="col-md-3"><strong>Total</strong></div>
            <div class="col-md-9">@{{ total }}</div>
        </div>
    </script>

    <script id="orders-list-template" type="text/x-handlebars-template">
        <h5>@{{ title }}</h5>
        <table class="table">
            <thead>
                <th>PO Number</th>
                <th>Order Date</th>
                <th>@{{ days_label }}</th>
            </thead>
            <tbody>
                @{{#each dataset}}
                <tr>
                    <td>@{{ po_number }}</td>
                    <td>@{{ order_date }}</td>
                    <td>@{{ days }}</td>
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

        $(document).ready(function(){
            $.get('get-user-order-count-status/{{ SimpleOMS\Helpers\Helpers::hash(Auth::user()->id) }}', {}, function(data){
                var ctr = 0,
                        total = 0,
                        dataset = JSON.parse(data);

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
            }).fail(function(){
                alert('Failed to get details.');
            });

            $.get('get-user-order-pending-count/{{ SimpleOMS\Helpers\Helpers::hash(Auth::user()->id) }}', {}, function(data){
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
        });
    </script>
@endsection('js')

@endsection('content')