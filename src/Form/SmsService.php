<?php

namespace App\Form;

use Twilio\Rest\Client;

class SmsService
{
    private string $twilioSid;
    private string $twilioToken;
    private string $twilioFrom;

    public function __construct()
    {   
        // Denne information kan findes i bunden af twilio hjemmesiden
        $this->twilioSid = 'AC261da035ec6da6e8f4b85b504ea1c0e2';    
        $this->twilioToken = '3e41867063140646f61586d768c5f0f4';  
        $this->twilioFrom = '+19787055940'; 
    }

    public function sendSms(string $to, string $message): void
    {
        $client = new Client($this->twilioSid, $this->twilioToken);

        $client->messages->create(
            $to,
            [
                'from' => $this->twilioFrom,
                'body' => $message
            ]
        );
    }
}
