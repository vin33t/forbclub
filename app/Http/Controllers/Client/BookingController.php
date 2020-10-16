<?php

namespace App\Http\Controllers\Client;

use App\Client\Client;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function createBooking($slug)
    {
      $client = Client::where('slug',$slug)->first();
      $breadcrumbs = [
        ['link' => "/dashboard-analytics", 'name' => "Home"], ['link'=>route('view.client',['slug'=>$client->slug]),'name' => "Client"], ['name' => 'Create Booking']
      ];
      return view('/client/booking/create', [
        'breadcrumbs' => $breadcrumbs
      ],compact('client'));
    }
}
