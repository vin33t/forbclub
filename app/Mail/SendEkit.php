<?php

namespace App\Mail;

use App\Client\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendEkit extends Mailable
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

        return $this->from('noreply@forbclub.com', 'Forb Club')
            ->subject('Certificate |'. $details->name)->view('emails.certificate')->with('client',$details);
    }
}
