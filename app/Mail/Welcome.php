<?php

namespace App\Mail;

use App\Client\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class Welcome extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $id;
    public function __construct($id)
    {
        $this->id = $id;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $details = Client::findOrFail($this->id);
        $file = "https://forbclub.com/software_cdn/T&C.pdf";
//        $file = 'http://myapp.forbclub.com/theme/assets/FCLP_10yrs_T&C.pdf';
        return $this->from('noreply@forbclub.com', 'Forb Club')
            ->subject('Welcome to Forbclub |'. $details->name)->view('emails.welcome')->with('client',$details);
//        return $this->from('noreply@forbclub.com', 'Forb Club')
//            ->subject('Welcome to ForbCorp |'. $details->name)->view('emails.client')->with('client',$details)->attach($file, [
//                'as'   => 'T&C.pdf',
//                'mime' => 'application/pdf',
//            ]);
    }
}
