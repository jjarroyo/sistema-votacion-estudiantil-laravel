<?php

namespace App\Livewire\Admin;

use App\Models\Election;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Str;

class ListElections extends Component
{
    use WithPagination;

    public $search = '';
    public $sortField = 'start_time';
    public $sortDirection = 'desc';
    protected $paginationTheme = 'tailwind';

    public $generatedVotingLink = null;
    public $selectedElectionForLink = null;
    protected $allowedSortFields = ['name', 'start_time', 'end_time', 'status', 'created_at'];
    public $electionToEndId = null;
    public $electionToEndName = '';
    public $showEndElectionConfirmationModal = false;

    /**
     * Muestra el modal para generar el enlace de votación de una elección.
     * Solo permite para elecciones programadas o activas.
     */
    public function showGenerateLinkModal(Election $election)
    {
        if (!in_array($election->status, ['scheduled', 'active'])) {
            session()->flash('message_type', 'error');
            session()->flash('message', 'Solo se pueden generar enlaces para elecciones programadas o activas.');
            return;
        }
        $this->selectedElectionForLink = $election;
        $this->generateVotingLink($election->id);
    }

    /**
     * Genera y guarda el token de votación para la elección.
     * Actualiza el estado si corresponde y muestra el enlace generado.
     */
    public function generateVotingLink($electionId)
    {
        $election = Election::find($electionId);
        if (!$election || !in_array($election->status, ['scheduled', 'active'])) {
            session()->flash('message_type', 'error');
            session()->flash('message', 'No se pudo generar el enlace para esta elección.');
            return;
        }

        if ($election->status === 'scheduled' && now()->gte($election->start_time)) {
            $election->status = 'active';
        }

        $election->voting_session_token = Str::random(32);
        $election->token_generated_at = now();
        $election->save();

        $this->generatedVotingLink = route('voting.station.login', ['token' => $election->voting_session_token]);

        session()->flash('generated_link_message', 'Enlace de votación generado para "' . $election->name . '". Por favor, cópielo y úselo en un dispositivo/navegador separado para la estación de votación. El enlace es:');
        session()->flash('generated_link', $this->generatedVotingLink);
    }

    /**
     * Cambia el campo y dirección de ordenamiento de la tabla.
     * Resetea la página al cambiar el orden.
     */
    public function sortBy(string $field)
    {
        if (!in_array($field, $this->allowedSortFields)) {
            return;
        }

        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
        $this->resetPage();
    }

    /**
     * Resetea la paginación al actualizar el campo de búsqueda.
     */
    public function updatingSearch()
    {
        $this->resetPage();
    }

    /**
     * Prepara y muestra el modal de confirmación para finalizar una elección activa.
     */
    public function confirmEndElection($electionId)
    {
        $election = Election::find($electionId);
        if ($election && $election->status === 'active') {
            $this->electionToEndId = $election->id;
            $this->electionToEndName = $election->name;
            $this->showEndElectionConfirmationModal = true;
        } else {
            session()->flash('message_type', 'error');
            session()->flash('message', 'Esta elección no está activa o no se encontró.');
        }
    }

    /**
     * Finaliza la elección activa seleccionada y actualiza su estado.
     * Cierra el modal de confirmación.
     */
    public function endElection()
    {
        $election = Election::find($this->electionToEndId);

        if ($election && $election->status === 'active') {
            $election->status = 'completed';
            if (now()->lt($election->end_time) || is_null($election->end_time)) {
                $election->end_time = now();
            }
            $election->save();

            session()->flash('message', 'La elección "' . $election->name . '" ha sido finalizada exitosamente.');
        } else {
            session()->flash('message_type', 'error');
            session()->flash('message', 'No se pudo finalizar la elección. Puede que ya no esté activa o no se haya encontrado.');
        }
        $this->cancelEndElectionConfirmation();
    }

    /**
     * Cancela y resetea las propiedades del modal de confirmación para finalizar elección.
     */
    public function cancelEndElectionConfirmation()
    {
        $this->reset(['electionToEndId', 'electionToEndName', 'showEndElectionConfirmationModal']);
    }

    /**
     * Renderiza la vista con la lista de elecciones filtradas y ordenadas.
     */
    public function render()
    {
        $query = Election::query()
            ->where(function ($q) {
                if (!empty($this->search)) {
                    $searchTerm = '%' . $this->search . '%';
                    $q->where('name', 'like', $searchTerm)
                      ->orWhere('description', 'like', $searchTerm);
                }
            });

        if (in_array($this->sortField, $this->allowedSortFields)) {
            $query->orderBy($this->sortField, $this->sortDirection);
        } else {
            $query->orderBy('start_time', 'desc');
        }

        $elections = $query->paginate(10);

        return view('livewire.admin.list-elections', [
            'elections' => $elections,
        ])->layout('components.layouts.app', ['header' => 'Gestión de Elecciones']);
    }
}