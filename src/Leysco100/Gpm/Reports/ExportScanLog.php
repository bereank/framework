<?php

namespace Leysco100\Gpm\Reports;

use Leysco100\Gpm\Services\ReportsService;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;


class ExportScanLog implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    public $fromDate;
    public $toDate;
    public $fields;
    public $docNum;
    public $users;
    public $gates;

    public function __construct($fromDate, $toDate, $fields, $docNum = null, $users, $gates)
    {
        $this->fromDate = $fromDate;
        $this->toDate = $toDate;
        $this->fields = $fields;
        $this->docNum = $docNum;
        $this->gates = $gates;
        $this->users = $users;
    }
    public function collection()
    {
        $scan_log_report = (new ReportsService())->scanLogs(
            $this->fromDate,
            $this->toDate,
            $this->docNum,
            $this->gates,
            $this->users
        );

        return $scan_log_report;
    }
    public function headings(): array
    {
        return [
            'Date',
            'Gate',
            'User',
            'Phone Number',
            'Document Type',
            'Document Number',
            'Scan State',
            'Scan Time',
            'Release Status',
        ];
    }
    public function map($scan_log): array
    {
        $data = [
            $scan_log->created_at,
            $scan_log->gates->Name ?? "",
            $scan_log->creator->name ?? "",
            $scan_log->Phone ?? "N/A",
            $scan_log->objecttype->DocumentName ?? "",
            $scan_log->DocNum,
            $scan_log->ResultDesc,
            $scan_log->created_at,
            $scan_log->ReleaseDesc ?? "",
        ];

        // Dynamically add attachment content to the data array
        foreach ($scan_log->attachments as $attachment) {
            $data[] = $this->getAttachmentContent($attachment, $scan_log);
        }

        return $data;
    }

    public function getAttachmentContent($attachment, $scan_log)
    {

        if (in_array($attachment['Type'], ['Text', 'Dropdown', 'Phone'])) {
            $selectedAttachment = $scan_log->attachments->where('Name', $attachment['Name'])->first();
            return $selectedAttachment ? $selectedAttachment->Name . '=' . $selectedAttachment->Content : "";
        }
        return "";
    }
    // public function headings(): array
    // {
    //    // return $this->fields;

    // }
    // public function map($scan_log): array
    // {
    //     $fieldMappings = [
    //         'Date' => 'created_at',
    //         'Gate' => 'gates.Name',
    //         'User' => 'creator.name',
    //         'Phone Number' => 'Phone',
    //         'Document Type' => 'objecttype.DocumentName',
    //         'Document Number' => 'DocNum',
    //         'Scan State' => 'ResultDesc',
    //         'Scan Time' => 'created_at',
    //         'Release Status' => 'ReleaseDesc'
    //     ];

    //     $data = [];

    //     foreach ($this->fields as $field) {
    //         $attributes = explode('.', $fieldMappings[$field]);
    //         $value = $this->getValueFromAttributes($scan_log, $attributes);

    //         $data[] = $value ?? '';
    //     }

    //     return $data;
    // }

    // private function getValueFromAttributes($object, $attributes)
    // {
    //     foreach ($attributes as $attribute) {
    //         if (!isset($object->$attribute)) {
    //             return null;
    //         }
    //         $object = $object->$attribute;
    //     }
    //     return $object;
    // }
}
