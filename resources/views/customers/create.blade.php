@extends('app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h3>Create Customer</h3>
            </div>
        </div>

        @include('alerts')

        {!! Form::open(['url' => url('customers'), 'name' => 'create_customer_form', 'id' => 'create_customer_form']) !!}
        <fieldset>
            <legend>Details</legend>
            <div class="row">
                <div class="col-md-4">
                    <div class="row">
                        <div class="col-md-12">
                            <label for="first_name" class="required">First name</label>
                            <input class="form-control" type="text" name="first_name" id="first_name" value="{{ Form::old('first_name') }}" required/>
                        </div>
                        <div class="col-md-12">
                            <label for="middle_name">Middle name</label>
                            <input class="form-control" type="text" name="middle_name" id="middle_name" value="{{ Form::old('middle_name') }}"/>
                        </div>
                        <div class="col-md-12">
                            <label for="last_name" class="required">Last name</label>
                            <input class="form-control" type="text" name="last_name" id="last_name" value="{{ Form::old('last_name') }}" required/>
                        </div>
                        <div class="col-md-6">
                            <label for="title">Title</label>
                            <input class="form-control" type="text" name="title" id="title" value="{{ Form::old('title') }}"/>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="row">
                        <div class="col-md-12">
                            <label class="required" for="billing_address">Billing address</label>
                            <input class="form-control" type="text" name="billing_address" id="billing_address" value="{{ Form::old('billing_address') }}" required/>
                        </div>
                        <div class="col-md-12">
                            <label for="zip_code_id" class="required">City/Town, Province and Zip Code</label>
                            <select class="form-control" name="zip_code_id" id="zip_code_id" required></select>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="row">
                        <div class="col-md-12">
                            <label for="company_name">Company name</label>
                            <input class="form-control" type="text" name="company_name" id="company_name" value="{{ Form::old('company_name') }}"/>
                        </div>
                        <div class="col-md-6">
                            <label for="phone_no" class="required">Phone No.</label>
                            <input class="form-control" type="text" name="phone_no" id="phone_no" value="{{ Form::old('phone_no') }}" required/>
                        </div>
                        <div class="col-md-6"><label for="fax_no">Fax No.</label>
                            <input class="form-control" type="text" name="fax_no" id="fax_no" value="{{ Form::old('fax_no') }}"/></div>
                        </div>
                    </div>
                </div>
        </fieldset>

        <fieldset>
            <legend>Credit</legend>
            <div class="row">
                <div class="col-md-4">
                    <label for="credit_amount" class="required">Amount</label>
                    <input class="form-control" type="number" name="credit_amount" id="credit_amount" value="{{ Form::old('credit_amount') }}" required/>
                </div>
            </div>
        </fieldset>

        <fieldset>
            <div class="row">
                <div class="col-md-12">
                    <input class="btn btn-primary" type="submit"/>
                </div>
            </div>
        </fieldset>

        <input type="hidden" name="windowed" value="{{ Input::has('windowed') ? 'true' : 'false'}}"/>
        {!! Form::close() !!}
    </div>

@section('css')
    <style>
        table.dataTable tr{
            cursor: pointer;
        }
        a.search-link{
            font-size:12px;
            float:right;
            margin-top:5px;
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
    <script>
        var $zip_code_id = $('select#zip_code_id');

        $(document).ready(function(){
            // Initialize address finder
            $zip_code_id.select2({
                placeholder: 'Enter a city, province, or zip code',
                ajax: {
                    dataType: 'json',
                    url: '{{ url('search-address') }}',
                    delay: 400,
                    data: function(params) {
                        return {
                            term: '%' + params.term + '%'
                        }
                    },
                    processResults: function (data) {

                        var formatted_data = [];

                        $(data).each(function(index, value){
                            formatted_data.push({
                                id: value.id,
                                text: value.city + ', ' + value.major_area + ' (' + value.zip_code + ')'
                            });
                        });

                        return {
                            results: formatted_data
                        };
                    },
                    cache: true
                }
            });
        });
    </script>
@endsection('js')
@endsection('content')