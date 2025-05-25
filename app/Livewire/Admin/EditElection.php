<?php

namespace App\Livewire\Admin;

use App\Models\Election;
use Livewire\Component;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;

class EditElection extends Component
{
    public Election $election;

    public $name;
    public $description;
    public $start_time;
    public $end_time;
    public $status;

    public $statusOptions = [
        'scheduled' => 'Programada',
        'active' => 'Activa',
        'completed' => 'Completada',
        'archived' => 'Archivada',
    ];

    /**
     * Inicializa el formulario con los datos de la elección a editar.
     */
    public function mount(Election $election)
    {
        $this->election = $election;
        $this->name = $election->name;
        $this->description = $election->description;
        $this->start_time = $election->start_time ? $election->start_time->format('Y-m-d\TH:i') : null;
        $this->end_time = $election->end_time ? $election->end_time->format('Y-m-d\TH:i') : null;
        $this->status = $election->status;
    }

    /**
     * Reglas de validación para el formulario de edición de elección.
     */
    protected function rules()
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('elections', 'name')->ignore($this->election->id),
            ],
            'description' => ['nullable', 'string', 'max:1000'],
            'start_time' => ['required', 'date_format:Y-m-d\TH:i'],
            'end_time' => ['required', 'date_format:Y-m-d\TH:i', 'after:start_time'],
            'status' => ['required', 'in:' . implode(',', array_keys($this->statusOptions))],
        ];
    }

    protected $messages = [
        'name.required' => 'El nombre de la elección es obligatorio.',
        'name.unique' => 'Ya existe otra elección con este nombre.',
        'start_time.required' => 'La fecha y hora de inicio son obligatorias.',
        'start_time.date_format' => 'El formato de fecha y hora de inicio no es válido.',
        'end_time.required' => 'La fecha y hora de finalización son obligatorias.',
        'end_time.date_format' => 'El formato de fecha y hora de fin no es válido.',
        'end_time.after' => 'La fecha de finalización debe ser posterior a la fecha de inicio.',
        'status.required' => 'El estado de la elección es obligatorio.',
        'status.in' => 'El estado seleccionado no es válido.',
    ];

    /**
     * Valida solo el campo actualizado en tiempo real.
     */
    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    /**
     * Valida y actualiza los datos de la elección en la base de datos.
     */
    public function updateElection()
    {
        $validatedData = $this->validate();

        try {
            $this->election->update([
                'name' => $validatedData['name'],
                'description' => $validatedData['description'],
                'start_time' => $validatedData['start_time'],
                'end_time' => $validatedData['end_time'],
                'status' => $validatedData['status'],
            ]);

            session()->flash('message', 'Elección actualizada exitosamente.');
            return redirect()->route('elections.index');

        } catch (\Exception $e) {
            session()->flash('message_type', 'error');
            session()->flash('message', 'Ocurrió un error al actualizar la elección.');
        }
    }

    /**
     * Renderiza la vista de edición de la elección.
     */
    public function render()
    {
        return view('livewire.admin.edit-election')
               ->layout('components.layouts.app', ['header' => 'Editar Elección: ' . $this->election->name]);
    }
}