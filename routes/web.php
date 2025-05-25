<?php

use App\Http\Controllers\VotingStationController;
use App\Livewire\Admin\AppSettings;
use App\Livewire\Admin\CreateCandidate;
use App\Livewire\Admin\CreateElection;
use App\Livewire\Admin\CreateStudent;
use App\Livewire\Admin\DashboardStats;
use App\Livewire\Admin\EditCandidate;
use App\Livewire\Admin\EditElection;
use App\Livewire\Admin\EditStudent;
use App\Livewire\Admin\ElectionResultsDisplay;
use App\Livewire\Admin\ListCandidates;
use App\Livewire\Admin\ListElections;
use App\Livewire\Admin\ListStudent;
use App\Livewire\Voting\ElectionBallot;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
})->name('home');



Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', DashboardStats::class)->name('dashboard');
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');

    Route::get('students', ListStudent::class)->name('students.index');
    Route::get('students/create', CreateStudent::class)->name('students.create');
    Route::get('students/{student}/edit', EditStudent::class)->name('students.edit');

    Route::get('elections', ListElections::class)->name('elections.index');
    Route::get('elections/create', CreateElection::class)->name('elections.create');
    Route::get('/elections/{election}/edit', EditElection::class)->name('elections.edit'); 

     Route::get('/settings', AppSettings::class)->name('settings.index');

    Route::get('candidates', ListCandidates::class)->name('candidates.index');
    Route::get('candidates/create', CreateCandidate::class)->name('candidates.create');
    Route::get('/candidates/{candidate}/edit', EditCandidate::class)->name('candidates.edit');

    Route::get('/elections/{election}/results', ElectionResultsDisplay::class)->name('elections.results');
});

Route::get('/votar/{token}', [VotingStationController::class, 'showLoginScreen'])->name('voting.station.login');

Route::get('/votar/eleccion/{election}/tarjeton', ElectionBallot::class) 
    ->name('voting.ballot');

Route::get('/votacion/gracias/{election_token?}', function ($election_token = null) {
    $message = session('vote_success_message', '¡Gracias por participar!');    
    return view('voting.thankyou', ['message' => $message, 'election_token' => $election_token]);
})->name('voting.thankyou');

require __DIR__.'/auth.php';
