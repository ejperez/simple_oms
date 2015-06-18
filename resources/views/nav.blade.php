<nav class="navbar navbar-default">
    <div class="container-fluid">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                <span class="sr-only">Toggle Navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="#">Simple OMS</a>
        </div>

        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            <ul class="nav navbar-nav">
                <li><a href="{{ url('/') }}">Home</a></li>
                @if (Auth::check())
                    <li class="dropdown">
                        <a class="dropdown-toggle" data-toggle="dropdown" href="#" aria-expanded="false" role="button">Orders <span class="caret"></span></a>
                        <ul class="dropdown-menu" role="menu">
                            <li><a href="{{ url('orders') }}">View Orders</a></li>
                            @if (Auth::user()->hasRole(['administrator', 'sales']))
                            <li class="divider"></li>
                            <li><a href="{{ url('orders/create') }}">Create Order</a></li>
                            @endif
                        </ul>
                    </li>
                @if (Auth::user()->hasRole(['administrator']))
                    <li class="dropdown">
                        <a class="dropdown-toggle" data-toggle="dropdown" href="#" aria-expanded="false" role="button">Users <span class="caret"></span></a>
                        <ul class="dropdown-menu" role="menu">
                            <li><a href="{{ url('users') }}">View Users</a></li>
                            <li class="divider"></li>
                            <li><a href="{{ url('users/create') }}">Create User</a></li>
                        </ul>
                    </li>
                    <li><a href="{{ url('/') }}">Audit Log</a></li>
                @endif
                @endif
            </ul>

            <ul class="nav navbar-nav navbar-right">
                @if (Auth::guest())
                <li><a href="{{ url('/auth/login') }}">Login</a></li>
                @else
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">{{ Auth::user()->name }}<span class="caret"></span></a>
                    <ul class="dropdown-menu" role="menu">
                        <li>
                            <a href="#">
                                <table style="cursor:default">
                                    <tr>
                                        <td>Name&nbsp;</td>
                                        <td>:&nbsp;<strong>{{ Auth::user()->customer->fullName() }}</strong></td>
                                    </tr>
                                    <tr>
                                        <td>Role&nbsp;</td>
                                        <td>:&nbsp;<strong>{{ Auth::user()->role->name }}</strong></td>
                                    </tr>
                                </table>
                            </a>
                        </li>
                        <li class="divider"></li>
                        <li><a href="{{ url('/users/'.SimpleOMS\Helpers\Helpers::hash(Auth::user()->id)).'/edit' }}">Edit Account</a></li>
                        <li class="divider"></li>
                        <li><a href="{{ url('/auth/logout') }}">Logout</a></li>
                    </ul>
                </li>
                @endif
            </ul>
        </div>
    </div>
</nav>