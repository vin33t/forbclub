<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravolt\Avatar\Avatar;


class UserController extends Controller
{
  public function profile()
  {
    $breadcrumbs = [
      ['link' => "/dashboard-analytics", 'name' => "Home"], ['name' => "Profile"]
    ];
    toast('Info Toast', 'info');
    return view('/users/profile', [
      'breadcrumbs' => $breadcrumbs
    ]);
  }

  public function updatePassword(Request $request)
  {
    $request->validate([
      'old_password' => 'required',
      'password' => 'required|confirmed|min:8',
    ]);
    if(\Auth::attempt(["email" => Auth::user()->email , "password" => $request->old_password])){
      Auth::user()->update([
        'password'=>Hash::make($request->password)
      ]);
      notifyToast('success','Updated','Password Updated Successfully');
      return redirect()->back();
    }
    else{
      notifyToast('error','OOPS!!','Incorrect Old Password');
      return redirect()->back();
    }
  }
}
