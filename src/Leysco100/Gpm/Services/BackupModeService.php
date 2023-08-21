<?php

namespace Leysco100\Gpm\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use Leysco100\Shared\Models\MarketingDocuments\Models\BackUpModeSetup;


class BackupModeService
{
    public function isBackupMode()
    {
        $userId = Auth::user()->id;
        $isNotAutomatic = BackUpModeSetup::where('Enabled', 1)->where('activatable_type', 1)->doesntExist();

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
            ->where(function ($query) use ($timestamp) {
                $query->whereDate('StartDate', '<',  $timestamp->toDateString())
                    ->orWhere(function ($query) use ($timestamp) {
                        $query->whereDate('StartDate', '=',  $timestamp->toDateString())
                            ->whereTime('StartTime', '<=',  $timestamp->toTimeString());
                    });
            })
            ->when($isNotAutomatic, function ($query) use ($timestamp) {
                $query->where('EndTime', '>=', $timestamp)
                    ->where('activatable_type', 2);
            })
            ->first();

        if ($mode) {
            return $mode;
        } else {
            return false;
        }
    }
}
