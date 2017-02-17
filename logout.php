<?php

require 'db_connect.php';    // database connect script.

if ($logged_in == 0) {
    die('Vous n\'êtes pas connecté, vous ne pouvez donc pas vous déconnecter.');
}

unset($_SESSION['username']);
unset($_SESSION['password']);
// kill session variables
$_SESSION = array(); // reset session array
session_destroy();   // destroy session.
header('Location: http://tietokone.haarukka.free.fr/BetOnPet/login.php');
?>