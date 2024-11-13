<?php
namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
class AdminScheduleExport implements FromCollection, WithHeadings,WithStyles
{
    protected $scheduleAdmin;

    public function __construct($scheduleAdmin)
    {
        $this->scheduleAdmin = $scheduleAdmin;
    }

    /**
     * Return the collection of data for the export.
     */
    public function collection()
    {
        return collect($this->scheduleAdmin);
    }

    /**
     * Define the headings for the Excel file.
     */
    public function headings(): array
    {
        return [
            'cin', 'Etudiants','Status', 'Encadrant', 'President', 'Email President', 'Rapporteur', 'Email Rapporteur'
        ];
    }
    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('1')->applyFromArray([
            'font' => ['bold' => true,
            'color' => [
                'rgb' => 'FFFFFF',  
               ],
              ],
            'fill' => ['fillType' => 'solid', 'color' => ['rgb' => '52A063']],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => 'DADADA'],
                ],
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ]);
    
        // Apply style for all other rows starting from the second row
        $sheet->getStyle('2:' . $sheet->getHighestRow())->applyFromArray([
            'fill' => ['fillType' => 'solid', 'color' => ['rgb' => 'C6DA81']],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => 'DADADA'],
                ],
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
            'font' => [
                'name' => 'Lato', // Set the font family
                'size' => 10,
                'color' => [
                   'rgb' => 'FFFFFF',  
    ],
            ],
        ]);
    
        // Set the column width to match the table width in CSS
        foreach(range('A', 'H') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true); // Automatically adjust column widths
        }
        
    }
}
