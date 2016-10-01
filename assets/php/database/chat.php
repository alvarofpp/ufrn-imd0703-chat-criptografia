<?php
echo '1';
include('chat_criptografia.php');

$conteudo = '';
$lendo = fopen('chat.txt', "r+"); // Abre o arquivo para leitura
// Ler o arquivo até chegar no fim
while (!feof($lendo)) {
    $linha = fgets($lendo, 4096); // Ler uma linha do arquivo
    $conteudo = $conteudo . $linha; // Adiciona a variável $conteudo
}
fclose($lendo); // Fecha o ponteiro do arquivo

// Cria objeto da classe chatCriptografia, salvando o tipo de criptografia e a chave usada
$cript = new chatCriptografia($_POST['json']['encryption'], $_POST['json']['key']);
$cript->import('../encrypt/s_des_script.php');
$cript->import('../encrypt/rc4_script.php');

$texto = $cript->action('c', $_POST['mensagem']); // Cripta o texto enviado

$escrevendo = fopen('chat.txt', "w+"); // Abre o arquivo para escrita
// Verifica se existia algo no arquivo
if (strlen($conteudo) > 0) {
    $dados = $conteudo . "\n" . $texto; // Adiciona quebra de linha, caso exista
    $escreve = fwrite($escrevendo, $dados); // Escreve no arquivo
} else {
    $escreve = fwrite($escrevendo, $texto); // Não adiciona quebra de linha
}
fclose($escrevendo); // Fecha o ponteiro do arquivo
return true;
