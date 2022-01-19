<?php

namespace App\Jobs;

use App\Mail\SendEkit;
use App\Mail\SendEkit1;
use App\Mail\Welcome;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;

class SendEkitJob implements ShouldQueue
{
  use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

  /**
   * Create a new job instance.
   *
   * @return void
   */
  public $details;
  public $date;
  public $id;
  public $tries = 10;

  public function __construct($details, $date, $id)
  {
    $this->details = $details;
    $this->date = $date;
    $this->id = $id;

  }

  /**
   * Execute the job.
   *
   * @return void
   */
  public function handle()
  {
    Mail::to($this->details['email'])->send(new Welcome($this->details['id']));
    sleep(1);
    Mail::to('mrd@forbclub.com')->send(new Welcome($this->details['id']));

        sleep(1);
        Mail::to('travel@forbclub.com')->send(new Welcome($this->details['id']));
        sleep(1);
        Mail::to('noreply@forbclub.com')->send(new Welcome($this->details['id']));


    sleep(1);
    Mail::to($this->details['email'])->send(new SendEkit1($this->details['id']));
    sleep(1);
    Mail::to('mrd@forbclub.com')->send(new SendEkit1($this->details['id']));
    sleep(1);
        Mail::to('travel@forbclub.com')->send(new SendEkit1($this->details['id']));
        sleep(1);
        Mail::to('noreply@forbclub.com')->send(new SendEkit1($this->details['id']));
        sleep(1);


    Mail::to($this->details['email'])->send(new SendEkit($this->details['id']));
    sleep(1);
    Mail::to('mrd@forbclub.com')->send(new SendEkit($this->details['id']));
    sleep(1);
        Mail::to('travel@forbclub.com')->send(new SendEkit($this->details['id']));
        sleep(1);
        Mail::to('noreply@forbclub.com')->send(new SendEkit($this->details['id']));
        sleep(1);

//    $el = new EkitLog;
//    $el->client_id = $this->details['id'];
//    $el->sent_on = $this->date;
//    $el->sent_by = $this->id;
//    $el->save();
//  }
//
//  public function failed(Exception $exception){
//    $eel = new EkitErrorLog;
//    $eel->client_id = $this->details['id'];
//    $eel->error_on = $this->date;
//    $eel->message =  $exception->getMessage();
//    $eel->sent_by = $this->id;
//    $eel->save();
//  }
  }
}
