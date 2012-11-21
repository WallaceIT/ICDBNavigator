<?php 
$pins = array(6,8,10,12,14,16,18,20,22,24,26,28,36,40);
for ($j = 0; $j<count($pins); $j++){
	$pkgname = "ssop".$pins[$j];
	$file = $packagefolder.$pkgname.".js";
	if (file_exists($file)) unlink($file) ;
	
	$rectheight = $pins[$j]*10 +5;
	$ypos = 300 - $rectheight/2;
	
	$namepos = $ypos - 10 + $rectheight/2;
	$fontsize = $pins[$j] * 2 - 5;
	if ($fontsize > 40) $fontsize = 40;
	$file_data = 
"// ICDBN - package ".$pkgname."
function drawpkg_".$pkgname."(paper, partname, scalefactor){
	var body = paper.rect(40,".$ypos.",150,".$rectheight.", 4).attr('fill', '#333');
	
	//pins
	var pin = {};";
	
	$y = 10 + $ypos;
	for ($i = 1; $i <= $pins[$j]/2; $i++){
		$file_data .= '
	pin.pin'.$i.' = paper.rect(20,'.$y.',20,10);
	pin.pin'.$i.'.node.setAttribute("class","pinobj");
	pin.pin'.$i.'.node.setAttribute("name","pin'.$i.'");
	pin.pin'.$i.'.node.setAttribute("orientation", "L");';
		$y += 20;
	}
	$file_data .= PHP_EOL;
	$y = 10 + $ypos;
	for ($i = $pins[$j]; $i > $pins[$j]/2; $i--){
		$file_data .= '
	pin.pin'.$i.' = paper.rect(190,'.$y.',20,10);
	pin.pin'.$i.'.node.setAttribute("class","pinobj");
	pin.pin'.$i.'.node.setAttribute("name","pin'.$i.'");
	pin.pin'.$i.'.node.setAttribute("orientation", "R");';
		$y += 20;
	}
	$file_data .= PHP_EOL;
	
	$mainypos = 20 + $ypos;
	$file_data .= "
	var main = paper.circle(60, ".$mainypos.", 8).attr('fill', '#bfbfbf');
	main.node.setAttribute('name','main');
	
	var pkgset = paper.set();
	pkgset.push(body, main);
	for (var pinnum in pin) {
		pkgset.push(pin[pinnum]);
	}
	
	pkgset.transform('s'+scalefactor+','+scalefactor+',115,0t0,0');
	
}";
	
	file_put_contents($file, $file_data);
	$sqlquery = "SELECT COUNT(*) FROM packages WHERE pkgname = '".$pkgname."'";
	$st = $db -> query($sqlquery);	
	if(!$st -> fetchColumn()){
		if($_CONFIG_DB_USE_SQLITE) $sql = "INSERT INTO packages ('pkgname','pinsnum') VALUES (?,?)";
		else $sql = "INSERT INTO packages (pkgname, pinsnum) VALUES (?,?)";
		$st = $db -> prepare($sql);
		$st->bindParam(1, $pkgname);
		$st->bindParam(2, $pins[$j]);
		$st -> execute();
	}
}
echo '<p>Package files generated for <b>SSOP</b>, pin counts: ';
for ($i=0; $i<count($pins); $i++){
	echo $pins[$i].', ';
}
echo '</p>';
?>