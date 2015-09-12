<?php
/* Pain - outil de gestion des services d'enseignement
 *
 * Copyright 2009-2015 Pierre Boudes,
 * département d'informatique de l'institut Galilée.
 *
 * This file is part of Pain.
 *
 * Pain is free software: you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Pain is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY
 * or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public
 * License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Pain.  If not, see <http://www.gnu.org/licenses/>.
 */

require_once('CAS.php');

/* nos differents CAS */
$auth_provider = array(
    "univ" => array("server" => "cas.univ-paris13.fr", "name" => "université Paris 13"),
    "ig" => array("server" => "ig.univ-paris13.fr", "name" => "Institut Galilée"),
    "laga" => array("server" => "sso.math.univ-paris13.fr", "name" => "LAGA"),
    "lipn" => array("server" => "sso.lipn.univ-paris13.fr", "name" => "LIPN")
);

$auth_default = "ig";


/* database connection link required for escaping strings */
require("commun/minoterie/iconnect.php");
$linkcas = $link;
$linkcas->query("SET NAMES 'utf8'");


/** recupere une chaine passee en HTTP/GET ou POST
 */
function _getclean($s) {
    global $link;
    if (isset($_GET[$s])) {
	$source = $_GET[$s];
    } else if (isset($_POST[$s])) {
	$source = $_POST[$s];
    } else {
	return NULL;
    }
    if(get_magic_quotes_gpc()) {
	return trim(htmlspecialchars($link->real_escape_string(stripslashes($source)), ENT_QUOTES));
    }
    else {
	return trim(htmlspecialchars($link->real_escape_string($source), ENT_QUOTES));
    }
}

function cookieclean($s) {
    global $link;
    if (isset($_COOKIE[$s])) {
        if(get_magic_quotes_gpc()) {
            return trim(htmlspecialchars($link->real_escape_string(stripslashes(($_COOKIE[$s]))), ENT_QUOTES));
        }
        else {
            return trim(htmlspecialchars($link->real_escape_string($_COOKIE[$s]), ENT_QUOTES));
        }
    }
    else return NULL;
}


/* which authentication to use ? */
$auth = "univ";

if (isset($_COOKIE["painAuthentication"])) {
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