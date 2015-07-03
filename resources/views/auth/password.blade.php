@extends('layout.public')

@section('inner-content')
<div class="row">
    <div class="col-md-12">
        <h4>Password Reset</h4>
        <p>A password reset link will be sent to your e-mail address.</p>
    </div>
</div>

<form class="form-horizontal" role="form" method="POST" action="{{ url('/password/email') }}">
	<input type="hidden" name="_token" value="{{ csrf_token() }}">

	<div class="row">
		<div class="col-md-8">
			<input type="email" class="form-control" name="email" value="{{ old('email') }}" placeholder="E-mail Address">
		</div>
		<div class="col-md-4">
			<button type="submit" class="btn btn-primary">
				Send link
			</button>
		</div>
	</div>
</form>

<br/>
<div class="col-md-12 text-center">
    <a class="btn btn-link" href="{{ url('/') }}">Go back to log in page</a>
</div>
<br/>
@endsection