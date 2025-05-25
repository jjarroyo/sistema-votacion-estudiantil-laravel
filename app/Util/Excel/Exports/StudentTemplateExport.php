<?php

namespace App\Util\Excel\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize; // Opcional, para auto-ajustar el ancho de las columnas

class StudentTemplateExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return collect([]);
    }

    /**
    * @return array
    */
    public function headings(): array
    {
        return [
            'Identificador',
            'Nombre Completo', 
            'Grado', 
        ];
    }
}