<?php

$nova_senha = '';
switch ($_GET['c']) {
    case 's_des':
        for ($i = 0; $i < 10; $i++) {
            $nova_senha .= rand(0, 1);
        }
        break;
    case 'rc4':
        $nova_senha = generateRandomString(rand(4, 10));
        break;
}
$texto_final = $_GET['c'] . '-' . $nova_senha;

include('../../php/encrypt/cifra_data.php');
$cifradata = new CifraData('07/07/1822', $texto_final);
$texto_final = $cifradata->cifradata('c');

$command = escapeshellcmd("python esconder.py '" . $texto_final . "'");
$output = shell_exec($command);

header("Location: ../../../trocar_senha.php");

function generateRandomString($length)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}