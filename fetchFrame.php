<?php

header("Content-Type: text/json");
require_once("API/Constants.php");
require_once("API/DB_Interface.php");

define("SESSION_NAME", "RenderStream", false);
$lowerBound = -1;
if(isset($_POST['frame'])){
	$lowerBound = intval($_POST["frame"]);
}
if($lowerBound < 1){
	$info = GetCacheInfo();
	//print_r($info);
	$res = $info->status;
	if($res == 200){
		$index = -1;
		$cacheInfo = $info->res->info;
		// run loop to check which stream we are looking for...
		// for now there is only 1 stream, so we can just use the first one we see.
		for($i = 0; $i < count($cacheInfo); $i++){
			if($cacheInfo[$i]->len != 0){
				$index = $i;
				break;
			}
		}
		if($index != -1){
			$obj = new stdClass();
			$mid = $cacheInfo[$i]->ll + ceil(($cacheInfo[$index]->ul - $cacheInfo[$index]->ll) / 2);
			$obj->currentFrame = $mid;
			$lowerBound = $obj->currentFrame;
		}
	}
}
// if for some reason we still dont have any frame count to reference
if($lowerBound < 1){
	echo '{"msg":"BLANK_FRAME", "status": 500}';
	return 1;
}
$lowL = $lowerBound;
$uppL = $lowerBound + 12;
$resp = GetNextFrame($lowL, $uppL);
//print_r($resp);
if($resp->status != 200){
	echo '{"msg":"failed to get frame", "status": 500}';
	return 0;
}
if(count($resp->res) == 0){
	echo '{"msg":"buffer info request queued", "status": 200}';
	return 0;
}
echo json_encode($resp);

return 0;

?>