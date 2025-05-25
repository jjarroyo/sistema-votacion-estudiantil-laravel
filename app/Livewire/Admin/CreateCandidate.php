<?php

namespace App\Livewire\Admin;

use App\Models\Candidate;
use App\Models\Election;
use App\Models\Student;
use Livewire\Component;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Livewire\WithFileUploads;

class CreateCandidate extends Component
{
    use WithFileUploads;

    public $election_id = '';
    public $student_id = '';
    public $proposal = '';
    public $photo = '';
    public $list_number = '';

    public $elections;
    public $students;

    /**
     * Inicializa el formulario cargando elecciones y estudiantes activos.
     */
    public function mount()
    {
        $this->elections = Election::whereIn('status', ['scheduled', 'active'])
                                    ->orderBy('name', 'asc')
                                    ->get();
        $this->students = Student::where('is_active', true)
                                 ->orderBy('full_name', 'asc')
                                 ->get();
    }

    /**
     * Reglas de validación para el formulario de creación de candidato.
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
                })
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
                })
            ],
        ];
    }

    protected $messages = [
        'election_id.required' => 'Debes seleccionar una elección.',
        'student_id.required' => 'Debes seleccionar un estudiante.',
        'student_id.unique' => 'Este estudiante ya está registrado como candidato para esta elección.',
        'proposal.max' => 'La propuesta no debe exceder los 2000 caracteres.',
        'photo.image' => 'El archivo debe ser una imagen.',
        'photo.max' => 'La imagen no debe pesar más de 2MB.',
        'list_number.unique' => 'Este número de lista ya está en uso para esta elección.',
    ];

    /**
     * Valida solo el campo actualizado en tiempo real.
     */
    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    /**
     * Valida solo la imagen al actualizar el campo photo.
     */
    public function updatedPhoto()
    {
        $this->validateOnly('photo');
    }

    /**
     * Valida y guarda un nuevo candidato en la base de datos.
     */
    public function saveCandidate()
    {
        $validatedData = $this->validate();
        $finalPhotoPath = null;

        try {
            if ($this->photo) {
                $finalPhotoPath = $this->photo->store('candidate-photos', 'public');
            }

            Candidate::create([
                'election_id' => $validatedData['election_id'],
                'student_id' => $validatedData['student_id'],
                'proposal' => $validatedData['proposal'],
                'photo_path' => $finalPhotoPath,
                'list_number' => $validatedData['list_number'],
            ]);

            session()->flash('message', 'Candidato registrado exitosamente.');
            return redirect()->route('candidates.index');

        } catch (\Exception $e) {
            Log::error('Error al registrar candidato: ' . $e->getMessage());
            if ($finalPhotoPath && \Illuminate\Support\Facades\Storage::disk('public')->exists($finalPhotoPath)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($finalPhotoPath);
            }
            session()->flash('message_type', 'error');
            session()->flash('message', 'Ocurrió un error al registrar el candidato: ' . $e->getMessage());
        }
    }

    /**
     * Renderiza la vista de creación de candidato.
     */
    public function render()
    {
        return view('livewire.admin.create-candidate')
               ->layout('components.layouts.app', ['header' => 'Registrar Nuevo Candidato']);
    }
}