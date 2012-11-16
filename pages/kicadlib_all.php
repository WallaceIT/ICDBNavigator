<?php
include_once('../data/config.php');
include ('kicad_func.php');
date_default_timezone_set('UTC');

$file_contents = "";

$sqlquery = "SELECT * FROM parts";
$part = $db -> query($sqlquery);
$row_part = $part -> fetch(PDO::FETCH_ASSOC);

do{
	$pkgs = preg_split("/;/", $row_part['package'],-1,PREG_SPLIT_NO_EMPTY);
	
	if(count($pkgs) == 1 && preg_match('/(other)/',$pkgs[0])) continue;
	for($i=0; $i<count($pkgs);$i++){
		$curpkg = preg_split("/:/", $pkgs[$i],-1,PREG_SPLIT_NO_EMPTY);
		if($curpkg[0] == 'other' ){continue;}
		elseif(preg_match('/(qf)/',$curpkg[0] )) $packagetype = 'QUAD';
		else $packagetype = 'DIP';
		
		if(count($pkgs) == 1){$name = $row_part['name'];}
		else{$name = $row_part['name'].'_'.$curpkg[0];}
		
		$partfile = "../data/pindescs/".$row_part['name'].".xml";
		if(!file_exists($partfile)) continue;
		$xml = file_get_contents($partfile);

		$xmlIterator = new SimpleXMLIterator($xml);
		$pkg = $xmlIterator -> xpath("/part/pkg[@type = '$curpkg[0]']");

		$pinsblock = '';

		if($pkg){
			
			$pkg = $pkg[0];
			for( $pkg->rewind(); $pkg->valid(); $pkg->next() ) {
				$pinfunc = $pkg -> current()["functions"];
				$pinsblock .= preg_replace( '/\s+/', '', $pinfunc );
				$pinsblock .= ';';
				}
			$file_contents .= kicadLIB($row_part['name'], $packagetype, $pinsblock).PHP_EOL;
		}
	}

}
while($row_part = $part -> fetch(PDO::FETCH_ASSOC));

header('Content-disposition: attachment; filename=ICDBN_allpart.lib');
header('Content-type: text/xml');
echo "EESchema-LIBRARY Version 2.3  Date: ".date(DATE_RFC822).PHP_EOL."#encoding utf-8".PHP_EOL;
echo $file_contents;
?>