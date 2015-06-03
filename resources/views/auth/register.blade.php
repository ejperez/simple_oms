@extends('app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h3>Register User</h3>
        </div>
    </div>

    @include('alerts')

    <form class="form" role="form" method="POST" action="{{ url('/auth/register') }}">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <div class="row">
                <div class="col-md-3">
                    <div class="row">
                        <div class="col-md-12">
                            <label class="required">First Name</label>
                            <input type="text" class="form-control" name="first_name" value="{{ old('first_name') }}" required>
                        </div>

                        <div class="col-md-12">
                            <label>Middle Name</label>
                            <input type="text" class="form-control" name="middle_name" value="{{ old('middle_name') }}">
                        </div>
                        <div class="col-md-12">
                            <label class="required">Last Name</label>
                            <input type="text" class="form-control" name="last_name" value="{{ old('last_name') }}" required>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="row">
                        <div class="col-md-12">
                            <label class="required">User Name</label>
                            <input type="text" class="form-control" name="name" value="{{ old('name') }}" required>
                        </div>
                        <div class="col-md-12">
                            <label class="required">E-Mail Address</label>
                            <input type="email" class="form-control" name="email" value="{{ old('email') }}" required>
                        </div>
                        <div class="col-md-12">
                            <label class="required">Password</label>
                            <input type="password" class="form-control" name="password" required>
                        </div>
                        <div class="col-md-12">
                            <label class="required">Confirm Password</label>
                            <input type="password" class="form-control" name="password_confirmation" required>
                        </div>
                        <div class="col-md-12">
                            <label class="required">Role</label><br/>
                            @foreach ($roles as $role)
                                <label>
                                    <input {{ old('role_id') == $role->id ? 'checked="checked"' : '' }} type="radio" name="role_id" value="{{ $role->id }}"/>
                                    {{ $role->name }}
                                </label>&nbsp;&nbsp;&nbsp;
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <input class="btn btn-primary" type="submit" value="Register"/>
                </div>
            </div>
    </form>
</div>
@endsection
