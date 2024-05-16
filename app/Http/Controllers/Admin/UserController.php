<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\Models\User;

class UserController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = User::select('users.id','users.name','users.email', 'users.created_at', 'profile.phone_number as phone', 'profile.driver_license as sim')
            ->join('profile', 'profile.user_id', 'users.id')
            ->get();

            return Datatables::of($data)
                ->addIndexColumn()
                ->editColumn('created_at', function ($row) {
                    return $row->created_at->format('d-m-Y H:i:s');
                })
                ->addColumn('action', function($row){
                    $actionBtn = "<div class='row p-2'>".
                        "<form action='". route('user.destroy', $row->id)."' class='form-delete' method='POST'>".
                        "<input type='hidden' name='_token' value='".csrf_token()."'>".
                        "<button id='delete-btn' type='button' onclick='showModal() class='btn btn-link btn-sm btn-danger btn-just-icon remove' name='delete_modal'>".
                        "<i class='fa fa-trash'></i>".
                        "</button>".
                        "</form>".
                    "</div>";
                    return $actionBtn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('pages.users.index');
    }

    public function destroy(Request $request, $id)
    {
        User::find($id)->delete();
        return redirect()->route('user.index')->with('success', 'User successfuly deleted');
    }
}