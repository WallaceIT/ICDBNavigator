<?php
include_once('../data/config.php');
if (!isset($_POST['appnoteID'])){?>
<div id="popup_header">
	Unlink a linked Application Note:
	<br>
	<form action="#" id="unlinkappnoteForm">
		<select name="unlinkappnoteID">
			<option value=""></option>
		<?php 
		$st = $db -> prepare("SELECT appnotes FROM parts WHERE ID = ?");
		$st -> bindParam(1, $_GET['partID']);
		$st -> execute();
		$appnotes = ($st -> fetchColumn());
		$appnotes_IDs = preg_split("/@/", $appnotes,-1,PREG_SPLIT_NO_EMPTY);
		for($i=0; $i<count($appnotes_IDs); $i++){
			$sqlquery = "SELECT * FROM appnotes WHERE ID =".$appnotes_IDs[$i];
			$appnote = $db -> query($sqlquery);
			$row_appnote = $appnote -> fetch(PDO::FETCH_ASSOC);
			echo '<option value="'.$row_appnote['ID'].'">'.$row_appnote['name'].' - '.$row_appnote['description'].'</option>';
		};
		?>
		</select>
	<input type="submit" id="unlinkappnoteButton" value="Unlink!">
	</form>
	<input type="button" class="OkButton" value="Cancel"/>
	<?php if (!$appnotes) echo '<br><b>No Linked Application Notes!</b><br><input type="button" class="OkButton" value="Cancel"/>'?>
</div>
<?php ;} else {
	$st = $db -> prepare("SELECT appnotes FROM parts WHERE ID = ?");
	$st->bindParam(1, $_POST['partID']);
	$st -> execute();
	$appnotes = ($st -> fetchColumn());
	$appnotes = preg_replace('/@'.$_POST['appnoteID'].'/', '', $appnotes);	
	$st = $db -> prepare("UPDATE parts SET appnotes = ? WHERE ID = ?");
	$st->bindParam(1, $appnotes);
	$st->bindParam(2, $_POST['partID']);
	$st -> execute();
	return true;
}?>