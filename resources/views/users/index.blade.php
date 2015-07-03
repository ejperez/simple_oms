@extends('layout.private')

@section('inner-content')
    <div class="row">
        <div class="col-md-12">
            <h3>List of Users</h3>
        </div>
    </div>

    <div class="row">
        <div class="col-md-9">
            <table id="tbl_users" class="table table-condensed table-striped table-selectable" cellspacing="0">
                <thead>
                <tr>
                    <th>User Name
                        <a title="{{ ($sort_column == 'username' && $sort_direction == 'desc' ? 'Sort ascending' : 'Sort descending') }}" href="{{ URL::action('UsersController@index', ['s' => 'username', 'd' => ($sort_column == 'username' && $sort_direction == 'desc' ? 'asc' : 'desc'), 'f' => Input::get('f') ]) }}"><img src="../build/images/sort_both.png" alt=""/></a>
                    </th>
                    <th>Email
                        <a title="{{ ($sort_column == 'email' && $sort_direction == 'desc' ? 'Sort ascending' : 'Sort descending') }}" href="{{ URL::action('UsersController@index', ['s' => 'email', 'd' => ($sort_column == 'email' && $sort_direction == 'desc' ? 'asc' : 'desc'), 'f' => Input::get('f') ]) }}"><img src="../build/images/sort_both.png" alt=""/></a>
                    </th>
                    <th>Role
                        <a title="{{ ($sort_column == 'role' && $sort_direction == 'desc' ? 'Sort ascending' : 'Sort descending') }}" href="{{ URL::action('UsersController@index', ['s' => 'role', 'd' => ($sort_column == 'role' && $sort_direction == 'desc' ? 'asc' : 'desc'), 'f' => Input::get('f') ]) }}"><img src="../build/images/sort_both.png" alt=""/></a>
                    </th>
                    <th>Name
                        <a title="{{ ($sort_column == 'name' && $sort_direction == 'desc' ? 'Sort ascending' : 'Sort descending') }}" href="{{ URL::action('UsersController@index', ['s' => 'name', 'd' => ($sort_column == 'name' && $sort_direction == 'desc' ? 'asc' : 'desc'), 'f' => Input::get('f') ]) }}"><img src="../build/images/sort_both.png" alt=""/></a>
                    </th>
                    <th>Created Date
                        <a title="{{ ($sort_column == 'created_at' && $sort_direction == 'desc' ? 'Sort ascending' : 'Sort descending') }}" href="{{ URL::action('UsersController@index', ['s' => 'created_at', 'd' => ($sort_column == 'created_at' && $sort_direction == 'desc' ? 'asc' : 'desc'), 'f' => Input::get('f') ]) }}"><img src="../build/images/sort_both.png" alt=""/></a>
                    </th>
                </tr>
                </thead>
                <tfoot>
                <tr>
                    <th colspan="5" class="text-right">
                        {!! $users->appends(['s' => Input::get('s'), 'd' => Input::get('d'), 'f' => Input::get('f'),])->render() !!}
                        <div>{{ 'Shown: '.(count($users)) . ' ' . 'Total: '.$users->total() }}</div>
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
        <div class="col-md-3">
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