<?php require 'db_connect.php'; ?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
	<head>
		<title>BetOnPet : pariez sur les boules de vos adversaires !</title>
		<meta name="author" content="Marc Autord">
		<meta name="date" content="2012-08-25">
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
			<div class="titre">BetOnPet : Le classement des parieurs <?php if ($logged_in == 1) echo '--- <a href="logout.php">Déconnexion</a>'; ?></div>		
				<?php /* Vérification que l'utilisateur est bien connecté */
					if ($logged_in == 0) {
						echo 'Désolé vous n\'êtes pas connecté, cette page nécessite que vous vous <a href="login.php">identifiez</a>';
    					echo '</div></body></html>';
    					exit;
					}
				?>
				
				<?php
					$qry = 'SELECT
    							parieurs.*,
    							@prev := @curr,
    							@curr := parieurs.SOLDE,
    							@rank := IF(@prev = @curr, @rank, @rank+1) as RANK
       						FROM moi_betonpet_joueurs parieurs, (SELECT @curr := null, @prev := null, @rank := 0) init
    						ORDER BY parieurs.SOLDE DESC';
				
					$res_qry = mysql_query($qry);
			
					if (mysql_num_rows($res_qry) == 0) {
						echo 'Erreur lors du calcul du classement des parieurs.';
 						echo '</div></body></html>';
   						exit;
    				} else {
						echo 'Du plus gros au plus petit capital, les parieurs sont :';
						echo '<ul>';
						while ($parieur = mysql_fetch_object($res_qry)) {
							echo '<li class="classement">', $parieur->RANK, '. <a href="./page_parieurs?id_parieur=', $parieur->ID,'">', $parieur->NOM, '</a> avec ', $parieur->SOLDE, ' caps</li>';
						}
						echo '</ul>';
    				}
				?>

			<p> Retour au <a href="./accueil.php">menu</a></p>			
		</div>
	</body>
</html>