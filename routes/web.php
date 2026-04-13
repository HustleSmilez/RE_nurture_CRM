<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
| Livewire components and web pages
|--------------------------------------------------------------------------
*/

// Dashboard
Route::get('/', 'App\Http\Controllers\DashboardController@index')->name('dashboard');

// Contacts
Route::prefix('contacts')->group(function () {
    Route::get('/', 'App\Http\Controllers\ContactController@index')->name('contacts.index');
    Route::get('/{contact}', 'App\Http\Controllers\ContactController@show')->name('contacts.show');
    Route::get('/{contact}/edit', 'App\Http\Controllers\ContactController@edit')->name('contacts.edit');
});

// Leads
Route::prefix('leads')->group(function () {
    Route::get('/', 'App\Http\Controllers\LeadController@index')->name('leads.index');
    Route::get('/{lead}', 'App\Http\Controllers\LeadController@show')->name('leads.show');
    Route::get('/{lead}/edit', 'App\Http\Controllers\LeadController@edit')->name('leads.edit');
});

// Pipelines
Route::prefix('pipelines')->group(function () {
    Route::get('/', 'App\Http\Controllers\PipelineController@index')->name('pipelines.index');
    Route::get('/{pipeline}', 'App\Http\Controllers\PipelineController@show')->name('pipelines.show');
});

// Tasks
Route::prefix('tasks')->group(function () {
    Route::get('/', 'App\Http\Controllers\TaskController@index')->name('tasks.index');
    Route::get('/upcoming', 'App\Http\Controllers\TaskController@upcoming')->name('tasks.upcoming');
    Route::get('/overdue', 'App\Http\Controllers\TaskController@overdue')->name('tasks.overdue');
});

// Properties
Route::prefix('properties')->group(function () {
    Route::get('/', 'App\Http\Controllers\PropertyController@index')->name('properties.index');
    Route::get('/{property}', 'App\Http\Controllers\PropertyController@show')->name('properties.show');
});

// Reports/Analytics
Route::prefix('reports')->group(function () {
    Route::get('/', 'App\Http\Controllers\ReportController@index')->name('reports.index');
    Route::get('/pipeline', 'App\Http\Controllers\ReportController@pipeline')->name('reports.pipeline');
    Route::get('/performance', 'App\Http\Controllers\ReportController@performance')->name('reports.performance');
});
