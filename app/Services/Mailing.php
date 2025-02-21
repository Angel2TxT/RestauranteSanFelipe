<?php
namespace App\Services;
use Mailgun\Mailgun;


class Mailing{

    protected $mail;

    public function __construct()
    {
       
        $this->mail = Mailgun::create('key-62da6f040b99a79c24f5594871e1462c-ac3d5f74-5e1fb59d','https://api.mailgun.net/v3/sandbox7382afb53be543f886dcd182d987dfb1.mailgun.org');

    }


    public function sendMessage(){

        $response = $this->mail->messages()->send('sandbox7382afb53be543f886dcd182d987dfb1.mailgun.org', [
            'from'    => 'lopeztrujilloxd@gmail.com',
            'to'      => 'wilberthlp4@gmail.com',
            'subject' => 'The PHP SDK is awesome!',
            'text'    => 'It is so simple to send a message.'
          ]);

          error_log(json_encode($response));

    }
}

