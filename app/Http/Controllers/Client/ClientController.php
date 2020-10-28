<?php

namespace App\Http\Controllers\Client;

use App\Client\Client;
use App\Client\Package\SoldPackageBenefits;
use App\Client\TimelineActivity;
use App\Employee;
use App\Http\Controllers\Controller;
use App\Client\Package\SoldPackages;
use App\Jobs\SendEkitJob;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\FollowUp;
use App\Document;
use Illuminate\Support\Facades\Hash;


class ClientController extends Controller
{

  public function viewClient($slug)
  {
    if(Auth::user()->client){
      $client = Auth::user()->client;
    } else{
    $client = Client::where('slug', $slug)->first();
    }
    if ($client) {

      $breadcrumbs = [
        ['link' => "/dashboard-analytics", 'name' => "Home"], ['name' => "Client"], ['name' => $client->name]
      ];
      return view('/client/profile', [
        'breadcrumbs' => $breadcrumbs
      ], compact('client'));
    } else {
      notifyToast('error', 'Client Not Found', 'Please check the Client Id You Entered');
      return redirect()->route('home');
    }
  }

  public function createClient()
  {
    $breadcrumbs = [
      ['link' => "/dashboard-analytics", 'name' => "Home"], ['name' => "Client"], ['name' => 'Create New Client']
    ];
    toast('Info Toast', 'info');
    return view('/client/create', [
      'breadcrumbs' => $breadcrumbs
    ]);
  }

  public function storeClient(Request $request)
  {
//      return $request;
    $this->validate($request, [
      'productMafNo' => 'required|unique:sold_packages,mafNo',
      'productFclpId' => 'required|unique:sold_packages,fclpId',
      'clientPhone' => 'required|integer|unique:clients,phone'
    ]);
    DB::beginTransaction();
    try {
      $client = new Client;
      $client->name = $request->clientName;
      $client->phone = $request->clientPhone;
      $client->email = $request->clientEmail;
      $client->birthDate = $request->clientBirthDate;
      $client->address = $request->address;
      if ($request->clientAltPhone) {
        $client->altPhone = $request->clientAltPhone;
      }
      if ($request->coDob) {
        $client->coDob = $request->coDob;
      }
      if ($request->relationShipWithClient) {
        $client->relationShipWithClient = $request->relationShipWithClient;
      }
      if ($request->coApplicantName) {
        $client->coApplicantName = $request->coApplicantName;
      }
      $client->save();
      $client->Packages()->create([
        'mafNo' => $request->productMafNo,
        'fclpId' => $request->productFclpId,
        'branch' => $request->productBranch,
        'saleBy' => $request->productSaleBy,
        'saleManager' => $request->productSaleManager,
        'enrollmentDate' => $request->productEnrollmentDate,
        'productType' => $request->productType,
        'productTenure' => $request->productTenure,
        'productName' => $request->productName,
        'productCost' => $request->productCost,
        'noOfEmi' => $request->noOfEmi,
        'emiAmount' => $request->noOfEmi,
//        'modeOfPayment'=>$request->productModeOfPayment,
      ]);
      DB::commit();
    } catch (\Exception $e) {
      DB::rollBack();
    }
  }

  public function storeTimelineComment(Request $request, $activityId)
  {
    $request->validate([
      'activityComment' => 'required|max:320'
    ]);
    TimelineActivity::findOrFail($activityId)->Comments()->create([
      'body' => $request->activityComment,
      'user_id' => Auth::user()->id,
    ]);
    notifyToast('Success', 'Comment Posted', '');
    return redirect()->back();
  }

  public function updateModeOfPayment(Request $request)
  {
    $client = Client::find($request->client);
    $package = $client->latestPackage;
    $package->modeOfPayment = $request->modeOfPayment;
    $package->save();
    return redirect()->back();
  }

  public function addAsc(Request $request)
  {
    $client = Client::find($request->client);
    $package = $client->latestPackage;
    $package->asc = $request->asc;
    $package->save();
    return redirect()->back();
  }

  public function addPackageBenefit(Request $request, $clientId, $packageId)
  {
    $input = $request->validate([
      'benefitName' => 'required|string',
      'benefitDescription' => 'required|string',
      'benefitConditions' => 'required|string',
      'benefitValidity' => 'required|date',
    ]);
    $benefit = new SoldPackageBenefits;
    $benefit->soldPackageId = $clientId;
    $benefit->clientId = $packageId;
    $benefit->benefitName = $input['benefitName'];
    $benefit->benefitDescription = $input['benefitDescription'];
    $benefit->benefitConditions = $input['benefitConditions'];
    $benefit->benefitValidity = $input['benefitValidity'];
    $benefit->save();

    (new TimelineActivity)->create([
      'user_id' => Auth::user()->id,
      'client_id' => $clientId,
      'title' => 'New Package Benefit',
      'parent_model' => 'App\Client\Package\SoldPackageBenefits',
      'parent_id' => $benefit->id,
      'body' => '<strong>' . $benefit->benefitName . '</strong> added. <br><strong>Description: </strong>' . $benefit->benefitDescription . ' <br> <strong>Conditions: </strong>' . $benefit->benefitConditions
    ]);
    notifyToast('Success', 'Package Benefit Added', $benefit->benefitName . ' Added');

    return redirect()->back();
  }


  public function addFollowUp(Request $request)
  {
    $followUp = new FollowUp;
    $followUp->client_id = $request->id;
    $followUp->follow_up_on = $request->followUpDate;
    $followUp->type = $request->followUpType;
    $followUp->subject = $request->followUpSubject;
    $followUp->details = $request->followUpRemarks;
    $followUp->save();
    return redirect()->back();
  }

  public function deleteFollowUp(Request $request,$id)
  {
    $followUp = FollowUp::find($id);
    if($followUp){
      $followUp->delete();
    }
    return redirect()->back();
  }

  public function updateFollowUp(Request $request, $id)
  {
//    return $request;
    $followUp = FollowUp::find($id);
    if ($followUp) {
      $followUp->follow_up_on = $request->followUpDate;
      $followUp->type = $request->followUpType;
      $followUp->subject = $request->followUpSubject;
      $followUp->details = $request->followUpRemarks;
      $followUp->save();
    }
    return redirect()->back();
  }


  public function migrate()
  {
    $clients = json_decode('[
    {
        "id": "1515",
        "maf_no": "1424",
        "user_id": "833",
        "creator_id": "177",
        "OTP_id": "",
        "verification_id": "",
        "verified": "1",
        "cancelled": "0",
        "forfieted": "0",
        "on_hold": "0",
        "incomplete": "1",
        "remarks": "",
        "breather": "0",
        "breather_months": "",
        "customer_no": "",
        "application_no": "601744",
        "application_categoryy": "Individual",
        "date_of_enrollment": "10/7/2020",
        "location": "Chandigarh",
        "salutation": "Mr.",
        "DOB": "1/10/1980",
        "name": "Rajinder Kumar",
        "occupation": "Salaried",
        "co_DOB": "",
        "co_salutation": "Mrs.",
        "co_name": "Sneha Rana",
        "relationship_with_applicant": "wife",
        "address": "Ward No. 01 opposite A.D. College Dharam Kot Moga Punjab 142042",
        "city": "Moga",
        "state": "Punjab",
        "country": "India",
        "postal_code": "142042",
        "mobile": "9814374500",
        "alternate_mobile": "7888351608",
        "email": "nanakmalhotra3@gmail.com",
        "emi_start_date": "11/5/2020",
        "emi_regular_plan": "24",
        "emi_mode_of_payment": "ACH",
        "emi_credit_card_issue_bank_name": "",
        "emi_ach_bank_name": "AXIS BANK",
        "address_proof": "",
        "id_proof": "",
        "kyc_id_proof": "Passport",
        "kyc_id_pan_no": "",
        "kyc_address_proof": "Ration Card",
        "extra_benefits": "",
        "refund_amount": "",
        "refund_date": "",
        "refund_status": "",
        "refund_authorised_by": "",
        "refund_remarks": "",
        "created_at": "10/13/2020 14:28",
        "updated_at": "10/13/2020 14:29",
        "maf_url": "http://dpfjx34w349o1.cloudfront.net/601744_Rajinder Kumar_scannedMaf_1602579519.pdf",
        "cancelled_date": "",
        "source": "venue",
        "place_of_enrollment": "Moga",
        "place_of_enrollment_city": "Moga",
        "branch": "CHANDIGARH",
        "sale_by": "Sachin",
        "sale_status": "",
        "last_breather_month": "",
        "fully_paid_package_price": "",
        "stay_only_per_night_price": "",
        "offer_night_per_night_price": "",
        "voucher_booking_per_night_price": "",
        "nach_disabled": "0",
        "sale_added_by": "",
        "maf_status": "1",
        "sale_manager": "Vishwas",
        "unit_head": "",
        "nach_disable_remarks": ""
    },
    {
        "id": "1516",
        "maf_no": "1425",
        "user_id": "834",
        "creator_id": "177",
        "OTP_id": "",
        "verification_id": "",
        "verified": "1",
        "cancelled": "0",
        "forfieted": "0",
        "on_hold": "0",
        "incomplete": "1",
        "remarks": "",
        "breather": "0",
        "breather_months": "",
        "customer_no": "",
        "application_no": "601852",
        "application_categoryy": "Individual",
        "date_of_enrollment": "10/13/2020",
        "location": "Chandigarh",
        "salutation": "Mr.",
        "DOB": "3/12/1994",
        "name": "Naveen Arora",
        "occupation": "Business",
        "co_DOB": "",
        "co_salutation": "Mrs.",
        "co_name": "Sheenam",
        "relationship_with_applicant": "wife",
        "address": "Beant Nagar Moga distt Moga 142001",
        "city": "Moga",
        "state": "Punjab",
        "country": "India",
        "postal_code": "142001",
        "mobile": "9041679586",
        "alternate_mobile": "8699469418",
        "email": "naveenarora002@gmail.com",
        "emi_start_date": "11/5/2020",
        "emi_regular_plan": "24",
        "emi_mode_of_payment": "ACH",
        "emi_credit_card_issue_bank_name": "",
        "emi_ach_bank_name": "AXIS BANK",
        "address_proof": "",
        "id_proof": "",
        "kyc_id_proof": "Driving License",
        "kyc_id_pan_no": "",
        "kyc_address_proof": "Ration Card",
        "extra_benefits": "",
        "refund_amount": "",
        "refund_date": "",
        "refund_status": "",
        "refund_authorised_by": "",
        "refund_remarks": "",
        "created_at": "10/13/2020 14:53",
        "updated_at": "10/13/2020 14:54",
        "maf_url": "http://dpfjx34w349o1.cloudfront.net/601852_Naveen Arora_scannedMaf_1602581014.pdf",
        "cancelled_date": "",
        "source": "venue",
        "place_of_enrollment": "Moga",
        "place_of_enrollment_city": "Moga",
        "branch": "CHANDIGARH",
        "sale_by": "Sachin Dhiman",
        "sale_status": "",
        "last_breather_month": "",
        "fully_paid_package_price": "",
        "stay_only_per_night_price": "",
        "offer_night_per_night_price": "",
        "voucher_booking_per_night_price": "",
        "nach_disabled": "0",
        "sale_added_by": "",
        "maf_status": "1",
        "sale_manager": "Vishwas",
        "unit_head": "",
        "nach_disable_remarks": ""
    },
    {
        "id": "1517",
        "maf_no": "1426",
        "user_id": "835",
        "creator_id": "177",
        "OTP_id": "",
        "verification_id": "",
        "verified": "1",
        "cancelled": "0",
        "forfieted": "0",
        "on_hold": "0",
        "incomplete": "1",
        "remarks": "",
        "breather": "0",
        "breather_months": "",
        "customer_no": "",
        "application_no": "601697",
        "application_categoryy": "Individual",
        "date_of_enrollment": "10/9/2020",
        "location": "Chandigarh",
        "salutation": "Mr.",
        "DOB": "9/4/1982",
        "name": "Jagdeep Singh",
        "occupation": "Salaried",
        "co_DOB": "",
        "co_salutation": "Mrs.",
        "co_name": "Manpreet",
        "relationship_with_applicant": "wife",
        "address": "H.No. 56 Ramlal Road Baddowal , Ludhiana Punjab",
        "city": "Ludhiana",
        "state": "Punjab",
        "country": "India",
        "postal_code": "142021",
        "mobile": "9876072771",
        "alternate_mobile": "",
        "email": "Nomail@gmail.com",
        "emi_start_date": "12/30/3000",
        "emi_regular_plan": "0",
        "emi_mode_of_payment": "NA",
        "emi_credit_card_issue_bank_name": "",
        "emi_ach_bank_name": "",
        "address_proof": "",
        "id_proof": "",
        "kyc_id_proof": "Aadhaar Card",
        "kyc_id_pan_no": "",
        "kyc_address_proof": "Ration Card",
        "extra_benefits": "",
        "refund_amount": "",
        "refund_date": "",
        "refund_status": "",
        "refund_authorised_by": "",
        "refund_remarks": "",
        "created_at": "10/13/2020 16:03",
        "updated_at": "10/24/2020 14:39",
        "maf_url": "http://dpfjx34w349o1.cloudfront.net/601697_Jagdeep Singh_scannedMaf_1602585207.pdf",
        "cancelled_date": "",
        "source": "venue",
        "place_of_enrollment": "Ludhiana",
        "place_of_enrollment_city": "Ludhiana",
        "branch": "CHANDIGARH",
        "sale_by": "Sachin",
        "sale_status": "",
        "last_breather_month": "",
        "fully_paid_package_price": "",
        "stay_only_per_night_price": "",
        "offer_night_per_night_price": "",
        "voucher_booking_per_night_price": "",
        "nach_disabled": "0",
        "sale_added_by": "",
        "maf_status": "1",
        "sale_manager": "Vishwas",
        "unit_head": "",
        "nach_disable_remarks": ""
    }
]');
    $packages = json_decode('[
    {
        "id": "1515",
        "maf_no": "1424",
        "user_id": "833",
        "creator_id": "177",
        "OTP_id": "",
        "verification_id": "",
        "verified": "1",
        "cancelled": "0",
        "forfieted": "0",
        "on_hold": "0",
        "incomplete": "1",
        "remarks": "",
        "breather": "0",
        "breather_months": "",
        "customer_no": "",
        "application_no": "601744",
        "application_categoryy": "Individual",
        "date_of_enrollment": "10/7/2020",
        "location": "Chandigarh",
        "salutation": "Mr.",
        "DOB": "1/10/1980",
        "name": "Rajinder Kumar",
        "occupation": "Salaried",
        "co_DOB": "",
        "co_salutation": "Mrs.",
        "co_name": "Sneha Rana",
        "relationship_with_applicant": "wife",
        "address": "Ward No. 01 opposite A.D. College Dharam Kot Moga Punjab 142042",
        "city": "Moga",
        "state": "Punjab",
        "country": "India",
        "postal_code": "142042",
        "mobile": "9814374500",
        "alternate_mobile": "7888351608",
        "email": "nanakmalhotra3@gmail.com",
        "emi_start_date": "11/5/2020",
        "emi_regular_plan": "24",
        "emi_mode_of_payment": "ACH",
        "emi_credit_card_issue_bank_name": "",
        "emi_ach_bank_name": "AXIS BANK",
        "address_proof": "",
        "id_proof": "",
        "kyc_id_proof": "Passport",
        "kyc_id_pan_no": "",
        "kyc_address_proof": "Ration Card",
        "extra_benefits": "",
        "refund_amount": "",
        "refund_date": "",
        "refund_status": "",
        "refund_authorised_by": "",
        "refund_remarks": "",
        "created_at": "10/13/2020 14:28",
        "updated_at": "10/13/2020 14:29",
        "maf_url": "http://dpfjx34w349o1.cloudfront.net/601744_Rajinder Kumar_scannedMaf_1602579519.pdf",
        "cancelled_date": "",
        "source": "venue",
        "place_of_enrollment": "Moga",
        "place_of_enrollment_city": "Moga",
        "branch": "CHANDIGARH",
        "sale_by": "Sachin",
        "sale_status": "",
        "last_breather_month": "",
        "fully_paid_package_price": "",
        "stay_only_per_night_price": "",
        "offer_night_per_night_price": "",
        "voucher_booking_per_night_price": "",
        "nach_disabled": "0",
        "sale_added_by": "",
        "maf_status": "1",
        "sale_manager": "Vishwas",
        "unit_head": "",
        "nach_disable_remarks": ""
    },
    {
        "id": "1516",
        "maf_no": "1425",
        "user_id": "834",
        "creator_id": "177",
        "OTP_id": "",
        "verification_id": "",
        "verified": "1",
        "cancelled": "0",
        "forfieted": "0",
        "on_hold": "0",
        "incomplete": "1",
        "remarks": "",
        "breather": "0",
        "breather_months": "",
        "customer_no": "",
        "application_no": "601852",
        "application_categoryy": "Individual",
        "date_of_enrollment": "10/13/2020",
        "location": "Chandigarh",
        "salutation": "Mr.",
        "DOB": "3/12/1994",
        "name": "Naveen Arora",
        "occupation": "Business",
        "co_DOB": "",
        "co_salutation": "Mrs.",
        "co_name": "Sheenam",
        "relationship_with_applicant": "wife",
        "address": "Beant Nagar Moga distt Moga 142001",
        "city": "Moga",
        "state": "Punjab",
        "country": "India",
        "postal_code": "142001",
        "mobile": "9041679586",
        "alternate_mobile": "8699469418",
        "email": "naveenarora002@gmail.com",
        "emi_start_date": "11/5/2020",
        "emi_regular_plan": "24",
        "emi_mode_of_payment": "ACH",
        "emi_credit_card_issue_bank_name": "",
        "emi_ach_bank_name": "AXIS BANK",
        "address_proof": "",
        "id_proof": "",
        "kyc_id_proof": "Driving License",
        "kyc_id_pan_no": "",
        "kyc_address_proof": "Ration Card",
        "extra_benefits": "",
        "refund_amount": "",
        "refund_date": "",
        "refund_status": "",
        "refund_authorised_by": "",
        "refund_remarks": "",
        "created_at": "10/13/2020 14:53",
        "updated_at": "10/13/2020 14:54",
        "maf_url": "http://dpfjx34w349o1.cloudfront.net/601852_Naveen Arora_scannedMaf_1602581014.pdf",
        "cancelled_date": "",
        "source": "venue",
        "place_of_enrollment": "Moga",
        "place_of_enrollment_city": "Moga",
        "branch": "CHANDIGARH",
        "sale_by": "Sachin Dhiman",
        "sale_status": "",
        "last_breather_month": "",
        "fully_paid_package_price": "",
        "stay_only_per_night_price": "",
        "offer_night_per_night_price": "",
        "voucher_booking_per_night_price": "",
        "nach_disabled": "0",
        "sale_added_by": "",
        "maf_status": "1",
        "sale_manager": "Vishwas",
        "unit_head": "",
        "nach_disable_remarks": ""
    },
    {
        "id": "1517",
        "maf_no": "1426",
        "user_id": "835",
        "creator_id": "177",
        "OTP_id": "",
        "verification_id": "",
        "verified": "1",
        "cancelled": "0",
        "forfieted": "0",
        "on_hold": "0",
        "incomplete": "1",
        "remarks": "",
        "breather": "0",
        "breather_months": "",
        "customer_no": "",
        "application_no": "601697",
        "application_categoryy": "Individual",
        "date_of_enrollment": "10/9/2020",
        "location": "Chandigarh",
        "salutation": "Mr.",
        "DOB": "9/4/1982",
        "name": "Jagdeep Singh",
        "occupation": "Salaried",
        "co_DOB": "",
        "co_salutation": "Mrs.",
        "co_name": "Manpreet",
        "relationship_with_applicant": "wife",
        "address": "H.No. 56 Ramlal Road Baddowal , Ludhiana Punjab",
        "city": "Ludhiana",
        "state": "Punjab",
        "country": "India",
        "postal_code": "142021",
        "mobile": "9876072771",
        "alternate_mobile": "",
        "email": "Nomail@gmail.com",
        "emi_start_date": "12/30/3000",
        "emi_regular_plan": "0",
        "emi_mode_of_payment": "NA",
        "emi_credit_card_issue_bank_name": "",
        "emi_ach_bank_name": "",
        "address_proof": "",
        "id_proof": "",
        "kyc_id_proof": "Aadhaar Card",
        "kyc_id_pan_no": "",
        "kyc_address_proof": "Ration Card",
        "extra_benefits": "",
        "refund_amount": "",
        "refund_date": "",
        "refund_status": "",
        "refund_authorised_by": "",
        "refund_remarks": "",
        "created_at": "10/13/2020 16:03",
        "updated_at": "10/24/2020 14:39",
        "maf_url": "http://dpfjx34w349o1.cloudfront.net/601697_Jagdeep Singh_scannedMaf_1602585207.pdf",
        "cancelled_date": "",
        "source": "venue",
        "place_of_enrollment": "Ludhiana",
        "place_of_enrollment_city": "Ludhiana",
        "branch": "CHANDIGARH",
        "sale_by": "Sachin",
        "sale_status": "",
        "last_breather_month": "",
        "fully_paid_package_price": "",
        "stay_only_per_night_price": "",
        "offer_night_per_night_price": "",
        "voucher_booking_per_night_price": "",
        "nach_disabled": "0",
        "sale_added_by": "",
        "maf_status": "1",
        "sale_manager": "Vishwas",
        "unit_head": "",
        "nach_disable_remarks": ""
    }
]');
    $benefits = json_decode('{
    "First Sheet": [
        {
            "id": "1",
            "client_id": "977",
            "service_type": "flight",
            "service_name": "hlgf n",
            "service_price": "49456",
            "service_eligibility": "",
            "maf": "552948",
            "ftk": "977",
            "created_at": "2019-08-24 22:05:41",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "2",
            "client_id": "978",
            "service_type": "hotel",
            "service_name": "5 Nights + 6 Days",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "552927",
            "ftk": "978",
            "created_at": "2019-08-26 16:44:04",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "3",
            "client_id": "979",
            "service_type": "hotel",
            "service_name": "NA",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "552950",
            "ftk": "979",
            "created_at": "2019-08-26 20:31:08",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "4",
            "client_id": "980",
            "service_type": "flight",
            "service_name": "Air tickets",
            "service_price": "15000",
            "service_eligibility": "",
            "maf": "552917",
            "ftk": "980",
            "created_at": "2019-08-26 20:47:02",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "5",
            "client_id": "981",
            "service_type": "hotel",
            "service_name": "3 Night stay",
            "service_price": "10000",
            "service_eligibility": "",
            "maf": "552974",
            "ftk": "981",
            "created_at": "2019-08-26 23:20:14",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "6",
            "client_id": "982",
            "service_type": "flight",
            "service_name": "Air ticket",
            "service_price": "15000",
            "service_eligibility": "",
            "maf": "552928",
            "ftk": "982",
            "created_at": "2019-08-26 23:34:23",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "7",
            "client_id": "983",
            "service_type": "flight",
            "service_name": "Air  Tickets",
            "service_price": "12000",
            "service_eligibility": "",
            "maf": "552915",
            "ftk": "983",
            "created_at": "2019-08-26 23:51:35",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "8",
            "client_id": "984",
            "service_type": "flight",
            "service_name": "Air  Tickets",
            "service_price": "12000",
            "service_eligibility": "",
            "maf": "552969",
            "ftk": "984",
            "created_at": "2019-08-26 23:51:37",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "9",
            "client_id": "985",
            "service_type": "flight",
            "service_name": "as",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "552964",
            "ftk": "985",
            "created_at": "2019-08-27 17:09:10",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "11",
            "client_id": "987",
            "service_type": "flight",
            "service_name": "Air ticket",
            "service_price": "20000",
            "service_eligibility": "",
            "maf": "553121",
            "ftk": "987",
            "created_at": "2019-08-27 17:59:29",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "12",
            "client_id": "988",
            "service_type": "flight",
            "service_name": "Air tickets",
            "service_price": "15000",
            "service_eligibility": "",
            "maf": "552589",
            "ftk": "988",
            "created_at": "2019-08-27 19:53:04",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "13",
            "client_id": "989",
            "service_type": "hotel",
            "service_name": "3night stay",
            "service_price": "10000",
            "service_eligibility": "",
            "maf": "552352",
            "ftk": "989",
            "created_at": "2019-08-27 20:15:31",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "14",
            "client_id": "992",
            "service_type": "flight",
            "service_name": "NA",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "552963",
            "ftk": "992",
            "created_at": "2019-08-27 20:50:19",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "15",
            "client_id": "993",
            "service_type": "flight",
            "service_name": "Air tickets",
            "service_price": "22000",
            "service_eligibility": "",
            "maf": "552909",
            "ftk": "993",
            "created_at": "2019-08-27 21:12:33",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "16",
            "client_id": "994",
            "service_type": "flight",
            "service_name": "NA",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "",
            "ftk": "",
            "created_at": "2019-08-27 21:53:57",
            "updated_at": "2019-08-27 21:53:57"
        },
        {
            "id": "17",
            "client_id": "995",
            "service_type": "flight",
            "service_name": "Air tickets",
            "service_price": "26500",
            "service_eligibility": "",
            "maf": "552905",
            "ftk": "995",
            "created_at": "2019-08-27 22:27:42",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "18",
            "client_id": "996",
            "service_type": "flight",
            "service_name": "NA",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "552368",
            "ftk": "996",
            "created_at": "2019-08-27 23:04:35",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "19",
            "client_id": "997",
            "service_type": "flight",
            "service_name": "aAiir tickets",
            "service_price": "18000",
            "service_eligibility": "",
            "maf": "552313",
            "ftk": "997",
            "created_at": "2019-08-27 23:18:48",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "20",
            "client_id": "998",
            "service_type": "flight",
            "service_name": "aAiir tickets",
            "service_price": "18000",
            "service_eligibility": "",
            "maf": "552951",
            "ftk": "998",
            "created_at": "2019-08-27 23:18:51",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "21",
            "client_id": "999",
            "service_type": "flight",
            "service_name": "NA",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "552967",
            "ftk": "999",
            "created_at": "2019-08-29 17:52:34",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "22",
            "client_id": "1101",
            "service_type": "flight",
            "service_name": "AIR TICKETS",
            "service_price": "12000",
            "service_eligibility": "",
            "maf": "",
            "ftk": "",
            "created_at": "2019-08-30 20:14:17",
            "updated_at": "2019-08-30 20:14:17"
        },
        {
            "id": "23",
            "client_id": "1102",
            "service_type": "flight",
            "service_name": "Air tickets",
            "service_price": "17000",
            "service_eligibility": "",
            "maf": "552565",
            "ftk": "1064",
            "created_at": "2019-08-30 20:36:40",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "24",
            "client_id": "1103",
            "service_type": "flight",
            "service_name": "Air tickets",
            "service_price": "17000",
            "service_eligibility": "",
            "maf": "",
            "ftk": "",
            "created_at": "2019-08-30 20:37:52",
            "updated_at": "2019-08-30 20:37:52"
        },
        {
            "id": "28",
            "client_id": "1107",
            "service_type": "flight",
            "service_name": "NA",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "",
            "ftk": "",
            "created_at": "2019-08-30 21:53:24",
            "updated_at": "2019-08-30 21:53:24"
        },
        {
            "id": "29",
            "client_id": "1108",
            "service_type": "flight",
            "service_name": "Air tickets",
            "service_price": "18000",
            "service_eligibility": "",
            "maf": "552422",
            "ftk": "1071",
            "created_at": "2019-08-30 16:41:45",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "30",
            "client_id": "1110",
            "service_type": "flight",
            "service_name": "Air tickets",
            "service_price": "27000",
            "service_eligibility": "",
            "maf": "",
            "ftk": "",
            "created_at": "2019-08-30 17:05:40",
            "updated_at": "2019-08-30 17:05:40"
        },
        {
            "id": "31",
            "client_id": "1111",
            "service_type": "flight",
            "service_name": "NA",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "552421",
            "ftk": "1073",
            "created_at": "2019-08-30 17:37:16",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "32",
            "client_id": "1112",
            "service_type": "flight",
            "service_name": "NA",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "",
            "ftk": "",
            "created_at": "2019-08-30 17:44:25",
            "updated_at": "2019-08-30 17:44:25"
        },
        {
            "id": "33",
            "client_id": "1113",
            "service_type": "flight",
            "service_name": "Air tickets",
            "service_price": "30000",
            "service_eligibility": "",
            "maf": "552414",
            "ftk": "1075",
            "created_at": "2019-08-30 17:52:57",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "34",
            "client_id": "1114",
            "service_type": "flight",
            "service_name": "Air tickets",
            "service_price": "12000",
            "service_eligibility": "",
            "maf": "552417",
            "ftk": "1076",
            "created_at": "2019-08-30 18:22:20",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "35",
            "client_id": "1115",
            "service_type": "hotel",
            "service_name": "3 Night stay",
            "service_price": "10000",
            "service_eligibility": "",
            "maf": "552453",
            "ftk": "1085",
            "created_at": "2019-08-31 11:29:01",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "37",
            "client_id": "1118",
            "service_type": "flight",
            "service_name": "Air ticket",
            "service_price": "30000",
            "service_eligibility": "",
            "maf": "552844",
            "ftk": "1098",
            "created_at": "2019-08-31 14:35:35",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "38",
            "client_id": "1119",
            "service_type": "hotel",
            "service_name": "3 night",
            "service_price": "10000",
            "service_eligibility": "",
            "maf": "552871",
            "ftk": "1101",
            "created_at": "2019-08-31 15:03:52",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "39",
            "client_id": "1120",
            "service_type": "hotel",
            "service_name": "3 night",
            "service_price": "10000",
            "service_eligibility": "",
            "maf": "",
            "ftk": "",
            "created_at": "2019-08-31 15:11:57",
            "updated_at": "2019-08-31 15:11:57"
        },
        {
            "id": "40",
            "client_id": "1121",
            "service_type": "flight",
            "service_name": "NA",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "552419",
            "ftk": "1105",
            "created_at": "2019-08-31 16:58:33",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "42",
            "client_id": "1123",
            "service_type": "flight",
            "service_name": "Air tickets",
            "service_price": "26000",
            "service_eligibility": "",
            "maf": "",
            "ftk": "",
            "created_at": "2019-09-02 14:58:26",
            "updated_at": "2019-09-02 14:58:26"
        },
        {
            "id": "43",
            "client_id": "1124",
            "service_type": "flight",
            "service_name": "Air tickets",
            "service_price": "15000",
            "service_eligibility": "",
            "maf": "552873",
            "ftk": "1102",
            "created_at": "2019-09-02 15:39:46",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "44",
            "client_id": "1125",
            "service_type": "flight",
            "service_name": "Air tickets",
            "service_price": "15000",
            "service_eligibility": "",
            "maf": "552432",
            "ftk": "1103",
            "created_at": "2019-09-02 15:59:50",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "45",
            "client_id": "1126",
            "service_type": "flight",
            "service_name": "Air tickets",
            "service_price": "24000",
            "service_eligibility": "",
            "maf": "552383",
            "ftk": "1104",
            "created_at": "2019-09-02 16:27:04",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "46",
            "client_id": "1127",
            "service_type": "flight",
            "service_name": "NA",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "552429",
            "ftk": "1106",
            "created_at": "2019-09-02 17:10:51",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "47",
            "client_id": "1128",
            "service_type": "flight",
            "service_name": "Air tickets",
            "service_price": "10000",
            "service_eligibility": "",
            "maf": "552423",
            "ftk": "1107",
            "created_at": "2019-09-02 17:18:43",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "48",
            "client_id": "1129",
            "service_type": "flight",
            "service_name": "Air tickets",
            "service_price": "27000",
            "service_eligibility": "",
            "maf": "552420",
            "ftk": "1115",
            "created_at": "2019-09-02 17:26:19",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "49",
            "client_id": "1133",
            "service_type": "flight",
            "service_name": "Air tickets",
            "service_price": "5000",
            "service_eligibility": "",
            "maf": "552443",
            "ftk": "1111",
            "created_at": "2019-09-05 17:16:16",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "50",
            "client_id": "1134",
            "service_type": "flight",
            "service_name": "Air tickets",
            "service_price": "23500",
            "service_eligibility": "",
            "maf": "552973",
            "ftk": "1112",
            "created_at": "2019-09-05 17:37:53",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "51",
            "client_id": "1135",
            "service_type": "hotel",
            "service_name": "NA",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "552501",
            "ftk": "1113",
            "created_at": "2019-09-05 17:53:10",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "52",
            "client_id": "1136",
            "service_type": "hotel",
            "service_name": "NA",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "552874",
            "ftk": "1114",
            "created_at": "2019-09-05 18:03:39",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "53",
            "client_id": "1137",
            "service_type": "flight",
            "service_name": "Air tickets",
            "service_price": "17000",
            "service_eligibility": "",
            "maf": "552876",
            "ftk": "1116",
            "created_at": "2019-09-05 18:11:35",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "54",
            "client_id": "1138",
            "service_type": "flight",
            "service_name": "Air tickets",
            "service_price": "17000",
            "service_eligibility": "",
            "maf": "",
            "ftk": "",
            "created_at": "2019-09-05 18:11:35",
            "updated_at": "2019-09-05 18:11:35"
        },
        {
            "id": "55",
            "client_id": "1139",
            "service_type": "hotel",
            "service_name": "Air tickets",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "552877",
            "ftk": "1117",
            "created_at": "2019-09-05 18:26:09",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "56",
            "client_id": "1140",
            "service_type": "hotel",
            "service_name": "NA",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "552883",
            "ftk": "1126",
            "created_at": "2019-09-09 15:22:46",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "57",
            "client_id": "1141",
            "service_type": "flight",
            "service_name": "Air tickets",
            "service_price": "27000",
            "service_eligibility": "",
            "maf": "",
            "ftk": "",
            "created_at": "2019-09-09 17:05:26",
            "updated_at": "2019-09-09 17:05:26"
        },
        {
            "id": "58",
            "client_id": "1142",
            "service_type": "flight",
            "service_name": "NA",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "",
            "ftk": "",
            "created_at": "2019-09-10 15:53:30",
            "updated_at": "2019-09-10 15:53:30"
        },
        {
            "id": "59",
            "client_id": "1144",
            "service_type": "hotel",
            "service_name": "NA",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "552860",
            "ftk": "1125",
            "created_at": "2019-09-10 17:26:58",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "60",
            "client_id": "1145",
            "service_type": "hotel",
            "service_name": "NA",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "552449",
            "ftk": "1120",
            "created_at": "2019-09-11 14:49:48",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "61",
            "client_id": "1146",
            "service_type": "hotel",
            "service_name": "NA",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "552878",
            "ftk": "1121",
            "created_at": "2019-09-11 15:03:38",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "62",
            "client_id": "1147",
            "service_type": "flight",
            "service_name": "Air ticket",
            "service_price": "18000",
            "service_eligibility": "",
            "maf": "552856",
            "ftk": "1124",
            "created_at": "2019-09-11 15:35:15",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "63",
            "client_id": "1148",
            "service_type": "flight",
            "service_name": "NA",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "",
            "ftk": "",
            "created_at": "2019-09-11 15:57:25",
            "updated_at": "2019-09-11 15:57:25"
        },
        {
            "id": "64",
            "client_id": "1149",
            "service_type": "flight",
            "service_name": "Air tickets",
            "service_price": "27000",
            "service_eligibility": "",
            "maf": "552885",
            "ftk": "1127",
            "created_at": "2019-09-11 16:08:09",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "65",
            "client_id": "1150",
            "service_type": "flight",
            "service_name": "Air Tickets",
            "service_price": "20000",
            "service_eligibility": "",
            "maf": "",
            "ftk": "",
            "created_at": "2019-09-11 16:25:03",
            "updated_at": "2019-09-11 16:25:03"
        },
        {
            "id": "67",
            "client_id": "1152",
            "service_type": "flight",
            "service_name": "NA",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601011",
            "ftk": "1129",
            "created_at": "2019-09-11 16:38:53",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "68",
            "client_id": "1153",
            "service_type": "flight",
            "service_name": "Airtickets",
            "service_price": "12000",
            "service_eligibility": "",
            "maf": "601012",
            "ftk": "1130",
            "created_at": "2019-09-11 16:50:32",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "69",
            "client_id": "1154",
            "service_type": "flight",
            "service_name": "Air tickets",
            "service_price": "12000",
            "service_eligibility": "",
            "maf": "552472",
            "ftk": "1131",
            "created_at": "2019-09-11 17:07:50",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "70",
            "client_id": "1155",
            "service_type": "flight",
            "service_name": "Air ticket",
            "service_price": "17000",
            "service_eligibility": "",
            "maf": "552888",
            "ftk": "1119",
            "created_at": "2019-09-12 15:31:31",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "71",
            "client_id": "1156",
            "service_type": "flight",
            "service_name": "na",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601013",
            "ftk": "1133",
            "created_at": "2019-09-12 17:09:53",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "72",
            "client_id": "1157",
            "service_type": "hotel",
            "service_name": "Stay",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601001",
            "ftk": "1135",
            "created_at": "2019-09-14 16:12:56",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "73",
            "client_id": "1158",
            "service_type": "hotel",
            "service_name": "NA",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "552509",
            "ftk": "1136",
            "created_at": "2019-09-14 16:19:17",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "74",
            "client_id": "1159",
            "service_type": "flight",
            "service_name": "Flight",
            "service_price": "10000",
            "service_eligibility": "",
            "maf": "601002",
            "ftk": "1137",
            "created_at": "2019-09-14 16:28:05",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "77",
            "client_id": "1160",
            "service_type": "landPackage",
            "service_name": "NA",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "552822",
            "ftk": "DEL1",
            "created_at": "2019-09-21 13:22:14",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "78",
            "client_id": "1161",
            "service_type": "hotel",
            "service_name": "NA",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "552828",
            "ftk": "DEL3",
            "created_at": "2019-09-21 14:45:55",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "79",
            "client_id": "1162",
            "service_type": "flight",
            "service_name": "Air Tickets",
            "service_price": "15000",
            "service_eligibility": "",
            "maf": "552894",
            "ftk": "DEL4",
            "created_at": "2019-09-21 15:13:46",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "80",
            "client_id": "1163",
            "service_type": "flight",
            "service_name": "Air tickets /Hotel",
            "service_price": "20000",
            "service_eligibility": "",
            "maf": "552848",
            "ftk": "DEL5",
            "created_at": "2019-09-21 15:20:40",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "81",
            "client_id": "1164",
            "service_type": "flight",
            "service_name": "NA",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "552832",
            "ftk": "DEL6",
            "created_at": "2019-09-21 16:39:09",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "82",
            "client_id": "1165",
            "service_type": "flight",
            "service_name": "Air tickets",
            "service_price": "18000",
            "service_eligibility": "",
            "maf": "601021",
            "ftk": "DEL7",
            "created_at": "2019-09-21 16:49:34",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "83",
            "client_id": "1166",
            "service_type": "flight",
            "service_name": "Air tickets",
            "service_price": "14000",
            "service_eligibility": "",
            "maf": "601015",
            "ftk": "DEL8",
            "created_at": "2019-09-21 16:56:53",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "84",
            "client_id": "1167",
            "service_type": "hotel",
            "service_name": "Hotel Stay",
            "service_price": "10000",
            "service_eligibility": "",
            "maf": "601006",
            "ftk": "DEL9",
            "created_at": "2019-09-21 17:05:04",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "85",
            "client_id": "1168",
            "service_type": "hotel",
            "service_name": "Flight",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601018",
            "ftk": "1138",
            "created_at": "2019-09-21 17:59:55",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "86",
            "client_id": "1169",
            "service_type": "hotel",
            "service_name": "NA",
            "service_price": "26500",
            "service_eligibility": "",
            "maf": "601014",
            "ftk": "1139",
            "created_at": "2019-09-21 18:19:26",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "87",
            "client_id": "1170",
            "service_type": "flight",
            "service_name": "Air tickets",
            "service_price": "20000",
            "service_eligibility": "",
            "maf": "601003",
            "ftk": "1140",
            "created_at": "2019-09-23 11:08:47",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "88",
            "client_id": "1171",
            "service_type": "flight",
            "service_name": "Flights",
            "service_price": "20000",
            "service_eligibility": "",
            "maf": "601022",
            "ftk": "1141",
            "created_at": "2019-09-23 11:29:28",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "89",
            "client_id": "1172",
            "service_type": "hotel",
            "service_name": "Hotel",
            "service_price": "10000",
            "service_eligibility": "",
            "maf": "601020",
            "ftk": "1142",
            "created_at": "2019-09-23 12:11:55",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "90",
            "client_id": "1173",
            "service_type": "flight",
            "service_name": "Flight",
            "service_price": "20000",
            "service_eligibility": "",
            "maf": "601029",
            "ftk": "1143",
            "created_at": "2019-09-23 12:48:22",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "93",
            "client_id": "1176",
            "service_type": "hotel",
            "service_name": "Hotel Stay",
            "service_price": "10000",
            "service_eligibility": "",
            "maf": "601311",
            "ftk": "1147",
            "created_at": "2019-09-23 15:12:15",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "94",
            "client_id": "1177",
            "service_type": "flight",
            "service_name": "Air tickets",
            "service_price": "10000",
            "service_eligibility": "",
            "maf": "601573",
            "ftk": "1149",
            "created_at": "2019-09-23 15:25:33",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "95",
            "client_id": "1133",
            "service_type": "others",
            "service_name": "Offer Nights(2 Nights 3 Days)",
            "service_price": "",
            "service_eligibility": "",
            "maf": "552443",
            "ftk": "1111",
            "created_at": "2019-09-23 17:25:30",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "96",
            "client_id": "1178",
            "service_type": "hotel",
            "service_name": "NA",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601599",
            "ftk": "1152",
            "created_at": "2019-09-24 12:08:45",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "97",
            "client_id": "1174",
            "service_type": "flight",
            "service_name": "Air tickets",
            "service_price": "10000",
            "service_eligibility": "",
            "maf": "601004",
            "ftk": "1144",
            "created_at": "2019-09-24 15:19:19",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "98",
            "client_id": "1179",
            "service_type": "flight",
            "service_name": "NA",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601589",
            "ftk": "1148",
            "created_at": "2019-09-24 15:37:34",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "99",
            "client_id": "1180",
            "service_type": "flight",
            "service_name": "Air tickets",
            "service_price": "10000",
            "service_eligibility": "",
            "maf": "601600",
            "ftk": "1150",
            "created_at": "2019-09-24 16:27:53",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "100",
            "client_id": "1181",
            "service_type": "flight",
            "service_name": "Air tikcets",
            "service_price": "15000",
            "service_eligibility": "",
            "maf": "601590",
            "ftk": "1151",
            "created_at": "2019-09-24 16:50:24",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "101",
            "client_id": "1182",
            "service_type": "flight",
            "service_name": "Airtickets",
            "service_price": "20000",
            "service_eligibility": "",
            "maf": "601598",
            "ftk": "1153",
            "created_at": "2019-09-24 17:27:19",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "102",
            "client_id": "1183",
            "service_type": "flight",
            "service_name": "Air tickets",
            "service_price": "10000",
            "service_eligibility": "",
            "maf": "601597",
            "ftk": "1154",
            "created_at": "2019-09-24 17:50:30",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "103",
            "client_id": "1184",
            "service_type": "hotel",
            "service_name": "NA",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601028",
            "ftk": "1155",
            "created_at": "2019-09-24 18:05:35",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "104",
            "client_id": "1185",
            "service_type": "hotel",
            "service_name": "Hotel stay",
            "service_price": "10000",
            "service_eligibility": "",
            "maf": "601008",
            "ftk": "1157",
            "created_at": "2019-09-25 10:45:41",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "105",
            "client_id": "1186",
            "service_type": "hotel",
            "service_name": "2night stay",
            "service_price": "10000",
            "service_eligibility": "",
            "maf": "552845",
            "ftk": "DEL2",
            "created_at": "2019-09-25 14:35:17",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "106",
            "client_id": "1130",
            "service_type": "others",
            "service_name": "2 Nights",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "552424",
            "ftk": "1108",
            "created_at": "2019-09-25 15:38:18",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "107",
            "client_id": "1187",
            "service_type": "hotel",
            "service_name": "Hotel stay",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601563",
            "ftk": "1159",
            "created_at": "2019-09-26 12:35:40",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "108",
            "client_id": "1188",
            "service_type": "hotel",
            "service_name": "3 nights",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601566",
            "ftk": "1160",
            "created_at": "2019-09-26 12:48:41",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "109",
            "client_id": "1188",
            "service_type": "flight",
            "service_name": "Air tickets",
            "service_price": "27000",
            "service_eligibility": "",
            "maf": "601566",
            "ftk": "1160",
            "created_at": "2019-09-26 12:48:41",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "112",
            "client_id": "1190",
            "service_type": "others",
            "service_name": "NA",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601570",
            "ftk": "1164",
            "created_at": "2019-09-28 11:22:47",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "113",
            "client_id": "1191",
            "service_type": "flight",
            "service_name": "Air tickets",
            "service_price": "40000",
            "service_eligibility": "",
            "maf": "601569",
            "ftk": "1165",
            "created_at": "2019-09-30 14:02:37",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "114",
            "client_id": "1192",
            "service_type": "hotel",
            "service_name": "5nyt Hotel stay",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601561",
            "ftk": "1167",
            "created_at": "2019-09-30 14:17:42",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "115",
            "client_id": "1193",
            "service_type": "flight",
            "service_name": "Air ticket",
            "service_price": "20000",
            "service_eligibility": "",
            "maf": "601557",
            "ftk": "1171",
            "created_at": "2019-09-30 14:25:57",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "117",
            "client_id": "1195",
            "service_type": "others",
            "service_name": "NA",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601537",
            "ftk": "1173",
            "created_at": "2019-09-30 14:52:32",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "118",
            "client_id": "1175",
            "service_type": "flight",
            "service_name": "Flight",
            "service_price": "15000",
            "service_eligibility": "",
            "maf": "601303",
            "ftk": "1145",
            "created_at": "2019-10-01 14:28:52",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "119",
            "client_id": "1196",
            "service_type": "flight",
            "service_name": "NA",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "509905",
            "ftk": "1161",
            "created_at": "2019-10-01 15:15:08",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "120",
            "client_id": "1197",
            "service_type": "others",
            "service_name": "NA",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "509904",
            "ftk": "1162",
            "created_at": "2019-10-01 15:22:01",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "121",
            "client_id": "1198",
            "service_type": "hotel",
            "service_name": "4NIGHT STAY",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601562",
            "ftk": "1166",
            "created_at": "2019-10-01 15:29:33",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "123",
            "client_id": "1200",
            "service_type": "others",
            "service_name": "NA",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601555",
            "ftk": "1170",
            "created_at": "2019-10-01 15:42:03",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "124",
            "client_id": "1201",
            "service_type": "hotel",
            "service_name": "3 Nights",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601026",
            "ftk": "1174",
            "created_at": "2019-10-01 15:48:14",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "125",
            "client_id": "1202",
            "service_type": "flight",
            "service_name": "Air ticket after 50%",
            "service_price": "5000",
            "service_eligibility": "",
            "maf": "601591",
            "ftk": "1175",
            "created_at": "2019-10-01 15:53:30",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "126",
            "client_id": "1202",
            "service_type": "hotel",
            "service_name": "3 Nights after 30%",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601591",
            "ftk": "1175",
            "created_at": "2019-10-01 15:53:30",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "128",
            "client_id": "1194",
            "service_type": "others",
            "service_name": "NA",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601559",
            "ftk": "1172",
            "created_at": "2019-10-03 15:39:04",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "129",
            "client_id": "1203",
            "service_type": "others",
            "service_name": "NA",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601577",
            "ftk": "1168",
            "created_at": "2019-10-07 14:09:44",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "130",
            "client_id": "1204",
            "service_type": "flight",
            "service_name": "Flight",
            "service_price": "17000",
            "service_eligibility": "",
            "maf": "601565",
            "ftk": "1176",
            "created_at": "2019-10-07 14:30:29",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "131",
            "client_id": "1205",
            "service_type": "others",
            "service_name": "NA",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601594",
            "ftk": "1177",
            "created_at": "2019-10-07 14:43:32",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "132",
            "client_id": "1206",
            "service_type": "flight",
            "service_name": "Flight",
            "service_price": "18500",
            "service_eligibility": "",
            "maf": "601595",
            "ftk": "1178",
            "created_at": "2019-10-07 14:55:03",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "133",
            "client_id": "1206",
            "service_type": "hotel",
            "service_name": "3 Nights",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601595",
            "ftk": "1178",
            "created_at": "2019-10-07 14:55:03",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "134",
            "client_id": "1207",
            "service_type": "hotel",
            "service_name": "4night",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601567",
            "ftk": "1179",
            "created_at": "2019-10-07 15:14:26",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "135",
            "client_id": "1208",
            "service_type": "others",
            "service_name": "NA",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601534",
            "ftk": "1180",
            "created_at": "2019-10-07 15:27:23",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "137",
            "client_id": "1210",
            "service_type": "hotel",
            "service_name": "3 Nights",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601535",
            "ftk": "1182",
            "created_at": "2019-10-07 16:01:58",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "138",
            "client_id": "1211",
            "service_type": "others",
            "service_name": "NA",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601301",
            "ftk": "1183",
            "created_at": "2019-10-07 16:11:40",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "139",
            "client_id": "1212",
            "service_type": "flight",
            "service_name": "Flight",
            "service_price": "15000",
            "service_eligibility": "",
            "maf": "601554",
            "ftk": "1184",
            "created_at": "2019-10-07 16:21:05",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "140",
            "client_id": "1212",
            "service_type": "hotel",
            "service_name": "2 Nights",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601554",
            "ftk": "1184",
            "created_at": "2019-10-07 16:21:05",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "142",
            "client_id": "1214",
            "service_type": "hotel",
            "service_name": "2 Nights",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "552833",
            "ftk": "DEL13",
            "created_at": "2019-10-09 14:48:39",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "143",
            "client_id": "1215",
            "service_type": "hotel",
            "service_name": "2 Nights",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "552456",
            "ftk": "DEL14",
            "created_at": "2019-10-09 15:03:00",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "144",
            "client_id": "1216",
            "service_type": "others",
            "service_name": "NA",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601539",
            "ftk": "1181",
            "created_at": "2019-10-09 15:26:36",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "145",
            "client_id": "1217",
            "service_type": "others",
            "service_name": "NA",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601536",
            "ftk": "1185",
            "created_at": "2019-10-09 15:47:53",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "146",
            "client_id": "1218",
            "service_type": "hotel",
            "service_name": "3 Nights",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601592",
            "ftk": "1186",
            "created_at": "2019-10-09 16:10:11",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "147",
            "client_id": "1219",
            "service_type": "hotel",
            "service_name": "3 Nights",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601538",
            "ftk": "1187",
            "created_at": "2019-10-09 16:29:24",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "149",
            "client_id": "1221",
            "service_type": "flight",
            "service_name": "Flight",
            "service_price": "10000",
            "service_eligibility": "",
            "maf": "601316",
            "ftk": "1189",
            "created_at": "2019-10-11 12:18:47",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "151",
            "client_id": "1223",
            "service_type": "flight",
            "service_name": "Flight",
            "service_price": "24500",
            "service_eligibility": "",
            "maf": "601318",
            "ftk": "1190",
            "created_at": "2019-10-15 16:15:48",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "152",
            "client_id": "1224",
            "service_type": "hotel",
            "service_name": "4night",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601317",
            "ftk": "1191",
            "created_at": "2019-10-15 16:41:09",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "153",
            "client_id": "1225",
            "service_type": "flight",
            "service_name": "Flight",
            "service_price": "10000",
            "service_eligibility": "",
            "maf": "601550",
            "ftk": "1192",
            "created_at": "2019-10-15 16:50:00",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "154",
            "client_id": "1225",
            "service_type": "hotel",
            "service_name": "2 Nights",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601550",
            "ftk": "1192",
            "created_at": "2019-10-15 16:50:00",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "157",
            "client_id": "1132",
            "service_type": "flight",
            "service_name": "Flight",
            "service_price": "26000",
            "service_eligibility": "",
            "maf": "552425",
            "ftk": "1110",
            "created_at": "2019-10-16 12:01:05",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "158",
            "client_id": "1227",
            "service_type": "others",
            "service_name": "NA",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601517",
            "ftk": "1194",
            "created_at": "2019-10-16 16:14:38",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "159",
            "client_id": "1228",
            "service_type": "flight",
            "service_name": "Flight",
            "service_price": "20000",
            "service_eligibility": "",
            "maf": "601579",
            "ftk": "1195",
            "created_at": "2019-10-16 16:42:20",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "160",
            "client_id": "1229",
            "service_type": "others",
            "service_name": "NA",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601576",
            "ftk": "1196",
            "created_at": "2019-10-16 16:53:29",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "162",
            "client_id": "1231",
            "service_type": "others",
            "service_name": "NA",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601585",
            "ftk": "1198",
            "created_at": "2019-10-16 17:20:07",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "163",
            "client_id": "1232",
            "service_type": "others",
            "service_name": "NA",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601587",
            "ftk": "DEL19",
            "created_at": "2019-10-16 17:29:44",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "164",
            "client_id": "1233",
            "service_type": "others",
            "service_name": "NA",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "552835",
            "ftk": "DEL18",
            "created_at": "2019-10-16 17:37:27",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "166",
            "client_id": "1235",
            "service_type": "hotel",
            "service_name": "2 Nights",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601583",
            "ftk": "DEL16",
            "created_at": "2019-10-21 15:40:47",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "167",
            "client_id": "1236",
            "service_type": "hotel",
            "service_name": "2 Nights",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601586",
            "ftk": "DEL17",
            "created_at": "2019-10-21 15:56:25",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "170",
            "client_id": "1238",
            "service_type": "flight",
            "service_name": "Flight",
            "service_price": "30000",
            "service_eligibility": "",
            "maf": "601588",
            "ftk": "1202",
            "created_at": "2019-10-22 15:36:50",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "171",
            "client_id": "1239",
            "service_type": "others",
            "service_name": "NA",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601596",
            "ftk": "1203",
            "created_at": "2019-10-22 16:04:26",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "172",
            "client_id": "1240",
            "service_type": "others",
            "service_name": "NA",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601323",
            "ftk": "1206",
            "created_at": "2019-10-22 16:40:42",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "173",
            "client_id": "1241",
            "service_type": "hotel",
            "service_name": "3 Nights",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601322",
            "ftk": "1208",
            "created_at": "2019-10-22 17:01:02",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "179",
            "client_id": "1244",
            "service_type": "flight",
            "service_name": "Flight",
            "service_price": "10000",
            "service_eligibility": "",
            "maf": "601326",
            "ftk": "1205",
            "created_at": "2019-10-23 16:45:43",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "180",
            "client_id": "1245",
            "service_type": "others",
            "service_name": "NA",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601338",
            "ftk": "1209",
            "created_at": "2019-10-23 17:08:37",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "181",
            "client_id": "1246",
            "service_type": "flight",
            "service_name": "Flights",
            "service_price": "15000",
            "service_eligibility": "",
            "maf": "601320",
            "ftk": "1204",
            "created_at": "2019-10-24 11:27:27",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "182",
            "client_id": "1246",
            "service_type": "hotel",
            "service_name": "3 Nights",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601320",
            "ftk": "1204",
            "created_at": "2019-10-24 11:27:27",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "183",
            "client_id": "1234",
            "service_type": "others",
            "service_name": "NA",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601584",
            "ftk": "DEL15",
            "created_at": "2019-10-24 12:13:09",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "185",
            "client_id": "1247",
            "service_type": "others",
            "service_name": "cash discount",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601344",
            "ftk": "1210",
            "created_at": "2019-10-24 15:14:36",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "186",
            "client_id": "1248",
            "service_type": "hotel",
            "service_name": "3 Nights",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601343",
            "ftk": "DEL21",
            "created_at": "2019-10-24 15:35:46",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "188",
            "client_id": "1249",
            "service_type": "others",
            "service_name": "NA",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601540",
            "ftk": "DEL22",
            "created_at": "2019-10-25 11:37:16",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "189",
            "client_id": "1250",
            "service_type": "flight",
            "service_name": "Flight",
            "service_price": "10000",
            "service_eligibility": "",
            "maf": "601353",
            "ftk": "1211",
            "created_at": "2019-10-25 14:01:20",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "194",
            "client_id": "1237",
            "service_type": "flight",
            "service_name": "Flight",
            "service_price": "25000",
            "service_eligibility": "",
            "maf": "601544",
            "ftk": "1201",
            "created_at": "2019-10-31 14:17:31",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "195",
            "client_id": "1237",
            "service_type": "hotel",
            "service_name": "5Night",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601544",
            "ftk": "1201",
            "created_at": "2019-10-31 14:17:31",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "196",
            "client_id": "1251",
            "service_type": "others",
            "service_name": "NA",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601355",
            "ftk": "1212",
            "created_at": "2019-10-31 15:15:37",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "198",
            "client_id": "1189",
            "service_type": "flight",
            "service_name": "Air tickets",
            "service_price": "10000",
            "service_eligibility": "",
            "maf": "601027",
            "ftk": "1158",
            "created_at": "2019-10-31 17:56:40",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "199",
            "client_id": "1189",
            "service_type": "hotel",
            "service_name": "2 Nights",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601027",
            "ftk": "1158",
            "created_at": "2019-10-31 17:56:40",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "200",
            "client_id": "1199",
            "service_type": "others",
            "service_name": "NA",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601560",
            "ftk": "1169",
            "created_at": "2019-10-31 17:58:58",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "201",
            "client_id": "1254",
            "service_type": "hotel",
            "service_name": "2 Nights",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601314",
            "ftk": "1214",
            "created_at": "2019-11-01 12:21:10",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "203",
            "client_id": "1255",
            "service_type": "others",
            "service_name": "NA",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601329",
            "ftk": "1216",
            "created_at": "2019-11-01 16:41:23",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "208",
            "client_id": "1259",
            "service_type": "flight",
            "service_name": "Flight",
            "service_price": "10000",
            "service_eligibility": "",
            "maf": "601327",
            "ftk": "1218",
            "created_at": "2019-11-05 15:19:09",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "211",
            "client_id": "1262",
            "service_type": "others",
            "service_name": "NA",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601359",
            "ftk": "1221",
            "created_at": "2019-11-05 15:56:18",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "212",
            "client_id": "1263",
            "service_type": "flight",
            "service_name": "Flight",
            "service_price": "10000",
            "service_eligibility": "",
            "maf": "601332",
            "ftk": "1222",
            "created_at": "2019-11-07 15:00:20",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "213",
            "client_id": "1263",
            "service_type": "hotel",
            "service_name": "3 Nights",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601332",
            "ftk": "1222",
            "created_at": "2019-11-07 15:00:20",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "214",
            "client_id": "1258",
            "service_type": "hotel",
            "service_name": "2 Nights",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601349",
            "ftk": "1217",
            "created_at": "2019-11-08 11:18:26",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "215",
            "client_id": "1243",
            "service_type": "flight",
            "service_name": "Flight",
            "service_price": "12000",
            "service_eligibility": "",
            "maf": "601030",
            "ftk": "1200",
            "created_at": "2019-11-08 11:52:51",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "216",
            "client_id": "1243",
            "service_type": "hotel",
            "service_name": "STAY",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601030",
            "ftk": "1200",
            "created_at": "2019-11-08 11:52:51",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "219",
            "client_id": "1253",
            "service_type": "hotel",
            "service_name": "4 night",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601367",
            "ftk": "DEL20",
            "created_at": "2019-11-09 17:16:05",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "220",
            "client_id": "1266",
            "service_type": "hotel",
            "service_name": "NA",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601009",
            "ftk": "1223",
            "created_at": "2019-11-11 15:53:22",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "221",
            "client_id": "1267",
            "service_type": "flight",
            "service_name": "Flight",
            "service_price": "18000",
            "service_eligibility": "",
            "maf": "601005",
            "ftk": "1224",
            "created_at": "2019-11-12 11:32:23",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "222",
            "client_id": "1268",
            "service_type": "others",
            "service_name": "NA",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601356",
            "ftk": "1225",
            "created_at": "2019-11-12 11:50:38",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "223",
            "client_id": "1269",
            "service_type": "flight",
            "service_name": "Flight",
            "service_price": "18000",
            "service_eligibility": "",
            "maf": "601401",
            "ftk": "1226",
            "created_at": "2019-11-12 12:03:18",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "224",
            "client_id": "1269",
            "service_type": "hotel",
            "service_name": "3 Nights",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601401",
            "ftk": "1226",
            "created_at": "2019-11-12 12:03:18",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "226",
            "client_id": "1271",
            "service_type": "flight",
            "service_name": "Flight",
            "service_price": "10000",
            "service_eligibility": "",
            "maf": "601405",
            "ftk": "1228",
            "created_at": "2019-11-12 14:45:34",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "227",
            "client_id": "1272",
            "service_type": "hotel",
            "service_name": "2 Nights",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601342",
            "ftk": "1229",
            "created_at": "2019-11-12 15:16:14",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "228",
            "client_id": "1273",
            "service_type": "flight",
            "service_name": "Flight",
            "service_price": "12000",
            "service_eligibility": "",
            "maf": "601341",
            "ftk": "1230",
            "created_at": "2019-11-12 15:35:49",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "229",
            "client_id": "1273",
            "service_type": "hotel",
            "service_name": "3 Nights",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601341",
            "ftk": "1230",
            "created_at": "2019-11-12 15:35:49",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "230",
            "client_id": "1274",
            "service_type": "flight",
            "service_name": "Flight",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601025",
            "ftk": "1231",
            "created_at": "2019-11-12 16:43:38",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "231",
            "client_id": "1260",
            "service_type": "hotel",
            "service_name": "2 Nights",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601016",
            "ftk": "1219",
            "created_at": "2019-11-13 14:05:57",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "232",
            "client_id": "1275",
            "service_type": "hotel",
            "service_name": "2 Nights",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601530",
            "ftk": "DEL23",
            "created_at": "2019-11-13 15:43:59",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "234",
            "client_id": "1277",
            "service_type": "flight",
            "service_name": "Flight",
            "service_price": "10000",
            "service_eligibility": "",
            "maf": "601553",
            "ftk": "DEL25",
            "created_at": "2019-11-13 16:35:48",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "235",
            "client_id": "1277",
            "service_type": "hotel",
            "service_name": "2 Nights",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601553",
            "ftk": "DEL25",
            "created_at": "2019-11-13 16:35:48",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "236",
            "client_id": "1257",
            "service_type": "hotel",
            "service_name": "2 Nights",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "552388",
            "ftk": "1057",
            "created_at": "2019-11-13 17:00:36",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "237",
            "client_id": "1257",
            "service_type": "flight",
            "service_name": "Flight",
            "service_price": "14000",
            "service_eligibility": "",
            "maf": "552388",
            "ftk": "1057",
            "created_at": "2019-11-13 17:00:36",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "239",
            "client_id": "1279",
            "service_type": "hotel",
            "service_name": "2 Nights",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601392",
            "ftk": "DEL27",
            "created_at": "2019-11-13 17:25:09",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "240",
            "client_id": "1280",
            "service_type": "flight",
            "service_name": "Flight",
            "service_price": "10000",
            "service_eligibility": "",
            "maf": "601304",
            "ftk": "1156",
            "created_at": "2019-11-13 17:56:33",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "241",
            "client_id": "1281",
            "service_type": "hotel",
            "service_name": "2 Nights",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601302",
            "ftk": "1146",
            "created_at": "2019-11-13 18:03:19",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "242",
            "client_id": "1282",
            "service_type": "flight",
            "service_name": "Flight",
            "service_price": "17000",
            "service_eligibility": "",
            "maf": "552801",
            "ftk": "1049",
            "created_at": "2019-11-14 11:02:51",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "243",
            "client_id": "1283",
            "service_type": "flight",
            "service_name": "Flight",
            "service_price": "22500",
            "service_eligibility": "",
            "maf": "552563",
            "ftk": "1061",
            "created_at": "2019-11-14 11:09:32",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "244",
            "client_id": "1284",
            "service_type": "hotel",
            "service_name": "2 Nights",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "552389",
            "ftk": "1062",
            "created_at": "2019-11-14 11:18:30",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "245",
            "client_id": "1285",
            "service_type": "flight",
            "service_name": "NA",
            "service_price": "20000",
            "service_eligibility": "",
            "maf": "552875",
            "ftk": "1118",
            "created_at": "2019-11-14 11:43:24",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "246",
            "client_id": "1278",
            "service_type": "others",
            "service_name": "NA",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601388",
            "ftk": "DEL26",
            "created_at": "2019-11-14 14:14:33",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "247",
            "client_id": "1286",
            "service_type": "flight",
            "service_name": "Flight",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601370",
            "ftk": "1232",
            "created_at": "2019-11-14 15:32:55",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "248",
            "client_id": "1230",
            "service_type": "hotel",
            "service_name": "2 Nights",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601578",
            "ftk": "1197",
            "created_at": "2019-11-14 15:55:00",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "249",
            "client_id": "1287",
            "service_type": "flight",
            "service_name": "Flight",
            "service_price": "8000",
            "service_eligibility": "",
            "maf": "601372",
            "ftk": "1233",
            "created_at": "2019-11-14 15:59:51",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "250",
            "client_id": "1287",
            "service_type": "hotel",
            "service_name": "2 Nights",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601372",
            "ftk": "1233",
            "created_at": "2019-11-14 15:59:51",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "251",
            "client_id": "1288",
            "service_type": "flight",
            "service_name": "Flight",
            "service_price": "20000",
            "service_eligibility": "",
            "maf": "601368",
            "ftk": "1234",
            "created_at": "2019-11-14 16:26:35",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "252",
            "client_id": "1288",
            "service_type": "hotel",
            "service_name": "3 Nights",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601368",
            "ftk": "1234",
            "created_at": "2019-11-14 16:26:35",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "253",
            "client_id": "1222",
            "service_type": "hotel",
            "service_name": "5 Night",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601575",
            "ftk": "DEL10",
            "created_at": "2019-11-15 12:18:47",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "254",
            "client_id": "1261",
            "service_type": "others",
            "service_name": "NA",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601358",
            "ftk": "1220",
            "created_at": "2019-11-15 17:51:28",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "255",
            "client_id": "1276",
            "service_type": "hotel",
            "service_name": "2 Nights",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601529",
            "ftk": "DEL24",
            "created_at": "2019-11-16 14:59:10",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "256",
            "client_id": "1104",
            "service_type": "flight",
            "service_name": "Air tickets",
            "service_price": "12000",
            "service_eligibility": "",
            "maf": "552511",
            "ftk": "1065",
            "created_at": "2019-11-20 10:26:30",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "257",
            "client_id": "1105",
            "service_type": "flight",
            "service_name": "Air tickets",
            "service_price": "10000",
            "service_eligibility": "",
            "maf": "552902",
            "ftk": "1068",
            "created_at": "2019-11-20 10:29:54",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "258",
            "client_id": "1106",
            "service_type": "flight",
            "service_name": "NA",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "552407",
            "ftk": "1069",
            "created_at": "2019-11-20 10:31:52",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "259",
            "client_id": "1122",
            "service_type": "hotel",
            "service_name": "3 nights",
            "service_price": "10000",
            "service_eligibility": "",
            "maf": "552452",
            "ftk": "1091",
            "created_at": "2019-11-20 10:35:03",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "260",
            "client_id": "1289",
            "service_type": "hotel",
            "service_name": "2 Nights",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601377",
            "ftk": "DEL29",
            "created_at": "2019-11-20 12:11:31",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "264",
            "client_id": "1291",
            "service_type": "others",
            "service_name": "NA",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601404",
            "ftk": "1237",
            "created_at": "2019-11-21 16:10:48",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "265",
            "client_id": "1292",
            "service_type": "others",
            "service_name": "NA",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601412",
            "ftk": "1235",
            "created_at": "2019-11-21 16:28:17",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "266",
            "client_id": "1293",
            "service_type": "hotel",
            "service_name": "2 Nights",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "552463",
            "ftk": "1238",
            "created_at": "2019-11-21 16:41:23",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "267",
            "client_id": "1294",
            "service_type": "others",
            "service_name": "NA",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601549",
            "ftk": "1240",
            "created_at": "2019-11-21 16:49:38",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "268",
            "client_id": "1295",
            "service_type": "others",
            "service_name": "NA",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601516",
            "ftk": "1241",
            "created_at": "2019-11-21 17:15:21",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "269",
            "client_id": "1296",
            "service_type": "others",
            "service_name": "NA",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601416",
            "ftk": "1242",
            "created_at": "2019-11-21 17:25:09",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "271",
            "client_id": "1298",
            "service_type": "hotel",
            "service_name": "5Night",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601417",
            "ftk": "1239",
            "created_at": "2019-11-23 11:51:43",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "272",
            "client_id": "1299",
            "service_type": "others",
            "service_name": "NA",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601346",
            "ftk": "1243",
            "created_at": "2019-11-23 12:30:47",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "273",
            "client_id": "1290",
            "service_type": "flight",
            "service_name": "Flight",
            "service_price": "20000",
            "service_eligibility": "",
            "maf": "601407",
            "ftk": "1236",
            "created_at": "2019-11-23 16:23:53",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "274",
            "client_id": "1300",
            "service_type": "others",
            "service_name": "NA",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "552821",
            "ftk": "DEL30",
            "created_at": "2019-11-23 17:36:43",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "275",
            "client_id": "1301",
            "service_type": "hotel",
            "service_name": "2 Nights",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601459",
            "ftk": "DEL31",
            "created_at": "2019-11-23 17:43:24",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "276",
            "client_id": "1302",
            "service_type": "others",
            "service_name": "NA",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601458",
            "ftk": "DEL32",
            "created_at": "2019-11-23 17:54:47",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "279",
            "client_id": "1303",
            "service_type": "others",
            "service_name": "NA",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601518",
            "ftk": "1246",
            "created_at": "2019-11-28 11:03:25",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "280",
            "client_id": "1304",
            "service_type": "flight",
            "service_name": "Flight",
            "service_price": "10000",
            "service_eligibility": "",
            "maf": "601331",
            "ftk": "1247",
            "created_at": "2019-11-28 11:35:40",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "282",
            "client_id": "1306",
            "service_type": "hotel",
            "service_name": "3 Nights",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601366",
            "ftk": "1249",
            "created_at": "2019-11-28 12:05:48",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "283",
            "client_id": "1307",
            "service_type": "others",
            "service_name": "NA",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601363",
            "ftk": "1250",
            "created_at": "2019-11-28 12:36:54",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "284",
            "client_id": "1308",
            "service_type": "flight",
            "service_name": "Flight",
            "service_price": "10000",
            "service_eligibility": "",
            "maf": "601461",
            "ftk": "1251",
            "created_at": "2019-11-28 14:26:33",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "286",
            "client_id": "1310",
            "service_type": "others",
            "service_name": "NA",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601371",
            "ftk": "1253",
            "created_at": "2019-11-28 14:58:06",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "287",
            "client_id": "1311",
            "service_type": "flight",
            "service_name": "Flight",
            "service_price": "25000",
            "service_eligibility": "",
            "maf": "601364",
            "ftk": "1254",
            "created_at": "2019-11-28 15:06:27",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "290",
            "client_id": "1314",
            "service_type": "flight",
            "service_name": "Flight",
            "service_price": "12000",
            "service_eligibility": "",
            "maf": "601485",
            "ftk": "1257",
            "created_at": "2019-11-28 16:47:19",
            "updated_at": "2020-10-20 07:58:48"
        },
        {
            "id": "291",
            "client_id": "1315",
            "service_type": "flight",
            "service_name": "Flight",
            "service_price": "30000",
            "service_eligibility": "",
            "maf": "601462",
            "ftk": "1258",
            "created_at": "2019-11-28 17:04:23",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "292",
            "client_id": "1316",
            "service_type": "others",
            "service_name": "NA",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601418",
            "ftk": "1259",
            "created_at": "2019-11-28 17:09:53",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "293",
            "client_id": "1242",
            "service_type": "others",
            "service_name": "NA",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601547",
            "ftk": "1199",
            "created_at": "2019-12-03 10:46:31",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "294",
            "client_id": "1317",
            "service_type": "others",
            "service_name": "NA",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601419",
            "ftk": "DEL33",
            "created_at": "2019-12-03 17:38:35",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "295",
            "client_id": "1318",
            "service_type": "flight",
            "service_name": "Flight",
            "service_price": "30000",
            "service_eligibility": "",
            "maf": "601347",
            "ftk": "1266",
            "created_at": "2019-12-03 17:46:20",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "296",
            "client_id": "1318",
            "service_type": "hotel",
            "service_name": "3 Nights",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601347",
            "ftk": "1266",
            "created_at": "2019-12-03 17:46:20",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "297",
            "client_id": "1319",
            "service_type": "flight",
            "service_name": "Flight",
            "service_price": "40000",
            "service_eligibility": "",
            "maf": "601336",
            "ftk": "DEL34",
            "created_at": "2019-12-03 18:13:34",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "299",
            "client_id": "1321",
            "service_type": "flight",
            "service_name": "Flight",
            "service_price": "10000",
            "service_eligibility": "",
            "maf": "601447",
            "ftk": "1261",
            "created_at": "2019-12-04 14:47:00",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "300",
            "client_id": "1322",
            "service_type": "flight",
            "service_name": "Flight",
            "service_price": "15000",
            "service_eligibility": "",
            "maf": "601446",
            "ftk": "1262",
            "created_at": "2019-12-04 14:56:51",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "301",
            "client_id": "1323",
            "service_type": "others",
            "service_name": "NA",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601443",
            "ftk": "1263",
            "created_at": "2019-12-04 15:03:33",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "302",
            "client_id": "1324",
            "service_type": "flight",
            "service_name": "Flight",
            "service_price": "15000",
            "service_eligibility": "",
            "maf": "601463",
            "ftk": "1264",
            "created_at": "2019-12-04 15:10:16",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "303",
            "client_id": "1325",
            "service_type": "hotel",
            "service_name": "NA",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601361",
            "ftk": "1245",
            "created_at": "2019-12-05 14:14:36",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "304",
            "client_id": "1326",
            "service_type": "others",
            "service_name": "NA",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601469",
            "ftk": "1267",
            "created_at": "2019-12-06 16:12:17",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "305",
            "client_id": "1327",
            "service_type": "flight",
            "service_name": "Flight",
            "service_price": "25000",
            "service_eligibility": "",
            "maf": "601409",
            "ftk": "1269",
            "created_at": "2019-12-06 16:34:57",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "308",
            "client_id": "1329",
            "service_type": "flight",
            "service_name": "Flight",
            "service_price": "10000",
            "service_eligibility": "",
            "maf": "601350",
            "ftk": "1271",
            "created_at": "2019-12-06 17:20:50",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "311",
            "client_id": "1256",
            "service_type": "others",
            "service_name": "NA",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601328",
            "ftk": "1215",
            "created_at": "2019-12-07 10:59:56",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "313",
            "client_id": "1331",
            "service_type": "hotel",
            "service_name": "3 Nights",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601448",
            "ftk": "1276",
            "created_at": "2019-12-07 15:13:48",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "314",
            "client_id": "1332",
            "service_type": "flight",
            "service_name": "Flight",
            "service_price": "10000",
            "service_eligibility": "",
            "maf": "601468",
            "ftk": "1274",
            "created_at": "2019-12-07 16:37:22",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "315",
            "client_id": "1333",
            "service_type": "hotel",
            "service_name": "2 Nights",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601420",
            "ftk": "1277",
            "created_at": "2019-12-09 17:52:43",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "316",
            "client_id": "1334",
            "service_type": "flight",
            "service_name": "Flight",
            "service_price": "10000",
            "service_eligibility": "",
            "maf": "601488",
            "ftk": "1278",
            "created_at": "2019-12-09 18:19:35",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "317",
            "client_id": "1334",
            "service_type": "hotel",
            "service_name": "3 Nights",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601488",
            "ftk": "1278",
            "created_at": "2019-12-09 18:19:35",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "318",
            "client_id": "1335",
            "service_type": "others",
            "service_name": "NA",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601486",
            "ftk": "1279",
            "created_at": "2019-12-10 10:35:44",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "319",
            "client_id": "1336",
            "service_type": "flight",
            "service_name": "Flight",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601487",
            "ftk": "1280",
            "created_at": "2019-12-10 11:07:53",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "320",
            "client_id": "1337",
            "service_type": "flight",
            "service_name": "Flight",
            "service_price": "12000",
            "service_eligibility": "",
            "maf": "601482",
            "ftk": "DEL35",
            "created_at": "2019-12-10 11:33:44",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "321",
            "client_id": "1338",
            "service_type": "flight",
            "service_name": "Flight",
            "service_price": "15000",
            "service_eligibility": "",
            "maf": "601479",
            "ftk": "DEL36",
            "created_at": "2019-12-10 11:45:04",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "323",
            "client_id": "1220",
            "service_type": "others",
            "service_name": "NA",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601582",
            "ftk": "1188",
            "created_at": "2019-12-10 16:09:15",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "326",
            "client_id": "1339",
            "service_type": "others",
            "service_name": "NA",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601422",
            "ftk": "1282",
            "created_at": "2019-12-12 14:38:45",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "327",
            "client_id": "1340",
            "service_type": "others",
            "service_name": "NA",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601424",
            "ftk": "1283",
            "created_at": "2019-12-12 15:12:37",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "328",
            "client_id": "1213",
            "service_type": "others",
            "service_name": "NA",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601580",
            "ftk": "DEL12",
            "created_at": "2019-12-12 15:14:59",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "332",
            "client_id": "1343",
            "service_type": "others",
            "service_name": "3 Movie ticket",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601395",
            "ftk": "1285",
            "created_at": "2019-12-14 12:33:34",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "333",
            "client_id": "1344",
            "service_type": "others",
            "service_name": "3movie tickets",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601394",
            "ftk": "1286",
            "created_at": "2019-12-14 12:51:00",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "334",
            "client_id": "1342",
            "service_type": "flight",
            "service_name": "Flight",
            "service_price": "12500",
            "service_eligibility": "",
            "maf": "601393",
            "ftk": "1284",
            "created_at": "2019-12-14 14:19:45",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "335",
            "client_id": "1328",
            "service_type": "flight",
            "service_name": "Flight",
            "service_price": "20000",
            "service_eligibility": "",
            "maf": "601455",
            "ftk": "1270",
            "created_at": "2019-12-14 14:37:09",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "336",
            "client_id": "1328",
            "service_type": "hotel",
            "service_name": "2 Nights",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601455",
            "ftk": "1270",
            "created_at": "2019-12-14 14:37:09",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "337",
            "client_id": "1345",
            "service_type": "flight",
            "service_name": "Flight",
            "service_price": "20000",
            "service_eligibility": "",
            "maf": "601478",
            "ftk": "1287",
            "created_at": "2019-12-14 14:42:38",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "339",
            "client_id": "1346",
            "service_type": "flight",
            "service_name": "Flight",
            "service_price": "10000",
            "service_eligibility": "",
            "maf": "601464",
            "ftk": "1275",
            "created_at": "2019-12-16 17:10:52",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "340",
            "client_id": "1346",
            "service_type": "hotel",
            "service_name": "3 Nights",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601464",
            "ftk": "1275",
            "created_at": "2019-12-16 17:10:52",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "342",
            "client_id": "1347",
            "service_type": "hotel",
            "service_name": "2Nights",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601337",
            "ftk": "DEL37",
            "created_at": "2019-12-17 16:07:13",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "344",
            "client_id": "1305",
            "service_type": "others",
            "service_name": "NA",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601365",
            "ftk": "1248",
            "created_at": "2019-12-18 11:24:59",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "345",
            "client_id": "1348",
            "service_type": "flight",
            "service_name": "Flight",
            "service_price": "20000",
            "service_eligibility": "",
            "maf": "601541",
            "ftk": "DEL38",
            "created_at": "2019-12-18 16:26:37",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "346",
            "client_id": "1349",
            "service_type": "flight",
            "service_name": "Flight",
            "service_price": "20000",
            "service_eligibility": "",
            "maf": "601527",
            "ftk": "DEL39",
            "created_at": "2019-12-18 16:51:01",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "352",
            "client_id": "1352",
            "service_type": "hotel",
            "service_name": "NA",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601396",
            "ftk": "1289",
            "created_at": "2019-12-19 11:41:29",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "353",
            "client_id": "1353",
            "service_type": "hotel",
            "service_name": "NA",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601398",
            "ftk": "1290",
            "created_at": "2019-12-19 12:27:11",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "354",
            "client_id": "1354",
            "service_type": "hotel",
            "service_name": "NA",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601397",
            "ftk": "1291",
            "created_at": "2019-12-19 14:51:50",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "356",
            "client_id": "1356",
            "service_type": "hotel",
            "service_name": "NA",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601509",
            "ftk": "1293",
            "created_at": "2019-12-19 15:49:14",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "357",
            "client_id": "1357",
            "service_type": "hotel",
            "service_name": "2 Nights",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601511",
            "ftk": "1288",
            "created_at": "2019-12-20 11:29:55",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "358",
            "client_id": "1358",
            "service_type": "flight",
            "service_name": "Air tickets",
            "service_price": "10000",
            "service_eligibility": "",
            "maf": "601519",
            "ftk": "1298",
            "created_at": "2019-12-23 15:55:32",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "359",
            "client_id": "1358",
            "service_type": "hotel",
            "service_name": "3 nights",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601519",
            "ftk": "1298",
            "created_at": "2019-12-23 15:55:32",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "360",
            "client_id": "1330",
            "service_type": "others",
            "service_name": "NA",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601471",
            "ftk": "1273",
            "created_at": "2019-12-23 18:10:27",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "361",
            "client_id": "1351",
            "service_type": "flight",
            "service_name": "Flight",
            "service_price": "20000",
            "service_eligibility": "",
            "maf": "601480",
            "ftk": "DEL41",
            "created_at": "2019-12-24 12:25:56",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "362",
            "client_id": "1359",
            "service_type": "flight",
            "service_name": "Air tickets after 30%",
            "service_price": "5000",
            "service_eligibility": "",
            "maf": "601369",
            "ftk": "1294",
            "created_at": "2019-12-24 16:17:42",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "363",
            "client_id": "1359",
            "service_type": "hotel",
            "service_name": "2Nights",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601369",
            "ftk": "1294",
            "created_at": "2019-12-24 16:17:42",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "365",
            "client_id": "1361",
            "service_type": "hotel",
            "service_name": "3 nights",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601460",
            "ftk": "1296",
            "created_at": "2019-12-24 16:57:49",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "366",
            "client_id": "1362",
            "service_type": "hotel",
            "service_name": "2Nights",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601357",
            "ftk": "1297",
            "created_at": "2019-12-24 17:20:27",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "368",
            "client_id": "1363",
            "service_type": "hotel",
            "service_name": "2 Nights",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601543",
            "ftk": "DEL42",
            "created_at": "2019-12-25 13:04:54",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "369",
            "client_id": "1364",
            "service_type": "hotel",
            "service_name": "3 Nights",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601489",
            "ftk": "DEL43",
            "created_at": "2019-12-25 15:47:57",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "371",
            "client_id": "1360",
            "service_type": "flight",
            "service_name": "Air tickets",
            "service_price": "10000",
            "service_eligibility": "",
            "maf": "601472",
            "ftk": "1295",
            "created_at": "2019-12-25 18:06:43",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "372",
            "client_id": "1226",
            "service_type": "hotel",
            "service_name": "2Nights",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601324",
            "ftk": "1193",
            "created_at": "2019-12-26 14:11:19",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "373",
            "client_id": "1355",
            "service_type": "flight",
            "service_name": "Flight after 30%",
            "service_price": "12500",
            "service_eligibility": "",
            "maf": "601399",
            "ftk": "1292",
            "created_at": "2019-12-26 15:54:54",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "375",
            "client_id": "1320",
            "service_type": "flight",
            "service_name": "Flight",
            "service_price": "25000",
            "service_eligibility": "",
            "maf": "601315",
            "ftk": "1260",
            "created_at": "2019-12-27 11:35:38",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "378",
            "client_id": "1366",
            "service_type": "hotel",
            "service_name": "2night",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601512",
            "ftk": "1300",
            "created_at": "2019-12-28 15:57:15",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "379",
            "client_id": "1367",
            "service_type": "flight",
            "service_name": "FLIGHT",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601400",
            "ftk": "1301",
            "created_at": "2019-12-28 16:07:53",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "380",
            "client_id": "1368",
            "service_type": "hotel",
            "service_name": "2night",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601386",
            "ftk": "DEL44",
            "created_at": "2019-12-28 16:19:50",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "382",
            "client_id": "1365",
            "service_type": "flight",
            "service_name": "FLIGHT",
            "service_price": "10000",
            "service_eligibility": "",
            "maf": "601444",
            "ftk": "1299",
            "created_at": "2020-01-01 13:37:47",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "383",
            "client_id": "1365",
            "service_type": "hotel",
            "service_name": "4DAYS",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601444",
            "ftk": "1299",
            "created_at": "2020-01-01 13:37:47",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "384",
            "client_id": "1370",
            "service_type": "flight",
            "service_name": "flight",
            "service_price": "15000",
            "service_eligibility": "",
            "maf": "601514",
            "ftk": "1304",
            "created_at": "2020-01-01 15:18:48",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "385",
            "client_id": "1370",
            "service_type": "hotel",
            "service_name": "3 night",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601514",
            "ftk": "1304",
            "created_at": "2020-01-01 15:18:48",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "386",
            "client_id": "1371",
            "service_type": "flight",
            "service_name": "Flight",
            "service_price": "15000",
            "service_eligibility": "",
            "maf": "601513",
            "ftk": "1305",
            "created_at": "2020-01-01 15:28:33",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "387",
            "client_id": "1372",
            "service_type": "hotel",
            "service_name": "3 Night",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601515",
            "ftk": "1306",
            "created_at": "2020-01-01 15:41:07",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "388",
            "client_id": "1372",
            "service_type": "flight",
            "service_name": "Flight",
            "service_price": "16000",
            "service_eligibility": "",
            "maf": "601515",
            "ftk": "1306",
            "created_at": "2020-01-01 15:41:07",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "389",
            "client_id": "1313",
            "service_type": "hotel",
            "service_name": "3 Nights",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601484",
            "ftk": "1256",
            "created_at": "2020-01-02 15:14:57",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "390",
            "client_id": "1369",
            "service_type": "hotel",
            "service_name": "2 night",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601439",
            "ftk": "1302",
            "created_at": "2020-01-02 17:40:08",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "391",
            "client_id": "1373",
            "service_type": "hotel",
            "service_name": "2 night",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601556",
            "ftk": "1303",
            "created_at": "2020-01-06 17:08:17",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "392",
            "client_id": "1374",
            "service_type": "hotel",
            "service_name": "2 night",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601506",
            "ftk": "1307",
            "created_at": "2020-01-07 12:18:41",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "393",
            "client_id": "1375",
            "service_type": "hotel",
            "service_name": "1 NIGHT PARKPLAZA",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601508",
            "ftk": "1308",
            "created_at": "2020-01-07 13:17:34",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "395",
            "client_id": "1377",
            "service_type": "flight",
            "service_name": "Flight",
            "service_price": "10000",
            "service_eligibility": "",
            "maf": "601501",
            "ftk": "1310",
            "created_at": "2020-01-07 15:48:55",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "396",
            "client_id": "1377",
            "service_type": "hotel",
            "service_name": "2 night",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601501",
            "ftk": "1310",
            "created_at": "2020-01-07 15:48:55",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "397",
            "client_id": "1378",
            "service_type": "hotel",
            "service_name": "2 night",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601497",
            "ftk": "1311",
            "created_at": "2020-01-07 16:30:36",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "398",
            "client_id": "1379",
            "service_type": "hotel",
            "service_name": "3 night",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601348",
            "ftk": "1312",
            "created_at": "2020-01-07 16:58:51",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "399",
            "client_id": "1380",
            "service_type": "hotel",
            "service_name": "3NIGHT",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601378",
            "ftk": "1313",
            "created_at": "2020-01-07 17:16:50",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "401",
            "client_id": "1382",
            "service_type": "hotel",
            "service_name": "5 nights",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601524",
            "ftk": "1315",
            "created_at": "2020-01-07 18:31:11",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "402",
            "client_id": "1383",
            "service_type": "hotel",
            "service_name": "3 Night",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601533",
            "ftk": "DEL45",
            "created_at": "2020-01-08 12:39:47",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "403",
            "client_id": "1384",
            "service_type": "hotel",
            "service_name": "3 Night",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601441",
            "ftk": "1316",
            "created_at": "2020-01-08 12:57:53",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "404",
            "client_id": "1385",
            "service_type": "hotel",
            "service_name": "Flight",
            "service_price": "10000",
            "service_eligibility": "",
            "maf": "601507",
            "ftk": "1317",
            "created_at": "2020-01-08 15:07:30",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "405",
            "client_id": "1386",
            "service_type": "others",
            "service_name": "NA",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601445",
            "ftk": "1318",
            "created_at": "2020-01-08 15:13:21",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "406",
            "client_id": "1376",
            "service_type": "hotel",
            "service_name": "5 night",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601442",
            "ftk": "1309",
            "created_at": "2020-01-11 11:45:14",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "407",
            "client_id": "1387",
            "service_type": "hotel",
            "service_name": "2 night",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601581",
            "ftk": "1320",
            "created_at": "2020-01-11 12:11:13",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "408",
            "client_id": "1388",
            "service_type": "hotel",
            "service_name": "2 night",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601436",
            "ftk": "1321",
            "created_at": "2020-01-11 12:57:22",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "410",
            "client_id": "1390",
            "service_type": "flight",
            "service_name": "Flight",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601354",
            "ftk": "1319",
            "created_at": "2020-01-13 11:31:58",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "411",
            "client_id": "1391",
            "service_type": "flight",
            "service_name": "Flight",
            "service_price": "10000",
            "service_eligibility": "",
            "maf": "601334",
            "ftk": "1323",
            "created_at": "2020-01-13 11:48:57",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "412",
            "client_id": "1350",
            "service_type": "flight",
            "service_name": "Flight",
            "service_price": "10000",
            "service_eligibility": "",
            "maf": "601345",
            "ftk": "DEL40",
            "created_at": "2020-01-15 11:28:51",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "413",
            "client_id": "1392",
            "service_type": "others",
            "service_name": "NA",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601499",
            "ftk": "1324",
            "created_at": "2020-01-15 12:27:25",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "415",
            "client_id": "1381",
            "service_type": "hotel",
            "service_name": "3 night",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601385",
            "ftk": "1314",
            "created_at": "2020-01-15 13:04:49",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "416",
            "client_id": "1394",
            "service_type": "others",
            "service_name": "NA",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601380",
            "ftk": "DEL46",
            "created_at": "2020-01-15 16:43:28",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "417",
            "client_id": "1395",
            "service_type": "others",
            "service_name": "NA",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601381",
            "ftk": "DEL47",
            "created_at": "2020-01-15 17:24:10",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "418",
            "client_id": "1396",
            "service_type": "hotel",
            "service_name": "2 night",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601430",
            "ftk": "DEL48",
            "created_at": "2020-01-15 18:10:35",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "419",
            "client_id": "1389",
            "service_type": "hotel",
            "service_name": "2 night",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601428",
            "ftk": "1322",
            "created_at": "2020-01-15 18:14:18",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "420",
            "client_id": "1397",
            "service_type": "flight",
            "service_name": "Air ticket",
            "service_price": "10000",
            "service_eligibility": "",
            "maf": "601571",
            "ftk": "1163",
            "created_at": "2020-01-17 10:35:44",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "423",
            "client_id": "1398",
            "service_type": "hotel",
            "service_name": "4 Night",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601601",
            "ftk": "1326",
            "created_at": "2020-01-18 15:31:17",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "426",
            "client_id": "1297",
            "service_type": "others",
            "service_name": "NA",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601415",
            "ftk": "1244",
            "created_at": "2020-01-20 12:29:57",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "427",
            "client_id": "1399",
            "service_type": "hotel",
            "service_name": "5 night",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601603",
            "ftk": "1328",
            "created_at": "2020-01-20 18:02:00",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "428",
            "client_id": "1400",
            "service_type": "hotel",
            "service_name": "2 night",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601604",
            "ftk": "1329",
            "created_at": "2020-01-20 18:32:59",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "429",
            "client_id": "1341",
            "service_type": "flight",
            "service_name": "Flight",
            "service_price": "20000",
            "service_eligibility": "",
            "maf": "601421",
            "ftk": "1281",
            "created_at": "2020-01-21 11:21:06",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "430",
            "client_id": "1401",
            "service_type": "hotel",
            "service_name": "2 night",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601493",
            "ftk": "1330",
            "created_at": "2020-01-21 11:48:03",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "431",
            "client_id": "1402",
            "service_type": "flight",
            "service_name": "Flight",
            "service_price": "15000",
            "service_eligibility": "",
            "maf": "601609",
            "ftk": "1331",
            "created_at": "2020-01-21 12:04:20",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "432",
            "client_id": "1402",
            "service_type": "hotel",
            "service_name": "4 night",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601609",
            "ftk": "1331",
            "created_at": "2020-01-21 12:04:20",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "433",
            "client_id": "1403",
            "service_type": "hotel",
            "service_name": "3 NIght",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601602",
            "ftk": "1327",
            "created_at": "2020-01-21 12:29:16",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "435",
            "client_id": "1405",
            "service_type": "flight",
            "service_name": "AIR TICKET",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601613",
            "ftk": "1333",
            "created_at": "2020-01-21 15:39:24",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "436",
            "client_id": "1406",
            "service_type": "flight",
            "service_name": "FLIGHT",
            "service_price": "18000",
            "service_eligibility": "",
            "maf": "601608",
            "ftk": "1334",
            "created_at": "2020-01-21 16:02:16",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "438",
            "client_id": "1407",
            "service_type": "flight",
            "service_name": "FLIGHT",
            "service_price": "20000",
            "service_eligibility": "",
            "maf": "601494",
            "ftk": "1335",
            "created_at": "2020-01-21 18:21:33",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "440",
            "client_id": "1404",
            "service_type": "others",
            "service_name": "NA",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601612",
            "ftk": "1332",
            "created_at": "2020-01-23 12:02:19",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "441",
            "client_id": "1408",
            "service_type": "flight",
            "service_name": "FLIGHT",
            "service_price": "12000",
            "service_eligibility": "",
            "maf": "601618",
            "ftk": "1336",
            "created_at": "2020-01-23 12:30:02",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "442",
            "client_id": "1409",
            "service_type": "hotel",
            "service_name": "2 night",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601520",
            "ftk": "DEL51",
            "created_at": "2020-01-24 17:04:02",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "443",
            "client_id": "1312",
            "service_type": "flight",
            "service_name": "Flight",
            "service_price": "20000",
            "service_eligibility": "",
            "maf": "601330",
            "ftk": "1255",
            "created_at": "2020-01-25 12:16:05",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "444",
            "client_id": "1410",
            "service_type": "hotel",
            "service_name": "3 Night",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601614",
            "ftk": "1339",
            "created_at": "2020-01-25 15:43:13",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "445",
            "client_id": "1411",
            "service_type": "hotel",
            "service_name": "2 night",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601615",
            "ftk": "1337",
            "created_at": "2020-01-28 17:49:22",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "446",
            "client_id": "1412",
            "service_type": "hotel",
            "service_name": "3 night",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601373",
            "ftk": "DEL49",
            "created_at": "2020-01-29 14:59:58",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "447",
            "client_id": "1413",
            "service_type": "hotel",
            "service_name": "3 night",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601481",
            "ftk": "DEL50",
            "created_at": "2020-01-29 15:12:45",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "448",
            "client_id": "1414",
            "service_type": "others",
            "service_name": "Na",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601502",
            "ftk": "DEL52",
            "created_at": "2020-01-29 15:28:42",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "449",
            "client_id": "1415",
            "service_type": "hotel",
            "service_name": "NA",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601465",
            "ftk": "DEL53",
            "created_at": "2020-01-29 15:43:01",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "450",
            "client_id": "1393",
            "service_type": "others",
            "service_name": "NA",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601504",
            "ftk": "1325",
            "created_at": "2020-01-30 10:44:32",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "451",
            "client_id": "1416",
            "service_type": "others",
            "service_name": "NA",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601379",
            "ftk": "DEL54",
            "created_at": "2020-01-30 14:49:06",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "453",
            "client_id": "1418",
            "service_type": "flight",
            "service_name": "FLIGHT",
            "service_price": "20000",
            "service_eligibility": "",
            "maf": "601616",
            "ftk": "1338",
            "created_at": "2020-01-30 15:48:42",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "454",
            "client_id": "1419",
            "service_type": "flight",
            "service_name": "FLIGHT",
            "service_price": "12000",
            "service_eligibility": "",
            "maf": "601491",
            "ftk": "1340",
            "created_at": "2020-01-30 17:08:25",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "455",
            "client_id": "1420",
            "service_type": "flight",
            "service_name": "FLIGHT",
            "service_price": "14500",
            "service_eligibility": "",
            "maf": "601620",
            "ftk": "1341",
            "created_at": "2020-01-30 17:26:42",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "456",
            "client_id": "1421",
            "service_type": "flight",
            "service_name": "FLIGHT",
            "service_price": "20000",
            "service_eligibility": "",
            "maf": "601495",
            "ftk": "1342",
            "created_at": "2020-01-30 17:46:22",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "457",
            "client_id": "1422",
            "service_type": "hotel",
            "service_name": "4 Night stay",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601490",
            "ftk": "1343",
            "created_at": "2020-01-30 17:56:40",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "459",
            "client_id": "1424",
            "service_type": "hotel",
            "service_name": "2 night",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601496",
            "ftk": "1345",
            "created_at": "2020-01-31 13:14:24",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "460",
            "client_id": "1425",
            "service_type": "hotel",
            "service_name": "2 night",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601619",
            "ftk": "1346",
            "created_at": "2020-01-31 15:13:50",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "461",
            "client_id": "1426",
            "service_type": "flight",
            "service_name": "FLIGHT",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601623",
            "ftk": "1347",
            "created_at": "2020-01-31 15:54:05",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "462",
            "client_id": "1427",
            "service_type": "hotel",
            "service_name": "4 night",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601622",
            "ftk": "1348",
            "created_at": "2020-01-31 16:01:13",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "463",
            "client_id": "1427",
            "service_type": "flight",
            "service_name": "FLIGHT",
            "service_price": "10000",
            "service_eligibility": "",
            "maf": "601622",
            "ftk": "1348",
            "created_at": "2020-01-31 16:01:13",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "464",
            "client_id": "1264",
            "service_type": "others",
            "service_name": "NA",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601548",
            "ftk": "1207",
            "created_at": "2020-01-31 16:17:39",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "465",
            "client_id": "1428",
            "service_type": "hotel",
            "service_name": "2 night",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601621",
            "ftk": "1349",
            "created_at": "2020-01-31 18:20:29",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "466",
            "client_id": "1429",
            "service_type": "flight",
            "service_name": "FLIGHT",
            "service_price": "10000",
            "service_eligibility": "",
            "maf": "601376",
            "ftk": "1350",
            "created_at": "2020-02-01 14:36:49",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "467",
            "client_id": "1429",
            "service_type": "hotel",
            "service_name": "3 night",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601376",
            "ftk": "1350",
            "created_at": "2020-02-01 14:36:49",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "468",
            "client_id": "1430",
            "service_type": "hotel",
            "service_name": "3 night",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601475",
            "ftk": "1351",
            "created_at": "2020-02-01 15:00:15",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "469",
            "client_id": "1431",
            "service_type": "others",
            "service_name": "NA",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601510",
            "ftk": "1352",
            "created_at": "2020-02-01 15:37:35",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "471",
            "client_id": "1423",
            "service_type": "flight",
            "service_name": "FLIGHT",
            "service_price": "5000",
            "service_eligibility": "",
            "maf": "601335",
            "ftk": "1344",
            "created_at": "2020-02-03 12:11:18",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "472",
            "client_id": "1432",
            "service_type": "flight",
            "service_name": "FLIGHT",
            "service_price": "15000",
            "service_eligibility": "",
            "maf": "601449",
            "ftk": "1353",
            "created_at": "2020-02-04 16:30:22",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "473",
            "client_id": "1417",
            "service_type": "hotel",
            "service_name": "3 night",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601437",
            "ftk": "DEL55",
            "created_at": "2020-02-05 11:42:44",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "474",
            "client_id": "1151",
            "service_type": "flight",
            "service_name": "Air Tickets",
            "service_price": "20000",
            "service_eligibility": "",
            "maf": "552884",
            "ftk": "1128",
            "created_at": "2020-02-05 11:50:43",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "476",
            "client_id": "1434",
            "service_type": "flight",
            "service_name": "Air ticket",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601625",
            "ftk": "1354",
            "created_at": "2020-02-07 16:19:31",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "477",
            "client_id": "1435",
            "service_type": "hotel",
            "service_name": "2 night",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601626",
            "ftk": "1355",
            "created_at": "2020-02-07 16:30:21",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "478",
            "client_id": "1436",
            "service_type": "flight",
            "service_name": "FLIGHT",
            "service_price": "20000",
            "service_eligibility": "",
            "maf": "601606",
            "ftk": "1356",
            "created_at": "2020-02-11 13:29:37",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "479",
            "client_id": "1436",
            "service_type": "hotel",
            "service_name": "3 night",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601606",
            "ftk": "1356",
            "created_at": "2020-02-11 13:29:37",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "480",
            "client_id": "1437",
            "service_type": "flight",
            "service_name": "FLIGHT",
            "service_price": "20000",
            "service_eligibility": "",
            "maf": "601641",
            "ftk": "1357",
            "created_at": "2020-02-11 13:36:59",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "481",
            "client_id": "1438",
            "service_type": "others",
            "service_name": "NA",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601643",
            "ftk": "1358",
            "created_at": "2020-02-11 13:46:12",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "482",
            "client_id": "1439",
            "service_type": "flight",
            "service_name": "FLIGHT",
            "service_price": "12000",
            "service_eligibility": "",
            "maf": "601630",
            "ftk": "1359",
            "created_at": "2020-02-11 13:53:13",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "483",
            "client_id": "1439",
            "service_type": "hotel",
            "service_name": "2 night",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601630",
            "ftk": "1359",
            "created_at": "2020-02-11 13:53:13",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "484",
            "client_id": "1440",
            "service_type": "flight",
            "service_name": "FLIGHT",
            "service_price": "12500",
            "service_eligibility": "",
            "maf": "601642",
            "ftk": "1360",
            "created_at": "2020-02-11 15:36:39",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "485",
            "client_id": "1441",
            "service_type": "flight",
            "service_name": "FLIGHT",
            "service_price": "12000",
            "service_eligibility": "",
            "maf": "601624",
            "ftk": "1361",
            "created_at": "2020-02-11 16:01:03",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "486",
            "client_id": "1441",
            "service_type": "hotel",
            "service_name": "3 night",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601624",
            "ftk": "1361",
            "created_at": "2020-02-11 16:01:03",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "487",
            "client_id": "1442",
            "service_type": "hotel",
            "service_name": "4 night",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601632",
            "ftk": "1362",
            "created_at": "2020-02-11 16:14:04",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "488",
            "client_id": "1443",
            "service_type": "hotel",
            "service_name": "2 night",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601634",
            "ftk": "1363",
            "created_at": "2020-02-11 16:25:12",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "489",
            "client_id": "1444",
            "service_type": "hotel",
            "service_name": "2 night",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601639",
            "ftk": "1364",
            "created_at": "2020-02-11 16:52:18",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "490",
            "client_id": "1445",
            "service_type": "hotel",
            "service_name": "2 night",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601640",
            "ftk": "1365",
            "created_at": "2020-02-11 17:00:36",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "491",
            "client_id": "1446",
            "service_type": "hotel",
            "service_name": "3 night",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601631",
            "ftk": "1366",
            "created_at": "2020-02-11 17:20:59",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "492",
            "client_id": "1447",
            "service_type": "hotel",
            "service_name": "4 night",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601633",
            "ftk": "1367",
            "created_at": "2020-02-11 17:27:42",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "493",
            "client_id": "1448",
            "service_type": "hotel",
            "service_name": "2 night",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601312",
            "ftk": "1368",
            "created_at": "2020-02-13 16:00:28",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "494",
            "client_id": "1449",
            "service_type": "others",
            "service_name": "NA",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601403",
            "ftk": "1369",
            "created_at": "2020-02-13 16:30:55",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "495",
            "client_id": "1450",
            "service_type": "hotel",
            "service_name": "3 night",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601638",
            "ftk": "1370",
            "created_at": "2020-02-13 17:27:37",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "497",
            "client_id": "1451",
            "service_type": "hotel",
            "service_name": "3 night",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601653",
            "ftk": "1371",
            "created_at": "2020-02-18 11:50:53",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "500",
            "client_id": "1454",
            "service_type": "hotel",
            "service_name": "3 night",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601306",
            "ftk": "DEL59",
            "created_at": "2020-02-18 16:15:37",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "501",
            "client_id": "1455",
            "service_type": "flight",
            "service_name": "FLIGHT",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601435",
            "ftk": "DEL60",
            "created_at": "2020-02-18 16:33:21",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "502",
            "client_id": "1456",
            "service_type": "others",
            "service_name": "NA",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601308",
            "ftk": "1372",
            "created_at": "2020-02-18 16:42:40",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "503",
            "client_id": "1457",
            "service_type": "hotel",
            "service_name": "4 night",
            "service_price": "20000",
            "service_eligibility": "",
            "maf": "601307",
            "ftk": "1373",
            "created_at": "2020-02-18 16:56:01",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "504",
            "client_id": "1458",
            "service_type": "flight",
            "service_name": "FLIGHT",
            "service_price": "10000",
            "service_eligibility": "",
            "maf": "601309",
            "ftk": "1374",
            "created_at": "2020-02-18 17:03:21",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "505",
            "client_id": "1459",
            "service_type": "others",
            "service_name": "NA",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601505",
            "ftk": "1375",
            "created_at": "2020-02-18 17:08:50",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "506",
            "client_id": "1309",
            "service_type": "flight",
            "service_name": "Flight",
            "service_price": "25000",
            "service_eligibility": "",
            "maf": "601362",
            "ftk": "1252",
            "created_at": "2020-02-19 16:30:07",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "508",
            "client_id": "1452",
            "service_type": "others",
            "service_name": "NA",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601375",
            "ftk": "57",
            "created_at": "2020-02-19 17:02:45",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "510",
            "client_id": "1453",
            "service_type": "hotel",
            "service_name": "2 night",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601522",
            "ftk": "DEL58",
            "created_at": "2020-02-19 17:03:25",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "511",
            "client_id": "1433",
            "service_type": "hotel",
            "service_name": "2 night",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601382",
            "ftk": "DEL56",
            "created_at": "2020-02-20 11:40:20",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "512",
            "client_id": "1460",
            "service_type": "hotel",
            "service_name": "2 night",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601374",
            "ftk": "DEL61",
            "created_at": "2020-02-24 14:19:38",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "514",
            "client_id": "1462",
            "service_type": "hotel",
            "service_name": "2 night",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601466",
            "ftk": "1378",
            "created_at": "2020-02-25 17:20:13",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "515",
            "client_id": "1463",
            "service_type": "hotel",
            "service_name": "2 night",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601429",
            "ftk": "1379",
            "created_at": "2020-02-25 17:36:12",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "517",
            "client_id": "1465",
            "service_type": "flight",
            "service_name": "FLIGHT",
            "service_price": "15000",
            "service_eligibility": "",
            "maf": "601671",
            "ftk": "1376",
            "created_at": "2020-02-26 11:34:12",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "518",
            "client_id": "1466",
            "service_type": "flight",
            "service_name": "FLIGHT",
            "service_price": "15000",
            "service_eligibility": "",
            "maf": "",
            "ftk": "",
            "created_at": "2020-02-26 11:47:15",
            "updated_at": "2020-02-26 11:47:15"
        },
        {
            "id": "519",
            "client_id": "1467",
            "service_type": "hotel",
            "service_name": "3 night",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601433",
            "ftk": "1381",
            "created_at": "2020-02-26 11:59:56",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "520",
            "client_id": "1468",
            "service_type": "flight",
            "service_name": "FLIGHT",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "552834",
            "ftk": "1382",
            "created_at": "2020-02-26 13:00:57",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "521",
            "client_id": "1469",
            "service_type": "hotel",
            "service_name": "2 night",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601483",
            "ftk": "1383",
            "created_at": "2020-02-26 14:56:16",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "522",
            "client_id": "1470",
            "service_type": "hotel",
            "service_name": "3 night",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601426",
            "ftk": "1384",
            "created_at": "2020-02-27 12:46:21",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "523",
            "client_id": "1471",
            "service_type": "hotel",
            "service_name": "2 night",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601679",
            "ftk": "1385",
            "created_at": "2020-02-27 13:08:57",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "525",
            "client_id": "1473",
            "service_type": "hotel",
            "service_name": "2 night",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601610",
            "ftk": "1394",
            "created_at": "2020-03-02 16:21:25",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "526",
            "client_id": "1474",
            "service_type": "hotel",
            "service_name": "4 night",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601645",
            "ftk": "1395",
            "created_at": "2020-03-02 16:47:59",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "527",
            "client_id": "1475",
            "service_type": "flight",
            "service_name": "FLIGHT",
            "service_price": "15000",
            "service_eligibility": "",
            "maf": "601611",
            "ftk": "1396",
            "created_at": "2020-03-02 17:31:39",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "528",
            "client_id": "1476",
            "service_type": "flight",
            "service_name": "FLIGHT",
            "service_price": "10000",
            "service_eligibility": "",
            "maf": "601683",
            "ftk": "1388",
            "created_at": "2020-03-03 10:15:49",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "529",
            "client_id": "1476",
            "service_type": "hotel",
            "service_name": "NA",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601683",
            "ftk": "1388",
            "created_at": "2020-03-03 10:15:49",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "532",
            "client_id": "1477",
            "service_type": "hotel",
            "service_name": "3 night",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601617",
            "ftk": "1393",
            "created_at": "2020-03-03 10:56:53",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "533",
            "client_id": "1478",
            "service_type": "flight",
            "service_name": "FLIGHT",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601669",
            "ftk": "1386",
            "created_at": "2020-03-03 13:04:47",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "534",
            "client_id": "1479",
            "service_type": "hotel",
            "service_name": "3 night",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601709",
            "ftk": "1398",
            "created_at": "2020-03-03 14:19:30",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "535",
            "client_id": "1479",
            "service_type": "flight",
            "service_name": "FLIGHT",
            "service_price": "10000",
            "service_eligibility": "",
            "maf": "601709",
            "ftk": "1398",
            "created_at": "2020-03-03 14:19:30",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "536",
            "client_id": "1480",
            "service_type": "flight",
            "service_name": "FLIGHT",
            "service_price": "14500",
            "service_eligibility": "",
            "maf": "601650",
            "ftk": "1392",
            "created_at": "2020-03-03 14:42:57",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "537",
            "client_id": "1481",
            "service_type": "hotel",
            "service_name": "3 night",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601681",
            "ftk": "1387",
            "created_at": "2020-03-04 14:51:44",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "538",
            "client_id": "1482",
            "service_type": "hotel",
            "service_name": "4 night",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601467",
            "ftk": "1390",
            "created_at": "2020-03-04 15:04:48",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "539",
            "client_id": "1482",
            "service_type": "flight",
            "service_name": "FLIGHT",
            "service_price": "10000",
            "service_eligibility": "",
            "maf": "601467",
            "ftk": "1390",
            "created_at": "2020-03-04 15:04:48",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "540",
            "client_id": "1483",
            "service_type": "flight",
            "service_name": "FLIGHT",
            "service_price": "10000",
            "service_eligibility": "",
            "maf": "601434",
            "ftk": "1391",
            "created_at": "2020-03-04 15:15:24",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "541",
            "client_id": "1484",
            "service_type": "flight",
            "service_name": "FLIGHT",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601655",
            "ftk": "1397",
            "created_at": "2020-03-04 15:35:58",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "542",
            "client_id": "1464",
            "service_type": "others",
            "service_name": "NA",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601523",
            "ftk": "1380",
            "created_at": "2020-03-05 11:32:30",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "543",
            "client_id": "1485",
            "service_type": "hotel",
            "service_name": "3 night",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601717",
            "ftk": "1399",
            "created_at": "2020-03-05 14:49:09",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "545",
            "client_id": "1486",
            "service_type": "hotel",
            "service_name": "2 night",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601705",
            "ftk": "DEL62",
            "created_at": "2020-03-07 14:16:50",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "546",
            "client_id": "1472",
            "service_type": "hotel",
            "service_name": "4night",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601432",
            "ftk": "1389",
            "created_at": "2020-03-09 17:42:26",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "550",
            "client_id": "1489",
            "service_type": "flight",
            "service_name": "Air tickets after 50%",
            "service_price": "20000",
            "service_eligibility": "",
            "maf": "601720",
            "ftk": "1400",
            "created_at": "2020-03-11 16:52:49",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "551",
            "client_id": "1489",
            "service_type": "hotel",
            "service_name": "3 nights in Goa",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601720",
            "ftk": "1400",
            "created_at": "2020-03-11 16:52:49",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "558",
            "client_id": "1488",
            "service_type": "flight",
            "service_name": "Travel voucher",
            "service_price": "20000",
            "service_eligibility": "",
            "maf": "601738",
            "ftk": "DEL64",
            "created_at": "2020-03-11 17:00:26",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "559",
            "client_id": "1488",
            "service_type": "hotel",
            "service_name": "3 Nights after 50%",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601738",
            "ftk": "DEL64",
            "created_at": "2020-03-11 17:00:26",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "566",
            "client_id": "1490",
            "service_type": "flight",
            "service_name": "Air ticket",
            "service_price": "10000",
            "service_eligibility": "",
            "maf": "601723",
            "ftk": "1401",
            "created_at": "2020-03-12 11:04:36",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "567",
            "client_id": "1490",
            "service_type": "hotel",
            "service_name": "2 nights",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601723",
            "ftk": "1401",
            "created_at": "2020-03-12 11:04:36",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "568",
            "client_id": "1491",
            "service_type": "hotel",
            "service_name": "3 Nights",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601718",
            "ftk": "1402",
            "created_at": "2020-03-12 11:17:08",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "571",
            "client_id": "1492",
            "service_type": "others",
            "service_name": "NA",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601719",
            "ftk": "1403",
            "created_at": "2020-03-12 11:29:06",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "572",
            "client_id": "1493",
            "service_type": "hotel",
            "service_name": "3 Nights after 30%",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601721",
            "ftk": "1404",
            "created_at": "2020-03-12 11:35:45",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "580",
            "client_id": "1494",
            "service_type": "hotel",
            "service_name": "3 nights in Goa",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601661",
            "ftk": "1405",
            "created_at": "2020-03-12 12:21:18",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "581",
            "client_id": "1494",
            "service_type": "hotel",
            "service_name": "2 nights after 20%",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601661",
            "ftk": "1405",
            "created_at": "2020-03-12 12:21:18",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "582",
            "client_id": "1494",
            "service_type": "hotel",
            "service_name": "Voucher 2 nights",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601661",
            "ftk": "1405",
            "created_at": "2020-03-12 12:21:18",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "583",
            "client_id": "1495",
            "service_type": "hotel",
            "service_name": "3 Nights after 30%",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601662",
            "ftk": "1406",
            "created_at": "2020-03-12 12:43:54",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "584",
            "client_id": "1496",
            "service_type": "hotel",
            "service_name": "3 nights with breakfast after30%",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601722",
            "ftk": "1407",
            "created_at": "2020-03-12 12:58:27",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "585",
            "client_id": "1497",
            "service_type": "hotel",
            "service_name": "2 nights",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601667",
            "ftk": "1408",
            "created_at": "2020-03-12 13:20:31",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "586",
            "client_id": "1498",
            "service_type": "hotel",
            "service_name": "3 nights",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601668",
            "ftk": "1409",
            "created_at": "2020-03-12 14:50:26",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "587",
            "client_id": "1499",
            "service_type": "hotel",
            "service_name": "4 nights after 20%",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601733",
            "ftk": "1410",
            "created_at": "2020-03-13 11:54:04",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "588",
            "client_id": "1499",
            "service_type": "hotel",
            "service_name": "2 Nights Voucher",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601733",
            "ftk": "1410",
            "created_at": "2020-03-13 11:54:04",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "589",
            "client_id": "1499",
            "service_type": "flight",
            "service_name": "airticket discount",
            "service_price": "10000",
            "service_eligibility": "",
            "maf": "601733",
            "ftk": "1410",
            "created_at": "2020-03-13 11:54:04",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "590",
            "client_id": "1487",
            "service_type": "flight",
            "service_name": "Flights discount",
            "service_price": "10000",
            "service_eligibility": "",
            "maf": "601737",
            "ftk": "DEL63",
            "created_at": "2020-03-13 15:38:18",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "592",
            "client_id": "1209",
            "service_type": "others",
            "service_name": "NA",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601574",
            "ftk": "DEL11",
            "created_at": "2020-03-14 17:04:34",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "595",
            "client_id": "1461",
            "service_type": "flight",
            "service_name": "FLIGHT",
            "service_price": "10500",
            "service_eligibility": "",
            "maf": "601637",
            "ftk": "1377",
            "created_at": "2020-03-16 12:10:21",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "597",
            "client_id": "1500",
            "service_type": "others",
            "service_name": "NA",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601699",
            "ftk": "DEL65",
            "created_at": "2020-03-17 15:16:29",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "598",
            "client_id": "1501",
            "service_type": "flight",
            "service_name": "FLIGHT",
            "service_price": "10000",
            "service_eligibility": "",
            "maf": "601656",
            "ftk": "1411",
            "created_at": "2020-03-17 17:49:09",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "599",
            "client_id": "1501",
            "service_type": "hotel",
            "service_name": "4 night",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601656",
            "ftk": "1411",
            "created_at": "2020-03-17 17:49:09",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "600",
            "client_id": "1252",
            "service_type": "flight",
            "service_name": "Flight",
            "service_price": "20000",
            "service_eligibility": "",
            "maf": "601339",
            "ftk": "1213",
            "created_at": "2020-03-19 10:53:28",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "601",
            "client_id": "1502",
            "service_type": "flight",
            "service_name": "FLIGHT",
            "service_price": "10000",
            "service_eligibility": "",
            "maf": "601425",
            "ftk": "1412",
            "created_at": "2020-03-20 12:42:31",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "602",
            "client_id": "1502",
            "service_type": "hotel",
            "service_name": "4 night",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601425",
            "ftk": "1412",
            "created_at": "2020-03-20 12:42:31",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "603",
            "client_id": "1503",
            "service_type": "flight",
            "service_name": "FLIGHT",
            "service_price": "17000",
            "service_eligibility": "",
            "maf": "601889",
            "ftk": "1413",
            "created_at": "2020-03-20 13:04:46",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "604",
            "client_id": "1504",
            "service_type": "flight",
            "service_name": "FLIGHT",
            "service_price": "12000",
            "service_eligibility": "",
            "maf": "601890",
            "ftk": "1414",
            "created_at": "2020-03-20 14:27:45",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "605",
            "client_id": "1505",
            "service_type": "hotel",
            "service_name": "4 night",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601724",
            "ftk": "1415",
            "created_at": "2020-03-20 14:43:24",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "606",
            "client_id": "1506",
            "service_type": "hotel",
            "service_name": "3 night",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601704",
            "ftk": "DEL66",
            "created_at": "2020-03-20 14:57:10",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "608",
            "client_id": "1117",
            "service_type": "flight",
            "service_name": "Air tcikets",
            "service_price": "26500",
            "service_eligibility": "",
            "maf": "552440",
            "ftk": "1095",
            "created_at": "2020-09-01 14:50:20",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "624",
            "client_id": "1507",
            "service_type": "hotel",
            "service_name": "4 Offer Nights",
            "service_price": "0000",
            "service_eligibility": "",
            "maf": "601900",
            "ftk": "1416",
            "created_at": "2020-09-11 12:15:45",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "625",
            "client_id": "1507",
            "service_type": "flight",
            "service_name": "15000 Air Ticket Discount",
            "service_price": "15000",
            "service_eligibility": "",
            "maf": "601900",
            "ftk": "1416",
            "created_at": "2020-09-11 12:15:45",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "626",
            "client_id": "1507",
            "service_type": "hotel",
            "service_name": "2 Nights Voucher",
            "service_price": "0000",
            "service_eligibility": "",
            "maf": "601900",
            "ftk": "1416",
            "created_at": "2020-09-11 12:15:45",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "629",
            "client_id": "1508",
            "service_type": "hotel",
            "service_name": "2 Nights in Chd after 20% payment",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601859",
            "ftk": "1417",
            "created_at": "2020-09-22 15:58:17",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "630",
            "client_id": "1508",
            "service_type": "flight",
            "service_name": "20000 air ticket discount for 18 Months",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601859",
            "ftk": "1417",
            "created_at": "2020-09-22 15:58:17",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "632",
            "client_id": "1509",
            "service_type": "hotel",
            "service_name": "3 Nights in shimla",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601857",
            "ftk": "1418",
            "created_at": "2020-10-01 15:00:49",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "650",
            "client_id": "1511",
            "service_type": "others",
            "service_name": "Cashback",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601706",
            "ftk": "1420",
            "created_at": "2020-10-08 10:23:42",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "651",
            "client_id": "1511",
            "service_type": "flight",
            "service_name": "Travel voucher worth 12500",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601706",
            "ftk": "1420",
            "created_at": "2020-10-08 10:23:42",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "652",
            "client_id": "1511",
            "service_type": "hotel",
            "service_name": "3 nights 4 days international after 30%",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601706",
            "ftk": "1420",
            "created_at": "2020-10-08 10:23:42",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "653",
            "client_id": "1511",
            "service_type": "hotel",
            "service_name": "2 nights voucher",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601706",
            "ftk": "1420",
            "created_at": "2020-10-08 10:23:42",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "654",
            "client_id": "1511",
            "service_type": "flight",
            "service_name": "5000 travel voucher every year india only",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601706",
            "ftk": "1420",
            "created_at": "2020-10-08 10:23:42",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "655",
            "client_id": "1510",
            "service_type": "hotel",
            "service_name": "2* 3* property according to company",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601742",
            "ftk": "1419",
            "created_at": "2020-10-08 10:25:48",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "657",
            "client_id": "1512",
            "service_type": "hotel",
            "service_name": "3 nights 4 days @ India or 2 nights voucher",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601745",
            "ftk": "1421",
            "created_at": "2020-10-08 11:46:59",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "662",
            "client_id": "1513",
            "service_type": "hotel",
            "service_name": "3 nights 4 days @ India",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601860",
            "ftk": "1422",
            "created_at": "2020-10-08 12:00:55",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "663",
            "client_id": "1513",
            "service_type": "flight",
            "service_name": "5000 worth air voucher against AMC",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601860",
            "ftk": "1422",
            "created_at": "2020-10-08 12:00:55",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "668",
            "client_id": "1514",
            "service_type": "flight",
            "service_name": "5000 air voucher every year against AMC",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601743",
            "ftk": "1423",
            "created_at": "2020-10-08 13:27:35",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "669",
            "client_id": "1514",
            "service_type": "hotel",
            "service_name": "3 days complimentry",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601743",
            "ftk": "1423",
            "created_at": "2020-10-08 13:27:35",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "676",
            "client_id": "1516",
            "service_type": "hotel",
            "service_name": "3 nights 4 days @ India",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601852",
            "ftk": "1425",
            "created_at": "2020-10-13 14:55:18",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "677",
            "client_id": "1515",
            "service_type": "hotel",
            "service_name": "3 night 4 days",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601744",
            "ftk": "1424",
            "created_at": "2020-10-13 14:58:33",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "678",
            "client_id": "1515",
            "service_type": "flight",
            "service_name": "travel voucher worth 15000",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601744",
            "ftk": "1424",
            "created_at": "2020-10-13 14:58:33",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "679",
            "client_id": "1515",
            "service_type": "flight",
            "service_name": "5000 air voucher against AMC",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601744",
            "ftk": "1424",
            "created_at": "2020-10-13 14:58:33",
            "updated_at": "2020-10-20 07:58:49"
        },
        {
            "id": "684",
            "client_id": "1517",
            "service_type": "hotel",
            "service_name": "3 nights 4 days @ India",
            "service_price": "0",
            "service_eligibility": "",
            "maf": "601697",
            "ftk": "1426",
            "created_at": "2020-10-13 16:23:22",
            "updated_at": "2020-10-20 07:58:49"
        }
    ]
}')->{'First Sheet'};
    $status = json_decode('[
    {
        "maf": "100176",
        "ftk": "1",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "100187",
        "ftk": "2",
        "remarks": "Cust. Ask to Cancel",
        "status": "Cancelled"
    },
    {
        "maf": "100502",
        "ftk": "3",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "100524",
        "ftk": "4",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "100179",
        "ftk": "5",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "100508",
        "ftk": "6",
        "remarks": "123",
        "status": "Cancelled"
    },
    {
        "maf": "100526",
        "ftk": "7",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "100521",
        "ftk": "8",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "100510",
        "ftk": "10",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "509198",
        "ftk": "11",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "509183",
        "ftk": "12",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509186",
        "ftk": "13",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "509158",
        "ftk": "14",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "0",
        "ftk": "15",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "100503",
        "ftk": "16",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509166",
        "ftk": "17",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509167",
        "ftk": "18",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509145",
        "ftk": "19",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509149",
        "ftk": "20",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509156",
        "ftk": "21",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509169",
        "ftk": "22",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509195",
        "ftk": "23",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509185",
        "ftk": "24",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509165",
        "ftk": "25",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509164",
        "ftk": "26",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "509142",
        "ftk": "27",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509190",
        "ftk": "28",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509196",
        "ftk": "29",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509192",
        "ftk": "30",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509182",
        "ftk": "31",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509187",
        "ftk": "32",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "509148",
        "ftk": "33",
        "remarks": "Refund Request",
        "status": "Cancelled"
    },
    {
        "maf": "509151",
        "ftk": "34",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509133",
        "ftk": "35",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509134",
        "ftk": "36",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509143",
        "ftk": "37",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509147",
        "ftk": "38",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509144",
        "ftk": "39",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "509150",
        "ftk": "40",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "509199",
        "ftk": "41",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "509171",
        "ftk": "42",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "509130",
        "ftk": "43",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "509138",
        "ftk": "44",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509184",
        "ftk": "45",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509191",
        "ftk": "46",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509128",
        "ftk": "47",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509141",
        "ftk": "48",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509127",
        "ftk": "49",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509125",
        "ftk": "50",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509140",
        "ftk": "51",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "509139",
        "ftk": "52",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509124",
        "ftk": "53",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509122",
        "ftk": "54",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "509123",
        "ftk": "55",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509177",
        "ftk": "56",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "509176",
        "ftk": "57",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509152",
        "ftk": "58",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "509178",
        "ftk": "59",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509146",
        "ftk": "60",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509129",
        "ftk": "61",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "509174",
        "ftk": "62",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509181",
        "ftk": "63",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509126",
        "ftk": "64",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "509189",
        "ftk": "65",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509188",
        "ftk": "66",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "509173",
        "ftk": "67",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "509220",
        "ftk": "68",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "509225",
        "ftk": "69",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509180",
        "ftk": "70",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509207",
        "ftk": "71",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509217",
        "ftk": "72",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509175",
        "ftk": "73",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509210",
        "ftk": "74",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509221",
        "ftk": "75",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509224",
        "ftk": "76",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "509223",
        "ftk": "77",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "509209",
        "ftk": "78",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "509211",
        "ftk": "79",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509219",
        "ftk": "80",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509222",
        "ftk": "81",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "509206",
        "ftk": "82",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509229",
        "ftk": "83",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509228",
        "ftk": "84",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "509230",
        "ftk": "85",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509218",
        "ftk": "86",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509203",
        "ftk": "87",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "509168",
        "ftk": "88",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "509243",
        "ftk": "89",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "509213",
        "ftk": "90",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "509214",
        "ftk": "91",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "509248",
        "ftk": "92",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509247",
        "ftk": "93",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509242",
        "ftk": "94",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509252",
        "ftk": "95",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "509253",
        "ftk": "96",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "50925",
        "ftk": "97",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509249",
        "ftk": "98",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509251",
        "ftk": "99",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509262",
        "ftk": "100",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509202",
        "ftk": "101",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509263",
        "ftk": "102",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "509261",
        "ftk": "103",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "509172",
        "ftk": "104",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "509257",
        "ftk": "105",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509235",
        "ftk": "106",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509256",
        "ftk": "107",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "509255",
        "ftk": "108",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "509245",
        "ftk": "109",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "509254",
        "ftk": "110",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "509244",
        "ftk": "111",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509201",
        "ftk": "112",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509239",
        "ftk": "113",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "509241",
        "ftk": "114",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509238",
        "ftk": "115",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "509246",
        "ftk": "116",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509298",
        "ftk": "117",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509240",
        "ftk": "118",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509233",
        "ftk": "119",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "509234",
        "ftk": "120",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "509275",
        "ftk": "121",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "509204",
        "ftk": "122",
        "remarks": "Asking for Refund",
        "status": "Cancelled"
    },
    {
        "maf": "509227",
        "ftk": "123",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "509267",
        "ftk": "124",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "509231",
        "ftk": "125",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509296",
        "ftk": "126",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509205",
        "ftk": "127",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509408",
        "ftk": "128",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509272",
        "ftk": "129",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509271",
        "ftk": "130",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509232",
        "ftk": "131",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509406",
        "ftk": "132",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509407",
        "ftk": "133",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "509404",
        "ftk": "134",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509405",
        "ftk": "135",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509402",
        "ftk": "136",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509297",
        "ftk": "137",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "509278",
        "ftk": "138",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509495",
        "ftk": "139",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "509496",
        "ftk": "140",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "509499",
        "ftk": "141",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509411",
        "ftk": "142",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509611",
        "ftk": "143",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509497",
        "ftk": "144",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "509609",
        "ftk": "145",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509612",
        "ftk": "146",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "509624",
        "ftk": "147",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509623",
        "ftk": "148",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509606",
        "ftk": "149",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "509498",
        "ftk": "150",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "509410",
        "ftk": "151",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509602",
        "ftk": "152",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509607",
        "ftk": "153",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "509614",
        "ftk": "154",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "509613",
        "ftk": "155",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "509401",
        "ftk": "156",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "509608",
        "ftk": "157",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "509610",
        "ftk": "158",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "509615",
        "ftk": "159",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509459",
        "ftk": "160",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "509416",
        "ftk": "161",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509450",
        "ftk": "162",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "0",
        "ftk": "163",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "509436",
        "ftk": "164",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "509445",
        "ftk": "165",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "509446",
        "ftk": "166",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "509448",
        "ftk": "167",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509449",
        "ftk": "168",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509603",
        "ftk": "169",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "509299",
        "ftk": "170",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509466",
        "ftk": "171",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "509469",
        "ftk": "172",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509470",
        "ftk": "173",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509472",
        "ftk": "174",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "509604",
        "ftk": "175",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "509451",
        "ftk": "176",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509605",
        "ftk": "177",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509453",
        "ftk": "178",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509454",
        "ftk": "179",
        "remarks": "Member has forwared a mail to cancel his membership & adjust his amount in any of the services. Services has been provided & sale has been closed and cancelled...",
        "status": "Breather"
    },
    {
        "maf": "509467",
        "ftk": "180",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "509493",
        "ftk": "181",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "509434",
        "ftk": "182",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "509458",
        "ftk": "183",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509455",
        "ftk": "184",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509456",
        "ftk": "185",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509420",
        "ftk": "186",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509418",
        "ftk": "187",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "509435",
        "ftk": "188",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509419",
        "ftk": "189",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509444",
        "ftk": "190",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "509452",
        "ftk": "191",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509421",
        "ftk": "192",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509135",
        "ftk": "193",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509426",
        "ftk": "194",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "509266",
        "ftk": "195",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509427",
        "ftk": "196",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "509265",
        "ftk": "197",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509412",
        "ftk": "198",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "509492",
        "ftk": "199",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "509432",
        "ftk": "200",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "509428",
        "ftk": "201",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "509430",
        "ftk": "202",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "509464",
        "ftk": "203",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "509433",
        "ftk": "204",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "509573",
        "ftk": "205",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "509431",
        "ftk": "206",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509574",
        "ftk": "207",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "509273",
        "ftk": "208",
        "remarks": "Refund Request",
        "status": "Cancelled"
    },
    {
        "maf": "509425",
        "ftk": "209",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "509424",
        "ftk": "210",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "509429",
        "ftk": "211",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509581",
        "ftk": "212",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509578",
        "ftk": "213",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "509580",
        "ftk": "214",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509576",
        "ftk": "215",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509579",
        "ftk": "216",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "509544",
        "ftk": "217",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "509552",
        "ftk": "218",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509549",
        "ftk": "219",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "509584",
        "ftk": "220",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "509583",
        "ftk": "221",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "509550",
        "ftk": "222",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "509600",
        "ftk": "223",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "509588",
        "ftk": "224",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "509586",
        "ftk": "225",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "509569",
        "ftk": "226",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "509585",
        "ftk": "227",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "509582",
        "ftk": "228",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "509423",
        "ftk": "229",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509524",
        "ftk": "230",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "509587",
        "ftk": "231",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "509545",
        "ftk": "232",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509546",
        "ftk": "233",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "509547",
        "ftk": "234",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "509551",
        "ftk": "235",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "509553",
        "ftk": "236",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "509536",
        "ftk": "237",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509598",
        "ftk": "238",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509706",
        "ftk": "239",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "509707",
        "ftk": "240",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509537",
        "ftk": "241",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509543",
        "ftk": "242",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509599",
        "ftk": "243",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509591",
        "ftk": "244",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "509541",
        "ftk": "245",
        "remarks": "Asking for Refund",
        "status": "Cancelled"
    },
    {
        "maf": "509709",
        "ftk": "246",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509457",
        "ftk": "247",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "509590",
        "ftk": "248",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509710",
        "ftk": "249",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "509708",
        "ftk": "250",
        "remarks": "demo remark",
        "status": "Active"
    },
    {
        "maf": "509704",
        "ftk": "251",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509575",
        "ftk": "252",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "509718",
        "ftk": "253",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "509705",
        "ftk": "254",
        "remarks": "Refund done",
        "status": "Cancelled"
    },
    {
        "maf": "509567",
        "ftk": "255",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "509534",
        "ftk": "256",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509530",
        "ftk": "257",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509702",
        "ftk": "258",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "509529",
        "ftk": "259",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "509717",
        "ftk": "260",
        "remarks": "qweqweads sdfsdf",
        "status": "Active"
    },
    {
        "maf": "509716",
        "ftk": "261",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509527",
        "ftk": "262",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "509528",
        "ftk": "263",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "509526",
        "ftk": "264",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509713",
        "ftk": "265",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509535",
        "ftk": "266",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "509720",
        "ftk": "267",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "509441",
        "ftk": "268",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "509721",
        "ftk": "269",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "509724",
        "ftk": "270",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509542",
        "ftk": "271",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "509570",
        "ftk": "272",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "509568",
        "ftk": "273",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509571",
        "ftk": "274",
        "remarks": "Asking For refund",
        "status": "Breather"
    },
    {
        "maf": "509722",
        "ftk": "275",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509525",
        "ftk": "276",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "509572",
        "ftk": "277",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509733",
        "ftk": "278",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509533",
        "ftk": "279",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "509715",
        "ftk": "280",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "509732",
        "ftk": "281",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "509725",
        "ftk": "282",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "509763",
        "ftk": "283",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509765",
        "ftk": "284",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "509766",
        "ftk": "285",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "509750",
        "ftk": "286",
        "remarks": "Member does not want to continue..... Sale cancel & closed...",
        "status": "Cancelled"
    },
    {
        "maf": "509748",
        "ftk": "287",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "509747",
        "ftk": "288",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "509756",
        "ftk": "289",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "509757",
        "ftk": "290",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "509786",
        "ftk": "291",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509792",
        "ftk": "292",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "509755",
        "ftk": "293",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "509788",
        "ftk": "294",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "509859",
        "ftk": "295",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "509793",
        "ftk": "296",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "509787",
        "ftk": "297",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509790",
        "ftk": "298",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "509791",
        "ftk": "299",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509743",
        "ftk": "300",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509758",
        "ftk": "301",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "509742",
        "ftk": "302",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509752",
        "ftk": "303",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509740",
        "ftk": "304",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509741",
        "ftk": "305",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "509785",
        "ftk": "306",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "509784",
        "ftk": "307",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "509745",
        "ftk": "308",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "509746",
        "ftk": "309",
        "remarks": "Asking for Refund",
        "status": "Cancelled"
    },
    {
        "maf": "509754",
        "ftk": "310",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "509751",
        "ftk": "311",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509744",
        "ftk": "312",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "509892",
        "ftk": "313",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "509893",
        "ftk": "314",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509894",
        "ftk": "315",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "0",
        "ftk": "316",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509671",
        "ftk": "317",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509873",
        "ftk": "318",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509665",
        "ftk": "319",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "509753",
        "ftk": "320",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "509783",
        "ftk": "321",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "509782",
        "ftk": "322",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "509781",
        "ftk": "323",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "509779",
        "ftk": "324",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "509780",
        "ftk": "325",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509759",
        "ftk": "326",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "509761",
        "ftk": "327",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509762",
        "ftk": "328",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "509634",
        "ftk": "329",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "509638",
        "ftk": "330",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "509501",
        "ftk": "331",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "509659",
        "ftk": "332",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509520",
        "ftk": "333",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509658",
        "ftk": "334",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "509639",
        "ftk": "335",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "509640",
        "ftk": "336",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "509566",
        "ftk": "337",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "509554",
        "ftk": "338",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "509557",
        "ftk": "339",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "509565",
        "ftk": "340",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "509560",
        "ftk": "341",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "509507",
        "ftk": "342",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "509563",
        "ftk": "343",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "509506",
        "ftk": "344",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "509635",
        "ftk": "345",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "509508",
        "ftk": "346",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "509663",
        "ftk": "347",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "509896",
        "ftk": "348",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509897",
        "ftk": "349",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "509776",
        "ftk": "350",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509513",
        "ftk": "351",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "509514",
        "ftk": "352",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509899",
        "ftk": "353",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "509486",
        "ftk": "354",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "509511",
        "ftk": "355",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "509775",
        "ftk": "356",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "509677",
        "ftk": "357",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509478",
        "ftk": "358",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "509773",
        "ftk": "359",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509595",
        "ftk": "360",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "509476",
        "ftk": "361",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "509649",
        "ftk": "362",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "509648",
        "ftk": "363",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "509650",
        "ftk": "364",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "509559",
        "ftk": "365",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "509651",
        "ftk": "366",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "509561",
        "ftk": "367",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "509518",
        "ftk": "368",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "509597",
        "ftk": "369",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "509596",
        "ftk": "370",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509594",
        "ftk": "371",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "509512",
        "ftk": "372",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509777",
        "ftk": "373",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "509731",
        "ftk": "374",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509662",
        "ftk": "375",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "509876",
        "ftk": "376",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509878",
        "ftk": "377",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509877",
        "ftk": "378",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "509646",
        "ftk": "379",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "509874",
        "ftk": "380",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "509516",
        "ftk": "381",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509880",
        "ftk": "382",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "509689",
        "ftk": "383",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "509885",
        "ftk": "384",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "509692",
        "ftk": "385",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509690",
        "ftk": "386",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509691",
        "ftk": "387",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509882",
        "ftk": "388",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "509685",
        "ftk": "389",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "509655",
        "ftk": "390",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509503",
        "ftk": "391",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "509864",
        "ftk": "392",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509664",
        "ftk": "393",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "509474",
        "ftk": "394",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "509593",
        "ftk": "395",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "509699",
        "ftk": "396",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "509875",
        "ftk": "397",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509883",
        "ftk": "398",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509856",
        "ftk": "399",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509866",
        "ftk": "400",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "509858",
        "ftk": "401",
        "remarks": "Refund Done",
        "status": "Cancelled"
    },
    {
        "maf": "509870",
        "ftk": "402",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509564",
        "ftk": "403",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "509871",
        "ftk": "404",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509515",
        "ftk": "405",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "509898",
        "ftk": "406",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "509521",
        "ftk": "407",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "509888",
        "ftk": "408",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "509889",
        "ftk": "409",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "509891",
        "ftk": "410",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "509890",
        "ftk": "411",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "509887",
        "ftk": "412",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509886",
        "ftk": "413",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "509865",
        "ftk": "414",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509867",
        "ftk": "415",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "509868",
        "ftk": "416",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509869",
        "ftk": "417",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509657",
        "ftk": "418",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "509881",
        "ftk": "419",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "509490",
        "ftk": "420",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "509484",
        "ftk": "421",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509770",
        "ftk": "422",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509772",
        "ftk": "423",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "509482",
        "ftk": "424",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "509860",
        "ftk": "425",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "509862",
        "ftk": "426",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "509693",
        "ftk": "427",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "510068",
        "ftk": "428",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509487",
        "ftk": "429",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "509440",
        "ftk": "430",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509439",
        "ftk": "431",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "509654",
        "ftk": "432",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509723",
        "ftk": "433",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509504",
        "ftk": "434",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509462",
        "ftk": "435",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509797",
        "ftk": "436",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "510061",
        "ftk": "437",
        "remarks": "Refund Request",
        "status": "Active"
    },
    {
        "maf": "510067",
        "ftk": "438",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "510066",
        "ftk": "439",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "510065",
        "ftk": "440",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "510049",
        "ftk": "441",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509505",
        "ftk": "442",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "510088",
        "ftk": "443",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "510069",
        "ftk": "444",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509800",
        "ftk": "445",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "510060",
        "ftk": "446",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "509477",
        "ftk": "447",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "510059",
        "ftk": "448",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "510058",
        "ftk": "449",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "510079",
        "ftk": "450",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "509656",
        "ftk": "451",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "510073",
        "ftk": "452",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "509760",
        "ftk": "453",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "509884",
        "ftk": "454",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "510054",
        "ftk": "455",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "510070",
        "ftk": "456",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "510010",
        "ftk": "457",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509483",
        "ftk": "458",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509479",
        "ftk": "459",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "511008",
        "ftk": "460",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "510009",
        "ftk": "461",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "510036",
        "ftk": "462",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "510082",
        "ftk": "463",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "509774",
        "ftk": "464",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "0",
        "ftk": "465",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "510087",
        "ftk": "466",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "510075",
        "ftk": "467",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "510083",
        "ftk": "468",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "510084",
        "ftk": "469",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "510086",
        "ftk": "470",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "510014",
        "ftk": "471",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "510013",
        "ftk": "472",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "510023",
        "ftk": "473",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "510017",
        "ftk": "474",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "509558",
        "ftk": "475",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509700",
        "ftk": "476",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "510011",
        "ftk": "477",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "0",
        "ftk": "478",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "0",
        "ftk": "479",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "510062",
        "ftk": "480",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "510064",
        "ftk": "481",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "510022",
        "ftk": "482",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "510063",
        "ftk": "483",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "510078",
        "ftk": "484",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "510044",
        "ftk": "485",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "510033",
        "ftk": "486",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "510047",
        "ftk": "487",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "510043",
        "ftk": "488",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509694",
        "ftk": "489",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "510042",
        "ftk": "490",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "510020",
        "ftk": "491",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "510005",
        "ftk": "492",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "510012",
        "ftk": "493",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "510041",
        "ftk": "494",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "5100040",
        "ftk": "495",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "510057",
        "ftk": "496",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "510050",
        "ftk": "497",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "510038",
        "ftk": "498",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "510048",
        "ftk": "499",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "510032",
        "ftk": "500",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "510031",
        "ftk": "501",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "0",
        "ftk": "502",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "509914",
        "ftk": "503",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "510027",
        "ftk": "504",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "510030",
        "ftk": "505",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "510034",
        "ftk": "506",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "510028",
        "ftk": "507",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509903",
        "ftk": "508",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "510081",
        "ftk": "509",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "510085",
        "ftk": "510",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509626",
        "ftk": "511",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509918",
        "ftk": "512",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509910",
        "ftk": "513",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "0",
        "ftk": "514",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "0",
        "ftk": "515",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509901",
        "ftk": "516",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "509940",
        "ftk": "517",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "509924",
        "ftk": "518",
        "remarks": "Asking Refund,No plan to avail any holiday Infuture ,cancel mails",
        "status": "Cancelled"
    },
    {
        "maf": "509641",
        "ftk": "519",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "509647",
        "ftk": "520",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552201",
        "ftk": "521",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552202",
        "ftk": "522",
        "remarks": "Amount Forfetied",
        "status": "Cancelled"
    },
    {
        "maf": "510007",
        "ftk": "523",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552258",
        "ftk": "524",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552269",
        "ftk": "525",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "510072",
        "ftk": "526",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509562",
        "ftk": "527",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "509795",
        "ftk": "528",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "510055",
        "ftk": "529",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552237",
        "ftk": "530",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552236",
        "ftk": "531",
        "remarks": "Refund Request",
        "status": "Cancelled"
    },
    {
        "maf": "509643",
        "ftk": "532",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552240",
        "ftk": "533",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "552251",
        "ftk": "534",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "552254",
        "ftk": "535",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552232",
        "ftk": "536",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "509480",
        "ftk": "537",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509437",
        "ftk": "538",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "509799",
        "ftk": "539",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552256",
        "ftk": "540",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552216",
        "ftk": "541",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552255",
        "ftk": "542",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "552212",
        "ftk": "543",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "552218",
        "ftk": "544",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "552208",
        "ftk": "545",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552219",
        "ftk": "546",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "552223",
        "ftk": "547",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552222",
        "ftk": "548",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552711",
        "ftk": "549",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "509926",
        "ftk": "550",
        "remarks": "2 PDC Present without client permission, Refund done",
        "status": "Cancelled"
    },
    {
        "maf": "552210",
        "ftk": "551",
        "remarks": "Over wrong commitment ,Person visit office and gave  written letter for refund,,,Refund Done",
        "status": "Cancelled"
    },
    {
        "maf": "552249",
        "ftk": "552",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "552263",
        "ftk": "553",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552213",
        "ftk": "554",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "552755",
        "ftk": "555",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "552752",
        "ftk": "556",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552749",
        "ftk": "557",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552746",
        "ftk": "558",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "510018",
        "ftk": "559",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552767",
        "ftk": "560",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552788",
        "ftk": "561",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "552789",
        "ftk": "562",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552740",
        "ftk": "563",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552786",
        "ftk": "564",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "552791",
        "ftk": "565",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "552703",
        "ftk": "566",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "552291",
        "ftk": "567",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552276",
        "ftk": "568",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "552278",
        "ftk": "569",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552712",
        "ftk": "570",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552279",
        "ftk": "571",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "552275",
        "ftk": "572",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "552277",
        "ftk": "573",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "552294",
        "ftk": "574",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "552292",
        "ftk": "575",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "552270",
        "ftk": "576",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552293",
        "ftk": "577",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "552340",
        "ftk": "578",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552282",
        "ftk": "579",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552273",
        "ftk": "580",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552297",
        "ftk": "581",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552280",
        "ftk": "582",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "552764",
        "ftk": "583",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "552290",
        "ftk": "584",
        "remarks": "Refund Request",
        "status": "Cancelled"
    },
    {
        "maf": "552299",
        "ftk": "585",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "552284",
        "ftk": "586",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "552742",
        "ftk": "587",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "552245",
        "ftk": "588",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552756",
        "ftk": "589",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552287",
        "ftk": "590",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552286",
        "ftk": "591",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552763",
        "ftk": "592",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "552748",
        "ftk": "593",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "552207",
        "ftk": "594",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "553101",
        "ftk": "595",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "552613",
        "ftk": "596",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "553109",
        "ftk": "597",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552259",
        "ftk": "598",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "552610",
        "ftk": "599",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552629",
        "ftk": "600",
        "remarks": "Refund Request",
        "status": "Cancelled"
    },
    {
        "maf": "552609",
        "ftk": "601",
        "remarks": "Refund Request",
        "status": "Cancelled"
    },
    {
        "maf": "553111",
        "ftk": "602",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552800",
        "ftk": "603",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "552622",
        "ftk": "604",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "553108",
        "ftk": "605",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552705",
        "ftk": "606",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "509644",
        "ftk": "607",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552243",
        "ftk": "608",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552785",
        "ftk": "609",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552769",
        "ftk": "610",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552607",
        "ftk": "611",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "553110",
        "ftk": "612",
        "remarks": "2kids include in fully paid",
        "status": "Cancelled"
    },
    {
        "maf": "552721",
        "ftk": "613",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552244",
        "ftk": "614",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552601",
        "ftk": "615",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "553122",
        "ftk": "616",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "553123",
        "ftk": "617",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552603",
        "ftk": "618",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552602",
        "ftk": "619",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "553124",
        "ftk": "620",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552621",
        "ftk": "621",
        "remarks": "Refund Request",
        "status": "Active"
    },
    {
        "maf": "552617",
        "ftk": "622",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552227",
        "ftk": "623",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552642",
        "ftk": "624",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552228",
        "ftk": "625",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552248",
        "ftk": "626",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552260",
        "ftk": "627",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552747",
        "ftk": "628",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552625",
        "ftk": "629",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552615",
        "ftk": "630",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "552618",
        "ftk": "631",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552770",
        "ftk": "633",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552614",
        "ftk": "634",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "553141",
        "ftk": "635",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552633",
        "ftk": "636",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "553138",
        "ftk": "637",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "553142",
        "ftk": "638",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552651",
        "ftk": "639",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552650",
        "ftk": "640",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552624",
        "ftk": "641",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552649",
        "ftk": "642",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552271",
        "ftk": "643",
        "remarks": "Refund Request",
        "status": "Breather"
    },
    {
        "maf": "552640",
        "ftk": "644",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552646",
        "ftk": "645",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552230",
        "ftk": "646",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552657",
        "ftk": "647",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552713",
        "ftk": "648",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552787",
        "ftk": "649",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "510077",
        "ftk": "650",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552771",
        "ftk": "651",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552643",
        "ftk": "652",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "553146",
        "ftk": "653",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "553106",
        "ftk": "654",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552604",
        "ftk": "655",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552627",
        "ftk": "656",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552774",
        "ftk": "657",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552782",
        "ftk": "658",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552224",
        "ftk": "659",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "553165",
        "ftk": "660",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552667",
        "ftk": "661",
        "remarks": "Refund Request",
        "status": "Cancelled"
    },
    {
        "maf": "552798",
        "ftk": "662",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552761",
        "ftk": "663",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "553127",
        "ftk": "664",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552674",
        "ftk": "665",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "553102",
        "ftk": "666",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "553103",
        "ftk": "667",
        "remarks": "Refund request",
        "status": "Cancelled"
    },
    {
        "maf": "552619",
        "ftk": "668",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552616",
        "ftk": "669",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "553104",
        "ftk": "670",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "553139",
        "ftk": "671",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "553168",
        "ftk": "672",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "552653",
        "ftk": "674",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "553172",
        "ftk": "675",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "552673",
        "ftk": "676",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "552605",
        "ftk": "677",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552654",
        "ftk": "678",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "552781",
        "ftk": "679",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "552701",
        "ftk": "680",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "552704",
        "ftk": "681",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552727",
        "ftk": "682",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "552688",
        "ftk": "683",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552697",
        "ftk": "684",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "552699",
        "ftk": "685",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "552670",
        "ftk": "686",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509941",
        "ftk": "687",
        "remarks": "Client did not pay any amount against the membership.",
        "status": "Cancelled"
    },
    {
        "maf": "552700",
        "ftk": "688",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "552736",
        "ftk": "689",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "553173",
        "ftk": "690",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "552639",
        "ftk": "691",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "553171",
        "ftk": "692",
        "remarks": "Refund Request",
        "status": "Cancelled"
    },
    {
        "maf": "553105",
        "ftk": "693",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "552687",
        "ftk": "694",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "553136",
        "ftk": "695",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "552631",
        "ftk": "696",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "552620",
        "ftk": "697",
        "remarks": "Jammu Client. Sale was cancelled & Consultant has made some fake promises. Company has approved for the adjustment of his DP amount of INR 15,000/- (Package or hotel booking)",
        "status": "Cancelled"
    },
    {
        "maf": "552783",
        "ftk": "698",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "552689",
        "ftk": "699",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552628",
        "ftk": "700",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "552400",
        "ftk": "701",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552698",
        "ftk": "702",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "552671",
        "ftk": "703",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "510019",
        "ftk": "704",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "552733",
        "ftk": "705",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "510021",
        "ftk": "706",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "553107",
        "ftk": "707",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "552399",
        "ftk": "708",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552396",
        "ftk": "709",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "553134",
        "ftk": "710",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "510024",
        "ftk": "711",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509939",
        "ftk": "712",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509698",
        "ftk": "713",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509943",
        "ftk": "714",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552710",
        "ftk": "715",
        "remarks": "Refund request",
        "status": "Cancelled"
    },
    {
        "maf": "509944",
        "ftk": "716",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "509942",
        "ftk": "717",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552304",
        "ftk": "718",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "553117",
        "ftk": "719",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "552669",
        "ftk": "720",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "553170",
        "ftk": "721",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "553116",
        "ftk": "722",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "552612",
        "ftk": "723",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "553164",
        "ftk": "724",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "553152",
        "ftk": "725",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "553156",
        "ftk": "726",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "553154",
        "ftk": "727",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "553151",
        "ftk": "728",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "553153",
        "ftk": "729",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "552777",
        "ftk": "730",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "552611",
        "ftk": "731",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "552732",
        "ftk": "732",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "553181",
        "ftk": "733",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "553130",
        "ftk": "734",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "552772",
        "ftk": "735",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "552211",
        "ftk": "736",
        "remarks": "Refund Request",
        "status": "Cancelled"
    },
    {
        "maf": "553148",
        "ftk": "737",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "553149",
        "ftk": "738",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552758",
        "ftk": "739",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "553112",
        "ftk": "740",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "552300",
        "ftk": "741",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "552288",
        "ftk": "742",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509523",
        "ftk": "743",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "552728",
        "ftk": "744",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "553120",
        "ftk": "745",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "553157",
        "ftk": "746",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "553162",
        "ftk": "747",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "552309",
        "ftk": "749",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "552679",
        "ftk": "750",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "552306",
        "ftk": "751",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "552718",
        "ftk": "752",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "552296",
        "ftk": "753",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "553147",
        "ftk": "754",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "553012",
        "ftk": "755",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509668",
        "ftk": "756",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "553019",
        "ftk": "757",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "553158",
        "ftk": "758",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "552677",
        "ftk": "759",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "552686",
        "ftk": "760",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "552784",
        "ftk": "761",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "552720",
        "ftk": "762",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "552726",
        "ftk": "763",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "553176",
        "ftk": "764",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552692",
        "ftk": "765",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552682",
        "ftk": "766",
        "remarks": "Member is not paying EMIs from his date of enrollment. Nach is continuously bouncing. Nach Disacled. Sale cancel.",
        "status": "Cancelled"
    },
    {
        "maf": "552731",
        "ftk": "767",
        "remarks": "Member has made no payment against his membership. Sale Cancel & closed.",
        "status": "Cancelled"
    },
    {
        "maf": "553025",
        "ftk": "768",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "552693",
        "ftk": "769",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "553007",
        "ftk": "770",
        "remarks": "Refund Request",
        "status": "Cancelled"
    },
    {
        "maf": "552656",
        "ftk": "771",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "553006",
        "ftk": "772",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "553160",
        "ftk": "773",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "553131",
        "ftk": "774",
        "remarks": "Member does not want to continue. Cancellation mail dropped. Non Refundable. Sale Cancel. Kitty Closed.",
        "status": "Cancelled"
    },
    {
        "maf": "552759",
        "ftk": "775",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552680",
        "ftk": "776",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "553008",
        "ftk": "777",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "552626",
        "ftk": "778",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "553119",
        "ftk": "779",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "553029",
        "ftk": "780",
        "remarks": "Refund Request",
        "status": "Cancelled"
    },
    {
        "maf": "552737",
        "ftk": "781",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "553014",
        "ftk": "782",
        "remarks": "Member does not want to continue. He has stopped his payment by cheque. Membership Cancelled",
        "status": "Cancelled"
    },
    {
        "maf": "553001",
        "ftk": "783",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "553150",
        "ftk": "784",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "553031",
        "ftk": "785",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "553030",
        "ftk": "786",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "552636",
        "ftk": "787",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "553163",
        "ftk": "788",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "553034",
        "ftk": "789",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "552226",
        "ftk": "790",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "552741",
        "ftk": "791",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "553125",
        "ftk": "792",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "552665",
        "ftk": "793",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "552662",
        "ftk": "794",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "552663",
        "ftk": "795",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552757",
        "ftk": "796",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "552664",
        "ftk": "797",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "552773",
        "ftk": "798",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "553010",
        "ftk": "799",
        "remarks": "Sale Cancel.... NO payment received from member... Neither DP nor EMIs... Nach disbaled... Sale closed....",
        "status": "Cancelled"
    },
    {
        "maf": "552775",
        "ftk": "800",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "552250",
        "ftk": "801",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "553058",
        "ftk": "802",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "553056",
        "ftk": "803",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "553057",
        "ftk": "804",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "553066",
        "ftk": "805",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "553081",
        "ftk": "806",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "553067",
        "ftk": "807",
        "remarks": "Refund Request",
        "status": "Cancelled"
    },
    {
        "maf": "553068",
        "ftk": "808",
        "remarks": "Refund Request",
        "status": "Active"
    },
    {
        "maf": "552760",
        "ftk": "809",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "553050",
        "ftk": "810",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "553049",
        "ftk": "811",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "553074",
        "ftk": "812",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "553073",
        "ftk": "813",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "553082",
        "ftk": "814",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "553062",
        "ftk": "816",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "553063",
        "ftk": "817",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "553075",
        "ftk": "818",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "553036",
        "ftk": "819",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "553028",
        "ftk": "820",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "509468",
        "ftk": "821",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552678",
        "ftk": "822",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "553133",
        "ftk": "823",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "553129",
        "ftk": "824",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "552311",
        "ftk": "825",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "552312",
        "ftk": "826",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552632",
        "ftk": "827",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "552220",
        "ftk": "828",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "553177",
        "ftk": "829",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "553061",
        "ftk": "830",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "553060",
        "ftk": "831",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "553059",
        "ftk": "832",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552322",
        "ftk": "833",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "552235",
        "ftk": "834",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "552676",
        "ftk": "835",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "552308",
        "ftk": "836",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "552641",
        "ftk": "837",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "552321",
        "ftk": "838",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "552695",
        "ftk": "839",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "553052",
        "ftk": "840",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "552264",
        "ftk": "841",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552246",
        "ftk": "842",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "552666",
        "ftk": "843",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552690",
        "ftk": "844",
        "remarks": "Member does not want to continue. he said it at the time of welcome call only.",
        "status": "Active"
    },
    {
        "maf": "553054",
        "ftk": "845",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "552691",
        "ftk": "846",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "552328",
        "ftk": "847",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552351",
        "ftk": "848",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552272",
        "ftk": "849",
        "remarks": "No payment received from member....Sale cancel....",
        "status": "Cancelled"
    },
    {
        "maf": "553020",
        "ftk": "850",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552363",
        "ftk": "851",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "552361",
        "ftk": "852",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552359",
        "ftk": "853",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "552360",
        "ftk": "854",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552358",
        "ftk": "855",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "552362",
        "ftk": "856",
        "remarks": "Cancel & refund request received by member.... Refund approved...",
        "status": "Cancelled"
    },
    {
        "maf": "552364",
        "ftk": "857",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552350",
        "ftk": "858",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "552356",
        "ftk": "859",
        "remarks": "Refund Request",
        "status": "Cancelled"
    },
    {
        "maf": "552345",
        "ftk": "860",
        "remarks": "Member has forwarded a cancellation mail as he has been misguided by sales people.... Sale Cancel.. Refund done of INR 29909/- ....from Karan sir\'s account on 19/11/19",
        "status": "Cancelled"
    },
    {
        "maf": "552357",
        "ftk": "861",
        "remarks": "Refund Request",
        "status": "Cancelled"
    },
    {
        "maf": "552343",
        "ftk": "862",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "552398",
        "ftk": "863",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "552348",
        "ftk": "864",
        "remarks": "Sale Cancelled as member do not want to continue.... Member retained again.... Given new maf no. 1049",
        "status": "Cancelled"
    },
    {
        "maf": "552370",
        "ftk": "865",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552371",
        "ftk": "866",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552716",
        "ftk": "867",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "553015",
        "ftk": "868",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "553041",
        "ftk": "869",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "553039",
        "ftk": "870",
        "remarks": "Sale Was cancelled at the time of enrollment only.....",
        "status": "Cancelled"
    },
    {
        "maf": "553040",
        "ftk": "871",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "553038",
        "ftk": "872",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "553024",
        "ftk": "873",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "553169",
        "ftk": "874",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "553132",
        "ftk": "875",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "552394",
        "ftk": "876",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552393",
        "ftk": "877",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "553042",
        "ftk": "878",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "552369",
        "ftk": "879",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "553091",
        "ftk": "880",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552318",
        "ftk": "881",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "553092",
        "ftk": "882",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "552372",
        "ftk": "883",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "552379",
        "ftk": "884",
        "remarks": "Member forwarded a mail to stop his ECS and cancel his membership....Nach disabled....Membership cancelled....",
        "status": "Cancelled"
    },
    {
        "maf": "553085",
        "ftk": "885",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "552315",
        "ftk": "886",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "553098",
        "ftk": "887",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "553095",
        "ftk": "888",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "552317",
        "ftk": "889",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "552380",
        "ftk": "890",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "553099",
        "ftk": "891",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552316",
        "ftk": "892",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "552320",
        "ftk": "893",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "552374",
        "ftk": "894",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552377",
        "ftk": "895",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "553088",
        "ftk": "896",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552730",
        "ftk": "897",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "553096",
        "ftk": "898",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552373",
        "ftk": "899",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "552516",
        "ftk": "900",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "553055",
        "ftk": "901",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "553097",
        "ftk": "902",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "552349",
        "ftk": "903",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "552523",
        "ftk": "904",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "552522",
        "ftk": "905",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552524",
        "ftk": "906",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "552376",
        "ftk": "907",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "552341",
        "ftk": "908",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "0",
        "ftk": "909",
        "remarks": "Sale was cancelled at the time of membership only....Sale is cancel....",
        "status": "Cancelled"
    },
    {
        "maf": "552392",
        "ftk": "910",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552530",
        "ftk": "911",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552531",
        "ftk": "912",
        "remarks": "Refund Request",
        "status": "Cancelled"
    },
    {
        "maf": "552353",
        "ftk": "913",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "552557",
        "ftk": "914",
        "remarks": "Refund Request",
        "status": "Cancelled"
    },
    {
        "maf": "552517",
        "ftk": "915",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "552518",
        "ftk": "916",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552520",
        "ftk": "917",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552382",
        "ftk": "918",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552532",
        "ftk": "920",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "552330",
        "ftk": "921",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552390",
        "ftk": "922",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552391",
        "ftk": "923",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552505",
        "ftk": "924",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "552543",
        "ftk": "925",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552544",
        "ftk": "926",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "552385",
        "ftk": "927",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "552507",
        "ftk": "928",
        "remarks": "Refund Request",
        "status": "Cancelled"
    },
    {
        "maf": "552386",
        "ftk": "929",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "552508",
        "ftk": "930",
        "remarks": "Member has not paid any amount...Sale cancel & closed",
        "status": "Cancelled"
    },
    {
        "maf": "552576",
        "ftk": "931",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552573",
        "ftk": "932",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552569",
        "ftk": "933",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "552574",
        "ftk": "934",
        "remarks": "Refund Request",
        "status": "Cancelled"
    },
    {
        "maf": "552570",
        "ftk": "935",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552546",
        "ftk": "936",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552585",
        "ftk": "937",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "552583",
        "ftk": "938",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "552365",
        "ftk": "939",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "552512",
        "ftk": "940",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "552514",
        "ftk": "941",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "552515",
        "ftk": "942",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "552334",
        "ftk": "943",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552346",
        "ftk": "944",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552528",
        "ftk": "945",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "552337",
        "ftk": "946",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "552921",
        "ftk": "947",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "552931",
        "ftk": "948",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552932",
        "ftk": "949",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "552572",
        "ftk": "950",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "552335",
        "ftk": "951",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "552735",
        "ftk": "952",
        "remarks": "Refund Request",
        "status": "Cancelled"
    },
    {
        "maf": "552930",
        "ftk": "953",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552918",
        "ftk": "954",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "552929",
        "ftk": "955",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "552940",
        "ftk": "956",
        "remarks": "Refund Request",
        "status": "Cancelled"
    },
    {
        "maf": "552939",
        "ftk": "957",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "552545",
        "ftk": "958",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "552946",
        "ftk": "959",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552506",
        "ftk": "960",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "552510",
        "ftk": "961",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552504",
        "ftk": "962",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "552942",
        "ftk": "963",
        "remarks": "Refund Request",
        "status": "Active"
    },
    {
        "maf": "552945",
        "ftk": "964",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552943",
        "ftk": "965",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "552550",
        "ftk": "967",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "552519",
        "ftk": "968",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "552285",
        "ftk": "969",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "552354",
        "ftk": "970",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "552584",
        "ftk": "972",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "552581",
        "ftk": "974",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "552582",
        "ftk": "975",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "552580",
        "ftk": "976",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "552948",
        "ftk": "977",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "552927",
        "ftk": "978",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "552950",
        "ftk": "979",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "552917",
        "ftk": "980",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "552974",
        "ftk": "981",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "552928",
        "ftk": "982",
        "remarks": "Converted into ONE TIME.... Payment received... Holiday Taken... Sale cancelled...",
        "status": "Cancelled"
    },
    {
        "maf": "552915",
        "ftk": "983",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552969",
        "ftk": "984",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552964",
        "ftk": "985",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "552590",
        "ftk": "986",
        "remarks": "Converted into ONE TIME.... Payment received... Holiday taken.... Sale Cancelled....",
        "status": "Cancelled"
    },
    {
        "maf": "553121",
        "ftk": "987",
        "remarks": "Converted into ONE TIME.... Payment received... Holiday Taken... Sale cancelled & closed....",
        "status": "Cancelled"
    },
    {
        "maf": "552589",
        "ftk": "988",
        "remarks": "Converted into ONE TIME... Payment received ... Holiday Taken... Sale Cancelled & closed...",
        "status": "Cancelled"
    },
    {
        "maf": "552352",
        "ftk": "989",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "552538",
        "ftk": "990",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "552963",
        "ftk": "992",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552909",
        "ftk": "993",
        "remarks": "Sale Cancelled as member does not want to continue ...NACH disabled & cheque stopped...",
        "status": "Cancelled"
    },
    {
        "maf": "552905",
        "ftk": "995",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552368",
        "ftk": "996",
        "remarks": "Member does not want to continue..... Sale cancelled & closed",
        "status": "Cancelled"
    },
    {
        "maf": "552313",
        "ftk": "997",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "552951",
        "ftk": "998",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "552967",
        "ftk": "999",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "552978",
        "ftk": "1000",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "552597",
        "ftk": "1001",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552599",
        "ftk": "1002",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552598",
        "ftk": "1003",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552955",
        "ftk": "1004",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552956",
        "ftk": "1005",
        "remarks": "Member has not paid any amount.... Forwarded mail to Stop NACH.... NACH Disabled...cheques stopped...",
        "status": "Cancelled"
    },
    {
        "maf": "552971",
        "ftk": "1006",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "552953",
        "ftk": "1007",
        "remarks": "Member came office for refund and cancellation of membership.... Refund done of INR 16,000/- from Karan sir account Yes bank on 07-08-2019.",
        "status": "Cancelled"
    },
    {
        "maf": "552976",
        "ftk": "1008",
        "remarks": "Refund done to Varun singla/Shruti Singh from Vishwas Account on 07-11-2019.... sale cancelled & closed",
        "status": "Cancelled"
    },
    {
        "maf": "552985",
        "ftk": "1009",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "552908",
        "ftk": "1010",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "552384",
        "ftk": "1011",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "552806",
        "ftk": "1012",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "552916",
        "ftk": "1013",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552807",
        "ftk": "1014",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "552936",
        "ftk": "1015",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "552817",
        "ftk": "1016",
        "remarks": "Converted into ONE TIME..... Payment received ...Holiday taken...Sale Cancelled & closed....",
        "status": "Cancelled"
    },
    {
        "maf": "552818",
        "ftk": "1017",
        "remarks": "Converted into ONE TIME... Payment received....Holiday Taken... Sale Cancelled & closed...",
        "status": "Cancelled"
    },
    {
        "maf": "552820",
        "ftk": "1018",
        "remarks": "Converted into ONE TIME.... Payment received....Holiday taken... Sale cancelled & closed...",
        "status": "Cancelled"
    },
    {
        "maf": "552957",
        "ftk": "1019",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "552982",
        "ftk": "1020",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "552959",
        "ftk": "1021",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552810",
        "ftk": "1022",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "552854",
        "ftk": "1023",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "552852",
        "ftk": "1024",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552853",
        "ftk": "1025",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552841",
        "ftk": "1026",
        "remarks": "Refund done to member from axis bank on 25-07-19 of INR 49450/- ...Sale cancelled...",
        "status": "Cancelled"
    },
    {
        "maf": "552991",
        "ftk": "1027",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "552986",
        "ftk": "1028",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "552958",
        "ftk": "1029",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "552924",
        "ftk": "1030",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "552925",
        "ftk": "1031",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "552548",
        "ftk": "1032",
        "remarks": "Sale cancel refund done on 12-07-2019 via Gagan Dhiman",
        "status": "Cancelled"
    },
    {
        "maf": "552913",
        "ftk": "1033",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "553037",
        "ftk": "1034",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552970",
        "ftk": "1035",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "552803",
        "ftk": "1036",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "552911",
        "ftk": "1037",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "552809",
        "ftk": "1039",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "552904",
        "ftk": "1040",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "552935",
        "ftk": "1041",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "552831",
        "ftk": "1042",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "553089",
        "ftk": "1043",
        "remarks": "Member has availed holidays then forwarded a request to cancel his membership....",
        "status": "Cancelled"
    },
    {
        "maf": "552310",
        "ftk": "1044",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "552910",
        "ftk": "1045",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "552992",
        "ftk": "1046",
        "remarks": "Member has forwarded a request to Cancel his membership..... Mail Forwarded....Sale cancelled & closed...",
        "status": "Cancelled"
    },
    {
        "maf": "552937",
        "ftk": "1047",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552588",
        "ftk": "1048",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "552933",
        "ftk": "1051",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "552954",
        "ftk": "1052",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "552984",
        "ftk": "1053",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552891",
        "ftk": "1054",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552802",
        "ftk": "1055",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "552552",
        "ftk": "1056",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "552983",
        "ftk": "1058",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "552965",
        "ftk": "1059",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "552994",
        "ftk": "1060",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "552813",
        "ftk": "1063",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "0",
        "ftk": "1066",
        "remarks": "No MAF received ... NO Payment received... Sale cancel...",
        "status": "Cancelled"
    },
    {
        "maf": "552431",
        "ftk": "1070",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552412",
        "ftk": "1072",
        "remarks": "Sale Cancel....Received INR 54,000/-... Adjusting his amount of INR 54,000/-...",
        "status": "Cancelled"
    },
    {
        "maf": "552418",
        "ftk": "1074",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "552413",
        "ftk": "1077",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552895",
        "ftk": "1078",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552842",
        "ftk": "1079",
        "remarks": "Refund done to member on 17/12/19 from axis bank of INR 54,000/-... Cancelled Client....",
        "status": "Cancelled"
    },
    {
        "maf": "552851",
        "ftk": "1080",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "552849",
        "ftk": "1081",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "552826",
        "ftk": "1082",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "552975",
        "ftk": "1083",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "552437",
        "ftk": "1084",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "552438",
        "ftk": "1086",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552920",
        "ftk": "1087",
        "remarks": "Sale is cancelled... as member did not pay any amount...",
        "status": "Cancelled"
    },
    {
        "maf": "552439",
        "ftk": "1088",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552892",
        "ftk": "1089",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552823",
        "ftk": "1090",
        "remarks": "Refund has been done to member due to some wrong commitments.. so Refund done to Pooja from Axis bank on 05/11 of INR 17000/-\r\nSale cancelled & closed....",
        "status": "Cancelled"
    },
    {
        "maf": "552993",
        "ftk": "1092",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552825",
        "ftk": "1093",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552450",
        "ftk": "1094",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "552441",
        "ftk": "1096",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "552866",
        "ftk": "1097",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552436",
        "ftk": "1099",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "552858",
        "ftk": "1100",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552565",
        "ftk": "1064",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "552511",
        "ftk": "1065",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "552902",
        "ftk": "1068",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "552407",
        "ftk": "1069",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "552422",
        "ftk": "1071",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552421",
        "ftk": "1073",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "552414",
        "ftk": "1075",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "552417",
        "ftk": "1076",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552453",
        "ftk": "1085",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "552440",
        "ftk": "1095",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "552844",
        "ftk": "1098",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "552871",
        "ftk": "1101",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "552419",
        "ftk": "1105",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552452",
        "ftk": "1091",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552873",
        "ftk": "1102",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "552432",
        "ftk": "1103",
        "remarks": "Member has not paid any amount against his package... Sale cancel & closed...",
        "status": "Cancelled"
    },
    {
        "maf": "552383",
        "ftk": "1104",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "552429",
        "ftk": "1106",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "552423",
        "ftk": "1107",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "552420",
        "ftk": "1115",
        "remarks": "Refund done to Vishal Lekhi form yes bank on 27-09-19 INR 22525/-\r\nSale Cancel & Cancelled....",
        "status": "Cancelled"
    },
    {
        "maf": "552424",
        "ftk": "1108",
        "remarks": "Member does not want to continue. Nach disabled as well.",
        "status": "Active"
    },
    {
        "maf": "552855",
        "ftk": "1109",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552425",
        "ftk": "1110",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "552443",
        "ftk": "1111",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "552973",
        "ftk": "1112",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "552501",
        "ftk": "1113",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "552874",
        "ftk": "1114",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552876",
        "ftk": "1116",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "552877",
        "ftk": "1117",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552883",
        "ftk": "1126",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "552869",
        "ftk": "1122",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "552860",
        "ftk": "1125",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "552449",
        "ftk": "1120",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552878",
        "ftk": "1121",
        "remarks": "Member has forwared a mail to cancel his membership & adjust his amount in any of the services. Services has been provided & sale has been closed and cancelled...",
        "status": "Breather"
    },
    {
        "maf": "552856",
        "ftk": "1124",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552885",
        "ftk": "1127",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552884",
        "ftk": "1128",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601011",
        "ftk": "1129",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "601012",
        "ftk": "1130",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552472",
        "ftk": "1131",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552888",
        "ftk": "1119",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601013",
        "ftk": "1133",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601001",
        "ftk": "1135",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552509",
        "ftk": "1136",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "601002",
        "ftk": "1137",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552822",
        "ftk": "DEL1",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "552828",
        "ftk": "DEL3",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552894",
        "ftk": "DEL4",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552848",
        "ftk": "DEL5",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "552832",
        "ftk": "DEL6",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601021",
        "ftk": "DEL7",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601015",
        "ftk": "DEL8",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "601006",
        "ftk": "DEL9",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601018",
        "ftk": "1138",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601014",
        "ftk": "1139",
        "remarks": "Refund done to member on 23/10/19 amt 26500 by Karan Sir... Sale cancelled & closed",
        "status": "Cancelled"
    },
    {
        "maf": "601003",
        "ftk": "1140",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601022",
        "ftk": "1141",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "601020",
        "ftk": "1142",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601029",
        "ftk": "1143",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601004",
        "ftk": "1144",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "601303",
        "ftk": "1145",
        "remarks": "asking for refund wrong committments",
        "status": "Cancelled"
    },
    {
        "maf": "601311",
        "ftk": "1147",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601573",
        "ftk": "1149",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "601599",
        "ftk": "1152",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "601589",
        "ftk": "1148",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601600",
        "ftk": "1150",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "601590",
        "ftk": "1151",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601598",
        "ftk": "1153",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "601597",
        "ftk": "1154",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "601028",
        "ftk": "1155",
        "remarks": "Member has forwarded a mail to stop his ECS and cancel his membership.... Sale cancelled ... Cancellation & no refund mail has been forwarded to member...",
        "status": "Cancelled"
    },
    {
        "maf": "601008",
        "ftk": "1157",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552845",
        "ftk": "DEL2",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "601563",
        "ftk": "1159",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601566",
        "ftk": "1160",
        "remarks": "Member has forwarded a mail to cancel his membership....Nach stopped and amount forfeited.... Sale Cancel & closed...",
        "status": "Cancelled"
    },
    {
        "maf": "601027",
        "ftk": "1158",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601570",
        "ftk": "1164",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "601569",
        "ftk": "1165",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "601561",
        "ftk": "1167",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "601557",
        "ftk": "1171",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601559",
        "ftk": "1172",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601537",
        "ftk": "1173",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "509905",
        "ftk": "1161",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "509904",
        "ftk": "1162",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601562",
        "ftk": "1166",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "601560",
        "ftk": "1169",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "601555",
        "ftk": "1170",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "601026",
        "ftk": "1174",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601591",
        "ftk": "1175",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "601577",
        "ftk": "1168",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "601565",
        "ftk": "1176",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601594",
        "ftk": "1177",
        "remarks": "Member has forwarded a mail to Stop his membership & cancel his ECS mandate... No refund mail forwarded to member & Sale cancelled & closed... Everything on mail...",
        "status": "Cancelled"
    },
    {
        "maf": "601595",
        "ftk": "1178",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "601567",
        "ftk": "1179",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "601534",
        "ftk": "1180",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601574",
        "ftk": "DEL11",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601535",
        "ftk": "1182",
        "remarks": "Cancellation mail received from member.... Nach disbaled.... Sale cancelled & closed.....",
        "status": "Cancelled"
    },
    {
        "maf": "601301",
        "ftk": "1183",
        "remarks": "Cancellation mail received from member.... He wants adjustment of his DP of INR 6,000/-",
        "status": "Cancelled"
    },
    {
        "maf": "601554",
        "ftk": "1184",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601580",
        "ftk": "DEL12",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552833",
        "ftk": "DEL13",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "552456",
        "ftk": "DEL14",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601539",
        "ftk": "1181",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "601536",
        "ftk": "1185",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601592",
        "ftk": "1186",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "601538",
        "ftk": "1187",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601582",
        "ftk": "1188",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "601316",
        "ftk": "1189",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "601575",
        "ftk": "DEL10",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "601318",
        "ftk": "1190",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "601317",
        "ftk": "1191",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601550",
        "ftk": "1192",
        "remarks": "Cancel Sale...DP cheque bounced.... Nach bouncing per month...Sale cancel & closed....",
        "status": "Cancelled"
    },
    {
        "maf": "601324",
        "ftk": "1193",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601517",
        "ftk": "1194",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601579",
        "ftk": "1195",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "601576",
        "ftk": "1196",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601578",
        "ftk": "1197",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601585",
        "ftk": "1198",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601587",
        "ftk": "DEL19",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "552835",
        "ftk": "DEL18",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601584",
        "ftk": "DEL15",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "601583",
        "ftk": "DEL16",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "601586",
        "ftk": "DEL17",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601544",
        "ftk": "1201",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "601588",
        "ftk": "1202",
        "remarks": "Member has forwarded a cancellation mail & also has asked for refund.... Refund has been approved. Dates are not yet scheduled.",
        "status": "Cancelled"
    },
    {
        "maf": "601596",
        "ftk": "1203",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601323",
        "ftk": "1206",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601322",
        "ftk": "1208",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601547",
        "ftk": "1199",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "601030",
        "ftk": "1200",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601326",
        "ftk": "1205",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "601338",
        "ftk": "1209",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601320",
        "ftk": "1204",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "601344",
        "ftk": "1210",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "601343",
        "ftk": "DEL21",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601540",
        "ftk": "DEL22",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601353",
        "ftk": "1211",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601355",
        "ftk": "1212",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "601339",
        "ftk": "1213",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601367",
        "ftk": "DEL20",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601314",
        "ftk": "1214",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "601329",
        "ftk": "1216",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "601328",
        "ftk": "1215",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "552388",
        "ftk": "1057",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601349",
        "ftk": "1217",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "601327",
        "ftk": "1218",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601016",
        "ftk": "1219",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601358",
        "ftk": "1220",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "601359",
        "ftk": "1221",
        "remarks": "Member asked for the cancellation of his package. No refund mail forwarded & Sale cancelled...",
        "status": "Cancelled"
    },
    {
        "maf": "601332",
        "ftk": "1222",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601548",
        "ftk": "1207",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "601009",
        "ftk": "1223",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "601005",
        "ftk": "1224",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "601356",
        "ftk": "1225",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "601401",
        "ftk": "1226",
        "remarks": "Member has stopped by the payment of his cheques. No payment received from member...Sale cancel & closed...",
        "status": "Cancelled"
    },
    {
        "maf": "601402",
        "ftk": "1227",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601405",
        "ftk": "1228",
        "remarks": "Member has forwarded a request to cancel his sale...Sale cancelled & amount forfeited ....mail also done...",
        "status": "Cancelled"
    },
    {
        "maf": "601342",
        "ftk": "1229",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "601341",
        "ftk": "1230",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601025",
        "ftk": "1231",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "601530",
        "ftk": "DEL23",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "601529",
        "ftk": "DEL24",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "601553",
        "ftk": "DEL25",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601388",
        "ftk": "DEL26",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601392",
        "ftk": "DEL27",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601304",
        "ftk": "1156",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601302",
        "ftk": "1146",
        "remarks": "Member has forwarded a request to stop and cancel his membership.... Sale cancelled & membership closed.... No refund allowed... mail forwarded...",
        "status": "Cancelled"
    },
    {
        "maf": "552801",
        "ftk": "1049",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "552563",
        "ftk": "1061",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "552389",
        "ftk": "1062",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "552875",
        "ftk": "1118",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601370",
        "ftk": "1232",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "601372",
        "ftk": "1233",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601368",
        "ftk": "1234",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "601377",
        "ftk": "DEL29",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601407",
        "ftk": "1236",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601404",
        "ftk": "1237",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601412",
        "ftk": "1235",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552463",
        "ftk": "1238",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "601549",
        "ftk": "1240",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601516",
        "ftk": "1241",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601416",
        "ftk": "1242",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "601415",
        "ftk": "1244",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "601417",
        "ftk": "1239",
        "remarks": "Refund of INR 5000 transferred to member via paytm on 10/12/2019\r\nSale Cancelled & Closed..",
        "status": "Cancelled"
    },
    {
        "maf": "601346",
        "ftk": "1243",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "552821",
        "ftk": "DEL30",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "601459",
        "ftk": "DEL31",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601458",
        "ftk": "DEL32",
        "remarks": "Refund done to Member via Google pay by Vinay sir dated 26-02-2020 INR 9,900/-...Karan sir paid by Google pay to Vinay Sir & vinay sir paid to Member",
        "status": "Cancelled"
    },
    {
        "maf": "601518",
        "ftk": "1246",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601331",
        "ftk": "1247",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601365",
        "ftk": "1248",
        "remarks": "Member wanted to cancel his sale & wanted the refund of hi s amount. Refund approved by Karan Sir himself. Refund of INR 26,100/- on 08/01/2020 from axis account",
        "status": "Cancelled"
    },
    {
        "maf": "601366",
        "ftk": "1249",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601363",
        "ftk": "1250",
        "remarks": "Member has been called before presenting his DP Chq of INR 14,000/- but he denied to present that & informed the team he does not want to continue with this membership.... INR 2,000/- has been received from the member....No refund mail has been forwarded to member....",
        "status": "Cancelled"
    },
    {
        "maf": "601461",
        "ftk": "1251",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601362",
        "ftk": "1252",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601371",
        "ftk": "1253",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "601364",
        "ftk": "1254",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601330",
        "ftk": "1255",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601484",
        "ftk": "1256",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "601485",
        "ftk": "1257",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601462",
        "ftk": "1258",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601418",
        "ftk": "1259",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "601419",
        "ftk": "DEL33",
        "remarks": "Sale Cancel. Refund done on 05/12/2019 from HDFC Karan Sir of INR 10,000/-",
        "status": "Cancelled"
    },
    {
        "maf": "601347",
        "ftk": "1266",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "601336",
        "ftk": "DEL34",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601315",
        "ftk": "1260",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601447",
        "ftk": "1261",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "601446",
        "ftk": "1262",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "601443",
        "ftk": "1263",
        "remarks": "Refund done on same day of INR 19530/- by Karan sir HDFC on 30/11\r\nSale Cancelled & closed..",
        "status": "Cancelled"
    },
    {
        "maf": "601463",
        "ftk": "1264",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "601361",
        "ftk": "1245",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "601469",
        "ftk": "1267",
        "remarks": "Member has been cancelled as he does not want to continue with us....he has paid INR 5,000/- which will be adjusted in future if he takes a package from here. Every thing is on mail..",
        "status": "Cancelled"
    },
    {
        "maf": "601409",
        "ftk": "1269",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601455",
        "ftk": "1270",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "601350",
        "ftk": "1271",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601471",
        "ftk": "1273",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601448",
        "ftk": "1276",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "601468",
        "ftk": "1274",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "601420",
        "ftk": "1277",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "601488",
        "ftk": "1278",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "601486",
        "ftk": "1279",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601487",
        "ftk": "1280",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601482",
        "ftk": "DEL35",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "601479",
        "ftk": "DEL36",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601422",
        "ftk": "1282",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601424",
        "ftk": "1283",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601421",
        "ftk": "1281",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "601393",
        "ftk": "1284",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "601395",
        "ftk": "1285",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601394",
        "ftk": "1286",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "601478",
        "ftk": "1287",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "601464",
        "ftk": "1275",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601337",
        "ftk": "DEL37",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "601541",
        "ftk": "DEL38",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "601527",
        "ftk": "DEL39",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "601345",
        "ftk": "DEL40",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "601480",
        "ftk": "DEL41",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "601396",
        "ftk": "1289",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "601398",
        "ftk": "1290",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601397",
        "ftk": "1291",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601399",
        "ftk": "1292",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "601509",
        "ftk": "1293",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601511",
        "ftk": "1288",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "601519",
        "ftk": "1298",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "601369",
        "ftk": "1294",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "601472",
        "ftk": "1295",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "601460",
        "ftk": "1296",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601357",
        "ftk": "1297",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "601543",
        "ftk": "DEL42",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601489",
        "ftk": "DEL43",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601444",
        "ftk": "1299",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601512",
        "ftk": "1300",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601400",
        "ftk": "1301",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "601386",
        "ftk": "DEL44",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "601439",
        "ftk": "1302",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "601514",
        "ftk": "1304",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601513",
        "ftk": "1305",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "601515",
        "ftk": "1306",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "601556",
        "ftk": "1303",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "601506",
        "ftk": "1307",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601508",
        "ftk": "1308",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601442",
        "ftk": "1309",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "601501",
        "ftk": "1310",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601497",
        "ftk": "1311",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "601348",
        "ftk": "1312",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "601378",
        "ftk": "1313",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601385",
        "ftk": "1314",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601524",
        "ftk": "1315",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601533",
        "ftk": "DEL45",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "601441",
        "ftk": "1316",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "601507",
        "ftk": "1317",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "601445",
        "ftk": "1318",
        "remarks": "Refund done to member on the same day of INR 28426/- IMPS/P2A/000518676008//HDFCBAN/X180678/Refu from axis bank on 05-01-2020",
        "status": "Cancelled"
    },
    {
        "maf": "601581",
        "ftk": "1320",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "601436",
        "ftk": "1321",
        "remarks": "Member has asked for cancellation on welcome call & also stopped the DP cheque. No payment received...Sale cancelled",
        "status": "Cancelled"
    },
    {
        "maf": "601428",
        "ftk": "1322",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "601354",
        "ftk": "1319",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "601334",
        "ftk": "1323",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601499",
        "ftk": "1324",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601504",
        "ftk": "1325",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "601380",
        "ftk": "DEL46",
        "remarks": "",
        "status": "Breather"
    },
    {
        "maf": "601381",
        "ftk": "DEL47",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "601430",
        "ftk": "DEL48",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "601571",
        "ftk": "1163",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601601",
        "ftk": "1326",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "601603",
        "ftk": "1328",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "601604",
        "ftk": "1329",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601493",
        "ftk": "1330",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "601609",
        "ftk": "1331",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "601602",
        "ftk": "1327",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "601612",
        "ftk": "1332",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601613",
        "ftk": "1333",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "601608",
        "ftk": "1334",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601494",
        "ftk": "1335",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "601618",
        "ftk": "1336",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601520",
        "ftk": "DEL51",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601614",
        "ftk": "1339",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "601615",
        "ftk": "1337",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601373",
        "ftk": "DEL49",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601481",
        "ftk": "DEL50",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601502",
        "ftk": "DEL52",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "601465",
        "ftk": "DEL53",
        "remarks": "Refund of INR 5000/-done to member by cash on the same dated 26-01-2020",
        "status": "Cancelled"
    },
    {
        "maf": "601379",
        "ftk": "DEL54",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601437",
        "ftk": "DEL55",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "601616",
        "ftk": "1338",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "601491",
        "ftk": "1340",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "601620",
        "ftk": "1341",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601495",
        "ftk": "1342",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601490",
        "ftk": "1343",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "601335",
        "ftk": "1344",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "601496",
        "ftk": "1345",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601619",
        "ftk": "1346",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601623",
        "ftk": "1347",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601622",
        "ftk": "1348",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601621",
        "ftk": "1349",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601376",
        "ftk": "1350",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "601475",
        "ftk": "1351",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "601510",
        "ftk": "1352",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601449",
        "ftk": "1353",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "601382",
        "ftk": "DEL56",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "601625",
        "ftk": "1354",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601626",
        "ftk": "1355",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601606",
        "ftk": "1356",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "601641",
        "ftk": "1357",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601643",
        "ftk": "1358",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "601630",
        "ftk": "1359",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "601642",
        "ftk": "1360",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "601624",
        "ftk": "1361",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "601632",
        "ftk": "1362",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601634",
        "ftk": "1363",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601639",
        "ftk": "1364",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601640",
        "ftk": "1365",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "601631",
        "ftk": "1366",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601633",
        "ftk": "1367",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "601312",
        "ftk": "1368",
        "remarks": "Refund done to member INR 6670 dated 28-02-2020",
        "status": "Cancelled"
    },
    {
        "maf": "601403",
        "ftk": "1369",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601638",
        "ftk": "1370",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "601653",
        "ftk": "1371",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601375",
        "ftk": "57",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "601522",
        "ftk": "DEL58",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "601306",
        "ftk": "DEL59",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "601435",
        "ftk": "DEL60",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "601308",
        "ftk": "1372",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601307",
        "ftk": "1373",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601309",
        "ftk": "1374",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "601505",
        "ftk": "1375",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "601374",
        "ftk": "DEL61",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "601637",
        "ftk": "1377",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601466",
        "ftk": "1378",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601429",
        "ftk": "1379",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "601523",
        "ftk": "1380",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601671",
        "ftk": "1376",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601433",
        "ftk": "1381",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "552834",
        "ftk": "1382",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "601483",
        "ftk": "1383",
        "remarks": "Client cancelled on their request and Also Agreed to forgo any any future adjustments and refunds. Check Follow up for more details and a call recording for the same",
        "status": "Cancelled"
    },
    {
        "maf": "601426",
        "ftk": "1384",
        "remarks": "INB/NEFT/AXIC200649494579/Deepika Refund/Refund...refund done to member from axis bank on 04-03-2020",
        "status": "Cancelled"
    },
    {
        "maf": "601679",
        "ftk": "1385",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601432",
        "ftk": "1389",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601610",
        "ftk": "1394",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601645",
        "ftk": "1395",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "601611",
        "ftk": "1396",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "601683",
        "ftk": "1388",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601617",
        "ftk": "1393",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601669",
        "ftk": "1386",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601709",
        "ftk": "1398",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "601650",
        "ftk": "1392",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601681",
        "ftk": "1387",
        "remarks": "",
        "status": "Cancelled"
    },
    {
        "maf": "601467",
        "ftk": "1390",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601434",
        "ftk": "1391",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601655",
        "ftk": "1397",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601717",
        "ftk": "1399",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "601705",
        "ftk": "DEL62",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "601737",
        "ftk": "DEL63",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601738",
        "ftk": "DEL64",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "601720",
        "ftk": "1400",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601723",
        "ftk": "1401",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601718",
        "ftk": "1402",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601719",
        "ftk": "1403",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601721",
        "ftk": "1404",
        "remarks": "ffinancial reasons welcome call cancellation",
        "status": "Cancelled"
    },
    {
        "maf": "601661",
        "ftk": "1405",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601662",
        "ftk": "1406",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "601722",
        "ftk": "1407",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601667",
        "ftk": "1408",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601668",
        "ftk": "1409",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "601733",
        "ftk": "1410",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "601699",
        "ftk": "DEL65",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601656",
        "ftk": "1411",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601425",
        "ftk": "1412",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601889",
        "ftk": "1413",
        "remarks": "",
        "status": "On Hold"
    },
    {
        "maf": "601890",
        "ftk": "1414",
        "remarks": "",
        "status": "Active"
    },
    {
        "maf": "601724",
        "ftk": "1415",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601704",
        "ftk": "DEL66",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601900",
        "ftk": "1416",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601859",
        "ftk": "1417",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601857",
        "ftk": "1418",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601742",
        "ftk": "1419",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601706",
        "ftk": "1420",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601745",
        "ftk": "1421",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601860",
        "ftk": "1422",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601743",
        "ftk": "1423",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601744",
        "ftk": "1424",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601852",
        "ftk": "1425",
        "remarks": "",
        "status": "Incomplete"
    },
    {
        "maf": "601697",
        "ftk": "1426",
        "remarks": "",
        "status": "Incomplete"
    }
]');
//
//        foreach($status as $st){
//      $soldPackage = \App\Client\Package\SoldPackages::where('fclpId',$st->maf)->first();
//      if($soldPackage){
//        $soldPackage->status = $st->status;
//        $soldPackage->remarks = $st->remarks;
//        $soldPackage->save();
//      }
//    }


//    foreach($benefits as $benefit){
//      $soldPackage = \App\Client\Package\SoldPackages::where('fclpId',$benefit->maf)->first();
//      if($soldPackage){
//        $b = new SoldPackageBenefits;
//        $b->clientId = $soldPackage->clientId;
//        $b->soldPackageId = $soldPackage->id;
//        $b->benefitName = $benefit->service_type;
//        $b->benefitDescription = $benefit->service_name . '(' . $benefit->service_price . ')';
//        $b->save();
//      }
//    }
//    foreach($clients as $client) {
//      $c = new Client();
//      $c->name = $client->name;
//      $c->phone = $client->mobile;
//      $c->altPhone = $client->alternate_mobile;
//      $c->email = $client->email;
//      $c->birthDate = Carbon::parse($client->DOB)->format('Y-m-d');
//      $c->maf = $client->maf_no;
//      $c->fclp = $client->application_no;
//      $c->address = $client->address . ' ' . $client->city . ' ' . $client->state . ' ' . $client->country . ' ' . $client->postal_code;
//      $c->branch = $client->branch;
//      $c->saleBy = $client->sale_by;
//      $c->dateOfEnrollment = $client->date_of_enrollment;
//      $c->emiRegularPlan = $client->emi_regular_plan;
//      $c->save();
//    }

//    foreach ($packages as $pack) {
//      $cl = Client::where('fclp', $pack->ftk)->get();
////                dd($cl->count());
//      if ($cl->count()) {
//        $pk = $cl->first()->latestPackage;
//        $pk->emiAmount = $pack->emi_amount;
//        $pk->save();
//                $package = new SoldPackages();
//                $package->clientId = $cl->first()->id;
//                $package->mafNo = $cl->first()->maf;
//                $package->fclpId = $cl->first()->fclp;
//                $package->branch = $cl->first()->branch;
//                $package->saleBy = $cl->first()->saleBy;
//                $package->enrollmentDate = $cl->first()->dateOfEnrollment;
//                $package->productTenure = $pack->product_tenure;
//                $package->productName = $pack->fclp_name;
//                $package->productCost = $pack->fclp_price;
//                $package->modeOfPayment = ' ';
//                $package->noOfEmi = $cl->first()->emiRegularPLan;
//                $package->save();
//      }
    }


  function search($array, $search_list)
  {

    // Create the result array
    $result = array();

    // Iterate over each array element
    foreach ($array as $key => $value) {

      // Iterate over each search condition
      foreach ($search_list as $k => $v) {

        // If the array element does not meet
        // the search condition then continue
        // to the next element
        if (!isset($value[$k]) || $value[$k] != $v) {

          // Skip two loops
          continue 2;
        }
      }

      // Append array element's key to the
      //result array
      $result[] = $value;
    }

    // Return result
    return $result;
  }

  public function updateBasicDetails(Request $request, $clientId)
  {
    $client = Client::find($clientId);
    if ($client) {
      $client->name = $request->clientName;
      $client->address = $request->address;
      $client->email = $request->email;
      $client->phone = $request->phone;
      $client->altPhone = $request->altPhone;
      $client->emiRegularPlan = $request->emiRegularPlan;
      $package = $client->latestPackage;
      $package->enrollmentDate = $request->enrollmentDate;
      $package->productTenure = $request->productTenure;
      $package->productCost = $request->productCost;
      $package->emiAmount = $request->emiAmount;
      $package->saleBy = $request->saleBy;
      $package->saleManager = $request->saleManager;
      $package->save();
      $client->save();
    }
    return redirect()->back();
  }

      public function uploadMaf(Request $request){
        $client = Client::findOrFail($request->id);
        $client->verified = 1;
        if($request->hasFile('maf')) {
            $this->validate($request, [
                'maf' => 'mimes:pdf|max:99048',
            ]);
            $mafName = $client->application_no . '_' . $client->name . '_scannedMaf_' . time() . '.' . $request->maf->getClientOriginalExtension();
            $image = $request->file('maf');
            $t = Storage::disk('s3')->put($mafName, file_get_contents($image), 'public');
            $mafURL = Storage::disk('s3')->url($mafName);
            Document::create([
              'client_id'=>$client->id,
              'type'=>'maf',
              'url'=> $mafURL,
            ]);
        }
        notification('Client Updated', 'MAF UPLOADED', 'success','okay');
        return redirect()->back();
    }

    public function updateStatus(Request $request,$id){
        $package = SoldPackages::find($id);
        if($package){
          $package->status = $request->status;
          $package->remarks = $request->remarks;
          $package->save();
        }
        return redirect()->back();

    }

    public function reports(){
    return 'asdjhvasdj abs djhvhkasdkh';
    }

    public function eMaf($slug){
        $client =  Client::where('slug',$slug)->get()->first();
        if($client){
          return view('emails.details')->with('client',$client);
        }
        return redirect()->back();
    }

    public function welcomeLetter($slug){
      $client =  Client::where('slug',$slug)->get()->first();
      if($client){
        return view('emails.welcome')->with('client',$client);
      }
      return redirect()->back();
    }

    public function certificate($slug){
      $client =  Client::where('slug',$slug)->get()->first();
      if($client){
        return view('emails.certificate')->with('client',$client);
      }
      return redirect()->back();
    }

    public function sendEkit($slug){
      $details = Client::where('slug',$slug)->get()->first();
      $contactEmail = $details->email;
      $contactName = $details->name;

      $details['email'] = $contactEmail;
      $details['id'] = $details->id;

      dispatch(new SendEkitJob($details,Carbon::now()->toDateString(),Auth::id()));
      $message = 'Sent to ' . $contactName;
      return redirect()->back();
    }

    public function createLogin($slug){
        $client = Client::where('slug',$slug)->get()->first();
      if (!User::where('email', $client->email)->count()) {
        $client->User()->create([
          'name' => $client->name,
          'email' => strtolower($client->email),
          'client_id' => $client->id,
          'password' => Hash::make('pass@123'),
        ]);
        notifyToast('success', 'Login Created', $client->name . '\'s Login Created Successfully');
      } else {
        notifyToast('error', 'Duplicate Email', 'Login with email: ' . $client->email . ' already exists, Please Update the email and try again');
      }
      return redirect()->back();
    }

}
