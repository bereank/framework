<?php

namespace Leysco100\MarketingDocuments\Services;

use Illuminate\Support\Facades\DB;
use Leysco100\Shared\Models\Administration\Models\OADM;

/**
 * Service for Markesz
 */
class DatabaseValidationServices
{
    public function validateTransactions(int $ObjectType, string $transactionType, int $DocEntry)
    {
        $company = OADM::where('id', 1)->first();

        if ($company->SPEnabled == 0) {
            return 0;
        }
        $getPost = DB::connection("tenant")->select('call SBO_SP_TRANSACTIONNOTIFICATION(?,?,?)', array($ObjectType, $transactionType, $DocEntry));
        if ($ObjectType == 191) {
            $getPost = DB::connection("tenant")->select('call SERVICE_CALL_SP_TRANSACTIONNOTIFICATION(?,?,?)', array($ObjectType, $transactionType, $DocEntry));
        }

        $response = $getPost[0];
        return $response;
    }
}
