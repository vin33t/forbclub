<?php

namespace App\Http\Controllers;

use App\todo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CalenderController extends Controller
{
  public function index()
  {
    $pageConfigs = [
      'pageHeader' => false
    ];

    return view('/calender', [
      'pageConfigs' => $pageConfigs
    ]);
  }

  public function addTodo(Request $request)
  {
//    return $request;
    if($request->id){
      $todo = todo::find($request->id);
    } else {
    $todo = new todo;
    }

    $todo->startDate = $request->startDate;
    $todo->endDate = $request->endDate;
    $todo->title = $request->title;
    $todo->description = $request->description;
    if ($request->color != '') {
      $todo->color = $request->color;
    }    if ($request->eventColor != '') {
      $todo->eventColor = $request->eventColor;
    }
    $todo->allDay = $request->allDay;
    $todo->user_id = Auth::user()->id;
    $todo->save();
    return $todo;
  }

  public function list(Request $request)
  {
    return Auth::user()->todo;
  }

  public function delete($id)
  {
    todo::find($id)->delete();
  }

  public function update(Request $request,$id)
  {

  }
}
