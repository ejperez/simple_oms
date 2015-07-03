@extends('layout.private')

@section('inner-content')
    <div class="row">
        <div class="col-md-12">
            <h3>Audit Log</h3>
        </div>
    </div>

    <div class="row">
        <div class="col-md-9">
            <table id="tbl_users" class="table table-condensed table-striped" cellspacing="0">
                <thead>
                <tr>
                    <th>Name
                        <a title="{{ ($sort_column == 'name' && $sort_direction == 'desc' ? 'Sort ascending' : 'Sort descending') }}" href="{{ URL::action('AuditController@index', ['s' => 'name', 'd' => ($sort_column == 'name' && $sort_direction == 'desc' ? 'asc' : 'desc'), 'f' => Input::get('f') ]) }}"><img src="../build/images/sort_both.png" alt=""/></a>
                    </th>
                    <th>Activity
                        <a title="{{ ($sort_column == 'activity' && $sort_direction == 'desc' ? 'Sort ascending' : 'Sort descending') }}" href="{{ URL::action('AuditController@index', ['s' => 'activity', 'd' => ($sort_column == 'activity' && $sort_direction == 'desc' ? 'asc' : 'desc'), 'f' => Input::get('f') ]) }}"><img src="../build/images/sort_both.png" alt=""/></a>
                    </th>
                    <th>Created Date
                        <a title="{{ ($sort_column == 'created_at' && $sort_direction == 'desc' ? 'Sort ascending' : 'Sort descending') }}" href="{{ URL::action('AuditController@index', ['s' => 'created_at', 'd' => ($sort_column == 'created_at' && $sort_direction == 'desc' ? 'asc' : 'desc'), 'f' => Input::get('f') ]) }}"><img src="../build/images/sort_both.png" alt=""/></a>
                    </th>
                </tr>
                </thead>
                <tfoot>
                <tr>
                    <th colspan="3" class="text-right">
                        {!! $audit->appends(['s' => Input::get('s'), 'd' => Input::get('d'), 'f' => Input::get('f'),])->render() !!}
                        <div>{{ 'Shown: '.(count($audit)) . ' ' . 'Total: '.$audit->total() }}</div>
                    </th>
                </tr>
                </tfoot>
                <tbody>
                @foreach ($audit as $row)
                    <tr title="Click to see details">
                        <td>{{ $row->name }}</td>
                        <td>{{ $row->activity }}</td>
                        <td>{{ $row->created_at }}</td>
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
                    <label for="">Name</label><br/>
                    <input type="text" class="form-control" id="name" value="{{ isset($filters->name) ? $filters->name : '' }}"/>
                    <label for="">Activity</label><br/>
                    <input type="text" class="form-control" id="activity" value="{{ isset($filters->activity) ? $filters->activity : '' }}"/>
                    <label for="">Created Date</label><br/>
                    <input type="text" class="form-control" id="created_at" value="{{ isset($filters->created_at) ? $filters->created_at : '' }}"/>
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
        var base_url = document.location.href.split('?')[0];

        // Selectors
        var $apply_filter = $('#apply_filter');

        $(document).ready(function() {
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
                    name: $('#name').val(),
                    activity: $('#activity').val(),
                    created_at: $('#created_at').val()
                }
                var url = base_url + '?f=' + encodeURIComponent(JSON.stringify(f));
                $(this).attr('href', url);
            });
        });
    </script>
@endsection('js')

@endsection('content')