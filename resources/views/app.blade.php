<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Simple OMS</title>

	<!-- Fonts -->
	<link href='//fonts.googleapis.com/css?family=Roboto:400,300' rel='stylesheet' type='text/css'>

	<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
	<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
	<!--[if lt IE 9]>
		<script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
		<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
	<![endif]-->

    {!! Html::style(elixir('css/app.css')) !!}
    <!-- Custom CSS for page -->
    @yield('css')
</head>
<body>
    @include('nav')

    @if (Auth::check())
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <h3>{{ $title }}</h3>
                </div>
            </div>
            @include('alerts')
        </div>
    @endif

    <div class="container-fluid">
	    @yield('content')
    </div>

	<!-- Scripts -->
    {!! Html::script(elixir('js/app.js')) !!}
    @yield('js')
</body>
</html>