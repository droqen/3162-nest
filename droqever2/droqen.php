<?php

require_once __DIR__ . '/db/.droqencredentials.php';

if (isset($_SESSION['username']) && $_SESSION['username'] == 'droqen') {
	require_once __DIR__ . '/pages/admin-page.php';
} else {
	require_once __DIR__ . '/pages/login-page.html';
}