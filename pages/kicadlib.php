<?php
include_once('../data/config.php');
include ('kicad_func.php');
date_default_timezone_set('UTC');

$file_contents = "";
	
$sqlquery = "SELECT * FROM parts WHERE ID =".$_GET['partID'];
$part = $db -> query($sqlquery);
$row_part = $part -> fetch(PDO::FETCH_ASSOC);

if(preg_match('/(qf)/',$_GET['pkg'] )) $packagetype = 'DIP';
else $packagetype = 'DIP';

$partfile = "../data/pindescs/".$row_part['name'].".xml";
$xml = file_get_contents($partfile);

$xmlIterator = new SimpleXMLIterator($xml);
$pkg = current($xmlIterator -> xpath("/part/pkg[@type = '$_GET[pkg]']"));

$pinsblock = '';

if($pkg){
	
	for( $pkg->rewind(); $pkg->valid(); $pkg->next() ) {
		$pinfunc = $pkg -> current();
		$pinfunc = $pinfunc["functions"];
		$pinsblock .= preg_replace('/\s+/', '', $pinfunc);
		$pinsblock .= ';';
		}
	$file_contents .= kicadLIB($row_part['name'], $packagetype, $pinsblock);
	
	header('Content-disposition: attachment; filename='.$row_part['name'].'.lib');
	header('Content-type: text/xml');
	echo "EESchema-LIBRARY Version 2.3  Date: ".date(DATE_RFC822).PHP_EOL."#encoding utf-8".PHP_EOL;
	echo $file_contents;
}
else {
	echo '<br>No pins\' description, operation aborted.<br><br>';
}
?>