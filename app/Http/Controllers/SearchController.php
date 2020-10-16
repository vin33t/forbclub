<?php

namespace App\Http\Controllers;

use App\Client\Client;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function Client($searchString){
      $clients = Client::where('name','like','%'.$searchString.'%')->orWhere('phone','like','%'.$searchString.'%')->orWhere('email','like','%'.$searchString.'%')->get();
      $data = [];
      foreach($clients as $client){
        $cl = [
          'name'=>$client->name. ' | ' . $client->phone . ' | ' . $client->email,
          'url'=>route('view.client',['slug'=>$client->slug]),
          'icon'=>'fa fa-user',
        ];
        array_push($data,$cl);
      }

      $res = [
        'listItems' => $data
      ];
      return $res;
    }
}
