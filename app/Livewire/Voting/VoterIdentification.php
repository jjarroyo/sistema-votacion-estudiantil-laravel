<?php
namespace App\Livewire\Voting;

use App\Models\Student;
use App\Models\Election;
use App\Models\StudentVote;
use Livewire\Component;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

class VoterIdentification extends Component
{
    public $electionId;
    public $electionName;
    public $student_identifier_input = '';
    public $errorMessage = '';
    public $successMessage = '';

    /**
     * Inicializa el componente y verifica la sesión de la elección.
     */
    public function mount($electionId, $electionName)
    {
        $this->electionId = $electionId;
        $this->electionName = $electionName;

        if (Session::get('current_voting_election_id') != $this->electionId) {
            return redirect('/')->withErrors(['session_error' => 'Error de sesión de votación.']);
        }
    }

    /**
     * Verifica el identificador del estudiante y su derecho a votar.
     */
    public function verifyStudent()
    {
        $this->reset(['errorMessage', 'successMessage']);
        $validatedData = $this->validate([
            'student_identifier_input' => 'required|string|max:50',
        ]);

        $student = Student::where('student_identifier', $validatedData['student_identifier_input'])
                          ->where('is_active', true)
                          ->first();

        if (!$student) {
            $this->errorMessage = 'El identificador de estudiante no existe o el estudiante no está activo.';
            return;
        }

        $hasVoted = StudentVote::where('student_id', $student->id)
                               ->where('election_id', $this->electionId)
                               ->exists();
        if ($hasVoted) {
            $this->errorMessage = 'Este estudiante ya ha registrado un voto para esta elección.';
            $this->student_identifier_input = '';
            return;
        }

        Session::put('current_voting_student_id', $student->id);
        Log::info("Votante verificado: Estudiante ID {$student->id} para Elección ID {$this->electionId}");

        return redirect()->route('voting.ballot', ['election' => $this->electionId]);
    }

    /**
     * Renderiza la vista de identificación del votante.
     */
    public function render()
    {
        return view('livewire.voting.voter-identification');
    }
}