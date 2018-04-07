<?PHP
// Print schedule from csv file
// Updated 7 March 2010 for use with timezone select script
// Updated 14 March 2011 to use time.inc and get_display_time ()
// Updated March 2018 for formatting, now playing
// @author EZ Computer Help LLC
// @author billcreswell.com

include 'time.inc';

$line_number = 0; //csv line number for error tracking
$last_printed_day = ''; // track day for new title/table

$schedulestring = ''; // full schedule
$currentstring = ''; // now playing
$nextstring = ''; // tbd next up
$daystring = ''; // day schedule

$data_file = fopen ('schedule2.csv', 'r');
if (!$data_file) die ("Couldn't open schedule file.");
date_default_timezone_set('America/Detroit');
$now = date('D g:ia');
$now_time = get_display_time ($now, $default_timezone);
$nowstring =  $now_time['day'] . " " . $now_time['time'] . " ";

// Schedule is stored as "start time", "end time", "name"(, "description")

while ($entry = fgetcsv ($data_file, 1024)) {
  
  $line_number++;
  
  if (count ($entry) < 3) continue;

  $start_time = get_display_time ($entry[0], $default_timezone);
  $end_time = get_display_time ($entry[1], $default_timezone);

// look for now playing
  $date1 = strtotime($entry[0]);
  $date2 = strtotime($entry[1]);
  $date3 = strtotime($now);
  if ($date3 < $date2 && $date3 > $date1){
     $currentstring = $entry[2];
  }

// check time for error
  if (!$start_time or !$end_time) {
    $schedulestring.="Warning: Couldn't understand ";
    if (!$start_time) {
      $schedulestring.= 'start ';
      if (!$end_time)
        $schedulestring.= 'or ';
    } // end no start time
    if (!$end_time)
      $schedulestring.= 'end ';
    $schedulestring.= "time on line $line_number<br/>";
    continue;
  } // end if couldn't understand a time

  $name = $entry[2];
  $description = ($entry[3]) ? $entry[3] : '';
  $day = $start_time['day'];

  if($day == $now_time['day']) {
     $daystring.= "<li>" . $start_time['time'] . " " . $name . "</li>";
  }

// New Day
  if ($day != $last_printed_day) {
    $schedulestring.= "<h3>$day</h3>";
    $last_printed_day = $day;
  } // end if a new day

// create a show entry line
  $schedulestring.= "<p>";
  $schedulestring.= $start_time['time'];
  $schedulestring.= " - ";
  $schedulestring.= $end_time['time'];
  $schedulestring.= "</p><h4 class='stitle'>";
  $schedulestring.= $name;
  $schedulestring.= '</h4><p>';
  if ($description) $schedulestring.= "$description";
  $schedulestring.= '</p>';
} // end while there's CSV data to read

?><!DOCTYPE html>
<html lang="en">
<head>
<meta http-equiv='content-type' content='text/html; charset=utf-8'/>
<title>MCBVI Radio Schedule</title>
<style>
html {background-color:#000033}
body {
    width:40em; padding:1em; margin: 10px auto;  padding: 10px 20px;
    color: #fff; font-size:1.25em; letter-spacing: .05em; line-height: 1.25;
    font-family:Trebuchet MS, Helvetica, sans-serif;
    box-shadow:1px 1px 1px #999; border:2px #eee solid;  border-radius:20px;
    background-color: #000033}
li {margin-left:1em}
a:link {color:#FFFF99}a:visited {color:#ccc;}a:hover{color:#FFFF66}
table {max-width:100%}
h1 {font-size:1.4em;margin:2px;text-align:center;}
.stimes {width:10em;vertical-align:top}
.stitle {font-size:1.1em;margin:0 0 8px 0;font-weight:bold}
</style>
</head>

<body>
	
<a href='/?page=radio'>M C B V I Radio</a> <a href='/'>M C B V I Home</a>

<h1>M C B V I Radio Schedule</h1>

<p>M C B V I Radio is new and we are adding new content as quickly as we can, please  check the schedule often.</p>


<?php print_timezone_setting (); ?>
<?php echo "<h2>Now Playing <a href='/?page=radio'>" . $currentstring . "</a></h2>"; ?>

<?php echo "<h2>Today</h2><ul>" . $daystring . "</ul>"; ?>

<h2>Full Schedule</h2>

<?php echo $schedulestring; ?>
</body>
</html>
