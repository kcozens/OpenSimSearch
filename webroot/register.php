<?
//////////////////////////////////////////////////////////////////////////////
// Register.php                                                             //
// (C) 2008, Fly-man-                                                       //
// This file contains the registration of a simulator to the database       //
// and checks if the simulator is new in the database or a reconnected one  //
//                                                                          //
// If the simulator is old, check if the lastcheck date > registration      //
// When the date is older, make a reuqest to the Parser to grab new data    //
//////////////////////////////////////////////////////////////////////////////

include("databaseinfo.inc");
//establish connection to master db server
mysql_connect ($DB_HOST, $DB_USER, $DB_PASSWORD);
mysql_select_db ($DB_NAME);

$hostname = $_GET['host'];
$port = $_GET['port'];


if ($hostname != "" && $port != "")
{
    // Check if there is already a database row for this host
    $checkhost = mysql_query("SELECT register from hostsregister where host = '$hostname'");

    // Get the request time as a timestamp for later
    $timestamp = $_SERVER['REQUEST_TIME'];

    // if greater then 1, check the lastcheck date
    if (mysql_num_rows($checkhost) > 0)
    {
     echo "Simulator already registered<br>";
     $update = "UPDATE hostsregister set register = $timestamp WHERE host = '$hostname' AND port = $port";
     $runupdate = mysql_query($update);
          if (mysql_affected_rows > -1)
          {
           echo "Updating Simulator info in database succesfull";
          }
     }
     else
     {
      echo "Registering Simulator . . .";
      $register = "INSERT INTO hostsregister VALUES ('$hostname', $port, $timestamp, 0, 1)";
      $runupdate = mysql_query($register);
          if (mysql_affected_rows > -1)
          {
           echo "Updating Simulator info in database succesfull";
          }
      }
}
else
{
 echo "There was no GET data available, possible broken setting";
}

?>