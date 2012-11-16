<?php 
$pins = array(6,8,10,12,14,16,18,20,22,24,26,28,40);
for ($j = 0; $j<count($pins); $j++){
	$pkgname = "soic".$pins[$j];
	$file = $packagefolder.$pkgname.".js";
	if (file_exists($file)) unlink($file) ;
	
	$rectheight = $pins[$j]*14 +5;
	$ypos = 300 - $rectheight/2;
	
	$namepos = $ypos + $rectheight/2 +3;
	$fontsize = $pins[$j] * 2 -3;
	if ($fontsize > 40) $fontsize = 40;
	$file_data = 
"// ICDBN - package ".$pkgname."
function drawpkg_".$pkgname."(paper, partname, scalefactor){
	var body = paper.rect(40,".$ypos.",150,".$rectheight.", 4).attr('fill', '#333');
	
	var name = paper.text(90, ".$namepos.", partname)
	.attr({fill: '#bfbfbf',
	'font-size': ".$fontsize.",
	'text-anchor' : 'middle'
	});
	
	//pins
	var pin = {};";
	
	$y = 10 + $ypos;
	for ($i = 1; $i <= $pins[$j]/2; $i++){
		$file_data .= '
	pin.pin'.$i.' = paper.rect(15,'.$y.',25,15);
	pin.pin'.$i.'.node.setAttribute("class","pinobj");
	pin.pin'.$i.'.node.setAttribute("name","pin'.$i.'");
	pin.pin'.$i.'.node.setAttribute("orientation", "L");';
		$y += 28;
	}
	$file_data .= PHP_EOL;
	$y = 10 + $ypos;
	for ($i = $pins[$j]; $i > $pins[$j]/2; $i--){
		$file_data .= '
	pin.pin'.$i.' = paper.rect(190,'.$y.',25,15);
	pin.pin'.$i.'.node.setAttribute("class","pinobj");
	pin.pin'.$i.'.node.setAttribute("name","pin'.$i.'");
	pin.pin'.$i.'.node.setAttribute("orientation", "R");';
		$y += 28;
	}
	$file_data .= PHP_EOL;
	
	$mainypos = 20 + $ypos;
	$file_data .= "
	var main = paper.circle(60, ".$mainypos.", 8).attr('fill', '#bfbfbf');
	main.node.setAttribute('name','main');
	
	var pkgset = paper.set();
	pkgset.push(body, name, main);
	for (var pinnum in pin) {
		pkgset.push(pin[pinnum]);
	}
	
	pkgset.transform('s'+scalefactor+','+scalefactor+',115,0t0,0');
	
	name.transform('r270');
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
echo '<p>Package files generated for <b>SOIC</b>, pin counts: ';
for ($i=0; $i<count($pins); $i++){
	echo $pins[$i].', ';
}
echo '</p>';
?>