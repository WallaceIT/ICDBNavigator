<?php 
$pins = array(3,5,15,20);
for ($j = 0; $j<count($pins); $j++){
	$pkgname = "to220_".$pins[$j];
	$file = $packagefolder.$pkgname.".js";
	if (file_exists($file)) unlink($file) ;
	
	if ($pins[$j] < 7){
		$bodywidth = 180;
		$bodyheight = $bodywidth * 3/4;
		$pinlenght= 250;
		$pinthick = 20;}
	else {
		$bodywidth = 230;
		$bodyheight = $bodywidth /2;
		$pinlenght= 75;
		$pinthick = 5;
	}
	$pindist = ($bodywidth - $pins[$j]* $pinthick)/($pins[$j]+1);
	$x = (230 -$bodywidth)/2;
	$y = (600 - 2*$bodyheight - $pinlenght) /2;
	$namepos = $y + $bodyheight * 4/3;
	$fontsize = 19;
	$file_data = 
"// ICDBN - package ".$pkgname."
function drawpkg_".$pkgname."(paper, partname, scalefactor){
	var hsink = paper.rect(".$x.",".$y.",".$bodywidth.",".$bodyheight.", 0).attr('fill', '#dbdbdb');
	var hole = paper.circle(".($x+$bodywidth/2).", ".($y+$bodyheight/2).", 25).attr('fill', 'url(\"images/metal.gif\")');
	var body = paper.rect(".$x.",".($y+$bodyheight).",".$bodywidth.",".$bodyheight.", 0).attr('fill', '#333');
	
	var name = paper.text(115, ".$namepos.", partname)
	.attr({fill: '#bfbfbf',
	'font-size': ".$fontsize."
	});
	
	//pins
	var pin = {};";
	
	$y = $y+2*$bodyheight;
	for ($i = 1; $i <= $pins[$j]; $i++){
		$x += $pindist;
		$file_data .= '
	pin.pin'.$i.' = paper.rect('.$x.','.$y.','.$pinthick.','.$pinlenght.');
	pin.pin'.$i.'.node.setAttribute("class","pinobj");
	pin.pin'.$i.'.node.setAttribute("name","pin'.$i.'");
	pin.pin'.$i.'.node.setAttribute("orientation", "B");';
		$x += $pinthick;
	}
	$file_data .= PHP_EOL;
	
	$file_data .= "
	
	var pkgset = paper.set();
	pkgset.push(hsink, hole, body, name);
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

echo '<p>Package files generated for <b>TO220</b>, pin counts: ';
for ($i=0; $i<count($pins); $i++){
	echo $pins[$i].', ';
}
echo '</p>';
?>