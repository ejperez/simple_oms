@if (Session::has('success'))
<div role="alert" class="alert alert-success">
    <strong>Success!</strong><br>
    {{ Session::get('success') }}
</div>
@endif

@if (Session::has('error_message'))
    <div role="alert" class="alert alert-warning">
        <strong>Validation errors:</strong><br>
        {{ Session::get('error_message') }}
    </div>
@endif

@if (!is_null($errors))
    <div role="alert" class="alert alert-warning">
        <strong>Validation errors:</strong><br>
        @foreach ($errors->all() as $error)
            {{ $error }}<br/>
        @endforeach
    </div>
@endif