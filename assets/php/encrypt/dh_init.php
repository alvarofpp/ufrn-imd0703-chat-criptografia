<?php
$dh_file = file_get_contents('dh.json');
$dh = json_decode($dh_file, true);

session_start();
$_SESSION['num_dh'] = $_POST['q'].'-'.$_POST['a'];

echo '<form method="get" action="diffie_hellman.php">';
foreach ($dh['users'] as $key => $value) {
    if(!($key == $_SESSION['username'])){
        echo '<input type="radio" name="user2" value="'.$value.'" required />'.$key.'<br/>';
    }
}
echo '<input type="submit" value="Continuar"/>';
echo '</form>';

exit();