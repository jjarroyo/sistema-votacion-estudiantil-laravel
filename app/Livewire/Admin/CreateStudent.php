<?php

namespace App\Livewire\Admin;

use App\Models\Student;
use Livewire\Component;

class CreateStudent extends Component
{
    public $student_identifier = '';
    public $full_name = '';
    public $grade = '';

    /**
     * Reglas de validación para el formulario de creación de estudiante.
     */
    protected function rules()
    {
        return [
            'student_identifier' => ['required', 'string', 'max:255', 'unique:students,student_identifier'],
            'full_name' => ['required', 'string', 'max:255'],
            'grade' => ['required', 'string', 'max:255'],
        ];
    }

    protected $messages = [
        'student_identifier.required' => 'El identificador del estudiante es obligatorio.',
        'student_identifier.unique' => 'Este identificador ya ha sido registrado.',
        'full_name.required' => 'El nombre completo es obligatorio.',
        'grade.required' => 'El grado es obligatorio.',
    ];

    /**
     * Valida solo el campo actualizado en tiempo real.
     */
    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    /**
     * Valida y guarda un nuevo estudiante en la base de datos.
     */
    public function saveStudent()
    {
        $validatedData = $this->validate();

        Student::create([
            'student_identifier' => $validatedData['student_identifier'],
            'full_name' => $validatedData['full_name'],
            'grade' => $validatedData['grade'],
            'is_active' => true,
        ]);

        session()->flash('message', 'Estudiante creado exitosamente.');

        return redirect()->route('students.index');
    }

    /**
     * Renderiza la vista de creación de estudiante.
     */
    public function render()
    {
        return view('livewire.admin.create-student')
               ->layout('components.layouts.app', ['header' => 'Añadir Nuevo Estudiante']);
    }
}