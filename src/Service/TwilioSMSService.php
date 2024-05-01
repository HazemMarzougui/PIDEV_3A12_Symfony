<?php

namespace App\Service;

use Twilio\Rest\Client;

class TwilioSMSService
{
    private $sid;
    private $token;
    private $twilioClient;

    public function __construct($sid, $token)
    {
        $this->sid = $sid;
        $this->token = $token;
        $this->twilioClient = new Client($sid, $token);
    }

    public function sendSMS($to, $messageBody)
    {
        try {
            $message = $this->twilioClient->messages->create(
                $to,
                ['from' => '+13156794697', 'body' => $messageBody]
            );
            return $message->sid;
        } catch (\Exception $e) {
            // Handle exceptions
            return null;
        }
    }
}



