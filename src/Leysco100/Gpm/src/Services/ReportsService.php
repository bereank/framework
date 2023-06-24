<?php

namespace Leysco\Gpm\Services;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Leysco100\Shared\Models\Marketing\Models\GMS1;
use Leysco100\Shared\Models\Marketing\Models\OGMS;


class  ReportsService extends Controller
{
    public function scanLogReport($startdate, $endate,  $paginate = false, $perPage = 10)
    {
        $startdate = $startdate;
        $endate = $endate;

        $scan_log_report = GMS1::whereBetween('created_at', [
            $startdate,
            $endate,
        ])
            ->with(['objecttype' => function ($query) {
                $query->select(["id", 'DocumentName', 'ObjectID']);
            }])
            ->with(['creator' => function ($query) {
                $query->select(["id", 'name', 'account', 'email', 'phone_number']);
            }])
            ->with(['gates' => function ($query) {
                $query->select(["id", 'Name', 'Longitude', 'Latitude', 'Address']);
            }])
            ->orderBy('GateID');
        if ($paginate) {
            $scan_log_report = $scan_log_report->paginate($perPage);
        } else {
            $scan_log_report = $scan_log_report->get();
        }



        // check scan status
        foreach ($scan_log_report as $key => $value) {
            if ($value->Status == 0) {
                $value->ResultDesc = "Successful";
                $value->ReleaseDesc = "Released";
            } elseif ($value->Status == 1) {
                $value->ResultDesc = "Does not Exist";
                $value->ReleaseDesc = "Not Released";
            } elseif ($value->Status == 2) {
                $value->ResultDesc = "Duplicate";
                $value->ReleaseDesc = "Not Released";
            } elseif ($value->Status == 3) {
                $value->ResultDesc = "Flagged";
                $value->ReleaseDesc = "Not Released";
            }

            if ($value->Released == 0) {
                $value->ReleaseDesc = "Not Released";
            } elseif ($value->Released == 1) {
                $value->ReleaseDesc = "Released";
            }
        }

        return $scan_log_report;
    }

    public function documentReport($startdate, $endate, $paginate = false, $perPage = 10)
    {
        $document_rpt = DB::table('o_g_m_s')
            ->whereBetween('o_g_m_s.GenerationDateTime', [
                $startdate,
                $endate,
            ])
            ->leftjoin('g_m_s1_s', 'g_m_s1_s.id', '=', 'o_g_m_s.ScanLogID')
            ->join('a_p_d_i_s', 'a_p_d_i_s.ObjectID', 'o_g_m_s.ObjType')
            ->leftjoin('gates', 'gates.id', '=', 'g_m_s1_s.GateID')
            ->select('gates.name as gate_name', 'a_p_d_i_s.DocumentName', 'g_m_s1_s.*', 'o_g_m_s.Status as state', 'o_g_m_s.ExtRefDocNum as document_number', 'o_g_m_s.GenerationDateTime as gen_time')
            ->groupBy('o_g_m_s.ExtRefDocNum')
            ->orderBy('o_g_m_s.Status', 'desc');


        if ($paginate) {
            $document_rpt  = $document_rpt->paginate($perPage);
        } else {
            $document_rpt  = $document_rpt->get();
        }


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
    public function doesNotExistReport($startdate, $endate, $paginate = false, $perPage = 10)
    {
        $scan_log = GMS1::whereBetween('created_at', [
            $startdate,
            $endate,
        ])->where('Status', 1)->select('DocNum')->get();
        $duplicate = GMS1::whereIn('DocNum', $scan_log->pluck('DocNum'))
            ->with(['objecttype' => function ($query) {
                $query->select(["id", 'DocumentName', 'ObjectID']);
            }])
            ->with(['creator' => function ($query) {
                $query->select(["id", 'name', 'account', 'email', 'phone_number']);
            }])
            ->with(['gates' => function ($query) {
                $query->select(["id", 'Name', 'Longitude', 'Latitude', 'Address']);
            }])
            ->orderBy('DocNum');
        if ($paginate) {
            $duplicate  = $duplicate->paginate($perPage);
        } else {
            $duplicate  = $duplicate->get();
        }

        foreach ($duplicate as $key => $value) {

            if ($value->Status == 0) {
                $value->ResultDesc = "Successfull";
            }
            if ($value->Status == 1) {
                $value->ResultDesc = "Does not Exist";
            }
            if ($value->Status == 2) {
                $value->ResultDesc = "Duplicate";
            }
            if ($value->Status == 3) {
                $value->ResultDesc = "Flagged";
            }


            $lastPart = Str::afterLast($value->DocNum, '-');

            $item = OGMS::where('ExtRefDocNum', $lastPart)->first();

            if ($item) {
                $value->ResultExist = "Synced Later";
                $value->GenerationTime = $item->created_at;
            } else {
                // the item does not exist
                $value->ResultExist = "Not Synced";
            }
        }

        return $duplicate;
    }
    public function duplicateScanLogsReport($startdate, $endate, $paginate = false, $perPage = 10)
    {

        $scan_log = GMS1::whereBetween('created_at', [
            $startdate,
            $endate,
        ])->where('Status', 2)->pluck('DocNum');

        $duplicate = GMS1::whereIn('DocNum', $scan_log)
            ->with(['objecttype' => function ($query) {
                $query->select(["id", 'DocumentName', 'ObjectID']);
            }])
            ->with(['creator' => function ($query) {
                $query->select(["id", 'name', 'account', 'email', 'phone_number']);
            }])
            ->with(['gates' => function ($query) {
                $query->select(["id", 'Name', 'Longitude', 'Latitude', 'Address']);
            }])
            ->orderBy('DocNum');
        if ($paginate) {
            $duplicate  = $duplicate->paginate($perPage);
        } else {
            $duplicate  = $duplicate->get();
        }

        // $duplicate = $duplicate->map(function ($item, $key) {
        foreach ($duplicate as $key => $item) {
            if ($item->Status === 0) {
                $item->ResultDesc = "Successfull";
            } else if ($item->Status === 1) {
                $item->ResultDesc = "Does not Exist";
            } else if ($item->Status === 2) {
                $item->ResultDesc = "Duplicate";
            } else if ($item->Status === 3) {
                $item->ResultDesc = "Flagged";
            }

            if ($item->Released === 0) {
                $item->ReleaseDesc = "Not Released";
            } else if ($item->Released === 1) {
                $item->ReleaseDesc = "Released";
            }
        };


        return $duplicate;
    }
}
