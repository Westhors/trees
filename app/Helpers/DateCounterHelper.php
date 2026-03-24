<?php

use Carbon\Carbon;

function dateCounter($date)
{
    // Get the current date and time
    $now = Carbon::now();
    // Parse the input date and time
    $date = Carbon::parse($date);
    // Calculate the difference between the input date and the current date

    $diff = $date->diff($now);
    $isPast = $date->isPast();

   // Calculate the remaining years, months, and days
    $years_left = floor($diff->days / 365);
    $months_left = floor(($diff->days % 365) / 30);
    $days_left = $diff->days % 30;

    $message = '';
    // Construct a message displaying the remaining time in years, months, and days
    if ($years_left > 0) {
        $message .= $years_left . ' Year';
        if ($years_left > 1) {
            $message .= 's';
        }
        $message .= ', ';
    }
    if ($months_left > 0) {
        $message .= $months_left . ' Month';
        if ($months_left > 1) {
            $message .= 's';
        }
        $message .= ', ';
    }
    $message .= $days_left . ' Day';
    if ($days_left > 1) {
        $message .= 's';
    }

     // Add "ago" if the date is in the past
     if ($isPast) {
        $message .= ' ago';
    }

    return $message;
}
