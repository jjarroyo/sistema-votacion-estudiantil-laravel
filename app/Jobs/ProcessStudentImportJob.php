<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
// NUEVOS EVENTOS BROADCASTABLES
use App\Events\StudentImportProgressUpdated;
use App\Events\StudentImportProcessingCompleted; 
use App\Util\Excel\Imports\StudentsImport;

class ProcessStudentImportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $filePath;
    public ?int $userId; // Lo hacemos público para que el evento lo pueda usar si es necesario

    public function __construct(string $filePath, ?int $userId)
    {
        $this->filePath = $filePath;
        $this->userId = $userId;
    }

    public function handle(): void // Ya no necesita retornar array si usamos eventos broadcast
    {
        Log::info("JOB_HANDLE: Iniciando para archivo {$this->filePath}, Usuario: {$this->userId}.");
        $resultsForEvent = [];

        try {
            $import = new StudentsImport($this->userId, function($progressPercentage, $processedRows, $totalRowsEstimate) {
                // Despachar evento de progreso
                StudentImportProgressUpdated::dispatch($progressPercentage, $processedRows, $totalRowsEstimate, $this->userId);
            });

            Excel::import($import, $this->filePath, 'local');

            $resultsForEvent = [
                'inserted' => $import->getInsertedRows(),
                'updated' => $import->getUpdatedRows(),
                'errors' => $import->getErrorRows(),
                'summary' => [
                    'total_rows' => $import->getTotalRows(), // O $totalRowsEstimate
                    'inserted_count' => $import->getInsertedCount(),
                    'updated_count' => $import->getUpdatedCount(),
                    'error_count' => $import->getErrorCount(),
                ]
            ];
           // Log::info("JOB_HANDLE: Resultados compilados para evento.", $resultsForEvent);
            StudentImportProcessingCompleted::dispatch($resultsForEvent, $this->userId);

        } catch (\Throwable $e) {
            Log::error("JOB_HANDLE: Excepción: " . $e->getMessage());
            $errorResults = [
                'inserted' => [], 'updated' => [],
                'errors' => [['row' => 'N/A', 'identifier' => 'JOB_EXCEPTION', 'error' => 'Error crítico en Job: ' . $e->getMessage()]],
                'summary' => ['total_rows' => 0, 'inserted_count' => 0, 'updated_count' => 0, 'error_count' => 1]
            ];
            StudentImportProcessingCompleted::dispatch($errorResults, $this->userId, true); // true para indicar que es un error
        } finally {
            if (Storage::disk('local')->exists($this->filePath)) {
                Storage::disk('local')->delete($this->filePath);
            }
            Log::info("JOB_HANDLE: Finalizado.");
        }
    }
}
/*


class ProcessStudentImportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $filePath;
    protected $userId;

    public function __construct(string $filePath, ?int $userId)
    {
        $this->filePath = $filePath;
        $this->userId = $userId;
        Log::info("ProcessStudentImportJob: CONSTRUCTOR EJECUTADO para archivo: {$this->filePath}, Usuario: {$this->userId}");
    }

    public function handle()
    {
        Log::info("JOB_HANDLE: Entrando al método handle.");
        $resultsToReturn = []; // Variable para almacenar lo que se va a retornar

        try {
            Log::info("JOB_HANDLE: Dentro del bloque try.");
            // ... (lógica de verificación de archivo, instanciación de $import) ...

            Log::info("JOB_HANDLE: Instanciando StudentsImport...");
            $import = new StudentsImport($this->userId, function($progress) {
                StudentImportProgress::dispatch($progress, $this->userId);
            });
            Log::info("JOB_HANDLE: StudentsImport instanciado.");

            Log::info("JOB_HANDLE: Iniciando Excel::import...");
            Excel::import($import, $this->filePath, 'local');
            Log::info("JOB_HANDLE: Excel::import completado.");

            $resultsToReturn = [
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
            // Loguea la estructura completa antes de asignarla para el retorno
            Log::info("JOB_HANDLE: Estructura de resultados ANTES de retornar desde TRY:", $resultsToReturn);

        } catch (Throwable $e) {
            Log::error("JOB_HANDLE: Excepción capturada: " . $e->getMessage());
            // Log::error("JOB_HANDLE: Stack trace: " . $e->getTraceAsString()); // Descomentar si necesitas el trace completo

            $resultsToReturn = [ // Re-asigna a $resultsToReturn
                'inserted' => [], 'updated' => [],
                'errors' => [['row' => 'N/A', 'identifier' => 'GENERAL_CATCH_ERROR', 'error' => 'Error crítico en Job: ' . $e->getMessage()]],
                'summary' => ['total_rows' => 0, 'inserted_count' => 0, 'updated_count' => 0, 'error_count' => 1]
            ];
            Log::info("JOB_HANDLE: Estructura de resultados ANTES de retornar desde CATCH:", $resultsToReturn);

        } finally {
            Log::info("JOB_HANDLE: Entrando al bloque finally.");
            if (Storage::disk('local')->exists($this->filePath)) {
                Storage::disk('local')->delete($this->filePath);
                Log::info("JOB_HANDLE: Archivo temporal {$this->filePath} eliminado.");
            } else {
                Log::warning("JOB_HANDLE: Archivo temporal {$this->filePath} no encontrado para eliminar.");
            }
            Log::info("JOB_HANDLE: Saliendo del bloque finally.");
        }

        // Log final antes del return definitivo
        Log::info("JOB_HANDLE: Valor final a retornar:", $resultsToReturn);
        return $resultsToReturn; // Devuelve la variable que has estado poblando
    }

}*/