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
        $this->twilioSid = $twilioSid;
        $this->twilioToken = $twilioToken;
        $this->twilioFrom = $twilioFrom;
    }

    public function sendSms(string $to, string $message): void
    {
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
