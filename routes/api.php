<?php

use App\Http\Controllers\API\ContactController;
use App\Http\Controllers\API\LeadController;
use App\Http\Controllers\API\TaskController;
use App\Http\Controllers\API\CommunicationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| RESTful API endpoints for the RE Nurture CRM
|--------------------------------------------------------------------------
*/

Route::prefix('api')->group(function () {
    // Contacts
    Route::apiResource('contacts', ContactController::class);
    Route::post('contacts/bulk-import', [ContactController::class, 'bulkImport']);
    Route::get('contacts/search', [ContactController::class, 'index']);

    // Leads
    Route::apiResource('leads', LeadController::class);
    Route::post('leads/{lead}/advance', [LeadController::class, 'advance']);
    Route::post('leads/{lead}/close', [LeadController::class, 'close']);
    Route::post('leads/{lead}/lose', [LeadController::class, 'lose']);
    Route::get('leads/pipeline/{pipelineId}/stats', [LeadController::class, 'pipelineStats']);

    // Tasks
    Route::apiResource('tasks', TaskController::class);
    Route::post('tasks/{task}/complete', [TaskController::class, 'complete']);
    Route::get('tasks/upcoming', [TaskController::class, 'upcoming']);
    Route::get('tasks/overdue', [TaskController::class, 'overdue']);

    // Communications
    Route::apiResource('communications', CommunicationController::class);
    Route::post('communications/email', [CommunicationController::class, 'sendEmail']);
    Route::post('communications/sms', [CommunicationController::class, 'sendSMS']);
    Route::get('communications/timeline/{contactId}', [CommunicationController::class, 'timeline']);

    // Webhooks (for email/SMS tracking)
    Route::post('webhooks/sendgrid', 'App\Http\Controllers\WebhookController@sendgrid');
    Route::post('webhooks/twilio', 'App\Http\Controllers\WebhookController@twilio');
    Route::post('webhooks/gmail', 'App\Http\Controllers\WebhookController@gmail');
});
