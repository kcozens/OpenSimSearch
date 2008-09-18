<?
include("databaseinfo.inc");
//establish connection to master db server
mysql_connect ($DB_HOST, $DB_USER, $DB_PASSWORD);
mysql_select_db ($DB_NAME);

$hostname = $_GET['host'];
$port = $_GET['port'];


if ($hostname != "" && $port != "")
{


  $objDOM = new DOMDocument();
  $objDOM->load("http://$hostname:$port/?method=collector"); //make sure path is correct

  $region = $objDOM->getElementsByTagName("info");
  echo "<u>Region:</u><br>";
  foreach( $region as $value )
  {
    $regionuuid = $value->getElementsByTagName("uuid");
    $regionuuid  = $regionuuid->item(0)->nodeValue;

    $regionname = $value->getElementsByTagName("name");
    $regionname  = $regionname->item(0)->nodeValue;

    $regionhandle = $value->getElementsByTagName("handle");
    $regionhandle  = $regionhandle->item(0)->nodeValue;

    mysql_query("insert into regions values('$regionname','$regionuuid','$regionhandle')");
    echo "$regionname - $regionuuid - $regiondescription<br>";
  }


  $parcel = $objDOM->getElementsByTagName("parcel");
  echo "<u>Parcels:</u><br>";
  foreach( $parcel as $value )
  {
    $parcelname = $value->getElementsByTagName("name");
    $parcelname  = $parcelname->item(0)->nodeValue;

    $parceluuid = $value->getElementsByTagName("uuid");
    $parceluuid  = $parceluuid->item(0)->nodeValue;

    $parcellanding = $value->getElementsByTagName("location");
    $parcellanding  = $parcellanding->item(0)->nodeValue;

    $parceldescription = $value->getElementsByTagName("description");
    $parceldescription  = $parceldescription->item(0)->nodeValue;

    $parcelsearch = $value->getAttributeNode("category");
    $parcelsearch = $parcelsearch->nodeValue;

    // Check bits on Public, Build, Script
    $parcelbuild = $value->getAttributeNode("build");
    $parcelbuild = $parcelbuild->nodeValue;

    $parcelscript = $value->getAttributeNode("scripts");
    $parcelscript = $parcelscript->nodeValue;

    $parcelpublic = $value->getAttributeNode("public");
    $parcelpublic = $parcelpublic->nodeValue;


    mysql_query("insert into parcels values('$regionuuid','$parcelname','$parceluuid','$parcellanding','$parceldescription','$parcelsearch','$parcelbuild','$parcelscript','$parcelpublic' )");
    echo "$parcelname - $parceldescription<br>";
  }

  $objects = $objDOM->getElementsByTagName("object");
  echo "<u>Objects:</u><br>";
  foreach( $objects as $value )
  {
    $title = $value->getElementsByTagName("title");
    $title  = $title->item(0)->nodeValue;

    $uuid = $value->getElementsByTagName("uuid");
    $uuid  = $uuid->item(0)->nodeValue;

    $description = $value->getElementsByTagName("description");
    $description  = $description->item(0)->nodeValue;

    $owner = $value->getElementsByTagName("owner");
    $owner  = $owner->item(0)->nodeValue;

    echo "$title - $uuid - $description - $owner<br>";
  }
}
else
{
echo "Sorry, the parser couldn't read the server info and will now quit";
}
?>

