<?php

$ip = $_SERVER['REMOTE_ADDR'];
$ip_allowed = false;

$handle = fopen("whitelist.txt", "r");
if ($handle) {
    while (($line = fgets($handle)) !== false) {
        if ($ip == $line) {
			$ip_allowed = true;
			break;
		}
    }
    fclose($handle);
}

if(!$ip_allowed) {
	echo("/!\ access denied, please contact your server administrator (droqen)");
	exit();
}

?>

<!DOCTYPE html>
<html>
<head>
	<style>
		div.inputContainer {
			position: relative;
		}
		div.inputContainer span.fieldName {
			display: inline-block;
			text-align: right;
			width:120px;
		}
		input#gameTitle, input#releaseDate, input#fileToUpload, input#iconToUpload, textarea#gameDescription {
			display: inline-block;
			width: 300px;
			border: 2px dotted rgb(208, 220, 255);	
			border-radius: 8px;
			padding: 10px;
			margin-bottom: .5em;
			margin-left: .5em;
		}

		div.inputContainer#submit {
			padding: 10px;
		}
	</style>
</head>
<body>

<form id="upload" action="do-upload.php" method="post" enctype="multipart/form-data">
	<div class="inputContainer">
		<span class="fieldName">Title:</span><input type="text" name="gameTitle" id="gameTitle">
	</div>
	<div class="inputContainer">
		<span class="fieldName">Date:</span><input type="date" name="releaseDate" id="releaseDate">
	</div>
	<div class="inputContainer">
		<span class="fieldName">Game file (ZIP):</span><input type="file" name="fileToUpload" id="fileToUpload">
	</div>
	<div class="inputContainer">
		<span class="fieldName">100x100 icon (PNG):</span><input type="file" name="iconToUpload" id="iconToUpload">
	</div>
	<div class="inputContainer">
		<span class="fieldName"></span><span class="fieldName" style="position:absolute; left:0; top:.6em;">Description:</span><textarea name="gameDescription" id="gameDescription">Description for game goes here</textarea>
	</div>
	<div class="inputContainer" id="submit">
		<input type="submit" value="Add Game (live!)" name="submit" id="submit"><br/>
	</div>
</form>

<script>
	document.getElementById("releaseDate").valueAsDate = new Date();
</script>

</body>
</html>