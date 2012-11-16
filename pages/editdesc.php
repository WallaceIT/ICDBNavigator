<?php
include_once('../data/config.php');
if(!isset($_POST['newdescription'])){
	$sqlquery = "SELECT name,description,category,summary FROM parts WHERE ID =".$_POST['partID'];
	$part = $db -> query($sqlquery);
	$row_part = $part -> fetch(PDO::FETCH_ASSOC);
	?>
	<br>
	Edit part descriptions
	<br>
	<b><?php echo $row_part['name'];?></b>
	<br><br>
	<input type="text" id="newdescription" size="50" value="<?php echo $row_part['description'];?>">
	<br>
	<?php 
	$sqlquery = "SELECT category FROM categories";
	$cats = $db -> query($sqlquery);
	echo 'Category: <select id="newcategory">';
	while($cat = $cats -> fetchColumn()){
		if($cat == $row_part['category']) $selected = " selected ";	
		else $selected = "";
		echo "<option value='$cat'$selected>$cat</option>";
	}
	echo '</select>';
	?>
	<br><br>
	<div align="center"><textarea id="newsummary"><?php echo $row_part['summary'];?></textarea></div>
	<input type="button" class="OkButton" value="Cancel">
	<input type="button" id="editdescButton" value="Edit!"> 
<?php ;}
else{
	$st = $db -> prepare("UPDATE parts SET description = ?, summary = ?, category = ? WHERE ID = ?");
	$st->bindParam(1, $_POST['newdescription']);
	$st->bindParam(2, $_POST['newsummary']);
	$st->bindParam(3, $_POST['newcategory']);
	$st->bindParam(4, $_POST['partID']);
	$st -> execute();
};
?>