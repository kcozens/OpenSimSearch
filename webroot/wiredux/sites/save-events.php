<?
##
## This will make the Event go into the database
##

$event_name = $_POST['event_name'];
$event_desc = $_POST['event_desc'];

## Make a UNIX_TIMESTAMP of the date/time
$event_date = mktime($_POST['time_hour'], $_POST['time_minutes'], 0, $_POST['event_month'],$_POST['event_day'], $_POST['event_year']);

$duration = $_POST['duration'];
$category = $_POST['category'];
$cover_charge = $_POST['cover_charge'];

## Checking if a Cover Charge is asked
if ($cover_charge == "Y")
{
 $cover_charge = 1;
 $cover_amount = $_POST['amount'];
}
else
{
 $cover_charge = 0;
 $cover_amount = 0;
}

# The hard part, getting the region and converting that to a GlobalPosition
$position = $_POST['parcel_chosen'];

list($location, $region) = explode("|", $position);

list($valX, $valY, $valZ) = explode("/", $location);

$ParcelX = intval($valX);
$ParcelY = intval($valY);
$ParcelZ = intval($valZ);

$DbLink->query("SELECT regionName, locX, locY FROM ".C_REGIONS_TBL." WHERE uuid = '".$region."'");

while(list($regionname,$locX,$locY) = $DbLink->next_record())
{
 $RegionX = ($locX * 256);
 $RegionY = ($locY * 256);
 $GlobalX = $RegionX + $ParcelX;
 $GlobalY = $RegionY + $ParcelY;
 $GlobalZ = $ParcelZ;
 $sim_name = $regionname;
}

$GlobalPos = "<".$GlobalX.",".$GlobalY.",".$GlobalZ.">";

# Checking if it's a event with Maturity
$access =  $_POST['access'];
if ($access == 21)
{
 $access = "true";
 $eventflags = 1;
}
else
{
 $access = "false";
 $eventflags = 0;
}

# Check if we have all the info and that there's no fault stuff here

if ($event_name != "" && $event_desc != "" && $sim_name != "" && $GlobalPos != "")
{
	$query = 'INSERT INTO ossearch.events (owneruuid,name,creatoruuid,category,description,dateUTC,duration,covercharge,coveramount,simname,globalPos,eventflags,mature) VALUES("'.$_SESSION[USERID].'","'.$event_name.'","'.$_SESSION[USERID].'",'.$category.',"'.$event_desc.'",'.$event_date.','.$duration.','.$cover_charge.','.$cover_amount.',"'.$sim_name.'","'.$GlobalPos.'",'.$eventflags.',"'.$access.'")';

	$DbLink->query($query);

	# Do some nice output for people to know that their event has been handled

	echo "<CENTER>Your event has been submitted to the database<p>Click on the left menu to find your event</CENTER>";
}
else
{
echo "<CENTER>Your event could not be saved, please check your event submission again<p><a href=# onClick=history.back()>Click here to go back to the page</a></CENTER>";
}
?>