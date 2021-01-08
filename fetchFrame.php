<?php

header("Content-Type: text/json");
require_once("API/Constants.php");
require_once("API/DB_Interface.php");

define("SESSION_NAME", "RenderStream", false);

session_start();
if(isset($_SESSION[SESSION_NAME][R_CACHE_NAME]) == false){
	$info = GetCacheInfo(True);
	$res = $info->status;
	if($res == 200){
		$index = -1;
		$cacheInfo = $info->res->info;
		// run loop to check which stream we are looking for...
		// for now there is only 1 stream, so we can just use index 0 at all time.
		for($i = 0; $i < count($cacheInfo); $i++){
			if($cacheInfo[$i]->len != 0){
				$index = $i;
				break;
			}
		}
		if($index != -1){
			$obj = new stdClass();
			$obj->currentFrame = ceil($cacheInfo[$index]->ul / $cacheInfo[$index]->ll);
			$_SESSION[SESSION_NAME][R_CACHE_NAME] = $obj;
		}
	}
}
// if for some reason we still dont have any frame count to reference
if(!isset($_SESSION[SESSION_NAME][R_CACHE_NAME]->currentFrame)){
	echo '{"msg":"BLANK_FRAME", "status": 500}';
	return 1;
}
$resp = GetNextFrame(True);
if($resp->status != 200){
	echo '{"msg":"failed to get frame", "status": 500}';
	return 0;
}
$_SESSION[SESSION_NAME][R_CACHE_NAME]->currentFrame++;
echo $resp;

return 0;

?>