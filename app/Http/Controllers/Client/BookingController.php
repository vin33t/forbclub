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
use Illuminate\Support\Facades\Mail;
use SebastianBergmann\Comparator\Book;
use App\Client\Holiday\ClientHoliday;
use App\Client\Holiday\ClientHolidayDetails;
use App\Client\Holiday\ClientHolidayTransactions;
class BookingController extends Controller
{

  public function cancelBooking($id){
    $booking =  Bookings::find($id);
    $booking->cancelled_booking = 1;
    $booking->save();
    return redirect()->back();

  }

  public function index(){
    $bookings = Bookings::where('status',NULL)->get();
    return view('client.booking.index')->with('bookings',$bookings);
  }
  public function show(){
    $bookings = Bookings::all();
    return view('client.booking.all')->with('bookings',$bookings);
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
      $b->exception = $request->exception;
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
    public function updateBooking(Request $request,$id)
    {
      $b = Bookings::find($id);
      try {
      DB::beginTransaction();
      $b->bookingRequestDate = $request->booking_request_date;
      $b->travelDate = $request->travel_date;
      $b->totalNights = $request->total_nights;
      $b->holidayType = $request->holiday_type;
      $b->eligible = 1;
      $b->breakfast = $request->breakfast;
      $b->remarks = $request->remarks;
      $b->save();
      foreach($b->bookingInfo as $bin){
        $bin->delete();
      }
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
      return redirect()->route('booking');
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

    public function approveOffer(Request $request,$bookingId){
      $booking =  Bookings::find($bookingId);
      $booking->offerStatus = $request->status;
      $booking->offerStatusRemarks = $request->remarks;
      $booking->offerStatusUpdatedBy = Auth::user()->id;
      $booking->offerStatusUpdatedOn = Carbon::now();
      $booking->save();
      return redirect()->back();
    }



    public function inProcessingByMrd(){
    $bookings = Bookings::where('status','approved')->where('cancelled_booking',0)->where('offerStatus',NULL)->get();
        return view('client.booking.inProcessingByMrd')->with('bookings',$bookings);
    }

    public function approvedByManager(){
    $bookings = Bookings::where('status','approved')->where('cancelled_booking',0)->where('offerStatus','approved')->doesntHave('ClientHoliday')->get();
        return view('client.booking.approvedByManager')->with('bookings',$bookings);
    }

    public function holidayInProgress(){
    $bookings = Bookings::where('status','approved')->where('offerStatus','approved')->whereHas('ClientHoliday')->get();
        return view('client.booking.holidayInProgress')->with('bookings',$bookings);
    }

    public function BookingOffer($bookingId){
        $booking =  Bookings::find($bookingId);
        if($booking){
          if(!$booking->bookingOffer){
            if($booking->holidayType == 'Stay Only Holiday'){
              return view('client.booking.offer.stayOnly')->with('booking',$booking);
            }  elseif($booking->holidayType == 'Adjustment'){
              return view('client.booking.offer.adjustment')->with('booking',$booking);
            } elseif($booking->holidayType == 'Fully Paid Holiday'){
              return view('client.booking.offer.fullyPaid')->with('booking',$booking);
            }elseif($booking->holidayType == 'Flight Only'){
              return view('client.booking.offer.flight')->with('booking',$booking);
            }elseif($booking->holidayType == 'Offer Nights'){
              return view('client.booking.offer.offerNights')->with('booking',$booking);
            }
          } else {
            if($booking->holidayType == 'Stay Only Holiday'){
              return view('client.booking.offer.editStayOnly')->with('booking',$booking)->with('convert',0);
            }  elseif($booking->holidayType == 'Adjustment'){
              return view('client.booking.offer.editAdjustment')->with('booking',$booking)->with('convert',0);
            } elseif($booking->holidayType == 'Fully Paid Holiday'){
              return view('client.booking.offer.editFullyPaid')->with('booking',$booking)->with('convert',0);
            }elseif($booking->holidayType == 'Flight Only'){
              return view('client.booking.offer.editFlight')->with('booking',$booking)->with('convert',0);
            }elseif($booking->holidayType == 'Offer Nights'){
              return view('client.booking.offer.editOfferNights')->with('booking',$booking)->with('convert',0);
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
      return redirect()->route('booking.processing.mrd');
    }


  public function updateBookingOffer(Request $request){
    $booking = \App\Client\Booking\Bookings::find($request->booking_id);
    $bo = $booking->BookingOffer;

    $bo->bookings_id = $booking->id;
    $bo->holiday_type = $request->holiday_type;
    $bo->destination = $request->offer_destination;
    $bo->date_of_travel = $request->date_of_travel;
    $bo->save();

    foreach($bo->BookingOfferInfo as $boi){
      $boi->delete();
    }

    $hotel_counter = 0;
    $flight_counter = 0;

    foreach($request->service_type as $index => $service_type){
      if($request->service_type[$index] == 'Hotel'){
        $boi = new \App\Client\Booking\BookingOfferInfo;
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

        $boi->nights = $request->nights[$hotel_counter];
        $boi->pax = $request->pax[$hotel_counter];
        $boi->check_in = $request->check_in[$hotel_counter];
        $boi->check_out = $request->check_out[$hotel_counter];
        $boi->hotel_name = $request->hotel_name[$hotel_counter];
        $boi->add_more = $request->add_more[$index];
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

        $boi->nights = $request->nights[$hotel_counter];
        $boi->pax = $request->pax[$hotel_counter];
        $boi->check_in = $request->check_in[$hotel_counter];
        $boi->check_out = $request->check_out[$hotel_counter];
        $boi->hotel_name = $request->hotel_name[$hotel_counter];
        $boi->add_more = $request->add_more[$index];
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
        $boi->flight_pax = $request->flight_pax[$flight_counter];
        $boi->flight_details = $request->flight_details[$flight_counter];
        $boi->add_more = $request->add_more[$index];
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


    return redirect()->route('booking.processing.mrd');

//    return redirect()->back();
  }

  public function ConvertBooking($bookingId){
    $booking =  Bookings::find($bookingId);

        if($booking->holidayType == 'Stay Only Holiday'){
          return view('client.booking.offer.editStayOnly')->with('booking',$booking)->with('convert',1);
        }  elseif($booking->holidayType == 'Adjustment'){
          return view('client.booking.offer.editAdjustment')->with('booking',$booking)->with('convert',1);
        } elseif($booking->holidayType == 'Fully Paid Holiday'){
          return view('client.booking.offer.editFullyPaid')->with('booking',$booking)->with('convert',1);
        }elseif($booking->holidayType == 'Flight Only'){
          return view('client.booking.offer.editFlight')->with('booking',$booking)->with('convert',1);
        }elseif($booking->holidayType == 'Offer Nights'){
          return view('client.booking.offer.editOfferNights')->with('booking',$booking)->with('convert',1);
        }
  }

  public function AddTransaction(Request $request){
    $booking = Bookings::find($request->booking_id);
    $boi = BookingOfferInfo::find($request->boi_id);
    $bo = $booking->BookingOffer;
    $add_on = Bookings::find($request->booking_id)->BookingOffer->BookingOfferInfo->where('add_on',1);
    // dd($booking, $boi, $bo, $add_on);
    return view('client.booking.addTransaction')->with('booking',$booking)
      ->with('boi',$boi)
      ->with('add_on',$add_on)
      ->with('bo',$bo);
  }

  public function Convert(Request $request){
//    return $request;
    // dd($request->all());
    $booking = Bookings::find($request->booking_id);
    $ch = new ClientHoliday;
    $ch->client_id = $booking->clientId;
    $ch->bookings_id = $booking->id;
    $ch->holiday_type = $request->holiday_type;
    $ch->destination = $request->offer_destination;
    $ch->date_of_travel = $request->date_of_travel;
    $ch->converted_by = Auth::id();
    $ch->save();

    $hotel_counter = 0;
    $flight_counter = 0;

    foreach($request->service_type as $index => $service_type){
      if($service_type == 'Hotel'){
        $chd = new ClientHolidayDetails;
        $chd->client_holiday_id = $ch->id;
        $chd->service_type = $service_type;
        $chd->vendor_name = $request->vendor[$index];
        $chd->destination = $request->destination[$index];
        $chd->remarks = $request->remarks[$index];

        if($request->add_on[$index] == 0){
          $chd->vendor_price = $request->vendor_price[$index];
          $chd->our_price = $request->our_price[$index];
        }else{
          $chd->add_on_service_price = $request->add_on_service_price[$index];
          $chd->amount_paid_by_client = $request->amount_paid_by_client[$index];
          $chd->add_on = 1;
        }

        $chd->nights = $request->nights[$hotel_counter];
        $chd->pax = $request->pax[$hotel_counter];
        $chd->check_in = $request->check_in[$hotel_counter];
        $chd->check_out = $request->check_out[$hotel_counter];
        $chd->hotel_name = $request->hotel_name[$hotel_counter];
        $chd->save();
        $hotel_counter++;


        foreach($request->amount as $k=>$amount){
          if($request->verify[$k] == $request->verify_token[$index] ){
            $cht = new ClientHolidayTransactions;
            $cht->client_holiday_details_id = $chd->id;
            $cht->amount = $amount;
            $cht->client_id = $booking->clientId;
            $cht->date_of_payment = $request->date_of_payment[$k];
            $cht->mode_of_payment  = $request->mode_of_payment[$k];
            if($request->mode_of_payment[$k] == null){
              $cht->paid  = 0;
            }else{
              $cht->paid  = 1;
            }
            $cht->last_four_card_digits = $request->last_four_card_digits[$k];
            $cht->card_description = $request->card_description[$k];
            $cht->bank_name = $request->bank_name[$k];
            $cht->cheque_number = $request->cheque_number[$k];
            $cht->add_on = $request->add_on[$index];
            $cht->save();
          }
        }

      }elseif($service_type == 'Land Package/Transfer'){
        $chd = new ClientHolidayDetails;
        $chd->client_holiday_id = $ch->id;
        $chd->service_type = $service_type;
        $chd->vendor_name = $request->vendor[$index];
        $chd->destination = $request->destination[$index];
        $chd->remarks = $request->remarks[$index];

        if($request->add_on[$index] == 0){
          $chd->vendor_price = $request->vendor_price[$index];
          $chd->our_price = $request->our_price[$index];
        }else{
          $chd->add_on_service_price = $request->add_on_service_price[$index];
          $chd->amount_paid_by_client = $request->amount_paid_by_client[$index];
          $chd->add_on = 1;
        }

        $chd->nights = $request->nights[$hotel_counter];
        $chd->pax = $request->pax[$hotel_counter];
        $chd->check_in = $request->check_in[$hotel_counter];
        $chd->check_out = $request->check_out[$hotel_counter];
        $chd->hotel_name = $request->hotel_name[$hotel_counter];
        $chd->save();
        $hotel_counter++;

        foreach($request->amount as $k=>$amount){
          if($request->verify[$k] == $request->verify_token[$index] ){
            $cht = new ClientHolidayTransactions;
            $cht->client_holiday_details_id = $chd->id;
            $cht->amount = $amount;
            $cht->client_id = $booking->clientId;
            $cht->date_of_payment = $request->date_of_payment[$k];
            $cht->mode_of_payment  = $request->mode_of_payment[$k];
            if($request->mode_of_payment[$k] == null){
              $cht->paid  = 0;
            }else{
              $cht->paid  = 1;
            }
            $cht->last_four_card_digits = $request->last_four_card_digits[$k];
            $cht->card_description = $request->card_description[$k];
            $cht->bank_name = $request->bank_name[$k];
            $cht->cheque_number = $request->cheque_number[$k];
            $cht->add_on = $request->add_on[$index];
            $cht->save();
          }
        }

      }elseif($service_type == 'Flight'){
        $chd = new ClientHolidayDetails;
        $chd->client_holiday_id = $ch->id;
        $chd->service_type = $service_type;
        $chd->vendor_name = $request->vendor[$index];
        $chd->destination = $request->destination[$index];
        $chd->remarks = $request->remarks[$index];

        if($request->add_on[$index] == 0){
          $chd->vendor_price = $request->vendor_price[$index];
          $chd->our_price = $request->our_price[$index];
        }else{
          $chd->add_on_service_price = $request->add_on_service_price[$index];
          $chd->amount_paid_by_client = $request->amount_paid_by_client[$index];
          $chd->add_on = 1;
        }

        $chd->flight_pax = $request->flight_pax[$flight_counter];
        $chd->flight_details = $request->flight_details[$flight_counter];
        $chd->save();
        $flight_counter++;

        foreach($request->amount as $k=>$amount){
          if($request->verify[$k] == $request->verify_token[$index] ){
            $cht = new ClientHolidayTransactions;
            $cht->client_holiday_details_id = $chd->id;
            $cht->amount = $amount;
            $cht->client_id = $booking->clientId;
            $cht->date_of_payment = $request->date_of_payment[$k];
            $cht->mode_of_payment  = $request->mode_of_payment[$k];
            if($request->mode_of_payment[$k] == null){
              $cht->paid  = 0;
            }else{
              $cht->paid  = 1;
            }
            $cht->last_four_card_digits = $request->last_four_card_digits[$k];
            $cht->card_description = $request->card_description[$k];
            $cht->bank_name = $request->bank_name[$k];
            $cht->cheque_number = $request->cheque_number[$k];
            $cht->add_on = $request->add_on[$index];
            $cht->save();
          }
        }

      }elseif($service_type == 'Visa'){
        $chd = new ClientHolidayDetails;
        $chd->client_holiday_id = $ch->id;
        $chd->service_type = $service_type;
        $chd->vendor_name = $request->vendor[$index];
        $chd->destination = $request->destination[$index];
        $chd->remarks = $request->remarks[$index];

        if($request->add_on[$index] == 0){
          $chd->vendor_price = $request->vendor_price[$index];
          $chd->our_price = $request->our_price[$index];
        }else{
          $chd->add_on_service_price = $request->add_on_service_price[$index];
          $chd->amount_paid_by_client = $request->amount_paid_by_client[$index];
          $chd->add_on = 1;
        }

        $chd->save();

        foreach($request->amount as $k=>$amount){
          if($request->verify[$k] == $request->verify_token[$index] ){
            $cht = new ClientHolidayTransactions;
            $cht->client_holiday_details_id = $chd->id;
            $cht->amount = $amount;
            $cht->client_id = $booking->clientId;
            $cht->date_of_payment = $request->date_of_payment[$k];
            $cht->mode_of_payment  = $request->mode_of_payment[$k];
            if($request->mode_of_payment[$k] == null){
              $cht->paid  = 0;
            }else{
              $cht->paid  = 1;
            }
            $cht->last_four_card_digits = $request->last_four_card_digits[$k];
            $cht->card_description = $request->card_description[$k];
            $cht->bank_name = $request->bank_name[$k];
            $cht->cheque_number = $request->cheque_number[$k];
            $cht->add_on = $request->add_on[$index];
            $cht->save();
          }
        }

      }elseif($service_type == 'Insurance'){
        $chd = new ClientHolidayDetails;
        $chd->client_holiday_id = $ch->id;
        $chd->service_type = $service_type;
        $chd->vendor_name = $request->vendor[$index];
        $chd->destination = $request->destination[$index];
        $chd->remarks = $request->remarks[$index];

        if($request->add_on[$index] == 0){
          $chd->vendor_price = $request->vendor_price[$index];
          $chd->our_price = $request->our_price[$index];
        }else{
          $chd->add_on_service_price = $request->add_on_service_price[$index];
          $chd->amount_paid_by_client = $request->amount_paid_by_client[$index];
          $chd->add_on = 1;
        }

        $chd->save();

        foreach($request->amount as $k=>$amount){
          if($request->verify[$k] == $request->verify_token[$index] ){
            $cht = new ClientHolidayTransactions;
            $cht->client_holiday_details_id = $chd->id;
            $cht->amount = $amount;
            $cht->client_id = $booking->clientId;
            $cht->date_of_payment = $request->date_of_payment[$k];
            $cht->mode_of_payment  = $request->mode_of_payment[$k];
            if($request->mode_of_payment[$k] == null){
              $cht->paid  = 0;
            }else{
              $cht->paid  = 1;
            }
            $cht->last_four_card_digits = $request->last_four_card_digits[$k];
            $cht->card_description = $request->card_description[$k];
            $cht->bank_name = $request->bank_name[$k];
            $cht->cheque_number = $request->cheque_number[$k];
            $cht->add_on = $request->add_on[$index];
            $cht->save();
          }
        }

      }

    }

    return redirect()->route('booking');
  }


  public function DeniedByMrd(){
    $bookings = Bookings::where('status','rejected')->get();
    return view('client.booking.deniedByMrd')->with('bookings',$bookings);
  }

  public function DeniedByManager(){
    $bookings = Bookings::where('offerStatus','rejected')->get();
    return view('client.booking.deniedByManager')->with('bookings',$bookings);
  }

  public function convertedToHoliday(){
    $bookings = Bookings::whereHas('ClientHoliday')->get();
    return view('client.booking.deniedByManager')->with('bookings',$bookings);
  }


  public function editBooking(Request $request, $id){
    $booking = Bookings::find($id);
    return view('client.booking.edit')->with('booking',$booking);
  }


  public function cancelClientHolidays(Request $request){
    $holiday = ClientHoliday::find($request->id);
    foreach ($holiday->clientHolidayDetails as $details){
      foreach ($details->ClientHolidayTransactions as $transaction){
        $transaction->cancelled = 1;
        $transaction->cancelled_on = Carbon::now();
        $transaction->cancelled_by = Auth::user()->id;
        $transaction->save();
      }
      $details->cancelled = 1;
      $details->cancelled_on = Carbon::now();
      $details->cancelled_by = Auth::user()->id;
      $details->save();
    }
    $holiday->cancelled = 1;
    $holiday->cancelled_on = Carbon::now();
    $holiday->cancelled_by = Auth::user()->id;
    $holiday->save();
    return redirect()->back();
  }


  public function addPartialPayment(Request $request){
    ClientHolidayTransactions::create([
      'client_holiday_details_id' => $request->holidayDetailsId,
      'add_on' => $request->addonPartial,
      'paid' => $request->paid,
      'amount' => $request->amount,
      'date_of_payment' => $request->date_of_payment,
      'client_id' => $request->client_id,
    ]);
    return redirect()->back();
  }

  public function  makePartialPayment(Request $request){
//        return $request;
    $payment = ClientHolidayTransactions::findOrFail($request->paymentId);
    $payment->date_of_payment = $request->dateOfPayment;
    $payment->amount = $request->paymentAmount;
    $payment->mode_of_payment = $request->modeOfPayment;
    if($request->mode_of_payment == 'Card'){
      $payment->bank_name = $request->cardBankName;
      $payment->last_four_card_digits = $request->cardLastFourDigits;
      $payment->card_description = $request->cardDescription;
    }
    if($request->mode_of_payment == 'Online' || $request->mode_of_payment == 'Bank Transfer'){
      $payment->bank_name = $request->bankName;
    }
    if($request->mode_of_payment == 'Cheque'){
      $payment->cheque_number = $request->chequeNumber;
    }
    $payment->paid = 1;
    $payment->save();
    return redirect()->back();
  }

  public function bookingQuery(Request $request){
    $client = Client::find($request->clientId);
    $destination = $request->destination;
    $travelDate = $request->travelDate;
    $adults = $request->adults;
    $kids = $request->kids;
    $rooms = $request->rooms;
    $remarks = $request->queryRemarks;

    $message = $client->name . ' | MAF: ' . $client->latestPackage->mafNo . ' | Destination: '. $destination . ' | Travel Date: ' . $travelDate . ' | Adults: ' .$adults . ' | Kids: '.$kids . ' | Rooms: '.$rooms . ' | Remarks: '. $remarks;
    Mail::raw($message, function ($message) {
          $message->to('mrd@forbclub.com')
        ->subject('New Client Request');
    });

    $previousUrl = app('url')->previous();

    return redirect()->to($previousUrl.'?'. http_build_query(['status'=>'success']));
  }

  public function otherQuery(Request $request){

    $client = Client::find($request->clientId);

    $query = $request->otherQuery;

    $message = $client->name . ' | MAF: ' . $client->latestPackage->mafNo . '| Query: '. $query;
    Mail::raw($message, function ($message) {
      $message->to('mrd@forbclub.com')
        ->subject('New Client Request');
    });

    $previousUrl = app('url')->previous();

    return redirect()->to($previousUrl.'?'. http_build_query(['status'=>'success']));
  }


}
