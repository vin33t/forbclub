<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Client\Client;
use Webklex\IMAP\Client as mailClient;


class EmailFetcherController extends Controller
{
  public function getMail(Request $request){
    $client = Client::find($request->id);
    $type = $request->type;
    if($type == 'accounts'){
      $oClient = new mailClient([
        'host'          => env('EMAIL_HOST',false),
        'port'          => env('EMAIL_PORT',false),
        'encryption'    => env('EMAIL_ENCRYPTION',false),
        'validate_cert' => env('EMAIL_VALIDATE_CERT',false),
        'username'      => env('EMAIL_ACCOUNTS_USERNAME',false),
        'password'      => env('EMAIL_ACCOUNTS_PASSWORD',false),
        'protocol'      => env('EMAIL_PROTOCOL',false)
      ]);
    }elseif($type == 'booking'){
      $oClient = new mailClient([
        'host'          => env('EMAIL_HOST',false),
        'port'          => env('EMAIL_PORT',false),
        'encryption'    => env('EMAIL_ENCRYPTION',false),
        'validate_cert' => env('EMAIL_VALIDATE_CERT',false),
        'username'      => env('EMAIL_BOOKING_USERNAME',false),
        'password'      => env('EMAIL_BOOKING_PASSWORD',false),
        'protocol'      => env('EMAIL_PROTOCOL',false)
      ]);
    }elseif($type == 'mrd'){
      $oClient = new mailClient([
        'host'          => env('EMAIL_HOST',false),
        'port'          => env('EMAIL_PORT',false),
        'encryption'    => env('EMAIL_ENCRYPTION',false),
        'validate_cert' => env('EMAIL_VALIDATE_CERT',false),
        'username'      => env('EMAIL_MRD_USERNAME',false),
        'password'      => env('EMAIL_MRD_PASSWORD',false),
        'protocol'      => env('EMAIL_PROTOCOL',false)
      ]);
    }
    $oClient->connect();
    $aFolder = $oClient->getFolder('INBOX');
    $aMessage = $aFolder->query()->from($client->email)->get();
    $aMessageSent = $aFolder->query()->to($client->email)->get();
    return view('emailChat.index')->with('emails',$aMessage)->with('client',$client)->with('sent',$aMessageSent);
  }

}
