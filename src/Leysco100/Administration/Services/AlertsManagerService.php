<?php

namespace Leysco100\Administration\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class AlertsManagerService
{

    public function processPeriod($FrqncyType, $FrqncyIntr, $ExecTime, $ExecDaY = null)
    {
        if ($FrqncyType == 'S') {
            //Minutes    
            $DayAndDate = $this->getMinDayAndDate($FrqncyIntr);
            return $DayAndDate;
        }
        if ($FrqncyType == 'H') {
            //Hours
            $DayAndDate = $this->getHourlyDayAndDate($FrqncyIntr);
            return $DayAndDate;
        }

        if ($FrqncyType == 'D') {
            //Daily
            $DayAndDate = Carbon::now()->format('Y-m-d');
            if (isset($FrqncyIntr) && isset($ExecTime)) {
                $FrqncyIntr = intval($FrqncyIntr);
                if (is_int($FrqncyIntr)) {
                    $DayAndDate = $this->getDailyDate($FrqncyIntr, $ExecTime);
                    return   $DayAndDate;
                }
            }
        }
        if ($FrqncyType == 'W') {
            //Weekly
            $DayAndDate = $this->getNextWeeklyDayAndDate($ExecDaY, $FrqncyIntr, $ExecTime);
            return $DayAndDate;
        }
        if ($FrqncyType == 'M') {
            //Monthly
            $FrqncyIntr = intval($FrqncyIntr);
            if (is_int($FrqncyIntr)) {
                $DayAndDate = $this->getMonthDate($ExecDaY, $FrqncyIntr, $ExecTime);
                return $DayAndDate;
            }
        }
    }

    public function getMinDayAndDate($FrqncyIntr)
    {
        $currentTime = Carbon::now();
        $Next = $currentTime->addMinutes($FrqncyIntr);
        $NextTime = $Next->format('H:i');
        $NextDay = $Next->format('Y-m-d');
        $Time = Carbon::now()->format('H:i');
        $data = [
            'ExecTime' => $Time,
            'ExecDay' =>  '',
            'NextDate' => $NextDay,
            'NextTime' => $NextTime,
        ];
        return $data;
    }
    public function getHourlyDayAndDate($FrqncyIntr)
    {

        $currentTime = Carbon::now();
        $Next = $currentTime->addHours($FrqncyIntr);
        $NextTime = $Next->format('H:i');
        $NextDay = $Next->format('Y-m-d');
        $Time = Carbon::now()->format('H:i');
        $data = [
            'ExecTime' => $Time,
            'ExecDay' =>  '',
            'NextDate' => $NextDay,
            'NextTime' => $NextTime
        ];

        return $data;
    }
    public function getDailyDate($dayNumber, $ExecTime)
    {
        $currentTime = Carbon::now()->format('H:i:s');

        $current = Carbon::today();
        $pattern1 = '/^\d{2}:\d{2}$/'; // Matches "00:00"
        $pattern2 = '/^\d{2}:\d{2}:\d{2}$/'; // Matches "00:00:00"

        if (preg_match($pattern1, $ExecTime)) {
            $timeCarbon = Carbon::createFromFormat('H:i', $ExecTime);
            $ExecTime = $timeCarbon->format('H:i:s');
        } elseif (preg_match($pattern2, $ExecTime)) {
            $ExecTime = Carbon::createFromFormat('H:i:s', $ExecTime);
        }
        $ExecTime =   Carbon::parse($ExecTime)->format('H:i:s');


        if ($dayNumber != 0) {
            if ($currentTime < $ExecTime) {

                $nextDate = $current;
            } else {

                $nextDate = $current->addDays($dayNumber);
            }
        } else {

            $nextDate = $current->addDay();
        }
        $nextDate = $nextDate->setTimeFromTimeString($ExecTime);
        $carbonDate = Carbon::parse($ExecTime);
        $data = [
            'ExecTime' => $carbonDate->format('H:i'),
            'ExecDay' =>  '',
            'NextDate' => $nextDate->format('Y-m-d'),
            'NextTime' => $nextDate->format('H:i'),
        ];

        return $data;
    }
    public function getNextWeeklyDayAndDate($dayOfWeek, $FrqncyIntr, $ExecTime)
    {
        $today = Carbon::now();

        $daysOfWeek = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

        $desiredDay = $daysOfWeek[$dayOfWeek];

        $nextWeekday = $today->next($desiredDay);

        $ExecTime =   Carbon::parse($ExecTime)->format('H:i');
        $currentTime = Carbon::now()->format('H:i');
        $ExecDay = $dayOfWeek;
        $dayOfWeek = $desiredDay;
        // Log::info([$today->dayOfWeek, Carbon::parse($dayOfWeek)->dayOfWeek]);
        if (($today->dayOfWeek == Carbon::parse($dayOfWeek)->dayOfWeek)
            && ($currentTime < $ExecTime)
        ) {
            $nextWeekday = $nextWeekday->addWeeks($FrqncyIntr - 1);
        } else {
            $nextWeekday = $nextWeekday->addWeeks($FrqncyIntr - 1);
        }


        $nextWeekday =  $nextWeekday->setTimeFromTimeString($ExecTime);
        $nextDate = $nextWeekday->setTimeFromTimeString($ExecTime);

        $data = [
            'ExecTime' => $ExecTime,
            'ExecDay' =>  $ExecDay,
            'NextDate' => $nextDate->format('Y-m-d'),
            'NextTime' => $nextDate->format('H:i'),
        ];

        return $data;
    }
    public function getMonthDate($dayNumber, $FrqncyIntr, $ExecTime)
    {
        $currentTime = Carbon::now()->format('H:i:s');
        $today = Carbon::today();
        $currentDay = $today->day;
        $currentMonth = $today->month;
        $currentYear = $today->year;

        if ($currentDay > $dayNumber) {
            // Return the selected day of the following month
            $nextDate = Carbon::create(
                $currentYear,
                $currentMonth + $FrqncyIntr,
                $dayNumber
            )->toDateString();
            Log::info(["selecteddaynext" => $nextDate]);
        } else {
            if ($currentTime > $ExecTime) {
                $nextDate = Carbon::create(
                    $currentYear,
                    $currentMonth + $FrqncyIntr,
                    $dayNumber
                )->toDateString();
            }
            // Return the selected day of the current month
            $nextDate = Carbon::create(
                $currentYear,
                $currentMonth,
                $dayNumber
            )->toDateString();
            //   Log::info(["current" => $nextDate]);
        }

        // Calculate the last day of the current month
        //  $lastDayOfCurrentMonth = Carbon::create($currentYear, $currentMonth, 1)->endOfMonth()->day;
        //  if ($dayNumber > $lastDayOfCurrentMonth) {
        //      // Return the last day of the current month
        //      $nextDate = Carbon::create(
        //          $currentYear,
        //          $currentMonth,
        //          $lastDayOfCurrentMonth
        //      )->toDateString();
        //      Log::info(["last" => $nextDate]);
        //  }

        $nextDate = Carbon::parse($nextDate);

        $nextDate = $nextDate->setTimeFromTimeString($ExecTime);

        $data = [
            'ExecTime' => $ExecTime,
            'ExecDay' =>  $dayNumber,
            'NextDate' => $nextDate->format('Y-m-d'),
            'NextTime' => $nextDate->format('H:i'),
        ];

        return $data;
    }
}
