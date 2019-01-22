<?php

$paypal_filename = "paypal.xml";
$json_filename = "paypal.json";

# xml to json
$xml = file_get_contents($paypal_filename);
$sxe = simplexml_load_file($paypal_filename);
$json = json_encode($sxe, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
file_put_contents($json_filename, $json);

# access json
$arr = json_decode(file_get_contents($json_filename), true);
$business = json_decode(file_get_contents($json_filename), true)['business'];

?>
<!DOCTYPE html>
<h1>xml to json</h1>
<xmp><?=$xml?></xmp>
<xmp><?php print_r($sxe)?></xmp>
<xmp><?=$json?></xmp>
<h1>access json</h1>
<xmp><?php print_r($arr)?></xmp>
<xmp><?=$business?></xmp>
