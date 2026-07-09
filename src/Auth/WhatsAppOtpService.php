<?php

declare(strict_types=1);

namespace Tavp\Core\Auth;

/**
 * OTP delivery via WhatsApp using Twilio.
 */
class WhatsAppOtpService
{
    public function __construct(
        private string $accountSid,
        private string $authToken,
        private string $fromNumber
    ) {
    }

    /**
     * Send OTP via WhatsApp to phone number.
     */
    public function send(string $phoneNumber, string $code): bool
    {
        $message = "🔑 Your TAVP verification code is: *{$code}*\n\nThis code expires in 5 minutes.";

        $url = "https://api.twilio.com/2010-04-01/Accounts/{$this->accountSid}/Messages.json";

        $data = [
            'To' => "whatsapp:{$phoneNumber}",
            'From' => "whatsapp:{$this->fromNumber}",
            'Body' => $message,
        ];

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_USERPWD => "{$this->accountSid}:{$this->authToken}",
            CURLOPT_POSTFIELDS => http_build_query($data),
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return $httpCode === 201;
    }
}
