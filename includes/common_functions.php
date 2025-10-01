<?php
/**
 * Common functions used across multiple files
 */

/**
 * Format a date string to a month abbreviation
 * 
 * @param string $date Date string in format YYYY-MM-DD
 * @return string Month abbreviation (e.g., Jan, Feb, etc.)
 */
function formatMonth($date) {
    $months = [
        '01' => 'Jan', '02' => 'Feb', '03' => 'Mar', '04' => 'Apr',
        '05' => 'May', '06' => 'Jun', '07' => 'Jul', '08' => 'Aug',
        '09' => 'Sep', '10' => 'Oct', '11' => 'Nov', '12' => 'Dec'
    ];
    $month = date('m', strtotime($date));
    return $months[$month] ?? '';
}
