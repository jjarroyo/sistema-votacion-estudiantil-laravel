<?php

namespace App\Livewire\Admin;

use App\Models\Candidate;
use App\Models\Election;
use App\Models\Student;
use Livewire\Component;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Livewire\WithFileUploads;

class EditCandidate extends Component
{
    use WithFileUploads;
    public Candidate $candidate;

    public $election_id;
    public $student_id;
    public $proposal;
    public $photo;
    public $list_number;
    public $existing_photo_path;
    public $elections;
    public $students;

    /**
     * Inicializa el formulario con los datos del candidato y carga elecciones y estudiantes.
     */
    public function mount(Candidate $candidate)
    {
        $this->candidate = $candidate;
        $this->election_id = $candidate->election_id;
        $this->student_id = $candidate->student_id;
        $this->proposal = $candidate->proposal;
        $this->list_number = $candidate->list_number;
        $this->existing_photo_path = $candidate->photo_path;
        $this->elections = Election::orderBy('name', 'asc')->get();
        $this->students = Student::where('is_active', true)
                                 ->orderBy('full_name', 'asc')
                                 ->get();
    }

    /**
     * Reglas de validación para el formulario de edición de candidato.
     */
    protected function rules()
    {
        return [
            'election_id' => ['required', 'exists:elections,id'],
            'student_id' => [
                'required',
                'exists:students,id',
                Rule::unique('candidates')->where(function ($query) {
                    return $query->where('election_id', $this->election_id)
                                 ->where('student_id', $this->student_id);
                })->ignore($this->candidate->id),
            ],
            'proposal' => ['nullable', 'string', 'max:2000'],
            'photo' => ['nullable', 'image', 'max:2048'],
            'list_number' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('candidates')->where(function ($query) {
                    return $query->where('election_id', $this->election_id)
                                 ->where('list_number', $this->list_number);
                })->ignore($this->candidate->id),
            ],
        ];
    }

    protected $messages = [
        'election_id.required' => 'Debes seleccionar una elección.',
        'student_id.required' => 'Debes seleccionar un estudiante.',
        'student_id.unique' => 'Este estudiante ya está registrado como candidato para esta elección (o la combinación ya existe).',
        'list_number.unique' => 'Este número de lista ya está en uso para esta elección.',
        'proposal.max' => 'La propuesta no debe exceder los 2000 caracteres.',
    ];

    /**
     * Valida solo el campo actualizado en tiempo real.
     */
    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    /**
     * Valida y actualiza los datos del candidato en la base de datos.
     */
    public function updateCandidate()
    {
        $validatedData = $this->validate();

        if ($this->photo) {
            $validatedData['photo_path'] = $this->photo->store('candidate-photos', 'public');
        }

        if ($this->existing_photo_path && isset($validatedData['photo_path'])) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($this->existing_photo_path);
        }

        try {
            $this->candidate->update([
                'election_id' => $validatedData['election_id'],
                'student_id' => $validatedData['student_id'],
                'proposal' => $validatedData['proposal'],
                'photo_path' => $validatedData['photo_path'] ?? $this->existing_photo_path,
                'list_number' => $validatedData['list_number'],
            ]);

            session()->flash('message', 'Candidatura actualizada exitosamente.');
            return redirect()->route('candidates.index');

        } catch (\Exception $e) {
            Log::error('Error al actualizar candidatura: ' . $e->getMessage() . ' Data: ' . json_encode($validatedData));
            session()->flash('message_type', 'error');
            session()->flash('message', 'Ocurrió un error al actualizar la candidatura: ' . $e->getMessage());
        }
    }

    /**
     * Renderiza la vista de edición del candidato.
     */
    public function render()
    {
        $studentName = $this->candidate->student->full_name ?? 'Desconocido';
        $electionName = $this->candidate->election->name ?? 'Desconocida';
        $headerTitle = "Editar Candidatura: {$studentName} para {$electionName}";

        return view('livewire.admin.edit-candidate')
               ->layout('components.layouts.app', ['header' => $headerTitle]);
    }
}