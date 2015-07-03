@extends('layout.public')

@section('inner-content')
<form class="form-horizontal" role="form" method="POST" action="{{ url('/auth/login') }}">
    <input type="hidden" name="_token" value="{{ csrf_token() }}">

    <div class="row">
        <div class="col-md-12">
            <input type="email" class="form-control" name="email" value="{{ old('email') }}" required tabindex="1" placeholder="E-mail address">
        </div>
    </div>

    <br/>

    <div class="row">
        <div class="col-md-12">
            <input type="password" class="form-control" name="password" required tabindex="2" placeholder="Password">
        </div>
    </div>

    <br/>

    <div class="row">
        <div class="col-md-8">
            <div class="checkbox">
                <label>
                    <input type="checkbox" name="remember">Remember me on this computer
                </label>
            </div>
        </div>
        <div class="col-md-4 text-right">
            <button type="submit" class="btn btn-primary btn-block" tabindex="3">Log in</button>
        </div>
    </div>
</form>

<br/>
<div class="col-md-12 text-center">
    <a class="btn btn-link" href="{{ url('/password/email') }}">Forgot your password?</a>
</div>
<br/>
@endsection