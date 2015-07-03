<div class="row" style="margin-top:20px">
    <div class="col-md-12">
        @if (Session::has('success'))
            <div role="alert" class="alert alert-success">
                <strong><span class="glyphicon glyphicon-ok"></span> Success!</strong><br>
                {{ Session::get('success') }}
            </div>
        @endif

        @if (Session::has('error_message'))
            <div role="alert" class="alert alert-danger">
                <strong><span class="glyphicon glyphicon-remove"></span> Validation error(s)</strong><br>
                {{ Session::get('error_message') }}
            </div>
        @endif

        @if (count($errors) > 0)
            <div role="alert" class="alert alert-danger">
                <strong><span class="glyphicon glyphicon-remove"></span> Validation error(s)</strong><br>
                @foreach ($errors->all() as $error)
                    {{ $error }}<br/>
                @endforeach
            </div>
        @endif
    </div>
</div>
