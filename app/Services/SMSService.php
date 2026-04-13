<?php

namespace App\Services;

use Twilio\Rest\Client;

class SMSService
{
    private Client $twilio;

    public function __construct()
    {
        $accountSid = config('services.twilio.account_sid');
        $authToken = config('services.twilio.auth_token');
        $this->twilio = new Client($accountSid, $authToken);
    }

    /**
     * Send an SMS via Twilio.
     *
     * @param string $to Recipient phone number
     * @param string $body Message body (max 160 chars)
     * @param array $metadata Additional metadata
     * @return array
     */
    public function sendSMS(string $to, string $body, array $metadata = []): array
    {
        try {
            $message = $this->twilio->messages->create(
                $to,
                [
                    'from' => config('services.twilio.phone_number'),
                    'body' => $body,
                ]
            );

            return [
                'success' => true,
                'external_id' => $message->sid,
                'status' => $message->status,
                'metadata' => [
                    'provider' => 'twilio',
                    'sent_at' => now()->toIso8601String(),
                    ...$metadata,
                ],
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get SMS delivery status.
     *
     * @param string $sid Twilio message SID
     * @return array
     */
    public function getMessageStatus(string $sid): array
    {
        try {
            $message = $this->twilio->messages($sid)->fetch();

            return [
                'success' => true,
                'status' => $message->status,
                'error_code' => $message->errorCode,
                'error_message' => $message->errorMessage,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Handle inbound SMS via webhook.
     */
    public function handleInboundMessage(array $payload): void
    {
        // Implementation for handling incoming SMS
        // Called from webhook endpoint
    }

    /**
     * Handle delivery status updates via webhook.
     */
    public function handleStatusCallback(array $payload): void
    {
        // Implementation for tracking delivery status
        // Called from webhook endpoint
    }
}
