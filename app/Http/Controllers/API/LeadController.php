<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LeadController extends Controller
{
    /**
     * GET /api/leads
     * List all leads with filtering.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Lead::with(['contact', 'pipeline', 'tasks']);

        // Filter by status
        if ($request->has('status')) {
            $query = $query->where('status', $request->input('status'));
        }

        // Filter by pipeline
        if ($request->has('pipeline_id')) {
            $query = $query->where('pipeline_id', $request->input('pipeline_id'));
        }

        // Filter by value range
        if ($request->has('min_value') && $request->has('max_value')) {
            $query = $query->whereBetween('value', [
                $request->input('min_value'),
                $request->input('max_value'),
            ]);
        }

        // Sort by value
        $query = $query->orderBy($request->input('sort_by', 'estimated_close_date'), 'asc');

        $leads = $query->paginate($request->input('per_page', 15));

        return response()->json($leads);
    }

    /**
     * POST /api/leads
     * Create a new lead.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'contact_id' => 'required|exists:contacts,id',
            'pipeline_id' => 'nullable|exists:pipelines,id',
            'status' => 'nullable|in:new,contacted,qualified,proposal,negotiation,closed,lost',
            'value' => 'nullable|numeric|min:0',
            'property_interest' => 'nullable|string',
            'estimated_close_date' => 'nullable|date',
            'source' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $lead = Lead::create($validated);
        $lead->load(['contact', 'pipeline']);

        return response()->json($lead, 201);
    }

    /**
     * GET /api/leads/{id}
     * Get a single lead.
     */
    public function show(Lead $lead): JsonResponse
    {
        $lead->load(['contact', 'pipeline', 'tasks', 'communications']);

        return response()->json($lead);
    }

    /**
     * PUT /api/leads/{id}
     * Update a lead.
     */
    public function update(Request $request, Lead $lead): JsonResponse
    {
        $validated = $request->validate([
            'pipeline_id' => 'nullable|exists:pipelines,id',
            'status' => 'in:new,contacted,qualified,proposal,negotiation,closed,lost',
            'value' => 'nullable|numeric|min:0',
            'property_interest' => 'nullable|string',
            'estimated_close_date' => 'nullable|date',
            'source' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        if ($request->has('status')) {
            $validated['last_contacted_at'] = now();
        }

        $lead->update($validated);

        return response()->json($lead);
    }

    /**
     * DELETE /api/leads/{id}
     * Delete a lead.
     */
    public function destroy(Lead $lead): JsonResponse
    {
        $lead->delete();

        return response()->json(['message' => 'Lead deleted successfully']);
    }

    /**
     * POST /api/leads/{id}/advance
     * Move lead to next stage.
     */
    public function advance(Lead $lead): JsonResponse
    {
        if ($lead->advance()) {
            return response()->json([
                'message' => 'Lead advanced to ' . $lead->status,
                'lead' => $lead->refresh(),
            ]);
        }

        return response()->json(['message' => 'Lead cannot be advanced'], 400);
    }

    /**
     * POST /api/leads/{id}/close
     * Mark lead as closed/won.
     */
    public function close(Lead $lead): JsonResponse
    {
        $lead->markAsClosed();

        return response()->json([
            'message' => 'Lead marked as closed',
            'lead' => $lead->refresh(),
        ]);
    }

    /**
     * POST /api/leads/{id}/lose
     * Mark lead as lost.
     */
    public function lose(Request $request, Lead $lead): JsonResponse
    {
        $validated = $request->validate([
            'reason' => 'nullable|string',
        ]);

        $lead->markAsLost($validated['reason'] ?? null);

        return response()->json([
            'message' => 'Lead marked as lost',
            'lead' => $lead->refresh(),
        ]);
    }

    /**
     * GET /api/leads/pipeline/{pipelineId}/stats
     * Get pipeline statistics.
     */
    public function pipelineStats($pipelineId): JsonResponse
    {
        $pipeline = \App\Models\Pipeline::findOrFail($pipelineId);
        $stats = $pipeline->getStats();

        return response()->json($stats);
    }
}
