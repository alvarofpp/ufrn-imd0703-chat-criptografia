<?php
$dh_file = file_get_contents('dh.json'); // Ler JSON
$dh = json_decode($dh_file, true); // Decodifica JSON

session_start(); // Inicia sessão
$_SESSION['num_dh'] = $_POST['q'].'-'.$_POST['a']; // Atribui valores

echo '<h2>Escolha o outro usuario com que ira realizar a troca de chaves</h2>';
echo '<form method="get" action="diffie_hellman.php">';
foreach ($dh['users'] as $key => $value) {
    if(!($key == $_SESSION['username'])){
        echo '<input type="radio" name="user2" value="'.$value.'" required />'.$key.'<br/>';
    }
}
echo '<input type="submit" value="Continuar"/>';
echo '</form>';

exit();