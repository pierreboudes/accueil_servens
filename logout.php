<?php
/*require_once('authentication.php');
function logout_php() {
    minoterie_logout();
}

logout_php();*/


require_once('multipleCAS.php');
$login = login();
phpCAS::logout();
?>
