<?php

namespace Leysco100\Shared\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class RecurrPeriodsService
{

    public function processPeriod(
        $Frequency,
        $Remind,
        $ExecTime,
        $StartDate,
        $EndDate,
        $TimeZone = 'Africa/Nairobi'
    ) {
        if ($Frequency == 'W') {

            $currentDate = Carbon::now($TimeZone);
            $DayAndDate = $this->getNextWeeklyDayAndDate($Remind, $currentDate, $ExecTime, $TimeZone);
            return $this->isValidDate($DayAndDate, $StartDate, $EndDate);
        }
        if ($Frequency == 'M') {
            $Remind = intval($Remind);
            if (is_int($Remind)) {
                $DayAndDate = $this->getMonthDate($Remind, $ExecTime, $TimeZone);
                return $this->isValidDate($DayAndDate, $StartDate, $EndDate);
            }
        }
        if ($Frequency == 'D') {
            $DayAndDate =     Carbon::now($TimeZone)->format('Y-m-d');
            if (isset($Remind) && isset($ExecTime)) {
                $Remind =    intval($Remind);
                if (is_int($Remind)) {
                    $DayAndDate = $this->getDailyDate($Remind, $ExecTime, $TimeZone);


                    $t = $this->isValidDate($DayAndDate, $StartDate, $EndDate);
                    Log::info($t);
                    return $t;
                }
            }
        }
        if ($Frequency == 'Q') {

            $DayAndDate = $this->getQuarter();
            return $this->isValidDate($DayAndDate, $StartDate, $EndDate);
        }
        if ($Frequency == 'S') {
            $DayAndDate = $this->getNextSemiAnnualStartDate();
            return $this->isValidDate($DayAndDate, $StartDate, $EndDate);
        }
    }

    /**
     * Get the next occurrence of a specific day of the week along with a given time.
     *
     * @param int    $dayWeek     The desired day of the week (1 for Monday, 2 for Tuesday, ..., 7 for Sunday).
     * @param string $currentDate The current date as a string in 'Y-m-d' format.
     * @param string $execTime    The execution time as a string in 'H:i:s' format.
     * @param string $timeZone    The time zone identifier (e.g., 'UTC', 'Africa/Nairobi').
     *
     * @return string|null The next date in 'Y-m-d' format, or null if the desired day is invalid.
     */
    public function getNextWeeklyDayAndDate($dayWeek, $currentDate, $execTime, $timeZone)
    {
        // Days of the week, where 1 is Monday and 7 is Sunday.
        $daysOfWeek = [1, 2, 3, 4, 5, 6, 7];

        // Get the day of the week for the current date.
        $currentDayOfWeek = Carbon::parse($currentDate)->dayOfWeek;

        // Find the index of the desired day in the daysOfWeek array.
        $desiredDayIndex = array_search($dayWeek, $daysOfWeek);

        // If the desired day is not found, return null.
        if ($desiredDayIndex === false) {
            return null;
        }

        // Calculate the number of days to add to reach the desired day.
        $daysToAdd = ($desiredDayIndex + 8 - $currentDayOfWeek) % 7;

        // Calculate the next date based on the number of days to add.
        $nextDate = Carbon::parse($currentDate)->addDays($daysToAdd);

        // Combine the next date with the execution time and set the time zone.
        $dateTime = Carbon::parse($nextDate->format('Y-m-d') . ' ' . $execTime, $timeZone);

        // Get the current time.
        $now = Carbon::now($timeZone);

        // If the calculated date and time is in the past, add 7 days to get the next occurrence.
        if ($dateTime->lessThan($now)) {
            $daysToAdd += 7;
            $nextDate = Carbon::parse($currentDate)->addDays($daysToAdd);
        }

        // Format and return the next date.
        return $nextDate->format('Y-m-d');
    }

    public function getMonthDate($dayNumber, $ExecTime, $TimeZone)
    {
        $currentTime = Carbon::now($TimeZone)->format('H:i:s');
        $today = Carbon::today();
        $currentDay = $today->day;
        $currentMonth = $today->month;
        $currentYear = $today->year;

        // Calculate the last day of the current month
        $lastDayOfCurrentMonth = Carbon::create($currentYear, $currentMonth, 1)->endOfMonth()->day;

        if ($dayNumber > $lastDayOfCurrentMonth) {
            // Return the last day of the current month
            return Carbon::create($currentYear, $currentMonth, $lastDayOfCurrentMonth)->toDateString();
        } elseif ($currentDay > $dayNumber) {
            // Return the selected day of the following month
            return Carbon::create($currentYear, $currentMonth + 1, $dayNumber)->toDateString();
        } else {
            if ($currentTime > $ExecTime) {
                return Carbon::create($currentYear, $currentMonth + 1, $dayNumber)->toDateString();
            }
            // Return the selected day of the current month
            return Carbon::create($currentYear, $currentMonth, $dayNumber)->toDateString();
        }
    }
    
    /**
     * Get the next occurrence of a specific day of the month along with a given time.
     *
     * @param int    $dayNumber The desired day of the month (1 to 31).
     * @param string $execTime  The execution time as a string in 'H:i:s' format.
     * @param string $timeZone  The time zone identifier (e.g., 'UTC', 'Africa/Nairobi').
     *
     * @return string|Carbon The next date in 'Y-m-d' format or Carbon instance.
     */
    public function getDailyDate($dayNumber, $execTime, $timeZone)
    {
        // Get today's date and current time in the specified time zone.
        $today = Carbon::today($timeZone)->format('Y-m-d');
        $currentTime = Carbon::now($timeZone)->format('H:i:s');

        // Create a Carbon instance representing the current date.
        $current = Carbon::today($timeZone);
        $currentMonth = $current->month;
        $currentYear = $current->year;

        // Define patterns to match the time format.
        $pattern1 = '/^\d{2}:\d{2}$/';      // Matches "00:00"
        $pattern2 = '/^\d{2}:\d{2}:\d{2}$/'; // Matches "00:00:00"

        // Normalize the execution time to 'H:i:s' format.
        if (preg_match($pattern1, $execTime)) {
            $timeCarbon = Carbon::createFromFormat('H:i', $execTime);
            $execTime = $timeCarbon->format('H:i:s');
        } elseif (preg_match($pattern2, $execTime)) {
            $execTime = Carbon::createFromFormat('H:i:s', $execTime)->format('H:i:s');
        }

        // Check if a specific day is provided.
        if ($dayNumber != 0) {
            $day = Carbon::create($currentYear, $currentMonth, $dayNumber)->toDateString();

            // Check if the specified day has already passed for the current month.
            if ($day == $today && $currentTime > $execTime) {
                return Carbon::create($currentYear, $currentMonth, $dayNumber + 1);
            } elseif ($day < $today) {
                return Carbon::create($currentYear, $currentMonth + 1, $dayNumber);
            } else {
                return $day;
            }
        } else {
            // If no specific day is provided, return the next day if the execution time has passed.
            if ($currentTime > $execTime) {
                return $current->addDay();
            }

            // Return the current date if the execution time is in the future.
            return $current;
        }
    }

    public function getQuarter()
    {
        $currentDate = Carbon::now();

        // Determine the current quarter
        $currentQuarter = ceil($currentDate->quarter);

        // Calculate the next quarter start date
        if ($currentQuarter == 1) {
            $nextQuarterStartDate = Carbon::create($currentDate->year, 4, 1);
        } elseif ($currentQuarter == 2) {
            $nextQuarterStartDate = Carbon::create($currentDate->year, 7, 1);
        } elseif ($currentQuarter == 3) {
            $nextQuarterStartDate = Carbon::create($currentDate->year, 10, 1);
        } else {
            $nextQuarterStartDate = Carbon::create($currentDate->year + 1, 1, 1);
        }

        // If the calculated date is before the current date, move to the next year
        if ($nextQuarterStartDate->isBefore($currentDate)) {
            $nextQuarterStartDate->addYear();
        }

        return $nextQuarterStartDate->toDateString();
    }

    function getNextSemiAnnualStartDate()
    {
        $currentDate = Carbon::now();

        // Calculate the next semi-annual start dates
        $firstSemiAnnualStartDate = Carbon::create($currentDate->year, 1, 1);
        $secondSemiAnnualStartDate = Carbon::create($currentDate->year, 7, 1);

        // Determine which semi-annual start date is next
        $nextSemiAnnualStartDate = $currentDate->isBefore($secondSemiAnnualStartDate)
            ? $secondSemiAnnualStartDate
            : $firstSemiAnnualStartDate;

        // If the calculated date is before the current date, move to the next year
        if ($nextSemiAnnualStartDate->isBefore($currentDate)) {
            $nextSemiAnnualStartDate->addYear();
        }

        return $nextSemiAnnualStartDate->toDateString();
    }

    public function isValidDate($DayAndDate, $StartDate, $EndDate)
    {
        $DayAndDate = Carbon::parse($DayAndDate);
        Log::info("validation  " . $DayAndDate);
        if ($DayAndDate->isBetween($StartDate, $EndDate)) {
            return $DayAndDate->toDateString();
        } else {
            return null;
        }
    }
}
