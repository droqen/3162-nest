<?php
	if ($_GET['mo']!=null && $_GET['yr']!=null) {
		$now = new DateTime($_GET['yr'] . '-' . ($_GET['mo']+1) . '-1');
	} else {
		$now = new DateTime('now');
	}

	$month = $now->format('m');
	$year = $now->format('Y');
	
	if ($currentGameMonth) {
		$month = $currentGameMonth;
		$year = $currentGameYear;
	}

	require_once "db/dbconn.php";
	if (!$conn) { die("No connection"); }
	$sth = $conn->query("SELECT zipname, release_date, DAY(release_date) FROM games 
		WHERE MONTH(release_date) = $month
			AND YEAR(release_date) = $year
		ORDER BY release_date ASC");

	$list=array();

	function addday($d, $zipname, $pending) {
		global $year,$month,$list;
		$time=mktime(12, 0, 0, $month, $d, $year);
		// $woy=
		$dow=date('w',$time);
		if (date('m', $time)==$month) {
			if ($zipname) { $list[] = [$zipname, $d]; }
			else { $list[] = 0; }
		}
	}

	$d = 1;

	$first_time=mktime(12, 0, 0, $month, $d, $year);
	$first_dow=date('w',$first_time);
	for($i=0;$i<$first_dow;$i++) {
		$list[] = -1;
	}

	foreach ($sth as $row) {
		$zipname = $row[0];
		$game_pubdate = $row[1];
		$game_pubday = $row[2];
		for(;$d<$game_pubday;$d++) { addday($d, false, false); }
		addday($d++, $zipname, false);
	}

	for(;$d<=31;$d++) { addday($d, false, false); }

	// echo "<pre>";
	// print_r($list);
	// echo "</pre>";

	// $calendarDisplayMonth = 
	// get all days in this month
	
	$monthname = date('F',$first_time);
	$listcount = count($list);

	$prevmonth = $month-1-1;
	$prevyear = $year;
	if ($prevmonth < 0) {
		$prevmonth += 12;
		$prevyear -= 1;
	}
	$nextmonth = $month+1-1;
	$nextyear = $year;
	if ($nextmonth >= 12) {
		$nextmonth -= 12;
		$nextyear += 1;
	}

	$MONTH_NAMES = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
	$prevmonthname = $MONTH_NAMES[$prevmonth];
	$nextmonthname = $MONTH_NAMES[$nextmonth];

	echo "<div class='month-label'><a href='javascript:showcal($prevmonth,$prevyear);'>$prevmonthname $prevyear</a></div>\n";
	// echo "<div class='month-label'><a href='/index-calendar.php?mo=$prevmonth&yr=$prevyear'>$prevmonthname $prevyear</a></div>\n";
	echo "<div class='month-label'>$monthname $year</div>\n";
	for($i=0;$i<$listcount;$i++) {
		if($i%7==0) {
			if($i>0){echo "\t</div>\n";} // end of prev week
			echo "\t<div class='week'>";
		}

		if ($list[$i]==-1) {
			echo "\t\t<div class='no day'></div>";
		} else if ($list[$i]==0) {
			echo "\t\t<div class='miss day'></div>";
		} else {
			// an actual game was released today
			// <a class="link-day day" href='?game=$zipname'><img src="games/2024/03/08--databug_sequel.png" title="Databug"></a>
			$zipname = $list[$i][0];
			$gamepubday = $list[$i][1];
			$month2 = str_pad($month, 2, "0", STR_PAD_LEFT);
			$day2 = str_pad($gamepubday, 2, "0", STR_PAD_LEFT);
			$a_href = "?game=$zipname";
			$img_src = "/games/$year/$month2/$day2--$zipname.png";
			echo "\t\t<a class='link-day day' href='$a_href'><img src='$img_src'></a>";
		}
	}
	echo "\t</div>\n"; // end of last week
	echo "<div class='month-label'><a href='javascript:showcal($nextmonth,$nextyear);'>$nextmonthname $nextyear</a></div>\n";
	// echo "<div class='month-label'><a href='/index-calendar.php?mo=$nextmonth&yr=$nextyear'>$nextmonthname $nextyear</a></div>\n";