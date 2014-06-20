<?php

ob_start();
 print date('D, d M Y H:i:s T');

$page = ob_get_contents();
ob_end_flush();
$fp = fopen("/root/123.txt","w");
fwrite($fp,$page);
fclose($fp);
?>
