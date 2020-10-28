<?php

namespace App\Http\Controllers\Client;

use App\Client\Booking\BookingOffer;
use App\Client\Booking\BookingOfferInfo;
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

    public function BookingOffer($bookingId){
        $booking =  Bookings::find($bookingId);
        if($booking){
          if(!$booking->bookingOffer){
            if($booking->holidayType == 'Stay Only Holiday'){
              return view('client.booking.offer.stayOnly')->with('booking',$booking);
            }
          } else {
            if($booking->holidayType == 'Stay Only Holiday'){
              return view('client.booking.offer.editStayOnly')->with('booking',$booking)->with('convert',0);
            }
          }
        }
    }

    public function storeBookingOffer(Request $request){
      $booking = Bookings::find($request->booking_id);
      $bo = new BookingOffer;

      $bo->bookings_id = $booking->id;
      $bo->holiday_type = $request->holiday_type;
      $bo->destination = $request->offer_destination;
      $bo->date_of_travel = $request->date_of_travel;
      $bo->save();

      $hotel_counter = 0;
      $flight_counter = 0;

      foreach($request->service_type as $index => $service_type){
        if($request->service_type[$index] == 'Hotel'){
          $boi = new BookingOfferInfo;
          $boi->booking_offer_id = $bo->id;
          $boi->service_type = $request->service_type[$index];
          $boi->vendor_name = $request->vendor[$index];
          $boi->destination = $request->destination[$index];
          $boi->remarks = $request->remarks[$index];
          if($request->add_on[$index] == 0){
            $boi->vendor_price = $request->vendor_price[$index];
            $boi->our_price = $request->our_price[$index];
          }else{
            $boi->add_on_service_price = $request->add_on_service_price[$index];
            $boi->amount_paid_by_client = $request->amount_paid_by_client[$index];
            $boi->add_on = 1;
          }

          $boi->add_more = $request->add_more[$index];

          $boi->nights = $request->nights[$hotel_counter];
          $boi->pax = $request->pax[$hotel_counter];
          $boi->check_in = $request->check_in[$hotel_counter];
          $boi->check_out = $request->check_out[$hotel_counter];
          $boi->hotel_name = $request->hotel_name[$hotel_counter];
          $boi->save();
          $hotel_counter++;

        }elseif($request->service_type[$index] == 'Land Package/Transfer'){
          $boi = new BookingOfferInfo;
          $boi->booking_offer_id = $bo->id;
          $boi->service_type = $request->service_type[$index];
          $boi->vendor_name = $request->vendor[$index];
          $boi->destination = $request->destination[$index];
          $boi->remarks = $request->remarks[$index];

          if($request->add_on[$index] == 0){
            $boi->vendor_price = $request->vendor_price[$index];
            $boi->our_price = $request->our_price[$index];
          }else{
            $boi->add_on_service_price = $request->add_on_service_price[$index];
            $boi->amount_paid_by_client = $request->amount_paid_by_client[$index];
            $boi->add_on = 1;
          }
          $boi->add_more = $request->add_more[$index];
          $boi->nights = $request->nights[$hotel_counter];
          $boi->pax = $request->pax[$hotel_counter];
          $boi->check_in = $request->check_in[$hotel_counter];
          $boi->check_out = $request->check_out[$hotel_counter];
          $boi->hotel_name = $request->hotel_name[$hotel_counter];
          $boi->save();
          $hotel_counter++;

        }elseif($request->service_type[$index] == 'Flight'){
          $boi = new BookingOfferInfo;
          $boi->booking_offer_id = $bo->id;
          $boi->service_type = $request->service_type[$index];
          $boi->vendor_name = $request->vendor[$index];
          $boi->destination = $request->destination[$index];
          $boi->remarks = $request->remarks[$index];


          if($request->add_on[$index] == 0){
            $boi->vendor_price = $request->vendor_price[$index];
            $boi->our_price = $request->our_price[$index];
          }else{
            $boi->add_on_service_price = $request->add_on_service_price[$index];
            $boi->amount_paid_by_client = $request->amount_paid_by_client[$index];
            $boi->add_on = 1;
          }
          $boi->check_in_baggage = $request->check_in_baggage[$flight_counter];
          $boi->check_in_baggage_price = $request->check_in_baggage_price[$flight_counter];
          $boi->cabin_baggage = $request->cabin_baggage[$flight_counter];
          $boi->add_more = $request->add_more[$index];
          $boi->flight_pax = $request->flight_pax[$flight_counter];
          $boi->flight_details = $request->flight_details[$flight_counter];
          $boi->save();
          $flight_counter++;

        }elseif($request->service_type[$index] == 'Visa'){
          $boi = new BookingOfferInfo;
          $boi->booking_offer_id = $bo->id;
          $boi->service_type = $request->service_type[$index];
          $boi->vendor_name = $request->vendor[$index];
          $boi->destination = $request->destination[$index];
          $boi->remarks = $request->remarks[$index];

          if($request->add_on[$index] == 0){
            $boi->vendor_price = $request->vendor_price[$index];
            $boi->our_price = $request->our_price[$index];
          }else{
            $boi->add_on_service_price = $request->add_on_service_price[$index];
            $boi->amount_paid_by_client = $request->amount_paid_by_client[$index];
            $boi->add_on = 1;
          }
          $boi->add_more = $request->add_more[$index];
          $boi->save();

        }elseif($request->service_type[$index] == 'Insurance'){
          $boi = new BookingOfferInfo;
          $boi->booking_offer_id = $bo->id;
          $boi->service_type = $request->service_type[$index];
          $boi->vendor_name = $request->vendor[$index];
          $boi->destination = $request->destination[$index];
          $boi->remarks = $request->remarks[$index];

          if($request->add_on[$index] == 0){
            $boi->vendor_price = $request->vendor_price[$index];
            $boi->our_price = $request->our_price[$index];
          }else{
            $boi->add_on_service_price = $request->add_on_service_price[$index];
            $boi->amount_paid_by_client = $request->amount_paid_by_client[$index];
            $boi->add_on = 1;
          }
          $boi->add_more = $request->add_more[$index];
          $boi->save();

        }elseif($request->service_type[$index] == 'Cruise'){
          $boi = new BookingOfferInfo;
          $boi->booking_offer_id = $bo->id;
          $boi->service_type = $request->service_type[$index];
          $boi->vendor_name = $request->vendor[$index];
          $boi->destination = $request->destination[$index];
          $boi->remarks = $request->remarks[$index];

          if($request->add_on[$index] == 0){
            $boi->vendor_price = $request->vendor_price[$index];
            $boi->our_price = $request->our_price[$index];
          }else{
            $boi->add_on_service_price = $request->add_on_service_price[$index];
            $boi->amount_paid_by_client = $request->amount_paid_by_client[$index];
            $boi->add_on = 1;
          }
          $boi->add_more = $request->add_more[$index];
          $boi->save();

        }

      }
      return redirect()->back();
    }




}
