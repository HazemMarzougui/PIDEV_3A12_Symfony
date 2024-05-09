<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Twilio\Rest\Client;

class TwilioSmsService extends AbstractController
{
    #[Route('/send', name: 'app_send_sms', methods: ['GET'])]

    public function sendSms(string $message, string $recipientPhoneNumber)
{
    $accountSid = $_ENV['TWILIO_ACCOUNT_SID'];
    $authToken = $_ENV['TWILIO_AUTH_TOKEN'];
    $twilioPhoneNumber = $_ENV['TWILIO_PHONE_NUMBER'];
    
    // Initialize Twilio client
    $twilioClient = new Client($accountSid, $authToken);
    
    // Recipient's phone number
   // $recipientPhoneNumber = '+21629224353'; // Replace with recipient's phone number
    
    // Send SMS message
    try {
        $twilioClient->messages->create(
            $recipientPhoneNumber,
            [
                'from' => $twilioPhoneNumber,
                'body' => $message
            ]
        );
        
        // Message was sent successfully
        return $this->json(['message' => 'SMS sent successfully']);
    } catch (\Exception $e) {
        // Error occurred while sending SMS
        return $this->json(['error' => $e->getMessage()]);
    }
}

}