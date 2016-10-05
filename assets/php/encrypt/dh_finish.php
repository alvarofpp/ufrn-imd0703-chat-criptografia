<?php
// Pega arquivo JSON com as configurações
$json_file = file_get_contents('../database/config.json');
$json = json_decode($json_file, true);

// Cria objeto da classe chatCriptografia, declarando o tipo de criptografia e a chave usada
include('../../php/database/chat_criptografia.php');
$cript = new chatCriptografia($json['encryption'], $json['key']);
$cript->import('../encrypt/s_des_script.php');
$cript->import('../encrypt/rc4_script.php');
$cript->encryption = $json['encryption'];

$texto_final = '';
$chat = fopen('../database/chat.txt', 'r+'); // Pega o chat
// Ler o arquivo até o final
while (!feof($chat)) {
    $linha = fgets($chat, 4096); // Ler uma linha do arquivo e avança o ponteiro
    $final = (feof($chat)) ? 1 : 0; // Verifica se é o final do arquivo
    if ($final == 0) {
        $linha = substr($linha, 0, strlen($linha) - 1); // Se não, retira o último caractere que é "\n"
    }

    // Esse IF serve para evitar imprimir algo caso não tenha nada no arquivo
    if (strlen($linha) > 1) {
        $cript->key = $json['key'];
        $texto = $cript->action('d', $linha);

        $cript->key = $_GET['psk'];
        $texto_final .= "\n" . $cript->action('c', $texto);
    }
}
fclose($chat); // Fecha

$texto_final = substr($texto_final, 1); // Retira o "\n" inicial
$chat = fopen('../database/chat.txt', 'w+'); // Abre chat para escrita
fwrite($chat, $texto_final); // Sobrescreve
fclose($chat); // Fecha

// Salva as configurações no JSON (criptografia e chave)
session_start();
$json['key'] = $_GET['psk'];
$fp = fopen('../database/config.json', 'w');
fwrite($fp, json_encode($json));
fclose($fp);

header("Location: ../../../chat.php"); // Redireciona
