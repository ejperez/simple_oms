@extends('app')

@section('content')
<div class="row">
	<div class="col-md-4">
		<div class="panel panel-default">
			<div class="panel-heading">Order Count by Status</div>
			<div class="panel-body">
                <div class="row">
                    <div class="col-md-8">
                        <div id="canvas-holder">
                            <canvas id="orders-count-by-status-chart-area" width="300" height="300"/>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div id="chart"></div>
                    </div>
                </div>
			</div>
		</div>
	</div>
    <div class="col-md-4">
        <div class="panel panel-default">
            <div class="panel-heading">Details</div>
            <div class="panel-body">
                <table class="table">
                    <thead>
                    <tr>
                        <th>Month</th>
                        <th>Count</th>
                        <th>Total Amount</th>
                    </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>January</td>
                            <td>22</td>
                            <td>₱ 20000.00</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@section('js')
    <script id="legends-template" type="text/x-handlebars-template">
        <ul>
            @{{#each dataset}}
                <li style="color:@{{ color }}">@{{ label }}</li>
            @{{/each  }}
        </ul>
    </script>

    <script>
        var legends = Handlebars.compile($("#legends-template").html());

        var colors = {
            Pending:        '#A8B3C5',
            Cancelled:      '#FFC870',
            Approved:       '#5AD3D1',
            Disapproved:    '#FF5A5E'
        };

        $(document).ready(function(){
            $.get('get-user-order-count-status/{{ SimpleOMS\Helpers\Helpers::hash(Auth::user()->id) }}', {}, function(data){
                var ctr = 0,
                        total = 0,
                        dataset = JSON.parse(data);

                $.each(dataset, function(index, item){
                    total += item.value;
                    dataset[index]['color'] = colors[item.label];
                    ctr++;
                });

                var ctx = document.getElementById("orders-count-by-status-chart-area").getContext("2d");

                console.log(dataset);

                window.myPie = new Chart(ctx).Pie(dataset, {
                    legendTemplate : legends({
                        dataset: dataset,
                        total: total
                    }),
                    percentageInnerCutout: 50
                });

                //then you just need to generate the legend
                var legend = window.myPie.generateLegend();

                //and append it to your page somewhere
                $('#chart').append(legend);
            }).fail(function(){
                alert('Failed to get details.');
            });
        });
    </script>
@endsection('js')

@endsection('content')