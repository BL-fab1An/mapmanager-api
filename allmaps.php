<?php

/*
To call this an API is a bit far fetched. It's a simple script to send a list of all maps currently in our mapcycles
Required:
textfile with mapcycle for every server, should be available anyway, since the mapcycle itself is usually a plain text file
you may need to make it available to the webserver somehow
*/
// ############### SETTINGS #################

// folder with map files, don't forget the trailing slash!
$downloadpath = "/path/to/mapfiles/on/webserver/q3ut4/";


// two dimensional array with servers
// portnumber => array (name, path/to/mapcycle file)
// (Name is not used yet)

$servers	=	array (
					27960	=>	array (
								"servername"	=>	"Server 1",
								"mapcyclefile"	=>	"/path/to/mapcycle/files/mapcycle1.txt")
					27970	=>	array (
								"servername"	=>	"Server 2",
								"mapcyclefile"	=>	"/path/to/mapcycle/files/mapcycle2.txt")
					27980	=>	array (
								"servername"	=>	"Server 3",
								"mapcyclefile"	=>	"/path/to/mapcycle/files/mapcycle3.txt")
					27990	=>	array (
								"servername"	=>	"Server 4",
								"mapcyclefile"	=>	"/path/to/mapcycle/files/mapcycle4.txt")
				);
							
if ($_GET["serverid"] && $_GET["serverid"] > 0  && !empty($servers[trim($_GET["serverid"])])) {
	$serverid = trim($_GET["serverid"]);
	}

// ############## END SETTINGS ##############

// mess with my crappy code below on your own risk

// ##########################################



header ("Content-Type:text/xml");
echo "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";


// reading mapcycles from given txt files
function get_mapcycle($serverlist,$mappath, $serverid=false) {

    $cycle = array();

	if (!$serverid) {	// if no explicit serverid given read all mapcycles

		foreach ($serverlist as $server) {
			$cycle = array_merge($cycle, file($server["mapcyclefile"]));
		}
	}
	else {	// read only mapcycle for requested server

		$cycle = file($serverlist[$serverid]["mapcyclefile"]);
	}

    foreach ($cycle as $map) {

    	$map_file = $mappath . trim($map) . ".pk3";
        // filter empty entries and standard maps (since usually there exists no according file)
        if (trim($map) != "" && file_exists($map_file)) {

  			$mapsize = filesize($map_file);

	        $cleancycle[] = trim($map) . "#" . $mapsize;

        }
    }
    return array_unique($cleancycle);
}

if (!empty($servers) && trim($downloadpath != "")) {

	$cycle = get_mapcycle($servers, $downloadpath, $serverid);
	echo "<maps>\n";
	
	foreach($cycle as $map) {
	
		list($map_name, $map_size) = split("#", $map);
		
		$mapfull = $map_name . ".pk3";
	
			echo "<map>\n";
			echo "<name>" . trim($mapfull) . "</name>\n";
			echo "<size>" . $map_size . "</size>\n";
			echo "</map>\n";
	}
	echo "</maps>";
}
else {
	echo "Missing mapcycle files or download path. Please configure correctly.";
}
?>