<?php
/* seulement des utilisateurs acceptés par le CAS*/
require('multipleCAS.php');
$login = login();

function configurer_authentication_php () {
    global $auth;
    global $login;
    global $linkcas;
    global $auth_provider;


    echo "<p>Vous utilisez actuellement le service central d'authentification (CAS) ";
    echo $auth_provider["$auth"]["name"].".";
    echo "<ul><li>Votre login CAS est <b>".phpCAS::getUser()."</b>.</li>";
    echo "<li>Votre login Pain est <b>".$login."</b>.</li>";

    /* liste des authentifications alternatives */
    $query = "SELECT alt_login, provider FROM minoterie_login WHERE login LIKE '$login'";
    $result = $linkcas->query($query);
    $no_alternative = true;

    while ($user = $result->fetch_array()) {
        $alt_login = $user["alt_login"];
        $provider = $user["provider"];
        if ($provider != $auth) {
            $no_alternative = false;
            echo "<li>Vos avez également donné à Pain votre login ".$auth_provider["$provider"]["name"];
            echo ". Il s'agit de <b>".$alt_login."</b>. Vous pouvez le changer ci-dessous.</li>";
        }
    }
    if ($no_alternative) {
        echo "<li>Vous n'avez pas donné à Pain d'autre login CAS. ";
        //            echo "Vous pouvez le renseigner ci-dessous.".
    }
    //    echo "En renseignant un autre mécanisme d'authentification que celui de l'université Paris 13 vous pouvez accéder à Pain même quand le CAS en panne ou fortement ralenti. </li>"
    echo "<ul>";
}

configurer_authentication_php();
?>
