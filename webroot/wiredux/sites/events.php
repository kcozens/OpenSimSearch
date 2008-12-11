<?

if($_SESSION[USERID] == ""){

echo "<script language='javascript'>

<!--

window.location.href='index.php?page=home';

// -->

</script>";

}else{
?>
<CENTER>
<H5>These are the upcoming events at this moment.To add a new event, <a href = index.php?page=make-events>click here</a></H5>
<TABLE width=600px border=1>
<TR>
<TD align=center width=150px><b><u>Time</u></b></TD>
<TD align=center width=50px><b><u>Type</u></b></TD>
<TD align=center width=200px><b><u>Event</u></b></TD>
<TD align=center width=200px><b><u>Host</u></b></TD>
</TR>
<?
function Convert($input)
{
// This needs to convert the UUID to a Name LastName combination
$host = "Unknown";
return $host;
}

# Make a new UNIX_TIMESTAMP
$now = time();
# Get all events that are in the future
$DbLink->query("select creatoruuid,dateUTC,eventid,name,eventflags from ossearch.events where dateUTC > $now ORDER BY dateUTC LIMIT 0,10");

while(list($creator,$time,$eventid,$eventname,$event_type) = $DbLink->next_record())
{
 $event_time = date("m/d/Y, h:i a",$time);

 $event_host = Convert($creator);

 if ($event_type == 0) $event_type = "<img height=25px width=25px title='PG Event' src = ./images/events/pink_star.gif>";
 if ($event_type == 1) $event_type = "<img height=25px width=25px title='Mature Event' src = ./images/events/blue_star.gif>";

?>
    <TR>

      <TD><B><?=$event_time?></B></TD>
 
      <TD align=center><B><?=$event_type?></B></TD>

	  <TD><B><?=$eventname?></B></TD>

      <TD><B><?=$event_host?></B></TD>

    </TR>
<?
  }
}
?>
</TABLE>
</CENTER>