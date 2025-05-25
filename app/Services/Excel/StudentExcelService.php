<?php

namespace App\Services\Excel;

use App\Util\Excel\Exports\StudentTemplateExport;
use Maatwebsite\Excel\Facades\Excel; // Importa el Facade

class StudentExcelService
{
    /**
     * Genera y descarga una plantilla de Excel para la importación de estudiantes.
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function downloadTemplate()
    {
        return Excel::download(new StudentTemplateExport, 'plantilla_estudiantes.xlsx');
    }

}