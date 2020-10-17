<?php

namespace App\Http\Controllers;

use App\Client\Client;
use App\Client\Package\SoldPackages;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function Client($searchString){
      $clients = Client::where('name','like','%'.$searchString.'%')->orWhere('phone','like','%'.$searchString.'%')->orWhere('email','like','%'.$searchString.'%')->with(['Packages' => function($query) use ($searchString){
        $query->where('mafNo', 'like', '%'.$searchString.'%')->orWhere('fclpId', 'like', '%'.$searchString.'%');
      }])->get();
      $data = [];
      foreach($clients as $client){
        $cl = [
          'name'=>$client->name. ' | ' . $client->phone . ' | ' . $client->email ,
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

    public function maf(Request $request){
      $package = SoldPackages::where('mafNo', $request->mafNo);
      if($package->count()){
        return redirect()->route('view.client',['slug'=>$package->first()->client->slug]);
      } else{
        notifyToast('OPPS','Invalid Maf No',  'Try Again');
        return redirect()->back();
      }
    }

    public function fclp(Request $request){
      $package = SoldPackages::where('fclpId', $request->mafNo);
      if($package->count()){
        return redirect()->route('view.client',['slug'=>$package->first()->client->slug]);
      } else{
        notifyToast('OPPS','Invalid FCLP',  'Try Again');
        return redirect()->back();
      }
    }
}
