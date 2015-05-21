@extends('app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h3>Transactions</h3>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
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
                        <tbody>
                        <tr>
                            <td>All</td>
                            <td>0</td>
                        </tr>
                        <tr>
                            <td>Pending</td>
                            <td>0</td>
                        </tr>
                        <tr>
                            <td>Completed</td>
                            <td>0</td>
                        </tr>
                        <tr>
                            <td>Disapproved</td>
                            <td>0</td>
                        </tr>
                        <tr>
                            <td>Cancelled</td>
                            <td>0</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <br>
            <a class="btn btn-default" href="{{ url('create-order') }}"><span class="glyphicon glyphicon-pencil"></span>Create Order</a>
        </div>
        <div class="col-md-8">
            <div class="panel panel-default">
                <div class="panel-heading">Order History</div>
                <div class="panel-body">
                    <span>Order history list here</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection