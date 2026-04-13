<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    /**
     * GET /api/contacts
     * List all contacts with pagination.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Contact::query();

        // Search
        if ($request->has('search')) {
            $query = $query->search($request->input('search'));
        }

        // Filter by tag
        if ($request->has('tag')) {
            $query = $query->byTag($request->input('tag'));
        }

        // Filter by source
        if ($request->has('source')) {
            $query = $query->where('source', $request->input('source'));
        }

        // Sorting
        $sortBy = $request->input('sort_by', 'created_at');
        $sortDir = $request->input('sort_dir', 'desc');
        $query = $query->orderBy($sortBy, $sortDir);

        $contacts = $query->paginate($request->input('per_page', 15));

        return response()->json($contacts);
    }

    /**
     * POST /api/contacts
     * Create a new contact.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:contacts',
            'phone' => 'nullable|string',
            'mobile' => 'nullable|string',
            'address' => 'nullable|string',
            'city' => 'nullable|string',
            'state' => 'nullable|string',
            'zip_code' => 'nullable|string',
            'country' => 'nullable|string',
            'company' => 'nullable|string',
            'title' => 'nullable|string',
            'source' => 'nullable|string',
            'notes' => 'nullable|string',
            'tags' => 'nullable|array',
        ]);

        $contact = Contact::create($validated);

        return response()->json($contact, 201);
    }

    /**
     * GET /api/contacts/{id}
     * Get a single contact with relationships.
     */
    public function show(Contact $contact): JsonResponse
    {
        $contact->load(['leads', 'tasks', 'communications']);

        return response()->json($contact);
    }

    /**
     * PUT /api/contacts/{id}
     * Update a contact.
     */
    public function update(Request $request, Contact $contact): JsonResponse
    {
        $validated = $request->validate([
            'first_name' => 'string|max:255',
            'last_name' => 'string|max:255',
            'email' => 'email|unique:contacts,email,' . $contact->id,
            'phone' => 'nullable|string',
            'mobile' => 'nullable|string',
            'address' => 'nullable|string',
            'city' => 'nullable|string',
            'state' => 'nullable|string',
            'zip_code' => 'nullable|string',
            'country' => 'nullable|string',
            'company' => 'nullable|string',
            'title' => 'nullable|string',
            'source' => 'nullable|string',
            'notes' => 'nullable|string',
            'tags' => 'nullable|array',
        ]);

        $contact->update($validated);

        return response()->json($contact);
    }

    /**
     * DELETE /api/contacts/{id}
     * Delete a contact.
     */
    public function destroy(Contact $contact): JsonResponse
    {
        $contact->delete();

        return response()->json(['message' => 'Contact deleted successfully']);
    }

    /**
     * POST /api/contacts/bulk-import
     * Import contacts from CSV or array.
     */
    public function bulkImport(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'contacts' => 'required|array|min:1',
            'contacts.*.first_name' => 'required|string',
            'contacts.*.last_name' => 'required|string',
            'contacts.*.email' => 'nullable|email',
            'contacts.*.phone' => 'nullable|string',
        ]);

        $imported = [];
        foreach ($validated['contacts'] as $contactData) {
            $contactData['imported_at'] = now();
            $imported[] = Contact::firstOrCreate(
                ['email' => $contactData['email'] ?? null],
                $contactData
            );
        }

        return response()->json([
            'message' => count($imported) . ' contacts imported',
            'contacts' => $imported,
        ], 201);
    }
}
