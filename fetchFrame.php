<?php

require_once("API/DB_Interface.php");
header("Content-Type: text/json");

session_start();
$frame = GetNextFrame();
echo $frame;

return 0;

?>