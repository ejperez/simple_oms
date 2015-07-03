@extends('layout.master')

@section('content')
    @section('css')
        <style>
            body{
                background-color:#011D58;
            }

            #company_logo{
                width:240px;
                height:45px;
            }

            #modal-container{
                margin-top:20px;
                padding-top:20px;
                background:#fff;
                border:solid thin #ccc;
            }

            #login_panel input{
                font-size:1em;
            }
        </style>
    @endsection

    <div class="container">
        <div class="row">
            <div class="col-md-4 col-md-offset-4" id="modal-container">
                <div class="row">
                    <div class="col-md-12 text-center">
                        <img id="company_logo" src="{{ url('/build/images/company/fountainhead-technologies-logo.jpg') }}" alt=""/>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 text-center">
                        <h3>Order Portal</h3>
                    </div>
                </div>
                <br/>

                @include('partials.alerts')

                @yield('inner-content')

                <br/>
            </div>
        </div>
    </div>
@endsection