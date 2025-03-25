<?php

namespace App\Http\Controllers;

use DataTables;

use App\Models\User;
use App\Models\UserType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;


class SettingController extends Controller
{
    public function __construct()
    {
        $this->middleware('superadmin')->except(['passwordChange', 'updatePassword']);
    }

    /**
     * Display a listing of the users.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $users = User::with('userType')->get(); // Fetch users with their user types
        $userTypes = UserType::all();
        return view('user.index', compact('users', 'userTypes'));
    }

    /**
     * Show the form for creating a new user.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $userTypes = UserType::all(); // Fetch all user types
        return view('user.create', compact('userTypes'));
    }

    /**
     * Store a newly created user in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $user = new User();
        $user->name = $request->input('name');
        $user->email = $request->input('email');
          $user->phone = $request->input('phone');
        $user->password = Hash::make($request->input('password'));
        $user->user_type_id = $request->input('user_type_id'); // Set user type
        $user->save();

        Session::flash('success', 'User created successfully.');
        return redirect()->route('user.index');
    }

    /**
     * Show the form for editing the specified user.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);
        $userTypes = UserType::all(); // Fetch all user types
        return view('user.edit', compact('user', 'userTypes'));
    }

    /**
     * Update the specified user in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $user->name = $request->input('name');
        $user->email = $request->input('email');
          $user->phone = $request->input('phone');
        $user->user_type_id = $request->input('user_type_id'); // Update user type
        $user->save();

        Session::flash('success', 'User updated successfully.');
        return redirect()->route('user.index');
    }

    /**
     * Show the form for changing the user's password.
     *
     * @return \Illuminate\View\View
     */
    public function passwordChange()
    {
        return view('user.passwordChange');
    }


 
public function updatePassword(Request $request)
{
    // Validate the request
    $request->validate([
        'current_password' => 'required',
        'new_password' => 'required|min:6|confirmed',
    ]);

    $user = Auth::user();

    // Check if the current password matches
    if (!Hash::check($request->input('current_password'), $user->password)) {
        Session::flash('error', 'The current password is incorrect.');
        return redirect()->back();
    }

    // Update the password
    $user->password = Hash::make($request->input('new_password'));
    $user->save();

    // Log out the user
    Auth::logout();

    Session::flash('success', 'Password changed successfully. Please login again.');
    return redirect()->route('login');
}
     
     }
     
     
   