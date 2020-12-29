<?php

define("CORE_API_PATH", "python ./Core/CoreClient.py", false);
define("DB_HOST", "localhost", false);
define("DB_KEY", "Z4QC6KAM9WrXsm58jkXtoOfXVaN82LSrxtkJXzK0NP87nftNtGw2dieFJBDW99Ri", false);
define("DB_NAME", "stream", false);

function GetNextFrame($debug = False){
	$reVal = json_decode('{"status":500, "msg": "unknown error"}');
	try{
		$path = CORE_API_PATH;
		$host = DB_HOST;
		$s = "$path $params -host $host";
		if($debug){
			echo "Debug out: $s\n";
		}
		$res = "";
		// executes a python call from a cgi shell to access the my database API
		// i should create an api for php, but I'm not in the mood
		// plus this works fine, for now.
		exec($s, $res);
        if($debug){
            echo "CoreAPI returned: ".var_dump($res)."\n";
        }
		$x = count($res);
		if($x == 0){
			$r->msg = "cache failure";
			return $reVal;
		}
		if($debug){
			echo "Debug out: ".var_dump($res)."\n";
		}
		$obj = json_decode($res[$x - 1]);
		if(!isset($obj->status)){
			$obj->status = "200";
		}
		return $obj;
	} catch(Exception $e){
		echo "Exception Caught: ".$e->getMessage();
		$reVal->msg = $e->getMessage();
	}
	return $reVal;
}

?>