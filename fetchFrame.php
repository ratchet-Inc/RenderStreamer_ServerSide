<?php

header("Content-Type: text/json");
require_once("API/Constants.php");
require_once("API/DB_Interface.php");

define("SESSION_NAME", "RenderStream", false);

session_start();
if(isset($_SESSION[SESSION_NAME][R_CACHE_NAME]) == false){
	$info = GetCacheInfo();
	$res = $info->status;
	if($res == 200){
		$index = -1;
		$cacheInfo = $info->res->info;
		// run loop to check which stream we are looking for...
		// for now there is only 1 stream, so we can just use the first one we see.
		for($i = 0; $i < count($cacheInfo); $i++){
			if($cacheInfo[$i]->len != 0){
				$index = $i;
				/*echo "upperlimit = ".$cacheInfo[$i]->ul."\n";
				echo "lowerlimit = ".$cacheInfo[$i]->ll."\n";
				$mid = $cacheInfo[$i]->ll + ceil(($cacheInfo[$index]->ul - $cacheInfo[$index]->ll) / 2);
				echo "mid point: $mid\n";*/
				break;
			}
		}
		if($index != -1){
			$obj = new stdClass();
			$mid = $cacheInfo[$i]->ll + ceil(($cacheInfo[$index]->ul - $cacheInfo[$index]->ll) / 2);
			$obj->currentFrame = $mid;
			$_SESSION[SESSION_NAME][R_CACHE_NAME] = $obj;
		}
	}
}
// if for some reason we still dont have any frame count to reference
if(!isset($_SESSION[SESSION_NAME][R_CACHE_NAME]->currentFrame)){
	echo '{"msg":"BLANK_FRAME", "status": 500}';
	return 1;
}
$lowL = $_SESSION[SESSION_NAME][R_CACHE_NAME]->currentFrame;
$uppL = $_SESSION[SESSION_NAME][R_CACHE_NAME]->currentFrame + 9;
$resp = GetNextFrame($lowL, $uppL);
if($resp->status != 200){
	echo '{"msg":"failed to get frame", "status": 500}';
	return 0;
}
if(count($resp->res) == 0){
	unset($_SESSION[SESSION_NAME][R_CACHE_NAME]);
	echo '{"msg":"buffer info request queued", "status": 200}';
	return 0;
}
$_SESSION[SESSION_NAME][R_CACHE_NAME]->currentFrame++;
echo json_encode($resp);

return 0;

?>