<?php

namespace App\Services;

use SendGrid;
use SendGrid\Mail\Mail;

class EmailService
{
    private SendGrid $sendGrid;

    public function __construct()
    {
        $this->sendGrid = new SendGrid(config('services.sendgrid.secret'));
    }

    /**
     * Send an email via SendGrid.
     *
     * @param string $to Recipient email address
     * @param string $subject Email subject
     * @param string $body Email body (HTML)
     * @param array $metadata Additional metadata
     * @return array
     */
    public function sendEmail(string $to, string $subject, string $body, array $metadata = []): array
    {
        try {
            $email = new Mail();
            $email->setFrom(config('mail.from.address'), config('mail.from.name'));
            $email->setSubject($subject);
            $email->addTo($to);
            $email->addContent('text/html', $body);

            // Add custom headers for tracking
            if (isset($metadata['campaign_id'])) {
                $email->addCustomHeader('X-Campaign-ID', $metadata['campaign_id']);
            }

            $response = $this->sendGrid->send($email);

            return [
                'success' => true,
                'status_code' => $response->statusCode(),
                'external_id' => $response->headers()['X-Message-Id'] ?? null,
                'metadata' => [
                    'provider' => 'sendgrid',
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
     * Send a templated email via SendGrid.
     *
     * @param string $to Recipient email address
     * @param string $templateId SendGrid template ID
     * @param array $dynamicData Data for template substitution
     * @return array
     */
    public function sendTemplatedEmail(string $to, string $templateId, array $dynamicData = []): array
    {
        try {
            $email = new Mail();
            $email->setFrom(config('mail.from.address'), config('mail.from.name'));
            $email->addTo($to);
            $email->setTemplateId($templateId);

            foreach ($dynamicData as $key => $value) {
                $email->addDynamicTemplateData($key, $value);
            }

            $response = $this->sendGrid->send($email);

            return [
                'success' => true,
                'status_code' => $response->statusCode(),
                'external_id' => $response->headers()['X-Message-Id'] ?? null,
                'metadata' => [
                    'provider' => 'sendgrid',
                    'template_id' => $templateId,
                    'sent_at' => now()->toIso8601String(),
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
     * Track email opens via webhook.
     */
    public function handleOpenEvent(array $event): void
    {
        // Implementation for tracking opens
        // Called from webhook endpoint
    }

    /**
     * Track email clicks via webhook.
     */
    public function handleClickEvent(array $event): void
    {
        // Implementation for tracking clicks
        // Called from webhook endpoint
    }
}
