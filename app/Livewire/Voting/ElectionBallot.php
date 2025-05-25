<?php

namespace App\Livewire\Voting;

use App\Models\Election;
use App\Models\Candidate;
use App\Models\RecordedVote;
use App\Models\StudentVote;
use App\Models\Student;
use Livewire\Component;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ElectionBallot extends Component
{
    public Election $election;
    public $candidates;
    public $studentId;
    public $selectedOption = null;
    public $errorMessage = '';
    public $showConfirmationModal = false;
    public $selectionMadeForConfirmation = '';

    /**
     * Inicializa el componente, valida sesión y carga candidatos.
     */
    public function mount(Election $election)
    {
        Log::info('ElectionBallot mounted with election ID: ' . $election->id);
        $this->election = $election;
        $this->studentId = Session::get('current_voting_student_id');

        if (!$this->studentId) {
            session()->flash('ballot_error', 'Error de sesión: Identificación de estudiante no encontrada.');
            Log::error('Student ID not found in session. Redirecting to login.');
            return redirect()->route('voting.station.login', ['token' => $this->election->voting_session_token ?? 'error_mount_no_student']);
        }

        if (Session::get('current_voting_election_id') != $this->election->id) {
            session()->flash('ballot_error', 'Error de sesión: Discrepancia en la elección.');
            Log::error('Election ID mismatch in session. Redirecting to login.');
            return redirect()->route('voting.station.login', ['token' => $this->election->voting_session_token ?? 'error_mount_election_mismatch']);
        }

        $hasVoted = StudentVote::where('student_id', $this->studentId)
                            ->where('election_id', $this->election->id)
                            ->exists();
        if ($hasVoted) {
            Log::info('Student has already voted. Redirecting to login.');    
            return redirect()->route('voting.station.login', ['token' => $this->election->voting_session_token ?? 'already_voted']);
        }

        if ($this->election->status !== 'active' || now()->lt($this->election->start_time) || now()->gt($this->election->end_time)) {
          
            session()->flash('ballot_error', 'Error: La elección no está activa o ha caducado.');
            return redirect()->route('voting.station.login', ['token' => $this->election->voting_session_token ?? 'not_active']);
        }

        $this->candidates = $this->election->candidates()->with('student')->get();
    }

    /**
     * Selecciona una opción de voto (candidato o voto en blanco).
     */
    public function selectOption($optionValue)
    {
        $this->selectedOption = $optionValue;
        $this->errorMessage = '';
    }

    /**
     * Prepara la confirmación del voto seleccionado.
     */
    public function confirmVote()
    {
        if (is_null($this->selectedOption)) {
            $this->errorMessage = 'Por favor, seleccione un candidato o la opción de voto en blanco.';
            return;
        }

        if ($this->selectedOption === 'blank_vote') {
            $this->selectionMadeForConfirmation = 'Voto en Blanco';
        } else {
            $selectedCandidate = $this->candidates->firstWhere('id', $this->selectedOption);
            $this->selectionMadeForConfirmation = $selectedCandidate ? $selectedCandidate->student->full_name : 'Candidato Desconocido';
        }
        
        $this->showConfirmationModal = true;
    }

    /**
     * Registra el voto en la base de datos y marca al estudiante como votante.
     */
    public function submitVote()
    {
        if (is_null($this->selectedOption)) {
            $this->errorMessage = 'No se ha seleccionado ninguna opción válida.';
            $this->showConfirmationModal = false;
            return;
        }

        $hasVoted = StudentVote::where('student_id', $this->studentId)
                               ->where('election_id', $this->election->id)
                               ->exists();
        if ($hasVoted) {
            session()->flash('ballot_error', 'Error: Ya se ha registrado un voto con esta sesión.');
            Session::forget(['current_voting_election_id', 'current_voting_student_id']);
            $this->showConfirmationModal = false;
            return redirect()->route('voting.station.login', ['token' => $this->election->voting_session_token ?? 'error_concurrent_vote']);
        }

        try {
            DB::transaction(function () {
                RecordedVote::create([
                    'election_id' => $this->election->id,
                    'candidate_id' => ($this->selectedOption !== 'blank_vote') ? $this->selectedOption : null,
                    'is_blank_vote' => ($this->selectedOption === 'blank_vote'),
                ]);

                StudentVote::create([
                    'student_id' => $this->studentId,
                    'election_id' => $this->election->id,
                    'voted_at' => now(),
                ]);
            });

            Session::forget(['current_voting_election_id', 'current_voting_student_id']);
            session()->flash('vote_success_message', '¡Gracias por tu voto! Tu voto ha sido registrado exitosamente.');
            return redirect()->route('voting.thankyou', ['election_token' => $this->election->voting_session_token ?? 'none']);

        } catch (\Exception $e) {
            $this->errorMessage = 'Ocurrió un error al registrar tu voto. Por favor, intenta de nuevo o contacta a un administrador.';
            $this->showConfirmationModal = false;
        }
    }

    /**
     * Cancela el modal de confirmación de voto.
     */
    public function cancelConfirmation()
    {
        $this->showConfirmationModal = false;
    }

    /**
     * Renderiza la vista de la boleta de votación.
     */
    public function render()
    {
        Log::info('Rendering ElectionBallot view for election ID: ' . $this->election->id);
        return view('livewire.voting.election-ballot')
               ->layout('components.layouts.voting-station');
    }
}