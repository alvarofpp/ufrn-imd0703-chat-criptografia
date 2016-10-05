<?php
$num = $_GET['num']; // Número novo

$dh_file = file_get_contents('dh.json'); // Ler JSON
$dh = json_decode($dh_file, true); // Decodifica JSON
session_start(); // Inicia sessão
$dh['users'][$_SESSION['username']] = (int)$num; // Atribui novo número ao do usuário logado

$json = fopen('dh.json', 'w+'); // Abre arquivo JSON para escrita
fwrite($json, json_encode($dh)); // Escreve novas configurações
fclose($json); // Fecha

header("Location: ../../../chat.php"); // Redireciona