<?PHP
include("databaseinfo.inc");

$now = time();

//
// Search DB
//
mysql_connect ($DB_HOST, $DB_USER, $DB_PASSWORD);
mysql_select_db ($DB_NAME);

# 
#  Copyright (c)Melanie Thielker (http://opensimulator.org/)
# 

###################### No user serviceable parts below #####################

#
# The XMLRPC server object
#

$xmlrpc_server = xmlrpc_server_create();

xmlrpc_server_register_method($xmlrpc_server, "dir_places_query",
		"dir_places_query");

function dir_places_query($method_name, $params, $app_data)
{
    $req            = $params[0];

    $text           = $req['text'];
    $category       = $req['category'];
    $query_start    = $req['query_start'];

	if ($text == "%%%")
	{
		$response_xml = xmlrpc_encode(array(
			'success'      => False,
			'errorMessage' => "Invalid search terms"
		));

		print $response_xml;

		return;
	}

	if ($category != -1)
	{
		$result = mysql_query("select * from parcels where " .
				"(searchcategory = -1 or searchcategory = '" .
				mysql_escape_string($category) ."') and (parcelname like '%" .
				mysql_escape_string($text) . "%' or description like '" .
				mysql_escape_string($text) . "%') order by " .
				"dwell desc, parcelname" .
				" limit ".(0+$query_start).",100");
	}
	else
	{
		$result = mysql_query("select * from parcels where " .
				"parcelname like '%" .
				mysql_escape_string($text) . "%' or description like '" .
				mysql_escape_string($text) . "%' order by " .
				"dwell desc, parcelname" .
				" limit ".(0+$query_start).",100");
	}

	$data = array();
	while (($row = mysql_fetch_assoc($result)))
	{
		$data[] = array(
					"parcel_id" => $row["infouuid"],
					"name" => $row["parcelname"],
					"for_sale" => "False",
					"auction" => "False",
					"dwell" => $row["dwell"]
				);
	}
	$response_xml = xmlrpc_encode(array(
		'success'      => True,
		'errorMessage' => "",
		'data' => $data
	));

	print $response_xml;
}

xmlrpc_server_register_method($xmlrpc_server, "dir_popular_query",
		"dir_popular_query");

function dir_popular_query($method_name, $params, $app_data)
{
    $req            = $params[0];

    $flags          = $req['flags'];

	$terms = array();

	if ($flags & 0x1000)
		$terms[] = "has_picture = 1";
	
	if ($flags & 0x0800)
		$terms[] = "mature = 0";
	
	$where = "";
	if (count($terms) > 0)
		$where = " where " . join(" and ", $terms);

	$result = mysql_query("select * from popularplaces" . $where);

	$data = array();
	while (($row = mysql_fetch_assoc($result)))
	{
		$data[] = array(
					"parcel_id" => $row["infoUUID"],
					"name" => $row["name"],
					"dwell" => $row["dwell"]
				);
	}

	$response_xml = xmlrpc_encode(array(
		'success'      => True,
		'errorMessage' => "",
		'data' => $data
	));

	print $response_xml;
}

xmlrpc_server_register_method($xmlrpc_server, "dir_land_query",
		"dir_land_query");

function dir_land_query($method_name, $params, $app_data)
{
    $req            = $params[0];

    $flags          = $req['flags'];
    $type           = $req['type'];
    $price          = $req['price'];
    $area           = $req['area'];
    $query_start    = $req['query_start'];

	$terms = array();
	$order = "lsq";
	if ($flags & 0x80000)
		$order = "parcelname";
	if ($flags & 0x10000)
		$order = "saleprice";
	if ($flags & 0x40000)
		$order = "area";
	if (!($flags & 0x8000))
		$order .= " desc";

	if ($flags & 0x100000)
		$terms[] = "saleprice <= '" . mysql_escape_string($price) . "'";

	if ($flags & 0x200000)
		$terms[] = "area >= '" . mysql_escape_string($area) . "'";

	if (($type & 26) == 2) // Auction
	{
		$response_xml = xmlrpc_encode(array(
			'success'      => False,
			'errorMessage' => "No auctions listed"
		));

		print $response_xml;

		return;
	}
	
	if (($type & 24) == 8)
		$terms[] = "parentestate = 1";
	if (($type & 24) == 16)
		$terms[] = "parentestate <> 1";

	if ($flags & 0x800)
		$terms[] = "mature = 'false'";
	if ($flags & 0x4000)
		$terms[] = "mature = 'true'";
	
	$where = "";
	if (count($terms) > 0)
		$where = " where " . join(" and ", $terms);

	$sql = "select *, saleprice/area as lsq from parcelsales" . $where .
			" order by " . $order . " limit " .
			mysql_escape_string($query_start) . ",101";
	
	$result = mysql_query($sql);

	$data = array();
	while (($row = mysql_fetch_assoc($result)))
	{
		$data[] = array(
					"parcel_id" => $row["infoUUID"],
					"name" => $row["parcelname"],
					"auction" => "false",
					"for_sale" => "true",
					"sale_price" => $row["saleprice"],
					"area" => $row["area"]
				);
	}

	$response_xml = xmlrpc_encode(array(
		'success'      => True,
		'errorMessage' => "",
		'data' => $data
	));

	print $response_xml;
}

#
# Process the request
#

$request_xml = $HTTP_RAW_POST_DATA;
xmlrpc_server_call_method($xmlrpc_server, $request_xml, '');
xmlrpc_server_destroy($xmlrpc_server);
?>
