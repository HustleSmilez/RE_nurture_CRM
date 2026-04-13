<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Communication;
use App\Services\EmailService;
use App\Services\SMSService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CommunicationController extends Controller
{
    public function __construct(
        private EmailService $emailService,
        private SMSService $smsService,
    ) {}

    /**
     * GET /api/communications
     * List communications for a contact.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Communication::query();

        if ($request->has('contact_id')) {
            $query = $query->where('contact_id', $request->input('contact_id'));
        }

        if ($request->has('type')) {
            $query = $query->byType($request->input('type'));
        }

        if ($request->has('status')) {
            $query = $query->where('status', $request->input('status'));
        }

        $communications = $query->orderBy('created_at', 'desc')
            ->paginate($request->input('per_page', 20));

        return response()->json($communications);
    }

    /**
     * POST /api/communications/email
     * Send an email to a contact.
     */
    public function sendEmail(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'contact_id' => 'required|exists:contacts,id',
            'lead_id' => 'nullable|exists:leads,id',
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
        ]);

        try {
            $contact = \App\Models\Contact::findOrFail($validated['contact_id']);

            $result = $this->emailService->sendEmail(
                $contact->email,
                $validated['subject'],
                $validated['body']
            );

            $communication = Communication::create([
                'contact_id' => $validated['contact_id'],
                'lead_id' => $validated['lead_id'] ?? null,
                'type' => Communication::TYPE_EMAIL,
                'subject' => $validated['subject'],
                'body' => $validated['body'],
                'status' => Communication::STATUS_SENT,
                'sent_at' => now(),
                'external_id' => $result['external_id'] ?? null,
                'metadata' => $result['metadata'] ?? null,
            ]);

            return response()->json($communication, 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to send email',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * POST /api/communications/sms
     * Send an SMS to a contact.
     */
    public function sendSMS(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'contact_id' => 'required|exists:contacts,id',
            'lead_id' => 'nullable|exists:leads,id',
            'body' => 'required|string|max:160',
        ]);

        try {
            $contact = \App\Models\Contact::findOrFail($validated['contact_id']);

            $result = $this->smsService->sendSMS(
                $contact->mobile ?? $contact->phone,
                $validated['body']
            );

            $communication = Communication::create([
                'contact_id' => $validated['contact_id'],
                'lead_id' => $validated['lead_id'] ?? null,
                'type' => Communication::TYPE_SMS,
                'body' => $validated['body'],
                'status' => Communication::STATUS_SENT,
                'sent_at' => now(),
                'external_id' => $result['external_id'] ?? null,
                'metadata' => $result['metadata'] ?? null,
            ]);

            return response()->json($communication, 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to send SMS',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * GET /api/communications/{id}
     * Get a single communication.
     */
    public function show(Communication $communication): JsonResponse
    {
        return response()->json($communication);
    }

    /**
     * PUT /api/communications/{id}
     * Update communication status (tracking).
     */
    public function update(Request $request, Communication $communication): JsonResponse
    {
        $validated = $request->validate([
            'status' => 'in:sent,delivered,opened,clicked,failed',
        ]);

        $communication->update($validated);

        return response()->json($communication);
    }

    /**
     * GET /api/communications/timeline/{contactId}
     * Get complete communication timeline for a contact.
     */
    public function timeline($contactId): JsonResponse
    {
        $timeline = Communication::where('contact_id', $contactId)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($comm) {
                return [
                    'id' => $comm->id,
                    'type' => $comm->type,
                    'subject' => $comm->subject,
                    'body' => substr($comm->body, 0, 100) . '...',
                    'status' => $comm->status,
                    'sent_at' => $comm->sent_at,
                    'opened_at' => $comm->opened_at,
                    'clicked_at' => $comm->clicked_at,
                ];
            });

        return response()->json($timeline);
    }
}
