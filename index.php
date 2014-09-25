<?php /* seulement des utilisateurs acceptés par le CAS*/
require_once('CAS.php');
// error_reporting(E_ALL & ~E_NOTICE);
phpCAS::client(CAS_VERSION_2_0,'cas.univ-paris13.fr',443,'/cas/',true);
// phpCAS::setDebug();
phpCAS::setNoCasServerValidation();

phpCAS::forceAuthentication();

include('index_texte.html');
?>
