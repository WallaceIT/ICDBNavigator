<?php
//sot23_3
$file = $packagefolder."sot23_3.js";
$pkgname = "sot23_3";
$file_data = 
'// ICDBN - package sot23_3
function drawpkg_sot23_3(paper, partname, scalefactor){
var body = paper.rect(30,253,170,80, 4).attr("fill", "#333");
	
	//pins
	var pin = {};
	pin.pin1 = paper.rect(151,210,25,43);
	pin.pin1.node.setAttribute("class","pinobj");
	pin.pin1.node.setAttribute("name","pin1");
	pin.pin1.node.setAttribute("orientation", "T");
	pin.pin2 = paper.rect(51,210,25,43);
	pin.pin2.node.setAttribute("class","pinobj");
	pin.pin2.node.setAttribute("name","pin3");
	pin.pin2.node.setAttribute("orientation", "T");

	pin.pin3 = paper.rect(101,333,25,43);
	pin.pin3.node.setAttribute("class","pinobj");
	pin.pin3.node.setAttribute("name","pin4");
	pin.pin3.node.setAttribute("orientation", "B");

	var main = paper.circle(175, 275, 8).attr("fill", "#bfbfbf");
	
	var pkgset = paper.set();
	pkgset.push(body, main);
	for (var pinnum in pin) {
		pkgset.push(pin[pinnum]);
	}
	
	pkgset.transform(\'s\'+scalefactor+\',\'+scalefactor+\',115,0\');
}';
file_put_contents($file, $file_data);
$sqlquery = "SELECT COUNT(*) FROM packages WHERE pkgname = '".$pkgname."'";
$st = $db -> query($sqlquery);
if(!$st -> fetchColumn()){
	$st = $db -> prepare("INSERT INTO packages ('pkgname','pinsnum') VALUES (?,?)");
	$st->bindParam(1, $pkgname);
	$st->bindParam(2, 3);
	$st -> execute();
}

//sot23_5
$file = $packagefolder."sot23_5.js";
$pkgname = "sot23_5";
$file_data =
'// ICDBN - package sot23_5
function drawpkg_sot23_5(paper, partname, scalefactor){
var body = paper.rect(30,253,170,80, 4).attr("fill", "#333");
	
	//pins
	var pin = {};
	pin.pin1 = paper.rect(151,210,25,43);
	pin.pin1.node.setAttribute("class","pinobj");
	pin.pin1.node.setAttribute("name","pin1");
	pin.pin1.node.setAttribute("orientation", "T");
	pin.pin2 = paper.rect(101,210,25,43);
	pin.pin2.node.setAttribute("class","pinobj");
	pin.pin2.node.setAttribute("name","pin2");
	pin.pin2.node.setAttribute("orientation", "T");
	pin.pin3 = paper.rect(51,210,25,43);
	pin.pin3.node.setAttribute("class","pinobj");
	pin.pin3.node.setAttribute("name","pin3");
	pin.pin3.node.setAttribute("orientation", "T");

	pin.pin4 = paper.rect(51,333,25,43);
	pin.pin4.node.setAttribute("class","pinobj");
	pin.pin4.node.setAttribute("name","pin4");
	pin.pin4.node.setAttribute("orientation", "B");
	pin.pin5 = paper.rect(151,333,25,43);
	pin.pin5.node.setAttribute("class","pinobj");
	pin.pin5.node.setAttribute("name","pin5");
	pin.pin5.node.setAttribute("orientation", "B");

	var main = paper.circle(175, 275, 8).attr("fill", "#bfbfbf");
	
	var pkgset = paper.set();
	pkgset.push(body, main);
	for (var pinnum in pin) {
		pkgset.push(pin[pinnum]);
	}
	
	pkgset.transform(\'s\'+scalefactor+\',\'+scalefactor+\',115,0\');
}';
file_put_contents($file, $file_data);
$sqlquery = "SELECT COUNT(*) FROM packages WHERE pkgname = '".$pkgname."'";
$st = $db -> query($sqlquery);
if(!$st -> fetchColumn()){
	$st = $db -> prepare("INSERT INTO packages ('pkgname','pinsnum') VALUES (?,?)");
	$st->bindParam(1, $pkgname);
	$st->bindParam(2, 5);
	$st -> execute();
}

//sot23_6
$file = $packagefolder."sot23_6.js";
$pkgname = "sot23_6";
$file_data =
'// ICDBN - package sot23_6
function drawpkg_sot23_6(paper, partname, scalefactor){
	var body = paper.rect(30,253,170,80, 4).attr("fill", "#333");
	
	//pins
	var pin = {};
	pin.pin1 = paper.rect(151,210,25,43);
	pin.pin1.node.setAttribute("class","pinobj");
	pin.pin1.node.setAttribute("name","pin1");
	pin.pin1.node.setAttribute("orientation", "T");
	pin.pin2 = paper.rect(101,210,25,43);
	pin.pin2.node.setAttribute("class","pinobj");
	pin.pin2.node.setAttribute("name","pin2");
	pin.pin2.node.setAttribute("orientation", "T");
	pin.pin3 = paper.rect(51,210,25,43);
	pin.pin3.node.setAttribute("class","pinobj");
	pin.pin3.node.setAttribute("name","pin3");
	pin.pin3.node.setAttribute("orientation", "T");

	pin.pin4 = paper.rect(51,333,25,43);
	pin.pin4.node.setAttribute("class","pinobj");
	pin.pin4.node.setAttribute("name","pin4");
	pin.pin4.node.setAttribute("orientation", "B");
	pin.pin5 = paper.rect(101,333,25,43);
	pin.pin5.node.setAttribute("class","pinobj");
	pin.pin5.node.setAttribute("name","pin5");
	pin.pin5.node.setAttribute("orientation", "B");
	pin.pin6 = paper.rect(151,333,25,43);
	pin.pin6.node.setAttribute("class","pinobj");
	pin.pin6.node.setAttribute("name","pin5");
	pin.pin6.node.setAttribute("orientation", "B");

	var main = paper.circle(175, 275, 8).attr("fill", "#bfbfbf");
	
	var pkgset = paper.set();
	pkgset.push(body, main);
	for (var pinnum in pin) {
		pkgset.push(pin[pinnum]);
	}
	
	pkgset.transform(\'s\'+scalefactor+\',\'+scalefactor+\',115,0\');
}';
file_put_contents($file, $file_data);
$sqlquery = "SELECT COUNT(*) FROM packages WHERE pkgname = '".$pkgname."'";
$st = $db -> query($sqlquery);
if(!$st -> fetchColumn()){
	$st = $db -> prepare("INSERT INTO packages ('pkgname','pinsnum') VALUES (?,?)");
	$st->bindParam(1, $pkgname);
	$st->bindParam(2, 6);
	$st -> execute();
}

echo '<p>Package files generated for <b>SOT23</b>, pin counts: 3, 5, 6';
?>