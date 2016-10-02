<html>
<head>
    <title>Nova senha</title>
    <meta charset="UTF-8"/>
</head>
<body>
<?php
$target_dir = "";
$target_file = $target_dir . basename($_FILES["image"]["name"]);

// Salva imagem
if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
    echo "The file " . basename($_FILES["image"]["name"]) . " has been uploaded.";
} else {
    echo "Sorry, there was an error uploading your file.";
}

$command = escapeshellcmd('python mostrar.py');
$output = shell_exec($command); // Recebe a mensagem escondida

include('../../php/encrypt/cifra_data.php');
$cifradata = new CifraData($_POST['data'], $output);
$texto_final = $cifradata->cifradata('d');

$config = explode('-', $texto_final); // Transforma em array
$config[1] = substr($config[1], 0, strlen($config[1]) - 1); // Retira o "\n"
echo '<br/>A nova senha é: <b>' . $config[1] . '</b>';

// Pega arquivo JSON com as configurações
$json_file = file_get_contents('../../php/database/config.json');
$json = json_decode($json_file, true);

// Cria objeto da classe chatCriptografia, salvando o tipo de criptografia e a chave usada
include('../../php/database/chat_criptografia.php');
$cript = new chatCriptografia($json['encryption'], $json['key']);
$cript->import('../../php/encrypt/s_des_script.php');
$cript->import('../../php/encrypt/rc4_script.php');

$texto_final = '';
// Pega o chat
$chat = fopen('../../php/database/chat.txt', 'r+');
// Ler o arquivo até o final
while (!feof($chat)) {
    $linha = fgets($chat, 4096); // Ler uma linha do arquivo e avança o ponteiro
    $final = (feof($chat)) ? 1 : 0; // Verifica se é o final do arquivo
    if ($final == 0) {
        $linha = substr($linha, 0, strlen($linha) - 1); // Se não, retira o último caractere que é "\n"
    }

    // Esse IF serve para evitar imprimir algo caso não tenha nada no arquivo
    if (strlen($linha) > 1) {
        $cript->encryption = $json['encryption'];
        $cript->key = $json['key'];
        $texto = $cript->action('d', $linha);

        $cript->encryption = $config[0];
        $cript->key = $config[1];
        $texto_final .= "\n" . $cript->action('c', $texto);
    }
}
fclose($chat);
$texto_final = substr($texto_final, 1); // Retira o "\n" inicial
$chat = fopen('../../php/database/chat.txt', 'w+');
fwrite($chat, $texto_final);
fclose($chat);

// Salva as configurações (criptografia e chave)
session_start();
$json['encryption'] = $config[0];
$json['key'] = $config[1];
$fp = fopen('../../php/database/config.json', 'w');
fwrite($fp, json_encode($json));
fclose($fp);

echo '<br/>Criptografia: <b>' . $config[0] . '</b>';
?>

<form method="GET" action="../../../chat.php">
    <input type="submit" value="OKAY"/>
</form>
</body>
</html>
