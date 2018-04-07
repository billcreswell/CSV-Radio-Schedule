<?php
// Time-related functions
// Geoff Shang - 14 March 2011

// Time display settings are either passed as get/post variables
// or as cookies.  See timezone.php for more information.

function savetimezone($tz) {
    setcookie('SaveTimeZoneName', $tz, time() + 3600 * 24 * 365);
}


// timezone used for scheduling and as a default display timezone.
$default_timezone = 'America/Detroit';
$timezone = 'America/Detroit';


// The day of the week where the schedule starts.
$schedule_start_day = 'Saturday';

// Get display settings:
if (isset($_COOKIE['timezone']) && strlen($_COOKIE['timezone'])>0) {
	$timezone = $_COOKIE['timezone'];
} 
if (isset($_COOKIE['format']) && strlen($_COOKIE['format'])>0) {
	$time_format = $_COOKIE['format'];
}
// If we have post data, override cookie settings

if (isset($_REQUEST['timezone']) && strlen($_REQUEST['timezone'])>0) {
	$timezone = $_REQUEST['timezone'];
}

if (isset($_REQUEST['format']) & strlen($_REQUEST['format'])>0) {
	$time_format = $_REQUEST['format'];
}

// Set defaults
if (empty ($time_format)) {
    if ($timezone == 'UTC') $time_format = 24;
    else $time_format = 12;
} 
// Set time format string. Consider moving this into get_display_time
if ($time_format == 12) $time_format_string = "%l:%M %p";
else $time_format_string = "%H:%M";
 

// Return display day of week and time.
function get_display_time ($source_time_string, $source_timezone = "") {
  global $timezone, $time_format, $time_format_string, $default_timezone, $schedule_start_day;

  if ($source_timezone == "") $source_timezone = $default_timezone;

// Try to set to source timezone
  if (!date_default_timezone_set ($source_timezone)) {
    echo "Warning: Invalid timezone $source_timezone\n";
    return false;
  } // end if invalid timezone

  // Get schedule start date as a timestamp ($schedule_start_day at midnight)
  if (strftime ("%A") == $schedule_start_day) // if it's the first day of the schedule
    $schedule_start = strtotime ("today");
  else $schedule_start = strtotime ("last $schedule_start_day");

// Get the timestamp of the time string this week.
  $timestamp = strtotime ($source_time_string, $schedule_start);
  if (!$timestamp) {
    echo "Warning: Couldn't understand time $source_time_string\n";
    return false;
  } // end if couldn't understand time
  date_default_timezone_set ($timezone);
  $return['day'] = strftime ("%A", $timestamp);
  $return['time'] = strftime ($time_format_string, $timestamp);
  return $return;
} // end get_display_time ()

// Display timezone setting and change button
function print_timezone_setting () {
    global $timezone, $time_format;
    echo "<form style='display:inline' action='timezone.php' method='post'>";
    echo "<p>Displaying the schedule in ";
    if ($timezone == "UTC") echo "Universal";
    else echo str_replace ("_", " ", $timezone);
    echo " time.";
    echo "<input type='hidden' NAME='timezone' VALUE=\"$timezone\">";
    echo "<input type='hidden' NAME='format' VALUE=\"$time_format\">";
    echo "<input type='hidden' NAME=return VALUE='" . $_SERVER['SCRIPT_NAME'] . "'>";
    echo "<input type='submit' VALUE='Change Timezone'>";
    echo "</form></p>";
} // end print_timezone_setting
