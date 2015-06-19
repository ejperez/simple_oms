@extends('app')

@section('content')
    <div class="row">
        <div class="col-md-10">
            <table id="tbl_users" class="table table-condensed table-striped table-selectable" cellspacing="0">
                <thead>
                <tr>
                    <th>User Name
                        <a title="Sort ascending" href="{{ URL::action('UsersController@index', ['s' => 'username', 'd' => 'asc', 'f' => Input::get('f') ]) }}"><span class="glyphicon glyphicon-arrow-up"></span></a>
                        <a title="Sort descending" href="{{ URL::action('UsersController@index', ['s' => 'username', 'd' => 'desc', 'f' => Input::get('f')]) }}"><span class="glyphicon glyphicon-arrow-down"></span></a>
                    </th>
                    <th>Email
                        <a title="Sort ascending" href="{{ URL::action('UsersController@index', ['s' => 'email', 'd' => 'asc', 'f' => Input::get('f') ]) }}"><span class="glyphicon glyphicon-arrow-up"></span></a>
                        <a title="Sort descending" href="{{ URL::action('UsersController@index', ['s' => 'email', 'd' => 'desc', 'f' => Input::get('f')]) }}"><span class="glyphicon glyphicon-arrow-down"></span></a>
                    </th>
                    <th>Role
                        <a title="Sort ascending" href="{{ URL::action('UsersController@index', ['s' => 'role', 'd' => 'asc', 'f' => Input::get('f') ]) }}"><span class="glyphicon glyphicon-arrow-up"></span></a>
                        <a title="Sort descending" href="{{ URL::action('UsersController@index', ['s' => 'role', 'd' => 'desc', 'f' => Input::get('f')]) }}"><span class="glyphicon glyphicon-arrow-down"></span></a>
                    </th>
                    <th>Name
                        <a title="Sort ascending" href="{{ URL::action('UsersController@index', ['s' => 'name', 'd' => 'asc', 'f' => Input::get('f') ]) }}"><span class="glyphicon glyphicon-arrow-up"></span></a>
                        <a title="Sort descending" href="{{ URL::action('UsersController@index', ['s' => 'name', 'd' => 'desc', 'f' => Input::get('f')]) }}"><span class="glyphicon glyphicon-arrow-down"></span></a>
                    </th>
                    <th>Created Date
                        <a title="Sort ascending" href="{{ URL::action('UsersController@index', ['s' => 'created_at', 'd' => 'asc', 'f' => Input::get('f') ]) }}"><span class="glyphicon glyphicon-arrow-up"></span></a>
                        <a title="Sort descending" href="{{ URL::action('UsersController@index', ['s' => 'created_at', 'd' => 'desc', 'f' => Input::get('f')]) }}"><span class="glyphicon glyphicon-arrow-down"></span></a>
                    </th>
                </tr>
                </thead>
                <tfoot>
                <tr>
                    <th colspan="5" class="text-right">
                        {!! $users->appends(['s' => Input::get('s'), 'd' => Input::get('d'), 'f' => Input::get('f'),])->render() !!}
                    </th>
                </tr>
                <tr>
                    <th colspan="7" class="text-right">
                        {{ 'Shown: '.(count($users)) }}
                        {{ 'Total: '.$users->total() }}
                    </th>
                </tr>
                </tfoot>
                <tbody>
                @foreach ($users as $user)
                    <tr title="Click to see details" data-user-id="{{ SimpleOMS\Helpers\Helpers::hash($user->id) }}">
                        <td>{{ $user->username }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->role }}</td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->created_at }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <div class="col-md-2">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Filters</h3>
                </div>
                <div class="panel-body">
                    <label for="">User Name</label><br/>
                    <input type="text" class="form-control" id="username" value="{{ isset($filters->username) ? $filters->username : '' }}"/>
                    <label for="">Email</label><br/>
                    <input type="text" class="form-control" id="email" value="{{ isset($filters->email) ? $filters->email : '' }}"/>
                    <label for="">Name</label><br/>
                    <input type="text" class="form-control" id="name" value="{{ isset($filters->name) ? $filters->name : '' }}"/>
                    <label for="">Created Date</label><br/>
                    <input type="text" class="form-control" id="created_at" value="{{ isset($filters->created_at) ? $filters->created_at : '' }}"/>
                    <label for="">Role</label><br/>
                    <select name="" id="role" multiple class="form-control">
                        @foreach ($roles as $role)
                            <option {{ isset($filters->role) && in_array($role->name, $filters->role) ? 'selected' : '' }}>{{ $role->name }}</option>
                        @endforeach
                    </select>
                    <br/><br/>
                    <div class="btn-group-justified" role="group">
                        <a class="btn btn-primary btn-sm" href="#" id="apply_filter">Apply filter</a>
                        <a class="btn btn-default btn-sm" href="{{ URL::action('UsersController@index') }}" id="apply_filter">Clear filter</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- The Modal -->
    <div id="modal" class="modal fade" role="dialog" data-keyboard="false">
        <div class="modal-dialog modal-lg">
            <!-- Modal content-->
            <div class="modal-content"></div>
        </div>
    </div>

@section('js')

    <script>
        var base_url = document.location.href.split('?')[0],

        // Selectors
                $apply_filter = $('#apply_filter'),

        // Modal
                $modal = $('#modal');

        $(document).ready(function() {
            // Initialize drop down
            $('#role').select2();

            // Initialize date pickers
            $('#created_at').datepicker({
                disableTouchKeyboard: true,
                format: '{{ DATE_FORMAT }}'
            }).on('changeDate', function () {
                $(this).datepicker('hide');
            });

            // Apply filter link
            $apply_filter.on('click', function (e) {
                var f = {
                    username: $('#username').val(),
                    email: $('#email').val(),
                    name: $('#name').val(),
                    created_at: $('#created_at').val(),
                    role: $('#role').val()
                }
                var url = base_url + '?f=' + encodeURIComponent(JSON.stringify(f));
                $(this).attr('href', url);
            });
        });
    </script>
@endsection('js')

@endsection('content')