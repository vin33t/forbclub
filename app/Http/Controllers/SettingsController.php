<?php

namespace App\Http\Controllers;

use App\Employee;
use App\Traits\Settings\RoleManagementTrait;
use Illuminate\Http\Request;


class SettingsController extends Controller
{
  use RoleManagementTrait;

  public function createEmployee(Request $request){
    $request->validate([
      'name'=>'required|string',
      'phone'=>'required|integer',
      'department'=>'required|string',
      'email'=>'required|unique:employees,email',
    ]);
    $employee = Employee::create([
      'name'=>$request->name,
      'phone'=>$request->phone,
      'department'=>$request->department,
      'email'=>$request->email
    ]);
    return redirect()->back();
  }
}
