<?php 
include_once('../data/config.php');
$sqlquery = "SELECT package FROM parts WHERE ID =".$_POST['partID'];
$pkgs = $db -> query($sqlquery);
$pkgs = $pkgs -> fetchColumn();
$pkgs = preg_split("/;/", $pkgs,-1,PREG_SPLIT_NO_EMPTY);	//split result in different items - package:quantity
$newpkgs = '';
$totalqty = 0;
for($i=0; $i<count($pkgs); $i++){	//for each item...
	$pkgs[$i] = preg_split("/:/", $pkgs[$i],-1,PREG_SPLIT_NO_EMPTY);	//split each item in package and quantity
	$curqty = 'qty_'.$pkgs[$i][0];
	$pkgs[$i][1] = $_POST[$curqty];	//set new package quantity
	$newpkgs .= $pkgs[$i][0].':'.$pkgs[$i][1].';';	//add package:(new)quantity to destination string
	$totalqty += $pkgs[$i][1];		//add new package quantity to total quantity
};
$st = $db -> prepare("UPDATE parts SET package = ?, quantity = ? WHERE ID = ?");
$st->bindParam(1, $newpkgs);
$st->bindParam(2, $totalqty);
$st->bindParam(3, $_POST['partID']);
$st -> execute();
?>
<div class="qtyitem">
	Quantity: <input id="qty_total" type="text" size="1" value="<?php echo $totalqty;?>" readonly>
</div>
<form action="#" id="qtyForm">
	<?php for($i=0; $i<count($pkgs); $i++){?>
	<div class="qtyitem">
		<b><?php echo $pkgs[$i][0]; ?></b>
		<input type="button" class="qtyminus" alt="<?php echo $pkgs[$i][0]; ?>" value="-">
		<input id="qty_<?php echo $pkgs[$i][0]; ?>" name="qty_<?php echo $pkgs[$i][0]; ?>" type="text" class="qtyinput" value="<?php echo $pkgs[$i][1]; ?>" readonly>
		<input type="button" class="qtyplus" alt="<?php echo $pkgs[$i][0]; ?>" value="+">
	</div>
	<?php ;} ?>
	<input name="partID" type="hidden" value="<?php echo $_POST['partID'];?>" >
</form>