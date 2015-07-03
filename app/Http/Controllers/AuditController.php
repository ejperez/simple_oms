<?php namespace SimpleOMS\Http\Controllers;

use SimpleOMS\Http\Requests;
use Input;
use DB;

class AuditController extends Controller {

	public function index()
    {
        \DB::enableQueryLog();

        // Get query parameters
        $filters = json_decode(Input::get('f'));
        $sort_column = Input::has('s') ? Input::get('s') : 'created_at';
        $sort_direction = Input::has('d') ? Input::get('d') : 'desc';

        // Query view
        $audit = DB::table('audit_vw');

        // Search parameters
        if (isset($filters->name) && !empty($filters->name))
            $audit->where('name', 'like', "%$filters->name%");

        if (isset($filters->acvitity) && !empty($filters->acvitity))
            $audit->where('acvitity', 'like', "%$filters->acvitity%");

        if (isset($filters->created_at) && !empty($filters->created_at))
            $audit->where('created_at', '=', $filters->created_at);

        // Sorting
        $audit = $audit->orderBy($sort_column, $sort_direction)
            ->paginate(PER_PAGE);

        return view('audit.index', compact('audit', 'filters', 'sort_column', 'sort_direction'));
    }

}
