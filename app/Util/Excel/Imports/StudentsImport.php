<?php

namespace App\Util\Excel\Imports;

use App\Models\Student;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Validators\Failure;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Concerns\RemembersRowNumber;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeSheet;
use Throwable;

class StudentsImport implements 
    ToModel, 
    WithHeadingRow, 
    WithValidation, 
    SkipsOnError,
    SkipsOnFailure,
    WithEvents
     
{
    use Importable, SkipsErrors, SkipsFailures, RemembersRowNumber,RegistersEventListeners;

    private $userId;
    private $progressCallback;
    private $totalRowsInSheet = 0;
    private $insertedRows = [];
    private $updatedRows = [];
    private $errorRows = [];
    private $totalRows = 0;
    private $currentRowNumber = 1;

    /**
     * Constructor para inicializar el importador con el usuario y callback de progreso.
     */
    public function __construct(callable $progressCallback = null)
    {
        $this->progressCallback = $progressCallback;
    }


    public function registerEvents(): array
    {
        return [
            BeforeSheet::class => function(BeforeSheet $event) {
                $this->totalRowsInSheet = $event->sheet->getDelegate()->getHighestRow();
                // Si tienes una fila de cabecera, resta 1 para obtener solo filas de datos
                if ($this instanceof WithHeadingRow) {
                    $this->totalRowsInSheet = $this->totalRowsInSheet - 1;
                }
                Log::info("StudentsImport: Total de filas detectadas en la hoja (datos): " . $this->totalRowsInSheet);
            },
        ];
    }
    
    /**
     * Procesa cada fila del Excel, insertando o actualizando estudiantes.
     */
    public function model(array $row)
    {
        $this->totalRows++;
        $this->currentRowNumber++;

    
        if ($this->progressCallback) {
            $progressPercentage = 0;
            if ($this->totalRowsInSheet > 0) {
                $progressPercentage = min(98, intval(($this->totalRows / $this->totalRowsInSheet) * 100));
            } else {
                // Fallback si por alguna razón no se pudo obtener el total,
                // aunque con BeforeSheet deberías tenerlo.
                // Muestra un progreso basado en bloques de filas.
                 $progressPercentage = min(98, intval($this->totalRows / 5));
            }

            if ($this->totalRows === 1 || $this->totalRows % 10 === 0 || $this->totalRows === $this->totalRowsInSheet) { // Actualiza al final también
                Log::info("StudentsImport: Enviando progreso. Filas procesadas: {$this->totalRows} de {$this->totalRowsInSheet}, Porcentaje: {$progressPercentage}%");
                call_user_func(
                    $this->progressCallback,
                    $progressPercentage,
                    $this->totalRows,
                    $this->totalRowsInSheet
                );
                sleep(1); // Simula un pequeño retraso para evitar sobrecargar el callback
            }
        }
       
        $studentIdentifier = $row['identificador'] ?? null;

        $student = Student::where('student_identifier', $studentIdentifier)->first();

        if ($student) {
            $student->full_name = $row['nombre_completo'] ?? $student->full_name;
            $student->grade = $row['grado'] ?? $student->grade;
            if ($student->isDirty()) {
                $student->save();
                $this->updatedRows[] = [
                    'row' => $this->currentRowNumber,
                     'identifier' => $student->student_identifier,
                    'name' => $student->full_name,
                    'grade' => $student->grade,
                    'details' => "Actualizado"
                ];
            }
        } else {
            $newStudent = Student::create([
                'student_identifier' => $studentIdentifier,
                'full_name'          => $row['nombre_completo'] ?? null,
                'grade'              => $row['grado'] ?? null,
                'is_active'          => true,
            ]);
            $this->insertedRows[] = [
                'row' => $this->currentRowNumber,
                'identifier' => $newStudent->student_identifier,
                'name' => $newStudent->full_name,
                'grade' => $newStudent->grade,
                'details' => "Insertado"
            ];
        }
        
        return null;
    }

    /**
     * Reglas de validación para cada fila del Excel.
     */
    public function rules(): array
    {
        return [
            'identificador' => ['required'],
            'nombre_completo' => ['required', 'string', 'max:255'],
            'grado' => ['required', 'string', 'max:255'],
        ];
    }

    /**
     * Mensajes personalizados de validación.
     */
    public function customValidationMessages()
    {
        return [
            'identificador.required' => 'El identificador es obligatorio en la fila :row.',
            'nombre_completo.required' => 'El nombre completo es obligatorio en la fila :row.',
            'grado.required' => 'El grado es obligatorio en la fila :row.',
        ];
    }
    
    /**
     * Maneja errores de formato del archivo Excel.
     */
    public function onError(Throwable $e)
    {
        $this->errorRows[] = [
            'row' => 'N/A',
            'identifier' => 'FORMATO',
            'error' => 'Error de formato en el archivo: ' . $e->getMessage()
        ];
        Log::error("Error de formato durante importación de estudiantes: " . $e->getMessage());
    }

    /**
     * Maneja errores de validación por fila.
     */
    public function onFailure(Failure ...$failures)
    {
        foreach ($failures as $failure) {
            $this->errorRows[] = [
                'row' => $failure->row(),
                'attribute' => $failure->attribute(), 
                'error' => implode(', ', $failure->errors()),
            ];
        }
    }

    public function getInsertedRows(): array { return $this->insertedRows; }
    public function getUpdatedRows(): array { return $this->updatedRows; }
    public function getErrorRows(): array { return $this->errorRows; }
    public function getTotalRows(): int { return $this->totalRows; }
    public function getInsertedCount(): int { return count($this->insertedRows); }
    public function getUpdatedCount(): int { return count($this->updatedRows); }
    public function getErrorCount(): int { return count($this->errorRows); }

}