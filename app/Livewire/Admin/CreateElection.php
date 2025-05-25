<?php

namespace App\Livewire\Admin;

use App\Models\Election;
use Livewire\Component;
use Illuminate\Support\Facades\Log;

class CreateElection extends Component
{
    public $name = '';
    public $description = '';
    public $start_time;
    public $end_time;
    public $status = 'scheduled';

    // Opciones para el estado de la elección
    public $statusOptions = [
        'scheduled' => 'Programada',
        'active' => 'Activa',
        'completed' => 'Completada',
        'archived' => 'Archivada',
    ];

    /**
     * Reglas de validación para el formulario de creación de elección.
     */
    protected function rules()
    {
        return [
            'name' => ['required', 'string', 'max:255', 'unique:elections,name'],
            'description' => ['nullable', 'string', 'max:1000'],
            'start_time' => ['required', 'date_format:Y-m-d\TH:i'],
            'end_time' => ['required', 'date_format:Y-m-d\TH:i', 'after:start_time'],
            'status' => ['required', 'in:' . implode(',', array_keys($this->statusOptions))],
        ];
    }

    protected $messages = [
        'name.required' => 'El nombre de la elección es obligatorio.',
        'name.unique' => 'Ya existe una elección con este nombre.',
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
     * Valida y guarda una nueva elección en la base de datos.
     */
    public function saveElection()
    {
        $validatedData = $this->validate();

        try {
            Election::create([
                'name' => $validatedData['name'],
                'description' => $validatedData['description'],
                'start_time' => $validatedData['start_time'],
                'end_time' => $validatedData['end_time'],
                'status' => $validatedData['status'],
            ]);

            session()->flash('message', 'Elección creada exitosamente.');
            return redirect()->route('elections.index');

        } catch (\Exception $e) {
            Log::error('Error al crear elección: ' . $e->getMessage());
            session()->flash('message_type', 'error');
            session()->flash('message', 'Ocurrió un error al crear la elección.');
        }
    }

    /**
     * Renderiza la vista de creación de elección.
     */
    public function render()
    {
        return view('livewire.admin.create-election')
               ->layout('components.layouts.app', ['header' => 'Crear Nueva Elección']);
    }
}