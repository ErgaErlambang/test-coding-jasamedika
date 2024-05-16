<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Profile;
use Exception, Validator, DB;

class AuthController extends Controller
{
    public function login()
    {
        return view('auth.login');
    }

    public function signin(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                "email" => "required|email",
                "password" => "required"
            ]);

            if($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $remember = isset($request->remember) ? true : false;
            $validated = $validator->validated();

            if(Auth::attempt($validated, $remember)) {
                return redirect()->route('dashboard');
            }

            return redirect()->back()->with("error", "credentials do not match our records.");
            
        } catch (Exception $e) {
            return redirect()->back()->with("error", $e->getMessage())->withInput();
        }
    }

    public function register()
    {
        return view('auth.register');
    }

    public function signup(Request $request)
    {
        try {
            
            $validator = Validator::make($request->all(), [
                "fullname" => "required",
                "email" => "required|email|unique:users",
                "phone" => "required|numeric",
                "sim" => "required|numeric",
                "address" => "required",
                "password" => "required|same:cpassword"
            ]);

            if($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $validated = $validator->validated();

            DB::beginTransaction();

            $user = User::create([
                "name" => $validated['fullname'],
                "email" => $validated['email'],
                "password" => Hash::make($validated['password']),
                "role_id" => 2
            ]);

            $profile = Profile::create([
                "phone_number" => $validated['phone'],
                "driver_license" => $validated['sim'],
                "address" => $validated['address'],
                "user_id" => $user->id
            ]);

            DB::commit();

            Auth::attempt(['email' => $validated['email'], 'password' => $validated['password']]);
            return redirect()->route('dashboard');

        } catch (Exception $e) {
            DB::rollback();
            return redirect()->back()->with("error", $e->getMessage())->withInput();
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->flush();

        return redirect()->route('auth.login');
    }
}