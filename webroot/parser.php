<?
include("databaseinfo.inc");

///////////////////////////////////////////////////////////////////////
//
// Search engine sim scanner
//

//
// Search DB
//
mysql_connect ($DB_HOST, $DB_USER, $DB_PASSWORD);
mysql_select_db ($DB_NAME);

//
// Read params
//
#$hostname = $_GET['host'];
#$port = $_GET['port'];
$hostname = $argv[1];
$port = $argv[2];

$now = time();

if ($hostname != "" && $port != "")
{
	//
	// Load XML doc from URL
	//
	$objDOM = new DOMDocument();
	$objDOM->load("http://$hostname:$port/?method=collector");

	//
	// Grabbing the expire to update
	//
	$regiondata = $objDOM->getElementsByTagName("regiondata")->item(0);
	$expire = $regiondata->getElementsByTagName("expire")->item(0)->nodeValue;

	//
	// Calculate new expire
	//
	$next = time() + (60 * $expire);

	$updater = mysql_query("UPDATE hostsregister set lastcheck = $next " .
			"where host = '" . mysql_escape_string($hostname) . "' AND " .
			"port = '" . mysql_escape_string($port) . "'");

	$regionlist = $regiondata->getElementsByTagName("region");

	foreach( $regionlist as $region )
	{
		//
		// Start reading the Region info
		//
		$info = $region->getElementsByTagName("info")->item(0);

		$regionuuid =
				$info->getElementsByTagName("uuid")->item(0)->nodeValue;

		$regionname =
				$info->getElementsByTagName("name")->item(0)->nodeValue;

		$regionhandle =
				$info->getElementsByTagName("handle")->item(0)->nodeValue;

		$url =
				$info->getElementsByTagName("url")->item(0)->nodeValue;

		//
		// First, check if we already have a region that is the same
		//
		$check = mysql_query("SELECT * FROM regions WHERE regionuuid = '" .
				mysql_escape_string($regionuuid) . "'");

		if (mysql_num_rows($check) > 0)
		{
			mysql_query("DELETE FROM regions WHERE regionuuid = '" .
					mysql_escape_string($regionuuid) . "'");
			mysql_query("DELETE FROM parcels WHERE regionuuid = '" .
					mysql_escape_string($regionuuid) . "'");
			mysql_query("DELETE FROM objects WHERE regionuuid = '" .
					mysql_escape_string($regionuuid) . "'");
		}

		$data = $region->getElementsByTagName("data")->item(0);
		$estate = $data->getElementsByTagName("estate")->item(0);
		$username = $estate->getElementsByTagName("user")->item(0)->nodeValue;

		//
		// Second, add the new info to the database
		//
		mysql_query("INSERT INTO regions VALUES('" .
				mysql_escape_string($regionname) . "','" .
				mysql_escape_string($regionuuid) . "','" .
				mysql_escape_string($regionhandle) . "','" .
				mysql_escape_string($url) . "','" .
				mysql_escape_string($username) ."')");

		if (mysql_affected_rows() > -1);
		{
			$request = $_SERVER['REQUEST_TIME'];
		}

		//
		// Start reading the parcel info
		//
		$parcel = $data->getElementsByTagName("parcel");

		foreach( $parcel as $value )
		{
			$parcelname =
					$value->getElementsByTagName("name")->item(0)->nodeValue;

			$parceluuid =
					$value->getElementsByTagName("uuid")->item(0)->nodeValue;

			$parcellanding =
					$value->getElementsByTagName("location")->item(0)->nodeValue;

			$parceldescription =
					$value->getElementsByTagName("description")->item(0)->nodeValue;

			$parcelsearch = $value->getAttributeNode("category")->nodeValue;

			//
			// Check bits on Public, Build, Script
			//
			$parcelbuild = $value->getAttributeNode("build")->nodeValue;
			$parcelscript = $value->getAttributeNode("scripts")->nodeValue;
			$parcelpublic = $value->getAttributeNode("public")->nodeValue;

			//
			// Save
			//
			mysql_query("insert into parcels values('" .
					mysql_escape_string($regionuuid) . "','" .
					mysql_escape_string($parcelname) . "','" .
					mysql_escape_string($parceluuid) . "','" .
					mysql_escape_string($parcellanding) . "','" .
					mysql_escape_string($parceldescription) . "','" .
					mysql_escape_string($parcelsearch) . "','" .
					mysql_escape_string($parcelbuild) . "','" .
					mysql_escape_string($parcelscript) . "','" .
					mysql_escape_string($parcelpublic) . "' )");

		}

		//
		// Handle objects
		//
		$objects = $data->getElementsByTagName("object");

		foreach( $objects as $value )
		{
			$uuid =
					$value->getElementsByTagName("uuid")->item(0)->nodeValue;

			$regionuuid =
					$value->getElementsByTagName("regionuuid")->item(0)->nodeValue;

			$parceluuid =
					$value->getElementsByTagName("parceluuid")->item(0)->nodeValue;

			$title =
					$value->getElementsByTagName("title")->item(0)->nodeValue;

			$description =
					$value->getElementsByTagName("description")->item(0)->nodeValue;

			$flags =
					$value->getElementsByTagName("flags")->item(0)->nodeValue;

			mysql_query("insert into objects values('" .
					mysql_escape_string($uuid) . "','" .
					mysql_escape_string($parceluuid) . "','','" .
					mysql_escape_string($title) . "','" .
					mysql_escape_string($description) . "','" .
					mysql_escape_string($regionuuid) . "')");

		}
	}
}
else
{
echo "Sorry, the parser couldn't read the server info and will now quit";
}
?>
