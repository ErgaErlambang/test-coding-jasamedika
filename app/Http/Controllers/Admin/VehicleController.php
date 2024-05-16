<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;
use App\Models\Vehicle;
use App\Models\Transaction;
use DB, Validator, Exception;


class VehicleController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Vehicle::all();
            
            $datatables = Datatables::of($data)
                ->addIndexColumn()
                ->editColumn('available', function ($row) {
                    $badge = $row->is_available == true ? 'success' : 'danger';
                    $active = $row->is_available == true ? 'Yes' : 'No';
                    $span = "<span class='badge badge-pill badge-".$badge."'>".$active."</span>";
                    return $span;
                });
                if(Auth::user()->hasRole->slug == "superadministrator") {
                    $datatables->editColumn('active', function ($row) {
                        $badge = $row->is_active == true ? 'success' : 'danger';
                        $active = $row->is_active == true ? 'Yes' : 'No';
                        $span = "<span class='badge badge-pill badge-".$badge."'>".$active."</span>";
                        return $span;
                    });
                }
                $datatables->addColumn('action', function($row){
                    $actionBtn = "<div class='row p-2'>".
                    "<a href='".route('vehicle.show', $row->slug)."' class='btn btn-link btn-sm mr-1 btn-primary btn-just-icon like' style='display: block;margin-top: 0em;margin-block-end: 1em;'><i class='fa fa-eye'></i></a>";
                    if(Auth::user()->hasRole->slug == "superadministrator") {
                        $actionBtn .= "<a href='".route('vehicle.edit', $row->slug)."' class='btn btn-link btn-sm mr-1 btn-info btn-just-icon like' style='display: block;margin-top: 0em;margin-block-end: 1em;'><i class='fa fa-edit'></i></a>".
                        "<form action='".route('vehicle.destroy', $row->id)."' class='form-delete' method='POST'>".
                        "<input type='hidden' name='_token' value='".csrf_token()."'>".
                        "<button id='delete-btn' type='button' onclick='showModal()' class='btn btn-link btn-sm btn-danger btn-just-icon remove ' name='delete_modal'>".
                        "<i class='fa fa-trash'></i>".
                        "</button>".
                        "</form>";
                    }
                    $actionBtn .= "</div>";
                    return $actionBtn;
                })
                ->rawColumns(['action', 'available', 'active']);
                

            return $datatables->make(true);
        }
        return view('pages.vehicle.index');
    }

    public function show($slug)
    {
        try {
            $vehicle = Vehicle::where('slug', $slug)->first();
            $checkAvailable = Transaction::where('vehicle_id', $vehicle->id)->whereIn('status', ['Booked', 'Active'])->whereDate('start_date', '>=', date('Y-m-d'))->first();
            if($vehicle->is_available && !empty($checkAvailable)) {
                if($checkAvailable->start_date == date('Y-m-d')) {
                    $vehicle->is_available = false;
                    $vehicle->available_until = $checkAvailable->end_date;
                    $vehicle->save();
                }
            }

            return view('pages.vehicle.detail', compact('vehicle'));
        } catch (Exception $e) {
            return redirect()->route('vehicle.index')->with('error', $e->getMessage());
        }
    }

    public function create()
    {
        $vehicles = Vehicle::select('brand', 'model')->groupBy('model','brand')->get();
        $data = [];
        foreach ($vehicles as $key => $value) {
            $data['model'][] = $value->model;
            $data['brand'][] = $value->brand;
        }
        return view('pages.vehicle.create', compact('data'));
    }

    public function store(Request $request)
    {
        try {            
            $validator = Validator::make($request->all(), [
                "brand" => "required",
                "model" => "required",
                "price" => "required",
                "plate_number.*" => "required",
            ]);

            if($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $data = $validator->validated();
            $data["is_active"] = isset($request->is_enable) ? true : false;


            DB::beginTransaction();
            foreach ($data['plate_number'] as $key => $plate) {
                $vehicle = Vehicle::create([
                    "brand" => $data['brand'],
                    "model" => $data['model'],
                    "plate_number" => $plate,
                    "price" => $data['price'],
                    "is_active" => $data['is_active'],
                    "is_avaiable" => true,
                ]);

                if($request->hasFile("photo")) {
                    $path = storage_path("uploads/vehicle/");
                    if(!is_dir($path)) {
                        mkdir($path,755,true);
                    }
                    
                    $image = $request->file('photo');
                    $newName = "vehicle_".date("YmdHis").Str::random(5).".".$request->photo->extension();
                    $move = $image->move($path, $newName);
                    $vehicle->image = $newName;
                    $vehicle->save();
                }
            }
            DB::commit();

            return redirect()->route('vehicle.index')->with('success', 'Vehicle successfuly created');

        } catch (Exception $th) {
            DB::rollback();
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function edit($slug)
    {
        $vehicle = Vehicle::where('slug',$slug)->first();

        $vehicles = Vehicle::select('brand', 'model')->groupBy('model','brand')->get();
        $data = [];
        foreach ($vehicles as $key => $value) {
            $data['model'][] = $value->model;
            $data['brand'][] = $value->brand;
        }
        return view('pages.vehicle.edit', compact('vehicle', 'data'));
    }

    public function update(Request $request, $id)
    {
        try {       
            $validator = Validator::make($request->all(), [
                "brand" => "required",
                "model" => "required",
                "price" => "required",
                "plate_number" => "required",
            ]);

            if($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $data = $validator->validated();
            $data["is_active"] = isset($request->is_enable) ? true : false;


            DB::beginTransaction();

            $vehicle = Vehicle::find($id)->update([
                "brand" => $data['brand'],
                "model" => $data['model'],
                "plate_number" => $data['plate_number'],
                "price" => $data['price'],
                "is_active" => $data['is_active'],
            ]);

            if($request->hasFile("photo")) {
                $path = storage_path("uploads/vehicle/");
                if(!is_dir($path)) {
                    mkdir($path,755,true);
                }
                
                $image = $request->file('photo');
                $newName = "vehicle_".date("YmdHis").Str::random(5).".".$request->photo->extension();
                $move = $image->move($path, $newName);
                $vehicle->image = $newName;
                $vehicle->save();
            }
            
            DB::commit();

            return redirect()->route('vehicle.index')->with('success', 'Vehicle successfuly updated');
        } catch (Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function destroy(Request $request, $id)
    {
        Vehicle::find($id)->delete();
        return redirect()->route('vehicle.index')->with('success', 'Vehicle successfuly deleted');
    }

    public function booking(Request $request, $slug)
    {
        try {
            $start_date = $request->start;
            $end_date = $request->end;

            $vehicle = Vehicle::where('slug', $slug)->first();
            $transaction = Transaction::where('vehicle_id', $vehicle->id)
            ->whereRaw('start_date BETWEEN ? AND ? AND ? BETWEEN start_date AND end_date', [
                $start_date,
                $end_date,
                $start_date
            ])
            ->whereIn('status', ['Booked', 'Active'])
            ->first();
            
            if(!empty($transaction)) {
                return redirect()->back()->with('error', 'Vehicle is not available');
            }

            $date = date('d', strtotime($end_date)) - date('d', strtotime($start_date)) + 1;
            $price = $vehicle->price * $date;
            
            DB::beginTransaction();
            $transaction = Transaction::create([
                "user_id" => Auth::user()->id,
                "vehicle_id" => $vehicle->id,
                "start_date" => $start_date,
                "end_date" => $end_date,
                "status" => "Booked",
                "base_price" => $vehicle->price,
                "total" => $price
            ]);

            if($start_date == date('Y-m-d')) {
                $vehicle->is_available = false;
                $vehicle->available_until = $end_date;
                $vehicle->save();
            }
            DB::commit();
            
            return redirect()->route('transaction.index')->with('success', $vehicle->model.' successfuly booked!');
        } catch (Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function checkstatus(Request $request)
    {
        try {

            $start_date = $request->start_date;
            $end_date = $request->end_date;

            $vehicle = Vehicle::where('slug', $request->model)->first();
            $transaction = Transaction::where('vehicle_id', $vehicle->id)
            ->whereRaw('start_date BETWEEN ? AND ? AND ? BETWEEN start_date AND end_date', [
                $start_date,
                $end_date,
                $start_date
            ])
            ->whereIn('status', ['Booked', 'Active'])
            ->first();

            if(!empty($transaction)) {
                return response([
                    "status" => false,
                    "message" => "Vehicle is not available",
                    "price" => "Rp. 0"
                ]);
            }

            $date = date('d', strtotime($request->end_date)) - date('d', strtotime($request->start_date)) + 1;
            
            $price = $vehicle->price * $date;
            return response([
                "status" => true,
                "message" => "Vehicle is available",
                "price" => "Rp. ".fRupiah($price)
            ]);

        } catch (Exception $e) {
            return response([
                "status" => false,
                "message" => $e->getMessage()
            ], 500);
        }
    }
}