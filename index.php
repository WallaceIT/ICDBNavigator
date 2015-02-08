<?php session_start(); ?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title>ICDB Navigator</title>
	<link rel="icon" type="image/ico" href="favicon.ico">
	<link rel="stylesheet" href="css/index.css" type="text/css" media="screen">
	<link rel="stylesheet" href="css/colorbox.css" />
	<link rel="stylesheet" href="css/jquery.cleditor.css" />
	<script src="js/jquery.min.js" type="text/javascript" charset="utf-8"></script>
	<script src="js/jquery.colorbox-min.js" type="text/javascript" charset="utf-8"></script>
	<script src="js/jquery.cleditor.min.js" type="text/javascript" charset="utf-8"></script>
	<script type="text/javascript" charset="utf-8">
	window.onload = function(){
		
		//class .OkButton close colorbox
		$( document ).on('click', '.OkButton', function() {
			$.colorbox.close();
		});
		
		//class .RefreshButton close colorbox and refresh the page
		$( document ).on('click', '.RefreshButton', function() {
			$.colorbox.close();
			parent.location.reload();
		});
		
        <?php if(!isset($_SESSION['logged'])){ ?>
        // login
        $("#login").click(function() {
			$.colorbox({
				html: $("#loginform").html()
            });
		});
        <?php ;} ?>
        
		//search
		$('#searchForm').submit(function(event) {
			event.preventDefault();
			term = $(this).find( 'input[name="tosearch"]' ).val(),
			$.ajax({
				type: "POST",
				url: "pages/search.php",
				data: {string: term, all: '0'},
				success: function(response){
					$("#results").html(response).fadeIn('slow');
					$("#resultstitle").html("Entries that match your search criteria:");					
				}
			});
		});

		$('#results').on('mousedown', '#resultstable tr.result_part', function(event) {
			var url = $(this).attr("url");
			switch(event.which)
		    {
		        case 1:
		        	window.location=url;
		        break;
		        case 2:
		        	window.open(url,"_blank");
		        break;
		    }
			return false;
		});
		
		//toolbar elements:
		$('#categories').click( function() {
			$.ajax({
				type: "POST",
				url: "pages/categories.php",
				data: {category: 'all'},
				success: function(response){
					$("#results").html(response).fadeIn();
				}
			});
		});
		$('#results').on('click', '#resultstable  tr.result_category', function() {
			$.ajax({
				type: "POST",
				url: "pages/categories.php",
				data: {category: $(this).attr("cat")},
				success: function(response){
					$("#results").html(response).fadeIn();
				}
			});
		});
		$('#allparts').click( function() {
			$.ajax({
				type: "POST",
				url: "pages/search.php",
				data: {string: '%', all: '1'},
				success: function(response){
					$("#results").html(response).fadeIn();
					$("#resultstitle").html("Parts in the Database:");
				}
			});
		});
		$('#allappnotes').click( function() {
			$.ajax({
				type: "POST",
				url: "pages/search.php",
				data: {string: '%', all: '2'},
				success: function(response){
					$("#results").html(response).fadeIn();
					$("#resultstitle").html("Application Notes in the Database:");
				}
			});
		});
        
        <?php if(isset($_SESSION['logged']) && $_SESSION['logged'] == 'OK'){ ?>
		$("#settings").click(function() {
			$.colorbox({
				href: "pages/utilities.php",
				width: "600px",
				height: "550px",
				overlayClose: false,
				escKey: false});
		});
		$('#addpart').click( function() {
			$.ajax({
				type: "POST",
				url: "pages/addpart.php",
				success: function(response){
					$.colorbox({
						html:response,
						width: "600px",
						height: "550px",
						});
					$("#clearea").cleditor({
						width:	500,
						height:	200,
						controls:	"bold italic underline subscript superscript | size " +
									"color highlight | bullets numbering | outdent " +
									"indent | alignleft center alignright justify | undo redo | source",
					});
				}
			});
		});
		//adding part...
		$( document ).on('submit', '#addpartForm', function(event){
			event.preventDefault();
			var name = $(this).find('input[name="addpartName"]').val();
			var pkg = $(this).find('select[name="addpartPackage"]').val();			
			if($(this).find('input[name="addpartDatasheetUrl"]').val() != ''){
				$.colorbox({
					html:"<div id='popup_header'>Downloading Datasheet, please wait...</div>",
					overlayClose: false,
					escKey: false
					})
			;}
			
			$.ajax({
				type: "POST",
				url: "pages/addpart.php",
				data: $(this).serialize(),
				dataType: "text",
				success: function(response1){
					if (response1 != 0) {
						$.colorbox({html:response1});
						}
					else {
						$.ajax({
							type: "POST",
							url: "pages/pinpopulate.php",
							data: {	name: name, pkg: pkg },
							dataType: "text",
							success: function(response2){
								$.colorbox({
									html:response2,
									width: "600px",
									height: "550px",
									overlayClose: false,
									escKey: false
									});
							}
						});
					}
				}
			});
		});
        
        <?php ;} ?>
			
	};
	</script>	
</head>
<body>
	<div id="logo"><img src="images/logo.png" width="60%"/></div>
	<div id="searchbox">
		<form action="#" id="searchForm">
			<input name="tosearch" type="text" placeholder="Search..." autofocus="autofocus">
			<input type="submit" name="search" value="Go!">
		</form>
		<div id="toolbar">
			<img src="images/categories.png" id="categories" class="tools" title="View Categories..."/>
			<img src="images/ic_22.png" id="allparts" class="tools" title="View all Parts..."/>
			<img src="images/pdf_22.png" id="allappnotes" class="tools" title="View all Application Notes..."/>
            <?php if(isset($_SESSION['logged']) && $_SESSION['logged'] == 'OK'){ ?>
			<img src="images/settings.png" id="settings" class="tools" title="Utilities..."/>
			<img src="images/plus_22.png" id="addpart" class="tools" title="Add New Part..."/>
            <?php ;} ?>
		</div>
	</div>
	<div id="results" class="hide"></div>
    <div id="loginform" class="hide">
	<div id="popup_header">
        	<form action="pages/login.php" method="post">
			<input name="username" type="text" placeholder="Username..." autofocus="autofocus">
            		<br>
            		<input name="password" type="password" placeholder="Password...">
           		<br><br>
			<input type="submit" value="Login">
		</form>
	</div>
    </div>
    <?php if(!isset($_SESSION['logged']))
        echo '<input type="button" id="login" value="Login">';
    ?>
</body>
</html>
