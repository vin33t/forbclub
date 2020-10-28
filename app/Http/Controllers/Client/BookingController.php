<?php

namespace App\Http\Controllers\Client;

use App\Client\Booking\BookingInfo;
use App\Client\Booking\Bookings;
use App\Client\Client;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use SebastianBergmann\Comparator\Book;

class BookingController extends Controller
{

  public function index(){
    $bookings = Bookings::where('status',NULL)->get();
    return view('client.booking.index')->with('bookings',$bookings);
  }

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

    public function storeBooking(Request $request,$slug)
    {
      $client = Client::where('slug',$slug)->first();
      try {
      DB::beginTransaction();
      $b =  new Bookings;
      $b->clientId = $client->id;
      $b->bookingRequestDate = $request->booking_request_date;
      $b->travelDate = $request->travel_date;
      $b->totalNights = $request->total_nights;
      $b->holidayType = $request->holiday_type;
      $b->eligible = 1;
      $b->breakfast = $request->breakfast;
      $b->remarks = $request->remarks;
      $b->addedBy =Auth::user()->id;
      $b->save();
      foreach($request->destination as $index => $d){
        $bi = new BookingInfo();
        $bi->bookings_id = $b->id;
        $bi->destination = $request->destination[$index];
        $bi->nights = $request->nights[$index];
        $bi->adults = $request->adults[$index];
        $bi->kids = $request->kids[$index];
        $bi->check_in = $request->check_in[$index];
        $bi->check_out = $request->check_out[$index];
        $bi->save();
        DB::commit();
      }
      } catch(\Exception $e){
        DB::rollBack();
      }
      return redirect()->route('view.client',['slug'=>$client->slug]);
    }

    public function updateStatus(Request $request,$bookingId){
      $booking =  Bookings::find($bookingId);
      $booking->status = $request->status;
      $booking->statusRemarks = $request->remarks;
      $booking->statusUpdatedBy = Auth::user()->id;
      $booking->statusUpdatedOn = Carbon::now();
      $booking->save();
      return redirect()->back();
    }
    public function inProcessingByMrd(){
    $bookings = Bookings::where('status','approved')->get();
        return view('client.booking.inProcessingByMrd')->with('bookings',$bookings);
    }
}
