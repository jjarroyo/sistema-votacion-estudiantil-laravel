<?php

namespace App\Livewire\Admin;

use App\Models\Candidate;
use App\Models\Election;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ListCandidates extends Component
{
    use WithPagination;

    public $searchStudent = '';
    public $selectedElectionId = '';
    public $elections;

    public $sortField = 'created_at';
    public $sortDirection = 'desc';
    protected $paginationTheme = 'tailwind';

    protected $allowedSortFields = ['list_number', 'created_at'];

    /**
     * Inicializa el componente cargando las elecciones disponibles para el filtro.
     */
    public function mount()
    {
        $this->elections = Election::whereIn('status', ['scheduled', 'active', 'completed'])
                                    ->orderBy('name', 'asc')
                                    ->get();
    }

    /**
     * Elimina un candidato y su foto si existe, mostrando mensajes según el resultado.
     */
    public function deleteCandidate($candidateId)
    {
        try {
            $candidate = Candidate::find($candidateId);

            if ($candidate) {
                if ($candidate->photo_path) {
                    if (Storage::disk('public')->exists($candidate->photo_path)) {
                        Storage::disk('public')->delete($candidate->photo_path);
                    }
                }
                $candidate->delete();
                session()->flash('message', 'Candidatura eliminada exitosamente.');
            } else {
                session()->flash('message_type', 'error');
                session()->flash('message', 'No se encontró la candidatura para eliminar.');
            }
        } catch (\Exception $e) {
            session()->flash('message_type', 'error');
            session()->flash('message', 'Ocurrió un error al eliminar la candidatura.');
        }
    }

    /**
     * Cambia el campo y dirección de ordenamiento de la tabla de candidatos.
     */
    public function sortBy(string $field)
    {
        if (!in_array($field, $this->allowedSortFields)) {
            if ($field === 'student_name') {
                 $this->sortField = 'student_name_placeholder';
                 $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
            } elseif ($field === 'election_name') {
                 $this->sortField = 'election_name_placeholder';
                 $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
            } else {
                return;
            }
        } else {
            if ($this->sortField === $field) {
                $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
            } else {
                $this->sortField = $field;
                $this->sortDirection = 'asc';
            }
        }
        $this->resetPage();
    }

    /**
     * Resetea la paginación al actualizar el campo de búsqueda de estudiante.
     */
    public function updatingSearchStudent()
    {
        $this->resetPage();
    }

    /**
     * Resetea la paginación al cambiar la elección seleccionada.
     */
    public function updatingSelectedElectionId()
    {
        $this->resetPage();
    }

    /**
     * Renderiza la vista con la lista de candidatos filtrados, ordenados y paginados.
     */
    public function render()
    {
        $query = Candidate::query()
            ->with(['student', 'election']);

        if (!empty($this->searchStudent)) {
            $query->whereHas('student', function ($q) {
                $q->where('full_name', 'like', '%' . $this->searchStudent . '%');
            });
        }

        if (!empty($this->selectedElectionId)) {
            $query->where('election_id', $this->selectedElectionId);
        }

        if ($this->sortField === 'student_name_placeholder') {
            $query->join('students', 'candidates.student_id', '=', 'students.id')
                  ->orderBy('students.full_name', $this->sortDirection)
                  ->select('candidates.*');
        } elseif ($this->sortField === 'election_name_placeholder') {
            $query->join('elections', 'candidates.election_id', '=', 'elections.id')
                  ->orderBy('elections.name', $this->sortDirection)
                  ->select('candidates.*');
        } elseif (in_array($this->sortField, $this->allowedSortFields)) {
            $query->orderBy($this->sortField, $this->sortDirection);
        } else {
            $query->orderBy('created_at', 'desc');
        }
        
        $candidates = $query->paginate(10);

        return view('livewire.admin.list-candidates', [
            'candidates' => $candidates,
        ])->layout('components.layouts.app', ['header' => 'Gestión de Candidatos']);
    }
}