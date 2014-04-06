<?php
session_start();
if(!isset($_SESSION['logged']) || $_SESSION['logged'] != 'OK')
    return header('HTTP/1.0 401 Unauthorized');

include_once('../data/config.php');
if (!isset($_POST['appnoteID'])){
	
	$st = $db -> prepare("SELECT appnotes FROM parts WHERE ID = ?");
	$st -> bindParam(1, $_GET['partID']);
	$st -> execute();
	$appnotes = ($st -> fetchColumn());
	if($appnotes){
?>
<div id="popup_header">
	Unlink a linked Application Note:
	<br>
	<form action="#" id="unlinkappnoteForm">
		<select name="unlinkappnoteID">
			<option value=""></option>
		<?php 
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
	<?php ;} else echo '<div id="popup_header"><b>No Linked Application Notes!</b><br><input type="button" class="OkButton" value="Close"/></div>'?>
</div>
<script type="text/javascript">
$().ready(function(){
	$( document ).on('submit', '#unlinkappnoteForm', function(event){
		event.preventDefault();
		var appnoteID = $(this).find( 'select[name="unlinkappnoteID"]' ).val();
		$.ajax({
			type: "POST",
			url: "pages/unlinkappnote.php",
			data: {appnoteID: appnoteID, partID: '<?php echo $_GET["partID"]?>'},
			dataType: "text",
			success: function(response){
				location.reload(true);
			}
		});
	});
});
</script>
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