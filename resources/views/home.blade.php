@extends('app')

@section('content')
<div class="row">
	<div class="col-md-8">
		<div class="panel panel-default">
			<div class="panel-heading">Monthly Order Transactions</div>
			<div class="panel-body">
                <img src="https://www.syncfusion.com/content/en-US/Products/Images/aspnetmvc/ejchart/ChartPie.png?v=03042015101404" alt="Pie chart"/>
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
@endsection
