<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * GET /api/tasks
     * List tasks for a contact or lead.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Task::query();

        if ($request->has('contact_id')) {
            $query = $query->where('contact_id', $request->input('contact_id'));
        }

        if ($request->has('lead_id')) {
            $query = $query->where('lead_id', $request->input('lead_id'));
        }

        if ($request->has('status')) {
            $query = $query->where('status', $request->input('status'));
        }

        if ($request->has('priority')) {
            $query = $query->where('priority', $request->input('priority'));
        }

        $tasks = $query->orderBy('due_date', 'asc')
            ->orderBy('priority', 'desc')
            ->paginate($request->input('per_page', 20));

        return response()->json($tasks);
    }

    /**
     * POST /api/tasks
     * Create a new task.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'contact_id' => 'required|exists:contacts,id',
            'lead_id' => 'nullable|exists:leads,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'nullable|in:pending,in_progress,completed,cancelled',
            'priority' => 'nullable|in:low,medium,high,urgent',
            'due_date' => 'nullable|date',
            'due_time' => 'nullable|date_format:H:i',
            'reminder_at' => 'nullable|date_time',
        ]);

        $task = Task::create($validated);

        return response()->json($task, 201);
    }

    /**
     * GET /api/tasks/{id}
     * Get a single task.
     */
    public function show(Task $task): JsonResponse
    {
        $task->load(['contact', 'lead']);

        return response()->json($task);
    }

    /**
     * PUT /api/tasks/{id}
     * Update a task.
     */
    public function update(Request $request, Task $task): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'string|max:255',
            'description' => 'nullable|string',
            'status' => 'in:pending,in_progress,completed,cancelled',
            'priority' => 'in:low,medium,high,urgent',
            'due_date' => 'nullable|date',
            'due_time' => 'nullable|date_format:H:i',
            'reminder_at' => 'nullable|date_time',
        ]);

        $task->update($validated);

        return response()->json($task);
    }

    /**
     * DELETE /api/tasks/{id}
     * Delete a task.
     */
    public function destroy(Task $task): JsonResponse
    {
        $task->delete();

        return response()->json(['message' => 'Task deleted successfully']);
    }

    /**
     * POST /api/tasks/{id}/complete
     * Mark task as completed.
     */
    public function complete(Task $task): JsonResponse
    {
        $task->complete();

        return response()->json([
            'message' => 'Task marked as completed',
            'task' => $task->refresh(),
        ]);
    }

    /**
     * GET /api/tasks/upcoming
     * Get upcoming tasks due within next 7 days.
     */
    public function upcoming(): JsonResponse
    {
        $tasks = Task::upcoming()->get();

        return response()->json($tasks);
    }

    /**
     * GET /api/tasks/overdue
     * Get overdue tasks.
     */
    public function overdue(): JsonResponse
    {
        $tasks = Task::where('status', '!=', Task::STATUS_COMPLETED)
            ->whereDate('due_date', '<', now()->startOfDay())
            ->orderBy('due_date', 'asc')
            ->get();

        return response()->json($tasks);
    }
}
