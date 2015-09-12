<?php
require_once('CAS.php');
require_once('commun/minoterie/utils.php');

/* nos differents CAS */
$auth_provider = array(
    "univ" => array("server" => "cas.univ-paris13.fr", "name" => "université Paris 13"),
    "ig" => array("server" => "ig.univ-paris13.fr", "name" => "Institut Galilée"),
    "laga" => array("server" => "sso.math.univ-paris13.fr", "name" => "LAGA"),
    "lipn" => array("server" => "sso.lipn.univ-paris13.fr", "name" => "LIPN")
);

$auth_default = "ig";

/* which authentication to use ? */
$auth = "univ";

if ((isset($_COOKIE["painAuthentication"])) {
        $auth = cookieclean("painAuthentication");
}
if (isset($_GET["cas"])) {
    $auth = getclean("cas");
}

// error_reporting(E_ALL & ~E_NOTICE);
phpCAS::client(CAS_VERSION_2_0,$auth_provider["$auth"]["server"],443,'/cas/',true);

// phpCAS::setDebug();
phpCAS::setNoCasServerValidation();

phpCAS::forceAuthentication();

/* S'en souvenir pour la prochaine fois */
if ($auth != $auth_default) {
    /* puisque ça fonctionne on continue pendant 30 jours avec le même CAS */
    setcookie("painAuthentication", $auth, time() + 3600 * 24 * 30);
}

require("commun/minoterie/iconnect.php");
$linkcas = $link;
$linkcas->query("SET NAMES 'utf8'");


function login() {
    global $linkcas;
    global $auth;
    $login =  phpCAS::getUser();
    if ($auth != "univ") {
        $query = "SELECT login FROM minoterie_login WHERE provider LIKE '$auth' AND alt_login LIKE '$login' LIMIT 1";
        $result = $linkcas->query($query);
        if ($user = $result->fetch_array()) {
            $login = $user["login"];
        }
    }
    return $login;
}
?>