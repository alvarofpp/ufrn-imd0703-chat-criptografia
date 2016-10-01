<?php
include('chat_criptografia.php');
$contador = $_POST['contador']; // O contador tem a quantidade de linhas lidas até o momento

$linhas = count(file('chat.txt')); // Conta a quantidade de linhas do arquivo de chat
$texto_final = '';

// Verifica se existe alguma linha não escrita no arquivo
if ($contador < $linhas) {
    // Cria objeto da classe chatCriptografia, salvando o tipo de criptografia e a chave usada
    $cript = new chatCriptografia($_POST['json']['encryption'], $_POST['json']['key']);
    $cript->import('../encrypt/s_des_script.php');
    $cript->import('../encrypt/rc4_script.php');

    $lendo = fopen('chat.txt', "r+");
    $i = 1;
    // Ler o arquivo até chegar no fim
    while (!feof($lendo)) {
        $linha = fgets($lendo, 4096); // Ler uma linha do arquivo e avança o ponteiro
        // Quando esse IF for ativado, significa que é uma linha não lida
        // Pois é posterior as linhas já lidas pelo usuário
        if ($i > $contador) {
            $texto = $cript->action('d', $linha); // Decripta
            $info = explode(";;;", $texto); // Transforma em array
            // Deixa no formato JSON
            $texto_final .= ',{"username":"' . $info[0] . '","data_hora":"' . $info[1] . '","mensagem":"' . $info[2] . '"}';
        }
        $i++;
    }
    fclose($lendo); // Fecha o ponteiro
    $texto_final = substr($texto_final, 1); // Elimina a "," inicial
    echo json_encode('[' . $texto_final . ']'); // Transforma em JSON
}
return true;