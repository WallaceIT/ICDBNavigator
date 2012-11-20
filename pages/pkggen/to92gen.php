<?php 
$pins = array('3');
for ($j = 0; $j<count($pins); $j++){
	$pkgname = "to92_".$pins[$j];
	$file = $packagefolder.$pkgname.".js";
	if (file_exists($file)) unlink($file) ;
	
	$bodyheight = 150;
	$pinlenght= 250;
	$pinthick = 20;
	$ypos = (600 - $bodyheight - $pinlenght)/2;
	$xpos = (230 -180)/2;
	$namepos = 50 + $ypos;
	$fontsize = 17;
	$file_data = 
"// ICDBN - package ".$pkgname."
function drawpkg_".$pkgname."(paper, partname, scalefactor){
	var bodyL = paper.rect(".$xpos.",".$ypos.",15,".$bodyheight.", 0).attr('fill', '#333');
	var bodyC = paper.rect(".($xpos+15).",".$ypos.",150,".$bodyheight.", 0).attr('fill', '#333');
	var bodyR = paper.rect(".($xpos+165).",".$ypos.",15,".$bodyheight.", 0).attr('fill', '#333');
	
	//pins
	var pin = {};";
	
	$y = $bodyheight + $ypos;
	$x = 15 + 30;
	for ($i = 1; $i <= $pins[$j]; $i++){
		$file_data .= '
	pin.pin'.$i.' = paper.rect('.$x.','.$y.','.$pinthick.','.$pinlenght.');
	pin.pin'.$i.'.node.setAttribute("class","pinobj");
	pin.pin'.$i.'.node.setAttribute("name","pin'.$i.'");
	pin.pin'.$i.'.node.setAttribute("orientation", "B");';
		$x += (140 - 3*$pinthick)/2 + $pinthick;
	}
	$file_data .= PHP_EOL;
	
	$file_data .= "
	
	var pkgset = paper.set();
	pkgset.push(bodyL, bodyC, bodyR);
	for (var pinnum in pin) {
		pkgset.push(pin[pinnum]);
	}
	
	pkgset.transform('s'+scalefactor+','+scalefactor+',115,0t0,0');

}";
	
	file_put_contents($file, $file_data);
	$sqlquery = "SELECT COUNT(*) FROM packages WHERE pkgname = '".$pkgname."'";
	$st = $db -> query($sqlquery);	
	if(!$st -> fetchColumn()){
		$st = $db -> prepare("INSERT INTO packages ('pkgname','pinsnum') VALUES (?,?)");
		$st->bindParam(1, $pkgname);
		$st->bindParam(2, $pins[$j]);
		$st -> execute();
	}
}

echo '<p>Package files generated for <b>TO92</b>, pin counts: ';
for ($i=0; $i<count($pins); $i++){
	echo $pins[$i].', ';
}
echo '</p>';
?>