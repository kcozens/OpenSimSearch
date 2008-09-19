<?
//////////////////////////////////////////////////////////////////////////////
// Cron.php                                                                 //
// (C) 2008, Fly-man-                                                       //
//                                                                          //
// This is the runnable CRON service that gets called each timeunit         //
// to check if there's new hosts to parse. If it finds new hosts to parse   //
// it starts an session to the parser to get things done                    //
//////////////////////////////////////////////////////////////////////////////

include("databaseinfo.inc");
//establish connection to master db server
mysql_connect ($DB_HOST, $DB_USER, $DB_PASSWORD);
mysql_select_db ($DB_NAME);

//getting UNIX_TIMESTAMP to check with
$now = time();
//getting a new UNIX_TIMESTAMP 1 hour in advance of $now
$next = time() + (60 * 60);

// Checking if there are hosts that need parsing by looking $now > lastparsed field
// in the database

$jobsearch = mysql_query("SELECT host, port from hostsregister where lastcheck < $now limit 0,1");

while ($jobs = mysql_fetch_row($jobsearch))
{
       // start the parser function to grab latest info from the simulator
       require_once("daemon/curl_http_client.php");
       $curl = &new Curl_HTTP_Client();
       $html_data = $curl->fetch_url("http://localhost/ossearch/parser.php?host=$jobs[0]&port=$jobs[1]");
       if ($html_data != "")
       {
        // Write a logbook to disk what it did
        $fp = fopen("logbook.log","a");
        fwrite($fp, "$now - $jobs[0]:$jobs[1] was updated succesfully");
        fclose($fp);
        // Set the new lastcheckdate 1 hour in front of now
        $updater = mysql_query("UPDATE hostsregister set lastcheck = $next where host = '$jobs[0]' AND port = $jobs[1]");
       }
       else
       {
        // Write a logbook to disk what it did
        $fp = fopen("logbook.log","a");
        fwrite($fp, "$now - $jobs[0]:$jobs[1] failed");
        fclose($fp);
       }

}
