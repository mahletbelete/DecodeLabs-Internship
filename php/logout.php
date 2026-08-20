<?php

ini_set('session.cookie_path', '/');
session_start();
session_destroy();


$host    = $_SERVER['HTTP_HOST'];
$baseDir = rtrim(dirname(dirname($_SERVER['SCRIPT_NAME'])), '/\\');
header('Location: http://' . $host . $baseDir . '/index.html');
exit;
