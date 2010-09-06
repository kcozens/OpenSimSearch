<?
//////////////////////////////////////////////////////////////////////////////
// register.php                                                             //
// (C) 2008, Fly-man-                                                       //
// This file contains the registration of a simulator to the database       //
// and checks if the simulator is new in the database or a reconnected one  //
//                                                                          //
// If the simulator is old, check if the nextcheck date > registration      //
// When the date is older, make a request to the Parser to grab new data    //
//////////////////////////////////////////////////////////////////////////////

include("databaseinfo.php");
//establish connection to master db server
mysql_connect ($DB_HOST, $DB_USER, $DB_PASSWORD);
mysql_select_db ($DB_NAME);

$hostname = $_GET['host'];
$port = $_GET['port'];
$service = $_GET['service'];

if ($hostname != "" && $port != "" && $service == "online")
{
    // Check if there is already a database row for this host
    $checkhost = mysql_query("SELECT register FROM hostsregister WHERE " .
            "host = '" . mysql_escape_string($hostname) . "' AND " .
            "port = '" . mysql_escape_string($port) . "'");

    // Get the request time as a timestamp for later
    $timestamp = $_SERVER['REQUEST_TIME'];

    // if greater than 1, check the nextcheck date
    if (mysql_num_rows($checkhost) > 0)
    {
        $update = "UPDATE hostsregister SET " .
                "register = '" . mysql_escape_string($timestamp) . "', " . 
                "nextcheck = '0', checked = '0', " .
                "failcounter = '0' " .  
                "WHERE host = '" . mysql_escape_string($hostname) . "' AND " .
                "port = '" . mysql_escape_string($port) . "'";

        $runupdate = mysql_query($update);
    }
    else
    {
        $register = "INSERT INTO hostsregister VALUES ".
                    "('" . mysql_escape_string($hostname) . "', " .
                    "'" . mysql_escape_string($port) . "', " .
                    "'" . mysql_escape_string($timestamp) . "', 0, 0, 0)";

        $runupdate = mysql_query($register);
    }
}
elseif ($hostname != "" && $port != "" && $service = "offline")
{
        $delete = "DELETE FROM hostsregister " .
                "WHERE host = '" . mysql_escape_string($hostname) . "' AND " .
                "port = '" . mysql_escape_string($port) . "'";

        $rundelete = mysql_query($delete);
}
?>
