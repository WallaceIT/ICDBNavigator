<?php
//include config file
include_once('data/config.php');
$ID = $_GET['ID'];

$sql = "SELECT * FROM parts WHERE ID = $ID";
$part = $db -> query($sql);
$row_part = $part -> fetch(PDO::FETCH_ASSOC);

// find previous/next part IDs
$sql = "SELECT ID FROM parts WHERE ID < $ID ORDER BY ID DESC LIMIT 0,1";
$prevID = $db -> query($sql);
$prevID = $prevID -> fetchColumn();
if($prevID == NULL) $prevID = $ID;

$sql = "SELECT ID FROM parts WHERE ID > $ID ORDER BY ID ASC LIMIT 0,1";
$nextID = $db -> query($sql);
$nextID = $nextID -> fetchColumn();
if($nextID == NULL) $nextID = $ID;

// find previous/next same category part IDs
$sql = "SELECT ID FROM parts WHERE ID < $ID AND category = '$row_part[category]' ORDER BY ID DESC LIMIT 0,1";
$prevcatID = $db -> query($sql);
$prevcatID = $prevcatID -> fetchColumn();
if($prevcatID == NULL) $prevcatID = $ID;

$sql = "SELECT ID FROM parts WHERE ID > $ID AND category = '$row_part[category]' ORDER BY ID ASC LIMIT 0,1";
$nextcatID = $db -> query($sql);
$nextcatID = $nextcatID -> fetchColumn();
if($nextcatID == NULL) $nextcatID = $ID;

// correctly size quantity box:
$numpkgs = 1;	
$nopkgs = FALSE;
$pkgs = preg_split("/;/", $row_part['package'],-1,PREG_SPLIT_NO_EMPTY);
if(!count($pkgs)){
	$pkgs[0][0] = 'other';
	$pkgs[0][1] = 0;
	$nopkgs = TRUE;
}
else {
	for($i=0; $i<count($pkgs); $i++){
		$pkgs[$i] = preg_split("/:/", $pkgs[$i],-1,PREG_SPLIT_NO_EMPTY);
		$numpkgs++;
	};
}
// this part's datasheet file exists locally?
$datasheethref= 'data/datasheets/'.$row_part["name"].'.pdf';
if(!file_exists($datasheethref)) $datasheethref = $row_part["datasheeturl"];
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title><?php echo $row_part['name']?> @ICDBN</title>
	<link rel="icon" type="image/ico" href="favicon.ico">
	<link rel="stylesheet" href="css/viewpart.css" type="text/css" media="screen">
	<link rel="stylesheet" href="css/jquery.qtip.css" type="text/css" media="screen">
	<link rel="stylesheet" href="css/colorbox.css">
	<link rel="stylesheet" href="css/jquery.cleditor.css" />
	<script src="js/jquery.min.js" type="text/javascript" charset="utf-8"></script>
	<script src="js/jquery.colorbox-min.js" type="text/javascript" charset="utf-8"></script>
	<script src="js/jquery.qtip.min.js" type="text/javascript" charset="utf-8"></script>
	<script src="js/raphael-min.js" type="text/javascript" charset="utf-8"></script>
	<script src="js/jquery.cleditor.min.js" type="text/javascript" charset="utf-8"></script>
	<?php
		for($i=0; $i<count($pkgs); $i++){
			echo '<script src="data/packages/'.$pkgs[$i][0].'.js" type="text/javascript" charset="utf-8"></script>';
		};
	?>
	<script type="text/javascript" charset="utf-8">
		var xmlData;
		window.onload = function(){

			var partname = '<?php echo $row_part["name"];?>';
			var partID = '<?php echo $ID;?>';
			
			$.ajaxSetup ({
			  	//Disable caching of AJAX responses
			   	cache: false
			});

			//loading pins description			
			$.ajax({
				type     : "GET",
				url      : "data/pindescs/<?php echo $row_part["name"];?>.xml",
				dataType : "xml",
				success  : function(xml){
					xmlData = xml;
				}
 			});
	
			//package dynamic dimensions
			var scalefactor = $(document).height() / 750;
			
			//default (first) package  
			var pincolor = '#dbdbdb';
			var paper = Raphael('package', 230, 600*scalefactor);
			var curpkg = '<?php echo $pkgs[0][0]; ?>';
			eval('drawpkg_'+curpkg+'(paper, partname, '+scalefactor+')');
			$('.pinobj').attr('fill',pincolor);
			
			//on package change...
			$('#pkgselectSelect').change(function() {
				  paper.clear();
				  curpkg = $('#pkgselectSelect').val();
				  eval('drawpkg_'+curpkg+'(paper, partname, '+scalefactor+', 0)');
				  $('.pinobj').attr('fill',pincolor);				  
			});
			
			//actions for pins
			var previous = null;
			$('#package').on('mouseover', '.pinobj', function() {	//coloring ad qtipping pins
				$(this).attr('fill', '#f5f5f5');

				var pinSet = $('pkg', xmlData).filter("[type='"+curpkg+"']");
				var pinFunctions = $($(this).attr('name'), pinSet).attr('functions');
				if(pinFunctions){
					var tipContent = '<b>'+$(this).attr('name').toUpperCase()+':</b> '+ pinFunctions;

					var orientation = $(this).attr('orientation');				
					switch (orientation){
						case 'R':
							var atpos = 'left center';
							var mypos = 'center right';
							break;
						case 'T':
							var atpos = 'top center';
							var mypos = 'bottom center';
							break;
						case 'B':
							var atpos = 'bottom center';
							var mypos = 'top center';
							break;
						case 'L':
						default:
							var atpos = 'right center';
							var mypos = 'center left';
							break;
					}
				$(this).qtip({
					overwrite: false,
					content: tipContent,
					show: {
						delay: 0,
						ready: true
					},
					position: {
						my: mypos,
						at: atpos
					},
					style: {
						classes: 'ui-tooltip-light ui-tooltip-rounded'
					}
				});
					

				}				
				else var tipContent = '';
			});
			
			$('#package').on('mouseout', '.pinobj', function() {	//de-coloring pins on mouse out
				if ($(this).attr('name') != previous) $(this).attr('fill', pincolor);
			});

			//class .OkButton close colorbox (for Ok/Cancel buttons)
			$( document ).on('click', '.OkButton', function() {
				$.colorbox.close();
			});

			//class .RefreshButton close colorbox and refresh the page
			$( document ).on('click', '.RefreshButton', function() {
				$.colorbox.close();
				parent.location.reload();
			});

			//toolbar
			$('.toolbarobj').mouseover(function() {
				$(this).qtip({
					overwrite: false,
					content: $(this).attr('title'),
					show: {
						delay: 0,
						ready: true
					},
					position: {
						my: 'center left',
						at: 'right center'
					},
					style: {
						classes: 'ui-tooltip-light ui-tooltip-rounded'
					}
				});
			});
			
			// -> remove part
			$('#removepartTool').click(function(){ 
				$.ajax({
					type: "POST",
					url: "pages/removepart.php",
					data: {partname: partname, partID: partID},
					dataType: "text",
					success: function(response){
						$.colorbox({
							html:response,
							overlayClose: false
						});
					}
				}); 
			});

			// -> add new package
			var y = 0;	//for colorbox resizing
			$('#addpkgTool').click(function() {
				$.colorbox({
					href:"pages/addpkg.php?partID="+partID+"&partname="+partname,
					width: "600px",
					height: "177px",
				});
			});

			$( document ).on('submit', '#addpkgForm', function(event) {
				event.preventDefault();
				$.ajax({
					type: "POST",
					url: "pages/addpkg.php",
					data: $(this).serialize(),
					dataType: "text",
					success: function(response){
						$('#popup_middle').html($(response).find('#popup_middle_content').html());
						$('#popup_footer').html($(response).find('#popup_footer_content').html());
						$('#popup_middle').show();
						y = $('#pinpopulate_list').height() + 240;
						$('#popup_middle').hide();
						if ($('#skipbox').is(':checked')){
							$('.pins').attr('disabled', true);
			          	}
						else{
						$('#popup_middle').show();
						y = $('#pinpopulate_list').height() + 240;
						$.colorbox.resize({height:y});
						}
					}
				});
			});

			$( document ).on('click', '#skipbox', function() {
		          if ($(this).is(':checked')){
						$('.pins').attr('disabled', true);
						$('#popup_middle').slideUp();
						$.colorbox.resize({height: 177});
		          }
		          else {
						$('.pins').attr('disabled', false);
						$.colorbox.resize({height:y});
						$('#popup_middle').slideDown();
		          }
	              	
		      });

			$( document ).on('click', '#addpkgButton', function() {
				$.ajax({
					type: "POST",
					url: "pages/addpkg.php",
					data: $("#pinpopulateForm").serialize(),
					dataType: "text",
					success: function(response){
						$('#popup_middle').hide();
						$.colorbox.resize({height: 177});
						$('#popup_footer').html(response);
					}
				});
			});

		    // -> edit package
		    $('#editpkgTool').click(function() {
				$.colorbox({
					href:"pages/editpkg.php?partID="+partID+"&partname="+partname,
					width: "600px",
					height: "177px",
				});
			});

		    $( document ).on('submit', '#editpkgForm', function(event) {
				event.preventDefault();
				$.ajax({
					type: "POST",
					url: "pages/editpkg.php",
					data: $(this).serialize(),
					dataType: "text",
					success: function(response){
						$('#popup_middle').html($(response).find('#popup_middle_content').html());
						$('#popup_middle').show();
						$('#popup_footer').html($(response).find('#popup_footer_content').html());
						y = $('#pinpopulate_list').height() + 250;
						$.colorbox.resize({height:y});
					}
				});
			});
		    $( document ).on('click', '#editpkgButton', function() {
				$.ajax({
					type: "POST",
					url: "pages/editpkg.php",
					data: $("#pineditForm").serialize(),
					dataType: "text",
					success: function(response){
						$('#popup_middle').hide();
						$.colorbox.resize({height: 177});
						$('#popup_footer').html(response);
					}
				});
			});
		    $( document ).on('submit', '#removepkgFirstForm', function(event) {
				event.preventDefault();
				$.ajax({
					type: "POST",
					url: "pages/removepkg.php",
					data: $(this).serialize(),
					dataType: "text",
					success: function(response){
						$.colorbox({html:response});
					}
				});
			});
			
			// -> search
			
			$('#searchTool').click(function(){
				$.ajax({
					type: "POST",
					url: "pages/search.php",
					success: function(response){
						$.colorbox({
							html: response,
							width: "600px",
							height: "520px",
						});
					}
				});
			});

			$( document ).on('submit', '#searchForm', function(event) {
				event.preventDefault();
				term = $(this).find('input[name="tosearch"]').val(),
				$.ajax({
					type: "POST",
					url: "pages/search.php",
					data: {string: term, all: '0'},
					dataType: "text",
					success: function(response){
						$('#results').html(response);
						$('#results').slideDown(500);
					}
				});
			});

			$( document ).on('click', '#resultstable tr', function() {
				window.location = $(this).attr("url");
			});
			$( document ).on('click', '#appnotestable tr', function() {
				var url = $(this).attr("url");
				window.open(url,"_blank");
			});
			
			//add/link and unlink appnotes and (eventually) datasheet
			$('#appnoteplus').click(function() {
				$.ajax({
					type: "POST",
					url: "pages/addappnote.php",
					data: {
							partID: partID,
							partname: partname<?php if($row_part['datasheeturl'] == '') echo ',needdatasheet: 1'; else echo ",datasheeturl: '$row_part[datasheeturl]'";?>
							},
					dataType: "text",
					success: function(response){
						$.colorbox({
							html: response,
							width: "600px",
						});
					}
				 });
			});

			$('#appnoteminus').click(function() {
				$.colorbox({href:"pages/unlinkappnote.php?partID="+partID});
			});

			//edit description
			$('#editdescTool').click( function() {

				$( document ).on('click', '#editdescButton', function() {
					$.ajax({
						type: "POST",
						url: "pages/editdesc.php",
						data: {	partID: partID,
								partname: partname,
								newdescription: $("#newdescription").val(),
								newsummary: $("#newsummary").val(),
								newcategory: $("#newcategory").val()},
						dataType: "text",
						success: function(response){
							window.location.reload(true);
						}
					});
				});

				$.ajax({
					type: "POST",
					url: "pages/editdesc.php",
					data: {partID: partID},
					dataType: "text",
					success: function(response){
						$.colorbox({
							html: response,
							width: "600px",
							height: "520px",
						});
						$('#newsummary').cleditor({
							width:	500,
							height:	270,
							controls:	"bold italic underline subscript superscript | size " +
										"color highlight | bullets numbering | outdent " +
										"indent | alignleft center alignright justify | undo redo | source",
						});
					}
				  });
				
			});

			<?php if(!$nopkgs){?>
			//quantity
			var qtychanged = 0;
			$('#qtybox').hover(
				function() {
					$(this).stop(true, true).animate({height: "<?php echo $numpkgs*45; ?>px"}, 500);
				},
				function() {
					$(this).animate({height: '25px'}, 500);
					if (qtychanged == 1){
						qtychanged = 0;
						var data = $("#qtyForm").serialize();
						$("#qtybox").html('Updating DB...');
						$.ajax({
							type: "POST",
							url: "pages/partqty.php",
							data: data,
							dataType: "text",
							success: function(response){
								$("#qtybox").html(response);
							}
						});						
					}
				}
			);

			$('#qtybox').on('click', '.qtyplus', function(){
				$("#qty_"+$(this).attr('alt')).val( Number($("#qty_"+$(this).attr('alt')).val()) + 1 );
				$("#qty_total").val( Number($("#qty_total").val()) + 1 );
				qtychanged = 1;				
			});
			
			$('#qtybox').on('click', '.qtyminus', function(){
				if($("#qty_"+$(this).attr('alt')).val() != 0){
					$("#qty_"+$(this).attr('alt')).val( Number($("#qty_"+$(this).attr('alt')).val()) - 1 );
					$("#qty_total").val( Number($("#qty_total").val()) - 1 );
					qtychanged = 1;
				}			
			});
			<?php ;}?>

			//create (and delete) kicad .lib file for this part
			$("#kicadlibTool").click( function(event) {
				location.href = 'pages/kicadlib.php?partID=<?php echo $row_part["ID"];?>&pkg='+curpkg;
			});
					
		};
	</script>
</head>
<body>
	<!-- TOOLBAR -->
	<div id="toolbar">
		<a href="index.html"><img src="images/icon.png" class="toolbarobj" title="Main Menu"/></a>
		<img src="images/search.png" class="toolbarobj" id="searchTool" title="Search"/>
		<div class="toolbarobj" id="toolbarDBarrows">
			<a href="viewpart.php?ID=<?php echo $prevID;?>"><img src="images/arrow_left.png" class="toolbararrow" title="Previous Part"/></a>
			<a href="viewpart.php?ID=<?php echo $nextID;?>"><img src="images/arrow_right.png" class="toolbararrow" title="Next Part"/></a>
		</div>
		<div class="toolbarobj" id="toolbarCATarrows">
			<a href="viewpart.php?ID=<?php echo $prevcatID;?>"><img src="images/arrow_left.png" class="toolbararrow" title="Previous Part in Current Category"/></a>
			<a href="viewpart.php?ID=<?php echo $nextcatID ;?>"><img src="images/arrow_right.png" class="toolbararrow" title="Next Part  in Current Category"/></a>
		</div>
		<img src="images/remove.png" class="toolbarobj" id="removepartTool" title="Remove this part"/>
		<img src="images/pkg_add.png" class="toolbarobj" id="addpkgTool" title="Add a package"/>
		<img src="images/pkg_edit.png" class="toolbarobj" id="editpkgTool" title="Edit a package"/>
		<img src="images/editdesc.png" class="toolbarobj" id="editdescTool" title="Edit Description"/>
		<img src="images/kicad_logo.png" class="toolbarobj" id="kicadlibTool" title="Generate .lib for Kicad"/>
	</div>
	<!-- END of TOOLBAR -->
	
	<!-- PKG SELECT -->
	<div id="pkgselect">
			Package: 	
			<select id="pkgselectSelect">
				<?php
				for($i=0; $i<count($pkgs); $i++){
					echo '<option value="'.$pkgs[$i][0].'">'.$pkgs[$i][0].'</option>';
				};
				?>
			</select>
	</div>
	<!-- END of PKG SELECT -->
	
	<!-- PACKAGE -->
	<div id="package"></div>	
	<!-- END of PACKAGE -->
	
	<div id="NAMEandID">
		<div id="myNameandLogo">
			<?php echo $row_part['name'];
			$sqlquery = "SELECT website FROM manufacturers WHERE name = '$row_part[manufacturer]'";
			if($result = $db -> query($sqlquery)) $website = $result -> fetchColumn();
			$logofile = 'data/logos/'.str_replace(' ', '', strtolower($row_part['manufacturer'])).'.gif';
			if (file_exists($logofile) && isset($website)) echo '<a href="'.$website.'" target="_blank"><img src="'.$logofile.'" class="manlogo"/></a>';
			elseif (file_exists($logofile) && !isset($website)) echo '<img src="'.$logofile.'" class="manlogo"/>';
			elseif (!file_exists($logofile) && isset($website)) echo '<a href="'.$website.'" target="_blank"><span class="manufacturerName">['.$row_part["manufacturer"].']</span></a>';
			?>
		</div>
		<div id="myID">
			[
			<?php echo str_replace('@', ' > ', $row_part['category']);?>
			]
			 - <i>Inventory #<?php echo $row_part['ID'];?></i>
		</div>
	</div>
	<div id="qtybox">
		<div class="qtyitem">
			Quantity: <input id="qty_total" type="text" size="1" value="<?php echo $row_part['quantity'];?>" readonly>
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
			<input name="partID" type="hidden" value="<?php echo $row_part['ID'];?>" >
		</form>
	</div>
	<div id="appnotes">
		<div id="appnotestitle">
		Datasheets &amp; Related Application Notes:
		<img src="images/plus.png" id="appnoteplus"/> <img src="images/minus.png" id="appnoteminus"/>
		</div>
		<div id="appnotesdiv"><table id="appnotestable">
			<colgroup>
				<col width="20%"/>
				<col />
			</colgroup>
			<tbody>
				<?php if ($row_part["datasheeturl"] != ''){?>
				<tr url="<?php echo $datasheethref;?>">
					<td><img src="images/pdf_16.png"/>Datasheet</td>
					<td><?php echo $row_part['description'];?></td>
				</tr>							
				<?php
				;}
				//extract linked appnotes from DB
				$appnotes_IDs = preg_split("/@/", $row_part['appnotes'],-1,PREG_SPLIT_NO_EMPTY);
				for($i=0; $i<count($appnotes_IDs); $i++){
					$sqlquery = "SELECT * FROM appnotes WHERE ID =".$appnotes_IDs[$i];
					$appnote = $db -> query($sqlquery);
					$row_appnote = $appnote -> fetch(PDO::FETCH_ASSOC);
					$url ='data/appnotes/'.$row_appnote["ID"].'_'.$row_appnote["name"].'.pdf';
					if(!file_exists($url)) $url = $row_appnote["url"];
					echo '
					<tr url="'.$url.'">
						<td><img src="images/pdf_16.png"/>'.$row_appnote["name"].'</td>
						<td>'.$row_appnote["description"].'</td>
					</tr>
					';
				};
				?>
			</tbody>
		</table></div>		
	</div>
	<div id="contents">
		<div class="cmain_d"><?php echo $row_part['description']?></div>
		<div class="cmain_f"><?php echo $row_part['summary']?></div>
	</div>		
</body>
</html>