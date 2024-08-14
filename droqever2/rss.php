<?php
require_once __DIR__ . '/db/init-$conn.php';

header('Content-Type: application/xml; charset=utf-8');
echo '<?xml version="1.0" encoding="utf-8"?>' . PHP_EOL;

// active games.

$res = $conn->query("SELECT postid, name, DATE(postdate), DATE(postexpires), NOW() < postexpires
	FROM posts
	WHERE wakes
	ORDER BY
		IF(NOW()<postexpires,postdate,DATE(postexpires)) DESC,
		postexpires DESC,
		postid DESC
	LIMIT 10");?>

<rss version="2.0">
    <channel>
        <title>droqever</title>
        <link>https://www.droqever.com/</link>
        <description>droqen's small games that expire after a week or so</description>
        <language>en-us</language>
        <?php
			foreach ($res as $row):
				$postid = $row[0];
				$postname = $row[1];
				$postdate = $row[2];
				$postexpirydate = $row[3];
				$is_active = $row[4];
				$greeting = "hello";
				$greeting_punc = " <3";
				$newsdate = $postdate;
				$news = "is playable now!";

				if (!$is_active) {
					$greeting = "goodbye";
					$greeting_punc = " ;-;";
					$newsdate = $postexpirydate;
					$news = "expired. (read comments)";
				}
		?>
        <item>
            <title><?=$greeting?> <?=$postname?> <?=htmlspecialchars($greeting_punc)?></title>
            <link>http://www.droqever.com/-/<?=$postname?></link>
            <description><?=$postname?> <?=$news?></description>
            <pubDate><?=date('r', strtotime($newsdate))?></pubDate>
        </item>
        <?php endforeach; ?>
    </channel>
</rss>

<!-- foreach ($res as $row) {
	// $is_expired = !$is_active;
	if ($is_active) {
		echo "<a href='/-/$postname'>$postname</a> posted $postdate (play game)<br/>";
	} else {
		echo "<a href='/-/$postname'>$postname</a> expired $postexpirydate (read comments)<br/>";
	}
} -->