<?php
session_start();
if(!isset($_SESSION['logged']) || $_SESSION['logged'] != 'OK')
    return header('HTTP/1.0 401 Unauthorized');

if(!isset($_POST['populate'])){
	include_once('../data/config.php');
	$query = "SELECT pinsnum FROM packages WHERE pkgname = '".$_POST['pkg']."'";
	$pinsnum = $db -> query($query);
	$pinsnum = $pinsnum -> fetchColumn();
	
	?>
	<div id="resultstitle">Step 2: Fill In Pins' Description</div>
	<form id="pinpopulateForm">
		<div id="pinpopulate_list">
			<input type="checkbox" name="skip" id="skipbox" value="1"> Skip pins' description fill in<br>
			<div id="pindiv">
				<i>Leave blank if Not Connected (NC)</i><br>
				<?php for($i=1; $i<=$pinsnum;$i++) echo '<input name="pin'.$i.'" class="pins" type="text" size="40" placeholder="pin '.$i.' functions..."><br>';?>
			</div>
		</div>
		<!-- <input type="hidden" name="partID" value="<?php echo $_POST['partID']; ?>"> -->
		<input type="hidden" name="name" value="<?php echo $_POST['name']; ?>">
		<input type="hidden" name="pkg" value="<?php echo $_POST['pkg']; ?>">
		<input type="hidden" name="pinsnum" value="<?php echo $pinsnum; ?>">
		<input type="hidden" name="populate" value="1">
		<br>
		<input type="submit" id="addpartSubmit" value="Add to DB!">
	</form>
	
	<script type="text/javascript">
	$(document).ready(function() {
		$( document ).on('submit', '#pinpopulateForm', function(event){
			event.preventDefault();
			$.ajax({
				type: "POST",
				url: "pages/pinpopulate.php",
				data: $(this).serialize(),
				dataType: "text",
				success: function(response){
					$.colorbox({html:response,
						onClosed:function(){parent.location.reload(); }
					});
				}
			});
		});
		$( document ).on('click', "#skipbox", function() {
	          if ($(this).is(':checked')){
	              $(".pins").attr("disabled", true);
	              $("#pindiv").slideUp();
	          }
	          else {
	              $(".pins").attr("disabled", false);
	              $("#pindiv").slideDown();
	          }
              	
	      });
	});
	</script>
<?php ;}
else{
	$partfile = "../data/pindescs/".$_POST['name'].".xml";
	
	$partXML = new SimpleXMLElement("<part></part>");
	$newpkgXML = $partXML -> addChild('pkg');
	$newpkgXML -> addAttribute('type', $_POST['pkg']);	
	
	for($i=1; $i<=$_POST['pinsnum'];$i++){
		$newpin = $newpkgXML -> addChild('pin'.$i);
		if (isset($_POST['skip'])) $newpin -> addAttribute('functions', '');
		elseif ($_POST['pin'.$i] != '')	$newpin -> addAttribute('functions', $_POST['pin'.$i]);		
		else $newpin -> addAttribute('functions', 'NC');
	}	
	
	file_put_contents($partfile, $partXML->asXML());	
	echo '<div id="popup_header"><b>'.$_POST['name'].'</b> added to DB!<br>[with pins description]<br><br><input type="submit" class="RefreshButton" value="Ok"></div>';
}
?>