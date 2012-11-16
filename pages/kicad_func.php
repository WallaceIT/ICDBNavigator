<?php
function kicadXML($name, $packagetype, $pins){
	
	$xml_out = '';
	
	$xml_out .= "<component refname=\"U\" compname=\"$name\" package=\"$packagetype\">".PHP_EOL;
	$pins = preg_split('/[;]+/', $pins, -1,PREG_SPLIT_NO_EMPTY);
	foreach ($pins as &$pin_desc) {
    	$xml_out .= "$pin_desc,B".PHP_EOL;
	}
	$xml_out .= "</component>".PHP_EOL;
	return $xml_out;
}

function kicadLIB($name, $packagetype, $pins){
	
	$pins = preg_split('/[;]+/', $pins, -1,PREG_SPLIT_NO_EMPTY);
	$pinno = count($pins);
	$pins_out = '';
	
	switch($packagetype){
		// DIP package
		case "DIP":
			$plen = 200;	//pin length
			$height = ($pinno/2 + 1)*100;	//height; 100mils spacing		
			$width = ((max(array_map('strlen', $pins))*50)*2/100)*100+100;	//width, according to max length of pin descriptions
			$left = -$width/2;
			$top = $height/2;
			$right = $width/2;
			$bottom = -$height/2;
			$refname_y = $top + 30;
			$compname_y = $top + 100;
			$box = "S $left $top $right $bottom 0 1 0 N";
			$count=1;
			// Construct the Pins Array For Left Side
			for ($count; $count<= $pinno/2; $count++){
				$ypos = $top - ($count*100);
				$xpos = $left - $plen;
				$n = $count-1;
				$pins_out .= "X $pins[$n] $count $xpos $ypos $plen R 50 50 1 1 B".PHP_EOL;
			}
			// Construct the Pins Array for Right Side
			for ($count; $count<= $pinno; $count++){
				$ypos = $bottom + (($count-$pinno/2)*100);
				$xpos = $right + $plen;
				$n = $count-1;
				$pins_out .= "X $pins[$n] $count $xpos $ypos $plen L 50 50 1 1 B".PHP_EOL;
			}
			break;
			
			
		// QUAD package
		case "QUAD":
			$plen = 200;
			// Width for the body according to Pin String
			$wdiff = max(array_map('strlen', $pins))*50;
			$width = ($wdiff*2)+100*$pinno/4;
			$height = $width;
			// Calculate the Co-ordinates
			$left = -$width/2;
			$top = $height/2;
			$right = $width/2;
			$bottom = -$height/2;
			$refname_y = 0;
			$compname_y = 100;
			$box = "S $left $top $right $bottom 0 1 0 N";
			$count=1;
			// For Left Pins
			$align = 1; #Pin Location Counter
			for ($count; $count<= $pinno/4; $count++){
				$ypos = $top - ($align*100) - $wdiff + 50;
				$xpos = $left - $plen;
				$n = $count-1;
				$pins_out .= "X $pins[$n] $count $xpos $ypos $plen R 50 50 1 1 B".PHP_EOL;
				$align++;
			}
			// For Bottom Pins
			$align = 1; #Pin Location Counter
			for ($count; $count<= $pinno/2 ; $count++){
				$ypos = $bottom - $plen;
				$xpos = $left + ($align*100) + $wdiff - 50;
				$n = $count-1;
				$pins_out .= "X $pins[$n] $count $xpos $ypos $plen U 50 50 1 1 B".PHP_EOL;
				$align++;
			}
			// For Right Pins
			$align = 1; #Pin Location Counter
			for ($count; $count<= $pinno*3/4; $count++){
				$ypos = $bottom + ($align*100) + $wdiff - 50;
      			$xpos = $right + $plen;
      			$n = $count-1;
				$pins_out .= "X $pins[$n] $count $xpos $ypos $plen L 50 50 1 1 B".PHP_EOL;
				$align++;
			}
			// For Top Pins
			$align = 1; #Pin Location Counter
			for ($count; $count<= $pinno; $count++){
				$ypos = $top + $plen;
      			$xpos = $right - ($align*100) - $wdiff + 50;
      			$n = $count-1;
				$pins_out .= "X $pins[$n] $count $xpos $ypos $plen D 50 50 1 1 B".PHP_EOL;
				$align++;
			}
			break;
			
	}
	

	$comp_out = '#
# '.$name.'
#
DEF '.$name.' U 0 40 Y Y 1 F N
F0 "U" 0 '.$refname_y.' 50 H V C C N N
F1 "'.$name.'" 0 '.$compname_y.' 50 H V C C N N
DRAW
'.$box.'
'.$pins_out.'ENDDRAW
ENDDEF';
	return $comp_out;
}
?>