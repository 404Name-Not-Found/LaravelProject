<?php

use App\Http\Controllers\ContactController;
use App\Http\Controllers\DonationController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\EventRegistrationController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

// @ai-start: landing page route for GenevaTrac application
Route::get('/', [HomeController::class, 'index'])->name('home');
// @ai-end

// @ai-start: web routes for people listing and detail view
Route::get('/people', [ContactController::class, 'index'])->name('people.index');
Route::get('/people/{contact}', [ContactController::class, 'show'])->name('people.show');

Route::get('/people/{contact}/donations/create', [DonationController::class, 'create'])->name('people.donations.create');
Route::post('/people/{contact}/donations', [DonationController::class, 'store'])->name('people.donations.store');
// @ai-end

// @ai-start: web routes for events directory, event detail view, and event registration workflow
Route::get('/events', [EventController::class, 'index'])->name('events.index');
Route::get('/events/{event}', [EventController::class, 'show'])->name('events.show');

Route::get('/people/{contact}/registrations/create', [EventRegistrationController::class, 'create'])->name('people.registrations.create');
Route::post('/people/{contact}/registrations', [EventRegistrationController::class, 'store'])->name('people.registrations.store');
// @ai-end

// @ai-start: web routes for donations directory listing
Route::get('/donations', [DonationController::class, 'index'])->name('donations.index');
// @ai-end
