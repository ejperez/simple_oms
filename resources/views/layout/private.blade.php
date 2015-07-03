@extends('layout.master')

@section('content')

    @include('partials.nav')

    <div class="container-fluid">
        @include('partials.alerts')
    </div>

    <div class="container">
        @yield('inner-content')
    </div>

    <br/>
    <br/>

    @include('partials.footer')

@endsection