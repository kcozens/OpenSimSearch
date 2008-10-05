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
			'errorMessage' => "Invalid serach terms"
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

#
# Process the request
#

$request_xml = $HTTP_RAW_POST_DATA;
xmlrpc_server_call_method($xmlrpc_server, $request_xml, '');
xmlrpc_server_destroy($xmlrpc_server);
?>
