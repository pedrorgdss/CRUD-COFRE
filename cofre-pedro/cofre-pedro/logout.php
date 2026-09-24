<?php
require __DIR__ . '/config.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); exit; }
checkToken();
$_SESSION = [];
session_destroy();
redirect('login.php');
