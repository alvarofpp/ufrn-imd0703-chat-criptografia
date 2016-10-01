<?php
$num = $_GET['num'];

$dh_file = file_get_contents('dh.json');
$dh = json_decode($dh_file, true);
session_start();
$dh['users'][$_SESSION['username']] = (int)$num;

$json = fopen('dh.json', 'w+');
fwrite($json, json_encode($dh));
fclose($json);

header("Location: ../../../chat.php");