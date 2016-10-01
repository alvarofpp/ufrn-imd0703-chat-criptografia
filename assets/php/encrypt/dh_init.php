<?php

echo var_dump($_POST);

$dh_file = file_get_contents('dh.json');
$dh = json_decode($dh_file, true);

session_start();
echo '<form>';
foreach ($dh['users'] as $key => $value) {
    if(!($key == $_SESSION['username'])){
        echo '<input type="radio" name="user2" value="'.$value.'" required />'.$key.'<br/>';
    }
}
echo '<input type="submit" value="Continuar"/>';
echo '</form>';

exit();