<?php

namespace App\Livewire\Admin;

use App\Models\Student;
use Livewire\Component;
use Illuminate\Validation\Rule;

class EditStudent extends Component
{
    public Student $student;

    public $student_identifier;
    public $full_name;
    public $grade;
    public $is_active;
    public $search = '';

    /**
     * Inicializa el formulario con los datos del estudiante a editar.
     */
    public function mount(Student $student)
    {
        $this->student = $student;
        $this->student_identifier = $student->student_identifier;
        $this->full_name = $student->full_name;
        $this->grade = $student->grade;
        $this->is_active = $student->is_active;
    }

    /**
     * Define las reglas de validación para el formulario de edición.
     */
    protected function rules()
    {
        return [
            'student_identifier' => [
                'required',
                'string',
                'max:255',
                Rule::unique('students', 'student_identifier')->ignore($this->student->id),
            ],
            'full_name' => ['required', 'string', 'max:255'],
            'grade' => ['required', 'string', 'max:255'],
            'is_active' => ['required', 'boolean'],
        ];
    }

    protected $messages = [
        'student_identifier.required' => 'El identificador del estudiante es obligatorio.',
        'student_identifier.unique' => 'Este identificador ya ha sido registrado para otro estudiante.',
        'full_name.required' => 'El nombre completo es obligatorio.',
        'grade.required' => 'El grado es obligatorio.',
        'is_active.required' => 'El estado es obligatorio.',
    ];

    /**
     * Valida solo el campo actualizado en tiempo real.
     */
    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    /**
     * Valida y actualiza los datos del estudiante en la base de datos.
     */
    public function updateStudent()
    {
        $validatedData = $this->validate();

        $this->student->update([
            'student_identifier' => $validatedData['student_identifier'],
            'full_name' => $validatedData['full_name'],
            'grade' => $validatedData['grade'],
            'is_active' => $validatedData['is_active'],
        ]);

        session()->flash('message', 'Estudiante actualizado exitosamente.');

        return redirect()->route('students.index');
    }

    /**
     * Renderiza la vista de edición del estudiante.
     */
    public function render()
    {
        return view('livewire.admin.edit-student')
            ->layout('components.layouts.app', ['header' => 'Editar Estudiante: ' . $this->student->full_name]);
    }
}