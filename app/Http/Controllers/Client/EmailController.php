<?php

namespace App\Http\Controllers\Client;

use App\Emails;
use App\Http\Controllers\Controller;
use App\Client\Client;
use App\EmailAttachments;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Obiefy\API\Facades\API;

class EmailController extends Controller
{
  public function emails()
  {
    $emails = Emails::paginate(10);
//    return $emails;
    {
      $pageConfigs = [
        'pageHeader' => false,
        'contentLayout' => "content-left-sidebar",
        'bodyClass' => 'email-application',
      ];

      return view('client.emails.emails', ['pageConfigs' => $pageConfigs])
        ->with('emails',$emails);
    }
  }
  public function emailsAccounts()
  {
    $emails = Emails::where('account','accounts')->paginate(10);
//    return $emails;
    {
      $pageConfigs = [
        'pageHeader' => false,
        'contentLayout' => "content-left-sidebar",
        'bodyClass' => 'email-application',
      ];

      return view('client.emails.emails', ['pageConfigs' => $pageConfigs])
        ->with('emails',$emails);
    }
  }
  public function emailsMrd()
  {
    $emails = Emails::where('account','mrd')->paginate(10);
//    return $emails;
    {
      $pageConfigs = [
        'pageHeader' => false,
        'contentLayout' => "content-left-sidebar",
        'bodyClass' => 'email-application',
      ];

      return view('client.emails.emails', ['pageConfigs' => $pageConfigs])
        ->with('emails',$emails);
    }
  }

  public function emailsNoreply()
  {
    $emails = Emails::where('account','noreply')->paginate(10);
//    return $emails;
    {
      $pageConfigs = [
        'pageHeader' => false,
        'contentLayout' => "content-left-sidebar",
        'bodyClass' => 'email-application',
      ];

      return view('client.emails.emails', ['pageConfigs' => $pageConfigs])
        ->with('emails',$emails);
    }
  }
  public function emailsBookings()
  {
    $emails = Emails::where('account','bookings')->paginate(10);
//    return $emails;
    {
      $pageConfigs = [
        'pageHeader' => false,
        'contentLayout' => "content-left-sidebar",
        'bodyClass' => 'email-application',
      ];

      return view('client.emails.emails', ['pageConfigs' => $pageConfigs])
        ->with('emails',$emails);
    }
  }
  public function emailsContent($id){
    $email = Emails::find($id);
    if($email->read == 0){
      $email->read = 1;
      $email->save();
    }
    $data = [
      'name' => unserialize($email->sender)[0]->personal == false ?  unserialize($email->sender)[0]->mailbox : strtoupper(unserialize($email->sender)[0]->personal),
      'subject' => $email->subject,
      'from' => $email->from,
      'avatar'=> '<img src="'. avatar(unserialize($email->sender)[0]->personal == false ?  unserialize($email->sender)[0]->mailbox : strtoupper(unserialize($email->sender)[0]->personal)).'" alt="avtar img holder" width="61" height="61">',
      'to'=>$email->to,
      'body'=>$email->html_body,
      'date'=>Carbon::parse($email->date)->format('F d,Y'),
      'time'=>Carbon::parse($email->time)->format('h:i A'),
    ];
    return $data;
  }
  public function fetchMails($account)
  {
    DB::beginTransaction();
    try {
      $mailbox = connectEmail($account);
      $aFolder = $mailbox->getFolder('INBOX');
      $aMessage = $aFolder->query()->unseen()->get();
      foreach ($aMessage as $oMessage) {
        $email = new Emails();
        $email->account = $account;
        $emailCheckReceived = Client::where('email', strtolower($oMessage->getFrom()[0]->mail))->get();

        $email->uid = $oMessage->getMessageId();
        $email->sender = serialize($oMessage->getSender());
        $email->subject = $oMessage->getSubject();
        $email->from = $oMessage->getFrom()[0]->mail;
        if (count($oMessage->getTo())) {
          $email->to = $oMessage->getTo()[0]->mail;

          $emailCheckSent = Client::where('email', strtolower($oMessage->getFrom()[0]->mail))->get();;
          if ($emailCheckSent->count()) {
            $email->client_id = $emailCheckSent->first()->client_id;
          }
        }
        $email->cc = serialize($oMessage->getCc());
        $email->bcc = serialize($oMessage->getBcc());
        $email->flags = serialize($oMessage->getFlags());
        $email->text_body = $oMessage->getTextBody();
        $email->html_body = $oMessage->getHtmlBody();
        $email->date = Carbon::parse($oMessage->getDate())->format('Y-m-d');
        $email->time = Carbon::parse($oMessage->getDate())->format('H:i:s');
        $email->labels = serialize([$account]);
        $email->save();
        $attachments = $oMessage->getAttachments();
        if ($attachments->count()) {
          $path = 'attachments/' . $account . '/' . preg_replace("/[^a-zA-Z0-9]+/", "", $oMessage->getMessageId()) . '/' . $email->id . '/';
          $attachments->each(function ($attachment) use ($oMessage, $path, $email) {
            EmailAttachments::create([
              'email_id' => $email->id,
              'path' => $path,
              'file_name' => str_replace(' ', '_', $attachment->name),
              'file_type' => $attachment->content_type,
              'file_size' => $attachment->size,
            ]);
            Storage::disk('s3')->put('mails/' . $path . str_replace(' ', '_', $attachment->name), $attachment->content);
//                    Storage::put($path . $attachment->name, $attachment->content);
          });
        }
      }
      DB::commit();
      return redirect()->back();
//      return API::response(200, 'Mails Fetched', '');
//      return API::response(200, 'Mails Fetched', '');
    } catch (\Exception $e) {
      DB::rollBack();
      return redirect()->back();
//      return API::response(500, 'Something Went Wrong While fetching the Mails', $e);
    }
  }



}
