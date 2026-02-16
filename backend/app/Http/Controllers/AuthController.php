<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function loginShow()
    {
        if(auth()->check()){
            return 'Logged in';
        }
        return view('login');
    }

    public function registerShow()
    {
        return view('register');
    }

    public function register(Request $request) {

        $validatedData = $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed'
        ]);

        $createUser = new User;
        $createUser->name = $validatedData['name'];
        $createUser->email = $validatedData['email'];
        $createUser->mobile = '09224573224';
        $createUser->password = $validatedData['password'];
        $createUser->city_id=1;
        $createUser->save();
        auth()->login($createUser);

        return redirect('/ainfo');

    }

    public function login(Request $request)
    {
        $user = User::where('email', $request->email)->first();
        if($user && Hash::check($request->password, $user->password)) {


            $credentials = ['email' => $request->email, 'password' => $request->password];
            if (Auth::attempt($credentials, true)) {
                return 'Logged in';
            }
            else{
                return 'Login Failed';
            }

        } else {
            return 'Invalid Credentials';
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        setcookie('city_id', '', -1, '/');
        return redirect(route('d.login'));
    }

    public function logoutClub(Request $request)
    {
        Auth::logout();
        setcookie('city_id', '', -1, '/');
        return redirect(route('cl.login'));
    }
}
