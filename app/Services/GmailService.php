<?php

namespace App\Services;

use Google\Client;
use Google\Service\Gmail;

class GmailService
{
    private Gmail $gmail;
    private Client $client;

    public function __construct()
    {
        $this->client = new Client();
        $this->client->setApplicationName('RE Nurture CRM');

        // Load service account credentials
        $credentialsPath = config('services.gmail.service_account_json');
        if (file_exists($credentialsPath)) {
            $this->client->setAuthConfig($credentialsPath);
        }

        $this->client->addScope(Gmail::MAIL_GOOGLE_COM);
        $this->gmail = new Gmail($this->client);
    }

    /**
     * Send an email via Gmail API.
     *
     * @param string $to Recipient email
     * @param string $subject Email subject
     * @param string $body Email body
     * @return array
     */
    public function sendEmail(string $to, string $subject, string $body): array
    {
        try {
            $message = $this->createMessage($to, $subject, $body);
            $result = $this->gmail->users_messages->send('me', $message);

            return [
                'success' => true,
                'external_id' => $result->id,
                'metadata' => [
                    'provider' => 'gmail',
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
     * List emails from inbox with optional query.
     *
     * @param string $query Gmail search query
     * @param int $limit Max results
     * @return array
     */
    public function listEmails(string $query = '', int $limit = 10): array
    {
        try {
            $optParams = [
                'maxResults' => $limit,
            ];

            if ($query) {
                $optParams['q'] = $query;
            }

            $results = $this->gmail->users_messages->listUsersMessages('me', $optParams);
            $messages = [];

            foreach ($results->getMessages() as $msg) {
                $messages[] = $this->getMessageDetails($msg->id);
            }

            return [
                'success' => true,
                'messages' => $messages,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get detailed message content.
     *
     * @param string $messageId Gmail message ID
     * @return array
     */
    public function getMessageDetails(string $messageId): array
    {
        try {
            $message = $this->gmail->users_messages->get('me', $messageId);

            $headers = [];
            foreach ($message->getPayload()->getHeaders() as $header) {
                $headers[$header->getName()] = $header->getValue();
            }

            return [
                'id' => $message->getId(),
                'from' => $headers['From'] ?? null,
                'to' => $headers['To'] ?? null,
                'subject' => $headers['Subject'] ?? null,
                'date' => $headers['Date'] ?? null,
                'snippet' => $message->getSnippet(),
            ];
        } catch (\Exception $e) {
            return [
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Create a Gmail message object.
     */
    private function createMessage(string $to, string $subject, string $body): \Google\Service\Gmail\Message
    {
        $mime = "From: " . config('mail.from.address') . "\r\n";
        $mime .= "To: $to\r\n";
        $mime .= "Subject: $subject\r\n";
        $mime .= "MIME-Version: 1.0\r\n";
        $mime .= "Content-Type: text/html; charset=UTF-8\r\n";
        $mime .= "\r\n";
        $mime .= $body;

        $message = new \Google\Service\Gmail\Message();
        $message->setRaw(rtrim(strtr(base64_encode($mime), '+/', '-_'), '='));

        return $message;
    }

    /**
     * Sync emails with CRM (create Communications records).
     */
    public function syncEmailsWithCRM(int $maxResults = 50): array
    {
        $emails = $this->listEmails('', $maxResults);

        if (!$emails['success']) {
            return $emails;
        }

        $synced = 0;
        foreach ($emails['messages'] as $email) {
            // Find matching contact by email
            $contact = \App\Models\Contact::where('email', $email['from'] ?? null)->first();

            if ($contact) {
                \App\Models\Communication::updateOrCreate(
                    ['external_id' => $email['id']],
                    [
                        'contact_id' => $contact->id,
                        'type' => \App\Models\Communication::TYPE_EMAIL,
                        'subject' => $email['subject'],
                        'body' => $email['snippet'],
                        'status' => \App\Models\Communication::STATUS_DELIVERED,
                        'sent_at' => \Carbon\Carbon::parse($email['date']),
                    ]
                );
                $synced++;
            }
        }

        return [
            'success' => true,
            'synced' => $synced,
        ];
    }
}
