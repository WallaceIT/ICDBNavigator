<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title>ICDBN - PackageWorkBench</title>
	<link rel="icon" type="image/ico" href="../../favicon.ico">
	<link rel="stylesheet" href="../../css/colorbox.css">
	<script src="../../js/jquery.min.js" type="text/javascript" charset="utf-8"></script>
	<script src="../../js/jquery.colorbox-min.js" type="text/javascript" charset="utf-8"></script>
	<script src="../../js/raphael-min.js" type="text/javascript" charset="utf-8"></script>
	<script src="codemirror/codemirror.js"></script>
	<link rel="stylesheet" href="codemirror/codemirror.css">
	<script src="codemirror/javascript.js"></script>
	<script type="text/javascript" charset="utf-8">
		window.onload = function () {

			
			//package dynamic dimensions
			var scalefactor = $(document).height() / 690;  
			var pincolor = '#bfbfbf';
			var paper = Raphael("pkg", 230, 600*scalefactor);

			var editor = CodeMirror.fromTextArea(document.getElementById('code'), {
		        lineNumbers: true 
		      });

			$('#eval').click( function() {
				paper.clear();
				var code = editor.getValue();
				eval(code);
				$('.pinobj').attr('fill',pincolor);
			});

			$('#save').click( function() {
				$.colorbox({
					html: $('#popup').html(),
					width: '600px',
					height: '220px'				
				});
			});

			$( document ).on('submit', '#savepkgForm', function(event) {
				event.preventDefault();
				var pkgName = $(this).find('input[name="pkgName"]').val();
				var pkgPinsNum = $(this).find('input[name="pkgPinsNum"]').val();
				var code = editor.getValue();
				$.ajax({
					type: "POST",
					url: "pkgcreate.php",
					data: {
						name: pkgName,
						pinsnum: pkgPinsNum,
						code: code
						},
					dataType: "text",
					success: function(response){
						$.colorbox({
							html: response,
							width: '600px',
							height: '220px'				
						});
					}
				});
			});

			$( document ).on('click', '.OkButton', function(event) {
				$.colorbox.close();
			});

			$('#ahowto').click(function(event) {
				event.preventDefault();
				$.colorbox({
					html: $('#howto').html(),
					width: '600px',
					height: '300px'
				});
			});						
		
		};
	</script>
	<style type="text/css">
		body {
	    background: url(../../images/metal.gif) repeat;
	    color: #000;
		}
		div#pkg{
			float:left;
			width: 25%;
			min-width: 230px;
			border: solid 1px #000000;
			margin: 0 0 0 3%;
			padding: 1%;
		}
		div#text{
			float:left;
			width: 63%;
			margin: 0 0 0 2%;
			
		}
		div.center{
		text-align: center;
		}
	</style>
</head>
<body>
	<div id="pkg"></div>
	<div id="text">
		<div class="center"><b>Package Generation Workbench</b></div>
		Create your own package drawing using <a href="http://raphaeljs.com/" target="_blank">Raphael</a> library, with these preset variables:<br>
		 - <b>paper</b>: a canvas object on which to draw;<br>
		 - <b>scalefactor</b>: a scale factor applied when resizing ICDBN browser window;<br>
		 - <b>partname</b>: a test part name (14ch);<br>
		Press Eval button to render your package and Save button to save your package.
		<br>
		<b>NOTE:</b> In order to pin labelling to work, each pin X must have the following attributes: class = pinobj, name = pinX, orientation = L/R/T/B (<a href="#" id="ahowto">example</a>)
		<br><br>
	<textarea id="code"></textarea>
	<br>
		<div class="center">
			<input type="button" id="eval" value="Eval"> 
			<input type="button" id="save" value="Save">
		</div>
	</div>
	<div id="popup" hidden="hidden">
		<br>
		<b>NOTE:</b> Nothing will check your code, so make sure it works!
		<br><br>
		<form id="savepkgForm" action="#">
			<input name="pkgName" type="text" size="40" placeholder="Package Name..." required>
			<br>
			Pins number: <input name="pkgPinsNum" type="text" size="3" pattern="[0-9]+" required>
			<br><br>
			<input type="button" class="OkButton" value="Cancel">
			<input type="submit" value="Save!">
		</form>
	</div>
	<div id="howto" hidden="hidden">
		<br>
		<b>Example</b><br><br>
		var pin = {};<br>
		pin.pin1 = paper.rect(10,235,20,22);<br>
		pin.pin1.node.setAttribute("class","pinobj");<br>
		pin.pin1.node.setAttribute("name","pin1");<br>
		pin.pin1.node.setAttribute("orientation", "L");<br><br>
		<input type="button" class="OkButton" value="Close">
	</div>	
</body>
</html>