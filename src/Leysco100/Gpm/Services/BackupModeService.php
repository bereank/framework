<?php

namespace Leysco100\Gpm\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use Leysco100\Shared\Models\Marketing\Models\BackUpModeSetup;


class BackupModeService
{
    public function isBackupMode()
    {
        $userId = Auth::user()->id;

        $timestamp = Carbon::now();
 
        $mode = BackUpModeSetup::where('Enabled', true)
            ->where(function (Builder $query) use ($userId) {
                $query->orwhereHas('gates.users', function (Builder $subQuery) use ($userId) {
                    $subQuery->where('id', $userId);
                })
                    ->orwhereHas('users', function (Builder $subQuery) use ($userId) {
                        $subQuery->where('UserSign', $userId);
                    })
                    ->orWhere('Type', '=', 1);
            })
            ->where('StartDate', '<=', $timestamp)
            ->where('EndTime', '>=', $timestamp)
            //     ->whereTime('StartTime', '<=', $time)
            ->first();

        if ($mode) {
            return $mode;
        } else {
            return false;
        }
    }
}
