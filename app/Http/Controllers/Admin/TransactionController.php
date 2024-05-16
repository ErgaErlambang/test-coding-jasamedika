<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;
use App\Models\Vehicle;
use App\Models\Transaction;
use DB, Validator, Exception;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Transaction::select('transaction.*', 'users.name as name', 'vehicles.model as model')
            ->join('users', 'users.id', 'transaction.user_id')
            ->join('vehicles', 'vehicles.id', 'transaction.vehicle_id')
            ->when(Auth::user()->hasRole->slug == 'user', function($q) {
                $q->where('transaction.user_id', Auth::user()->id);
            })->get();

            $datatables = Datatables::of($data)
                ->addIndexColumn()
                ->editColumn('status', function ($row) {
                    if($row->status == "Booked") {
                        $badge = 'warning';
                    }else if($row->status == "Canceled") {
                        $badge = 'danger';
                    }else {
                        $badge = 'success';
                    }
                    
                    $span = "<span class='badge badge-pill badge-".$badge."'>".$row->status."</span>";
                    return $span;
                })
                ->editColumn('total', function ($row) {
                    return "Rp. ".fRupiah($row->total);
                });
                $datatables->addColumn('action', function($row){
                    $actionBtn = "<div class='row p-2'>".
                    "<a href='".route('transaction.show', $row->id)."' class='btn btn-link btn-sm mr-1 btn-primary btn-just-icon like' style='display: block;margin-top: 0em;margin-block-end: 1em;'><i class='fa fa-eye'></i></a>".
                    "</div>";
                    return $actionBtn;
                })
                ->rawColumns(['action', 'status']);
                

            return $datatables->make(true);
        }
        return view('pages.transaction.index');
    }

    public function show($id)
    {
        $user = Auth::user();
        $transaction = Transaction::when($user->hasRole->slug == 'user', function($q) use($user) {
            return $q->where('user_id', $user->id);
        })->find($id);
        
        if(empty($transaction)) {
            abort(404);
        }
        
        return view('pages.transaction.detail', compact('transaction'));
    }

    public function updatestatus(Request $request, $id)
    {
        $transaction = Transaction::find($id);
        $transaction->status = $request->status;
        if($transaction->status !== "Active" && ($transaction->status == "Returned" || $transaction->status == "Done")) {
            $transaction->returned_date = date('Y-m-d');
        }else {
            if($transaction->status == "Returned") {
                $transaction->returned_date = date('Y-m-d');
            }else if($transaction->status == "Done" || $transaction->status == "Canceled") {
                $transaction->is_available = true;
                $transaction->available_until = null;
            }
        }
        $transaction->save();
        return redirect()->back()->with('success', 'Status successfully updated');
    }

    public function checkplate(Request $request)
    {
        $transaction = Transaction::select('transaction.user_id','transaction.status', 'vehicles.plate_number')
        ->join('vehicles', 'vehicles.id', 'transaction.vehicle_id')
        ->where('transaction.user_id', Auth::user()->id)
        ->where('vehicles.plate_number', $request->plate_number)
        ->where('transaction.status', "Active")
        ->first();

        if(!empty($transaction)) {
            return response([
                "status" => true,
                "message" => "Transaction found"
            ], 200);
        }
        return response([
            "status" => false,
            "message" => "Transaction not found"
        ], 404);
    }

    public function return(Request $request)
    {
        try {
            $transaction = Transaction::select('transaction.*', 'vehicles.plate_number')
            ->join('vehicles', 'vehicles.id', 'transaction.vehicle_id')
            ->where('transaction.user_id', Auth::user()->id)
            ->where('vehicles.plate_number', $request->return_plate)
            ->where('transaction.status', "Active")
            ->first();
    
            if(empty($transaction)) {
                return redirect()->route('transaction.index')->with('error', 'Transaction not found');
            }
    
            $transaction->returned_date = date('Y-m-d');
            $transaction->status = "Returned";
            $transaction->update();

            return redirect()->back()->with('success', 'Status successfully updated'); 
            
        } catch (Exception $th) {
            return redirect()->back()->with('error', $e->getMessage()); 
        }

        
    }
}