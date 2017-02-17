<?php require 'db_connect.php'; include 'bibli.php'; ?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
	<head>
		<title>BetOnPet : pariez sur les boules de vos adversaires !</title>
		<meta name="author" content="Marc Autord">
		<meta name="date" content="2012-08-26">
		<meta name="keywords" content="">
		<meta name="description" content="">
		<meta name="ROBOTS" content="NOINDEX, NOFOLLOW">
		<meta http-equiv="Content-Style-Type" content="text/css">
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<link rel="stylesheet" type="text/css" href="./styles/default.css"/>
		<script src="sorttable.js"></script>


	</head>
	<body>
		<div class="section">
			<div class="titre">BetOnPet : Et les années d'avant c'était comment ? <?php if ($logged_in == 1) echo '--- <a href="logout.php">Déconnexion</a>'; ?></div>		
				<?php /* Vérification que l'utilisateur est bien connecté */
					if ($logged_in == 0) {
						echo 'Désolé vous n\'êtes pas connecté, cette page nécessite que vous vous <a href="./login.php">identifiez</a>';
    					echo '</div></body></html>';
    					exit;
					}
				?>
				
				<?php
															
/****************************************************************************************
*							 Lister toutes les galeries des années précédentes
****************************************************************************************/
					echo '<p>Les casseroles des années précédentes, c\'est par ici !</p>';
					echo '<ul>';
					echo '<li><a href="http://flickr.com/gp/zedouille/8K8Y7p/">2009</a></li>';					
					echo '<li><a href="http://flickr.com/gp/zedouille/R1279a/">2010</a></li>';					
					echo '<li><a href="http://flickr.com/gp/zedouille/mJJ837/">2011</a></li>';															
				?>
			<p> Retour au <a href="./accueil.php">menu</a></p>			
		</div>
	</body>
</html>