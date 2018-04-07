<?php
// Set display timezone
// Geoff Shang - 27 February 2010
// Updated 14 December 2010

// Incoming parameters:

// Post variables:
// timezone: timezone code (e.g. UTC, Australia/Sydney)
// format: Time display format (12 or 24)
// Region: Region selected on this page.
// zone: Zone selected on this page.
// return: Path to return to after timezone selected.
// remember: Whether to remember settings (either "yes" or "no")
// action: Action to take (i.e. which button was pressed

$regions_list = array (
    "UTC" => "Universal Time",
    "Africa" => "Africa",
    "America" => "The Americas",
    "Antarctica" => "Antarctica",
    "Arctic" => "The Arctic Region",
    "Asia" => "Asia",
    "Atlantic" => "Atlantic Region",
    "Australia" => "Australia",
    "Europe" => "Europe",
    "Indian" => "The Indian Ocean Region",
    "Pacific" => "The Pacific Region"
);

foreach (array ('timezone', 'format', 'region', 'zone','return', 'remember', 'action') as $var)
  if (!empty ($_POST[$var]))  $$var = $_POST[$var];

// Set the timezone
if (($action == "Set Timezone")
 or (($region == "UTC") and ($action == "Continue"))) {
  $timezone = $region;
  if (($region != "UTC") and !empty ($zone))
    $timezone .= "/$zone";

// Only continue with this if the timezone is valid.
  if (date_default_timezone_set ($timezone)) {

// Use 24-hour format if UTC
    if ($region == "UTC") $format = 24;

// For some reason, we need to send the redirect first, then set the cookie.
    $header = "Location: $return";
    if (empty ($remember)) $header .= "?timezone=" . rawurlencode ($timezone) . "&format=$format";
    header ($header);
    if ($remember == "yes") {
      $expire = strtotime ("31 Dec 2037 23:59:59 +0000");
      setcookie ("timezone", $timezone, $expire, "/", 'blindmi.org');
      setcookie ("format", "$format", $expire, "/", 'blindmi.org');
    } // end if remembering
    exit;
  } // end if a valid timezone
  else $zone = "";
} // end if setting timezone

// If we're still here, we're printing a form

if (empty ($region)) {
// We've not been here before
  if (!empty ($timezone)) {
// Split timezone string in to its parts (it will cope if it's "UTC")
    $tmp = explode ("/", $timezone, 2);
    $region = $tmp[0];
    $zone = $tmp[1];
  } // end if timezone set from elsewhere
  else 
// Set defaults
    $region = "UTC";
} // end if no timezone set

if (empty ($return))  $return = "/";

if ((($action == "Continue") or ($action == "Change Region")) and empty ($remember))  $remember = "no";
?><!DOCTYPE html>
<html lang="en">
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<title>Time Zone Set</title>
</head>
<body>

<h2>Select your Timezone</h2>

<p>Choose your time display preferences using the form below.</p>

<form method='post'>
<?PHP

echo "<input type='hidden' name='return' value=\"" . htmlspecialchars ($return) . "\">";
echo "<P><LABEL FOR=\"region\">Select your region:</LABEL>";
echo "<SELECT ID=\"region\" NAME=\"region\">\n";
reset ($regions_list);
while (list ($key, $value) = each ($regions_list)) {
  echo "<OPTION VALUE=\"$key\"";
  if ($key == $region)
    echo " SELECTED";
  echo ">$value</OPTION>\n";
} // end list of regions
echo "</SELECT>\n";

if ($region != "UTC")
  echo "<INPUT TYPE=submit NAME=action VALUE=\"Change Region\">\n";

echo "</P>";

if ($region != "UTC") {
  echo "<P><LABEL FOR=\"zone\">Select timezone: </LABEL>\n";
  echo "<SELECT ID=\"zone\" NAME=\"zone\">\n";

// use this code in PHP > 5.3.0
//  $upper_region = strtoupper ($region);
//  $zones = timezone_identifiers_list (DateTimeZone::$upper_region);

// This code is needed in PHP < 5.3.0
  foreach (timezone_identifiers_list () as $value) {
    $tmp = explode ("/", $value, 2);
    if ($tmp[0] == $region)
      $zones[] = $value;
  } // end foreach timezone identifier

// Now $zones contains a list of the timezones we want.

// Put zones in alphabetical order
  sort ($zones);

  foreach ($zones as $value) {
    $tmp = explode ("/", $value, 2);
    $z = $tmp[1];
    echo "<OPTION VaLUE=\"$z\"";
    if ($z == $zone)
      echo " SELECTED";
    echo ">" . str_replace ("_", " ", $z) . "</OPTION>\n";
  } 
// end foreach zones

  echo "</SELECT></P>\n\n";

  echo "<P><LABEL FOR=\"format\">Display times using</LABEL>\n";
  echo "<SELECT ID=\"format\" NAME=\"format\">\n";
  echo "<OPTION VALUE=\"12\"";
  if ($format == 12)
    echo " SELECTED";
  echo ">12 hour format</OPTION>\n";
  echo "<OPTION VALUE=\"24\"";
  if ($format == 24)
    echo " SELECTED";
  echo ">24 hour format</OPTION>\n";
  echo "</SELECT></P>\n\n";
} // end if not UTC

echo "<P><INPUT TYPE=checkbox NAME=\"remember\" VALUE=\"yes\"";
if ($remember != "no")
  echo "
 CHECKED";
echo ">Remember my settings</P>\n\n";

echo "<P><INPUT TYPE=submit NAME=action VALUE=\"";
if ($region == "UTC")
  echo "Continue";
else echo "Set Timezone";
echo "\"></P>";
?>
</form>
</BODY>
</HTML>


