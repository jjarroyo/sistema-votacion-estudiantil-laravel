<?php

namespace App\Livewire\Admin;

use App\Jobs\ProcessStudentImportJob;
use App\Models\Student;
use App\Services\Excel\StudentExcelService;
use App\Util\Excel\Imports\StudentsImport;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class ListStudent extends Component
{
    use WithPagination;
    use WithFileUploads;

    public $search = '';
    protected $paginationTheme = 'tailwind';

    public $sortField = 'full_name';
    public $sortDirection = 'asc';

    protected $allowedSortFields = ['student_identifier', 'full_name', 'grade', 'is_active', 'created_at'];

    public $showImportModal = false;
    public $importFile; 
    public $currentImportSection = 'form'; 
    public $importProgress = 0; 
    public $importResults = [
        'inserted' => [],
        'updated' => [],
        'errors' => [],
        'summary' => [
            'total_rows' => 0,
            'inserted_count' => 0,
            'updated_count' => 0,
            'error_count' => 0,
        ]
    ];

    public $filePathToImport;
    public $lastProcessedRow = 0;
    public $rowsPerChunk = 50; 
   

    public $activeResultTab = 'inserted';

    /**
     * Reglas de validación para la importación de estudiantes.
     */
    protected function importRules() {
        return [
            'importFile' => ['required', 'file', 'mimes:xlsx,xls', 'max:10240'], 
        ];
    }

    protected $importMessages = [
        'importFile.required' => 'Por favor, selecciona un archivo.',
        'importFile.file' => 'El archivo seleccionado no es válido.',
        'importFile.mimes' => 'El archivo debe ser de tipo XLSX o XLS.',
        'importFile.max' => 'El archivo no debe exceder los 10MB.',
    ];

    /**
     * Abre el modal de importación y resetea el estado.
     */
    public function openImportModal()
    {
        $this->resetImportState();
        $this->showImportModal = true;
    }

    /**
     * Cierra el modal de importación y resetea el estado.
     */
    public function closeImportModal()
    {
        $this->showImportModal = false;
        $this->resetImportState();
    }

    /**
     * Resetea el estado de la importación.
     */
    public function resetImportState()
    {
        $this->importFile = null;
        $this->currentImportSection = 'form';
        $this->importProgress = 0;
        $this->importResults = [
            'inserted' => [],
            'updated' => [],
            'errors' => [],
            'summary' => [
                'total_rows' => 0,
                'inserted_count' => 0,
                'updated_count' => 0,
                'error_count' => 0,
            ]
        ];
        $this->activeResultTab = 'inserted';
        $this->resetValidation('importFile');
    }

    /**
     * Inicia el proceso de importación de estudiantes.
     */
    public function startImportProcess()
    {
        $this->validate($this->importRules(), $this->importMessages);
        $this->currentImportSection = 'progress';
        $this->importProgress = 0;
        $this->lastProcessedRow = 0;
          try {
             $filePath = $this->importFile->store('imports', 'local');
            $import = new StudentsImport(function($progressPercentage, $processedRows, $totalRowsEstimate) {
              $this->importProgress = $progressPercentage;
              Log::info("JOB_HANDLE: Progreso de importación: {$progressPercentage}% - Procesadas: {$processedRows} de {$totalRowsEstimate} filas.");
            });

            Excel::import($import, $filePath, 'local');

            $resultsForEvent = [
                'inserted' => $import->getInsertedRows(),
                'updated' => $import->getUpdatedRows(),
                'errors' => $import->getErrorRows(),
                'summary' => [
                    'total_rows' => $import->getTotalRows(), 
                    'inserted_count' => $import->getInsertedCount(),
                    'updated_count' => $import->getUpdatedCount(),
                    'error_count' => $import->getErrorCount(),
                ]
            ];

            $this->importResults = $resultsForEvent;
            $this->importProgress = 100;
            $this->currentImportSection = 'results';
            
            if(isset($eventPayload['hasError']) && $eventPayload['hasError'] === true) {
                session()->flash('message_type', 'error');
                session()->flash('message', 'La importación finalizó con errores generales.');
            } else {
                session()->flash('message', 'Proceso de importación completado.');
            }

    
        } catch (\Throwable $e) {
            Log::error("JOB_HANDLE: Excepción: " . $e->getMessage());
            $errorResults = [
                'inserted' => [], 'updated' => [],
                'errors' => [['row' => 'N/A', 'identifier' => 'JOB_EXCEPTION', 'error' => 'Error crítico en Job: ' . $e->getMessage()]],
                'summary' => ['total_rows' => 0, 'inserted_count' => 0, 'updated_count' => 0, 'error_count' => 1]
            ];
            
        }
    }


    /**
     * Cambia la pestaña activa de resultados de importación.
     */
    public function setActiveResultTab(string $tabName)
    {
        $this->activeResultTab = $tabName;
    }

    /**
     * Resetea la paginación al actualizar el campo de búsqueda.
     */
    public function updatingSearch()
    {
        $this->resetPage();
    }

    /**
     * Descarga la plantilla de estudiantes.
     */
    public function downloadStudentTemplate(StudentExcelService $studentExcelService)
    {
        return $studentExcelService->downloadTemplate();
    }

    /**
     * Cambia el estado activo/inactivo de un estudiante.
     */
    public function toggleStudentStatus(Student $student)
    {
        $student->is_active = !$student->is_active;
        $student->save();
        session()->flash('message', 'Estado del estudiante actualizado exitosamente.');
    }

    /**
     * Cambia el campo y dirección de ordenamiento de la tabla.
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
     * Renderiza la vista con la lista de estudiantes filtrados y ordenados.
     */
    public function render()
    {
        $query = Student::query()
           ->when($this->search, function ($query) {
                $query->where('full_name', 'like', '%' . $this->search . '%')
                    ->orWhere('student_identifier', 'like', '%' . $this->search . '%');
            });

            if (in_array($this->sortField, $this->allowedSortFields)) {
                $query->orderBy($this->sortField, $this->sortDirection);
            } else {
                $query->orderBy('full_name', 'asc');
            }
            
            $students = $query->paginate(10);

        return view('livewire.admin.list-student', [
            'students' => $students,
        ])->layout('components.layouts.app');
    }
}

