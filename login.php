<?php

// database connect script.

require 'db_connect.php';

if($logged_in == 1) {
    die('Bienvenue, '.$_SESSION['username'].'.');

}

?>
<html>
<head>
		<meta name="author" content="Marc Autord">
		<meta name="date" content="2012-08-25">
		<meta name="keywords" content="">
		<meta name="description" content="">
		<meta name="ROBOTS" content="NOINDEX, NOFOLLOW">
		<meta http-equiv="Content-Style-Type" content="text/css">
		<meta name="generator" content="jEdit 4.3 pre 12"/>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<link rel="stylesheet" type="text/css" href="./styles/default.css"/>
<title>Connexion</title>
</head>
<body>
<?php

if (isset($_POST['submit'])) { // if form has been submitted

    /* check they filled in what they were supposed to and authenticate */
    if(!$_POST['uname'] | !$_POST['passwd']) {
        die('Un champ nécessaire à la connexion n\'a pas été rempli.');
    }

    // authenticate.

    if (!get_magic_quotes_gpc()) {
        $_POST['uname'] = addslashes($_POST['uname']);
    }

    $qry = "SELECT ID, NOM, MDP, ID_EQUIPE FROM moi_betonpet_joueurs WHERE NOM = '".$_POST['uname']."'";
    $check = mysql_query($qry);

    if (mysql_num_rows($check) == 0) {
        die('Utilisateur inconnu.');
    }

    $info = mysql_fetch_object($check);
	
    $_POST['passwd'] = stripslashes($_POST['passwd']);
    $_POST['passwd'] = md5($_POST['passwd']);
	$info->MDP = stripslashes($info->MDP);
	
    if ($_POST['passwd'] != $info->MDP) {
        die('Mot de passe incorrect.');
    }

    // if we get here username and password are correct, 
    //register session variables and set last login time.

    $_POST['uname'] = stripslashes($_POST['uname']);
    $_SESSION['username'] = $_POST['uname'];
    $_SESSION['password'] = $_POST['passwd'];
    $_SESSION['id_joueur'] = $info->ID;
    $_SESSION['id_equipe'] = $info->ID_EQUIPE;    
?>

<div class="section">
	<div class="titre">BetOnPet : Connexion réussie</div>		
<p>Bienvenue <?php echo $_SESSION['username']; ?>, vous avez maintenant accès <a href="accueil.php">au site complet</a>.</p>
</div>


<?php

} else {    // if form hasn't been submitted

?>
<div class="section">
	<div class="titre">BetOnPet : Login</div>		
		<form action="<?php echo $_SERVER['PHP_SELF']?>" method="post">
			<table align="center" border="0" cellspacing="0" cellpadding="3">
				<tr> 
					<td>
						Nom d'utilisateur :
					</td>
					<td>
						<input type="text" name="uname" maxlength="40">
					</td>
				</tr>
				<tr>
					<td>
						Mot de passe :
					</td>
					<td>
						<input type="password" name="passwd" maxlength="50">
					</td>
				</tr>
				<tr>
					<td colspan="2" align="right">
						<input type="submit" name="submit" value="Hop !">
					</td>
				</tr>
			</table>
		</form>
	</div>
</br>
<?php
}
?>
</body>
</html>