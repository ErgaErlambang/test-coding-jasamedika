<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\Models\Roles;

class RolesController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Roles::get();

            return Datatables::of($data)
                ->addIndexColumn()
                ->editColumn('created_at', function ($row) {
                    return $row->created_at->format('d-m-Y H:i:s');
                })
                ->make(true);
        }

        return view('pages.roles.index');
    }
}