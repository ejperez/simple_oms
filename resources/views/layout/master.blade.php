<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Order Portal - Fountainhead Technologies</title>

	<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
	<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
	<!--[if lt IE 9]>
		<script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
		<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
	<![endif]-->

    {!! Html::style(elixir('css/app.css')) !!}
    <!-- Custom CSS for page -->
    <style>
        .table-selectable tr {
            cursor:pointer;
        }
        .table>tbody>tr>td, .table>tbody>tr>th, .table>tfoot>tr>td, .table>tfoot>tr>th, .table>thead>tr>td, .table>thead>tr>th {
            border-top:none;
        }
        .table-striped>tbody>tr:nth-child(odd){
            background-color:#eee;
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
            color:#06308A;
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

        /* Bootstrap Override */
        body{
            background-color:#fff;
        }

        #loading_screen{
            padding:10px;
            background-color:#011D58;
            position:absolute;
            top:0;
            bottom:0;
            left:0;
            right:0;
            z-index:10;
            color:#fff;
        }

        #loading_message{
            position:absolute;
            left:50%;
            margin-left:-250px;
            width:500px;
            bottom:25%;
            font-size:1.2em;
            text-align:center;
        }

        .panel, .navbar, .btn, .alert, .dropdown-menu, input.form-control, input{
            border-radius:0px;
        }

        .alert{
            border:none;
            color:#fff;
        }

        .alert-danger{
            background-color: #a94442;
        }

        .alert-success{
            background-color: #3c763d;
        }

        .alert-warning{
            background-color: #808080;
        }

        .navbar{
            background-color:#011D58;
            border:none;
            margin-bottom:0px;
        }

        .panel{
            background-color:inherit;
        }

        .panel .panel-heading{
            background-color:#06308A;
            border-radius:0px;
            color:#fff;
        }

        .navbar-default .navbar-nav>li>a, .navbar-default .navbar-text{
            color:#fff;
        }

        .navbar-default .navbar-nav>li>a:hover, .navbar-default .navbar-text:hover{
            box-shadow: 0 4px 0 #00D8C8;
            color:#fff;
        }

        .navbar-default .navbar-nav>.active>a, .navbar-default .navbar-nav>.active>a:focus, .navbar-default .navbar-nav>.active>a:hover{
            background-color:#fff;
            color:#333;
        }

        .navbar-header .navbar-brand{
            color:#fff;
        }

        .navbar-header .navbar-brand:hover{
            color:#fff;
        }

        .navbar-default .navbar-nav>.open>a, .navbar-default .navbar-nav>.open>a:focus, .navbar-default .navbar-nav>.open>a:hover{
            background-color: #00D8C8;
            color: #fff;
        }

        footer{
            background-color:#00133A;
            font-size:12px;
            width:100%;
            position:fixed;
            bottom:0;
            color:#fff;
            z-index:3;
            margin-top:10px;
            padding:3px;
        }

        #company_logo{
            width:140px;
            height:25px;
        }
    </style>

    @yield('css')
</head>
<body>
    @include('partials.loading_screen')

    @yield('content')

	<!-- Scripts -->
    {!! Html::script(elixir('js/app.js')) !!}
    <script>
        var $loading_screen = $('#loading_screen');
        var delay_load = false;

        window.onload = function(){
            if (!delay_load) {
                showLoadingScreen(false);
            }
        };

        window.onbeforeunload = function (e) {
            showLoadingScreen(true);
        };

        function showLoadingScreen(shown){
            if (shown){
                $loading_screen.fadeIn();
            } else {
                $loading_screen.fadeOut();
            }
        }
    </script>
    @yield('js')
</body>
</html>