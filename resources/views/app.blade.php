<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Simple OMS</title>

	<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
	<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
	<!--[if lt IE 9]>
		<script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
		<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
	<![endif]-->

    {!! Html::style(elixir('css/app.css')) !!}
    <!-- Custom CSS for page -->
    @yield('css')
    <style>
        .table-selectable tr {
            cursor:pointer;
        }
        .table-selectable tbody tr:hover {
            outline:solid thin #0063dc;
        }
        .table-selectable tbody tr.selected {
            background:#0088cc;
            outline:none;
        }
        .table-selectable tbody tr.selected td:not(.input){
            color: #fff;
        }
        .table-selectable thead tr {
            background:#f5f5f5;
            border:solid thin #ddd;
        }
        .table-selectable thead span.glyphicon {
            font-size:0.75em;
        }
        .table-selectable tr.shown td {
            background:#76b4ff !important;
        }
        .pagination{
            margin:0;
        }
    </style>
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