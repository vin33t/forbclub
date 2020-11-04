<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    // Dashboard - Analytics
    public function dashboardAnalytics(){

      if(Auth::user()->client){
          $client = Auth::user()->client;
        $breadcrumbs = [
          ['link' => "/dashboard-analytics", 'name' => "Home"], ['name' => "Client"], ['name' => $client->name]
        ];
        return view('/client/profile', [
          'breadcrumbs' => $breadcrumbs
        ], compact('client'));
      }  elseif(Auth::user()->name = 'Amit Chhada'){
          return redirect()->route('reimbursement.summary');
      } else {

        $pageConfigs = [
            'pageHeader' => false
        ];

        return view('/pages/dashboard-analytics', [
            'pageConfigs' => $pageConfigs
        ]);

      }

    }



    // Dashboard - Ecommerce
    public function dashboardEcommerce(){
        $pageConfigs = [
            'pageHeader' => false
        ];

        return view('/pages/dashboard-ecommerce', [
            'pageConfigs' => $pageConfigs
        ]);
    }
}

