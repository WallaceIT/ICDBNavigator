<?php
if (!isset($_POST['string'])){?>
	<div id="popup_header">
		<form id="searchForm" action="#">
			<input name="tosearch" type="text" size="15" placeholder="Search..." autofocus="autofocus">
			<input type="submit" id="searchButton" value="Search!">
			<div id="results" class="hide"></div>
			<br>
			<input type="button" class="OkButton" value="Close">
		</form>
	</div>
<?php ;}
else{
	$noresults = 1;
	include_once('../data/config.php');
	$thead = '
	<div id="resultstitle">Entries that match your search criteria:</div>
	<div id="results_inner">
		<table id="resultstable">
			<colgroup>
				<col width="30%"/>
				<col/>
			</colgroup>
	';
	/*
	 * searching between parts
	 */
	$sqlquery = "SELECT * FROM parts WHERE name LIKE '%".$_POST['string']."%' OR description LIKE '%".$_POST['string']."%' ORDER BY name ASC";
	$results = $db -> query($sqlquery);
	$row_results = $results -> fetch(PDO::FETCH_ASSOC);
	if(isset($row_results['name']) && $_POST['all'] != 2) {
		$noresults = 0;
		echo $thead;
		do {?>
			<tr class="clickme" url="viewpart.php?ID=<?php echo $row_results['ID'] ?>">
				<td><img src="images/ic_22.png" border="0"/> <?php echo $row_results['name']?> [ <?php echo $row_results['quantity']?> ]</td>
				<td>
					<?php echo $row_results['description'].' ['.$row_results['manufacturer'].']'?>
				</td>
			</tr>
	<?php ;} while ($row_results = $results -> fetch(PDO::FETCH_ASSOC));}
	/*
	 * searching between appnotes
	 */
	$sqlquery = "SELECT * FROM appnotes WHERE description LIKE '%".$_POST['string']."%'  ORDER BY name ASC";
	$results = $db -> query($sqlquery);
	$row_results = $results -> fetch(PDO::FETCH_ASSOC);
	
	if(isset($row_results['name']) && $_POST['all'] != 1){
		if($noresults) echo $thead;
		$noresults = 0;
		do {?>
			<tr class="clickme" url="data/appnotes/<?php echo $row_results['ID'].'_'.$row_results['name'].'.pdf'; ?>">
				<td><img src="images/pdf_22.png" border="0"/> <?php echo $row_results['name'];?></td>
				<td><?php echo $row_results['description']?></td>
			</tr>
	<?php ;} while ($row_results = $results -> fetch(PDO::FETCH_ASSOC));}
	if ($noresults) echo '<div id="noresults">No Results!</div>';
	echo '</table></div>';
	}?>