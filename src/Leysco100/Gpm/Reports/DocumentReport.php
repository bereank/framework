<?php

namespace Leysco100\Gpm\Reports;


use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Leysco100\Shared\Models\MarketingDocuments\Models\OGMS;


class DocumentReport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    public $date;
    public function __construct($date)
    {
        $this->date = $date;
    }
    public function collection()
    {



        $document_rpt = OGMS::with('scanlog.gates', 'objecttype')
            ->whereDate('GenerationDateTime', $this->date)->get();


        // $document_rpt = DB::connection('tenant')->table('o_g_m_s')
        //     ->whereDate('o_g_m_s.GenerationDateTime', $this->date)
        //     ->leftjoin('g_m_s1_s', 'g_m_s1_s.id', '=', 'o_g_m_s.ScanLogID')
        //     ->join('a_p_d_i_s', 'a_p_d_i_s.ObjectID', 'o_g_m_s.ObjType')
        //     ->leftjoin('gates', 'gates.id', '=', 'g_m_s1_s.GateID')
        //     ->select('gates.name as gate_name', 'a_p_d_i_s.DocumentName', 'g_m_s1_s.*', 'o_g_m_s.Status as state', 'o_g_m_s.ExtRefDocNum as document_number', 'o_g_m_s.GenerationDateTime as gen_time')
        //     ->groupBy('o_g_m_s.ExtRefDocNum')
        //     ->orderBy('o_g_m_s.Status', 'desc')
        //     ->get();


        foreach ($document_rpt as $key => $value) {
            if ($value->state == 0) {
                $value->ReleaseDesc = "Open";
            } elseif ($value->state == 1) {
                $value->ReleaseDesc = "Scanned But Not Confirmed";
            } elseif ($value->state == 2) {
                $value->ReleaseDesc = "Scanned But Flagged";
            } elseif ($value->state == 3) {
                $value->ReleaseDesc = "Released";
            } else {
                $value->ReleaseDesc = "No Status Indicated";
            }
        }



        return $document_rpt;
    }
    public function headings(): array
    {
        return [
            'Date',
            'Gate',
            'Document Type',
            'Document Number',
            'Generation Time',
            'Status',
            'Release Time',
        ];
    }
    public function map($scan_log): array
    {

        return [
            $this->date,
            $scan_log->gate_name ?? "",
            $scan_log->DocumentName ?? "",
            $scan_log->document_number ?? "",
            $scan_log->gen_time ?? "",
            $scan_log->ReleaseDesc ?? "",
            $scan_log->updated_at ?? "N/A"

        ];
    }
}
