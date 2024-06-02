<p id='gamelist' style='position:fixed; left: 10px; top: 10px;'>
<?php
	require_once "dbconn.php";
	if (!$conn) { die("No connection"); }
	$sth = $conn->query("SELECT zipname, title, release_date FROM games ORDER BY release_date ASC");
	foreach ($sth as $row) {
		$zipname = $row[0];
		$game_title = $row[1];
		$game_pubdate = $row[2];
		echo "<a href='?game=$zipname'>$game_title</a> <i>($game_pubdate)</i><br/>";

		// $year = "0000";
		// $month = "00";
		// $day = "00";
		// $explodedDate = explode("-", $row[2]);
		// if (count($explodedDate)==3) {
		// 	$year = $explodedDate[0];
		// 	$month = $explodedDate[1];
		// 	$day = $explodedDate[2];
		// }
		// $zip_path = __DIR__."/games/$year/$month/$day--".$row[3].".zip";
		// echo
		// 	"<a href='$zip_path'>" .
		// 		$row[1] . "<i>(" . $row[2] . ")</i>" .
		// 	"</a>" .
		// 	"<br/>";
	}

	// function str_ends_with($val, $str) {
	// 	if(mb_substr($val, -mb_strlen($str), mb_strlen($str)) == $str) {
	// 		return true;
	// 	} else {
	// 		return false;
	// 	}
	// }
	// foreach (scandir(__DIR__.'/games') as $filename) {
	// 	if (str_ends_with($filename, ".zip")) {
	// 		$game = substr($filename, 0, strrpos($filename, "."));
	// 		echo "* <a href='?game=$game'>$game</a><br/>";
	// 	}
	// }
?>
</p>