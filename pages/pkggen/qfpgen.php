<?php 
$pins = array(28,32,40,44,64,80,100);
for ($j = 0; $j<count($pins); $j++){
	$pkgname = "qfp".$pins[$j];
	$file = $packagefolder.$pkgname.".js";
	if (file_exists($file)) unlink($file) ;
	
	$ypos = 100;
	$N = $pins[$j]/4; //pins per side
	if($N<15){
		$bodyside = 170;
		$pinthick = 10;
	}
	else{
		$bodyside = 190;
		$pinthick = 7;
	}
	
	$pinlenght = (230 -$bodyside)/2;	
	$pindist = ($bodyside - $pinthick*$N)/($N +1);
	
	$namexpos = $pinlenght + $bodyside/2;
	$nameypos = $ypos + 50;
	$fontsize = 20;
	$file_data = 
"// ICDBN - package ".$pkgname."
function drawpkg_".$pkgname."(paper, partname, scalefactor){
	var body = paper.rect(".$pinlenght.",".$ypos.",".$bodyside.",".$bodyside.", 4).attr('fill', '#333');
	
	//pins
	var pin = {};";
	//left pins
	$x = 0;
	$y = $ypos + $pindist;
	for ($i = 1; $i <= $N; $i++){
		$file_data .= '
		pin.pin'.$i.' = paper.rect('.$x.','.$y.','.$pinlenght.','.$pinthick.');
		pin.pin'.$i.'.node.setAttribute("class","pinobj");
		pin.pin'.$i.'.node.setAttribute("name","pin'.$i.'");
		pin.pin'.$i.'.node.setAttribute("orientation", "L");';
		$y += $pindist + $pinthick;
	}
	
	$file_data .= PHP_EOL;
	
	//bottom pins
	$x += $pinlenght + $pindist;
	for ($i = $N +1; $i <= $N*2; $i++){
		$file_data .= '
		pin.pin'.$i.' = paper.rect('.$x.','.$y.','.$pinthick.','.$pinlenght.');
		pin.pin'.$i.'.node.setAttribute("class","pinobj");
		pin.pin'.$i.'.node.setAttribute("name","pin'.$i.'");
		pin.pin'.$i.'.node.setAttribute("orientation", "B");';
		$x += $pindist + $pinthick;
	}
	
	$file_data .= PHP_EOL;
	
	//right pins
	$y -= $pinthick + $pindist;
	for ($i = $N*2 +1; $i <= $N*3; $i++){
		$file_data .= '
		pin.pin'.$i.' = paper.rect('.$x.','.$y.','.$pinlenght.','.$pinthick.');
		pin.pin'.$i.'.node.setAttribute("class","pinobj");
		pin.pin'.$i.'.node.setAttribute("name","pin'.$i.'");
		pin.pin'.$i.'.node.setAttribute("orientation", "R");';
		$y -= $pindist + $pinthick;
	}
	
	$file_data .= PHP_EOL;
	
	//top pins
	$x -= $pindist + $pinthick;
	$y -= $pinlenght - $pinthick;
	for ($i = $N*3 +1; $i <= $N*4; $i++){
		$file_data .= '
		pin.pin'.$i.' = paper.rect('.$x.','.$y.','.$pinthick.','.$pinlenght.');
		pin.pin'.$i.'.node.setAttribute("class","pinobj");
		pin.pin'.$i.'.node.setAttribute("name","pin'.$i.'");
		pin.pin'.$i.'.node.setAttribute("orientation", "T");';
		$x -= $pindist + $pinthick;
	}
	
	$mainypos = 15 + $ypos;
	$mainxpos = 15 + $pinlenght;
	$file_data .= "
	var main = paper.circle(".$mainxpos.", ".$mainypos.", 5).attr('fill', '#bfbfbf');
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
		$st = $db -> prepare("INSERT INTO packages ('pkgname','pinsnum') VALUES (?,?)");
		$st->bindParam(1, $pkgname);
		$st->bindParam(2, $pins[$j]);
		$st -> execute();
	}
}
echo '<p>Package files generated for <b>QFP</b>, pin counts: ';
for ($i=0; $i<count($pins); $i++){
	echo $pins[$i].', ';
}
echo '</p>';
?>