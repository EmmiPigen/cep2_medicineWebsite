<?php

namespace App\Service;

use Twilio\Rest\Client;

class SmsService
{
    private string $twilioSid;
    private string $twilioToken;
    private string $twilioFrom;

    public function __construct()
    {   
        // Denne information kan findes i bunden af twilio hjemmesiden.
        // MIDLERTIDIGT FJERNET! JEG SÆTTER IND IGEN SENERE
        $this->twilioSid = $twilioSid;
        $this->twilioToken = $twilioToken;
        $this->twilioFrom = $twilioFrom;
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
