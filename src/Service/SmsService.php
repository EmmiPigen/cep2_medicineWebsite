<?php

namespace App\Service;

use Twilio\Rest\Client;

class SmsService
{
    private string $twilioSid;
    private string $twilioToken;
    private string $twilioFrom;

    public function __construct(string $twilioSid, string $twilioToken, string $twilioFrom)
    {   
        dump('TWILIO_SID:', $twilioSid);
        dump('TWILIO_TOKEN:', $twilioToken);
        dump('TWILIO_FROM:', $twilioFrom);

        // Denne information kan findes i bunden af twilio hjemmesiden.
        #$this->twilioSid = getenv('TWILIO_SID');
        #$this->twilioToken = getenv('TWILIO_TOKEN');
        #$this->twilioFrom = getenv('TWILIO_FROM');

        #dump('TWILIO_SID:', $this->twilioSid);
        #dump('TWILIO_TOKEN:', $this->twilioToken);
        #dump('TWILIO_FROM:', $this->twilioFrom);
        
        $this->twilioSid = $twilioSid;
        $this->twilioToken = $twilioToken;
        $this->twilioFrom = $twilioFrom;
    }

    public function sendSms(string $to, string $message): void
    {
        echo "Sender SMS til {$to} med besked: {$message}\n";

        $client = new Client($this->twilioSid, $this->twilioToken);

        $client->messages->create(
            $to, // eksempel: '+4521900301'
            [
                'from' => $this->twilioFrom,
                'body' => $message
            ]
        );
    }

}
