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
	echo("/!\ upload failed (access denied), please contact your server administrator (droqen)");
	exit();
}

if(isset($_POST["submit"])) {

	echo "server document root = " . $_SERVER['DOCUMENT_ROOT'];
	echo "<br/><br/>";

	// $target_dir = $_SERVER['DOCUMENT_ROOT'] . "uploads/";
	$base_filename = strtolower(pathinfo( basename($_FILES["fileToUpload"]["name"]) ,PATHINFO_FILENAME));
	$base_filetype = strtolower(pathinfo( basename($_FILES["fileToUpload"]["name"]) ,PATHINFO_EXTENSION));
	$uploadOk = 0;

	$year = "0000";
	$month = "00";
	$day = "00";
	echo "OK, attempting to add game...";
	echo "<br/>gameTitle = " . $_POST["gameTitle"];
	echo "<br/>gameDescription = " . $_POST["gameDescription"];
	echo "<br/>releaseDate = " . $_POST["releaseDate"];
	$explodedDate = explode("-", $_POST["releaseDate"]);
	if (count($explodedDate)==3) {
		$year = $explodedDate[0];
		$month = $explodedDate[1];
		$day = $explodedDate[2];
		echo "<br/> - yyyy = $year";
		echo "<br/> - mm = $month";
		echo "<br/> - dd = $day";
		$uploadOk = 1; // ok!
	} else {
		echo "<br/><b>Problem!</b> Release date not properly formatted (it is not hyphen-delimited into three parts)";
		echo "<br/>-- explodedDate = $explodedDate";
		echo "<br/>-- count(explodedDate) = " . count($explodedDate);
		$uploadOk = 0;
	}
	echo "<br/>fileToUpload = $base_filename.$base_filetype";
	if ($base_filetype != "zip") {
		echo "<br/><b>Problem!</b> Uploaded file is invalid (only ZIP files are allowed)";
		$uploadOk = 0;
	}
	if ($_FILES["fileToUpload"]["size"] > 500000) {
		echo "<br/><b>Problem!</b> Uploaded file is too large (must be <= 500KB)";
		$uploadOk = 0;
	}

	echo "<br/>iconToUpload = " . $_FILES["iconToUpload"]["name"];

	$check = getimagesize($_FILES["iconToUpload"]["tmp_name"]);

	if($check !== false) {
		if($check[0]==100 && $check[1]==100) {
			if($check[2]==IMAGETYPE_PNG) {
				echo "<br/>-- <i>(Looks good!)</i>";
				$uploadOk = 1;
			} else {
				echo "<br/><b>Problem!</b> Icon is not PNG (must be PNG)";
				$uploadOk = 0;
			}
		} else {
			echo "<br/><b>Problem!</b> Icon is wrong size (must be EXACTLY 100x100)";
			echo "<br/>-- size is ".$check[0]." x ".$check[1];
			$uploadOk = 0;
		}
		// echo "Icon is an image - " . $check["mime"] . ".";
	} else {
		echo "<br/><b>Problem!</b> Icon file is not an image.";
		$uploadOk = 0;
	}

	$target_filename = "$day--$base_filename.$base_filetype";
	$target_filedir = $_SERVER['DOCUMENT_ROOT'] . "/games/$year/$month";
	$target_filepath = "$target_filedir/$target_filename";
	echo "<br/>target filepath = " . $target_filepath;
	if (file_exists($target_filepath)) {
		echo "<br/><b>Problem!</b> File already exists there";
		$uploadOk = 0;
	}
	$target_iconpath = "$target_filedir/$day--$base_filename.png";
	echo "<br/>target iconpath = " . $target_iconpath;
	if (file_exists($target_iconpath)) {
		echo "<br/><b>Problem!</b> File already exists there";
		$uploadOk = 0;
	}

	if (!is_dir($target_filedir)) {
		if (mkdir($target_filedir, 0777, true) === false) {
			echo "<br/><b>Problem!</b> Failed making dir $target_filedir";
			$uploadOk = 0;
		}
	}

	echo "<br/>...";
	echo "<br/>...";

	if ($uploadOk == 0) {
		echo "<br/><b>Game Add action terminated.</b> Not moving on to MySQL due to previous problems. Fix them first!";
		exit;
	}

	include ".dbcredentials.php";
	$dsn = "mysql:host=$dbhost;dbname=$dbname";
	$options = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION];
	try {
		$conn = new PDO($dsn, $dbuser, $dbpass, $options);
	} catch (PDOException $e) {
		die("<br/><b>Problem!</b> PDO connection failed: " . $e->getMessage() . "");
	}

	$sth = $conn->prepare("INSERT INTO games (release_date, zipname, title, longdesc) VALUES (?, ?, ?, ?)");

	$id = -1;

	try {
		$id = $sth->execute([$_POST["releaseDate"], $base_filename, $_POST["gameTitle"], $_POST["gameDescription"]]);
	} catch (PDOException $e) {
		die("<br/><b>Problem!</b> PDO execute failed: " . $e->getMessage() . "");
	}

	echo "<br/><i>Success! (1/3)</i> Entry added to db. Response: $id";

	if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_filepath)) {
		echo "<br/><i>Success! (2/3)</i> The file " . htmlspecialchars( basename( $_FILES["fileToUpload"]["name"] ) ) . " has been uploaded to $target_filepath.";
	} else {
		echo "<br/><b>Problem! BIG problem!</b> There was an error uploading the file. An invalid entry now exists in the db.";
	}
	
	if (move_uploaded_file($_FILES["iconToUpload"]["tmp_name"], $target_iconpath)) {
		echo "<br/><i>Success! (3/3)</i> The icon " . htmlspecialchars( basename( $_FILES["iconToUpload"]["name"] ) ) . " has been uploaded to $target_iconpath.";
	} else {
		echo "<br/><b>Problem! BIG problem!</b> There was an error uploading the icon. An invalid entry now exists in the db.";
	}
} else {
	header("Location: index.html");
	exit;
}