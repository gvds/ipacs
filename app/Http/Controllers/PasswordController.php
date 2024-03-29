<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PasswordController extends Controller {

  public function __construct() {
    $this->middleware('auth');
  }


  public function showChangePasswordForm(){
    return view('auth.passwords.change');
  }

  public function changePassword(Request $request){

    if (!(\Hash::check($request->get('current-password'), \Auth::user()->password))) {
      // The passwords matches
      return redirect()->back()->with("error","Your current password does not match the password you provided. Please try again.");
    }

    if(strcmp($request->get('current-password'), $request->get('new-password')) == 0){
      //Current password and new password are same
      return redirect()->back()->with("error","New Password cannot be same as your current password. Please choose a different password.");
    }

    $validatedData = $request->validate([
      'current-password' => 'required',
      'new-password' => 'required|string|min:10|confirmed',
    ]);

    //Change Password
    $user = \Auth::user();
    $user->password = bcrypt($request->get('new-password'));
    $user->save();

    return redirect()->back()->with("success","Password changed successfully !");
  }

}
