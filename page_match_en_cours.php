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
			<div class="titre">BetOnPet : Match en cours  <?php if ($logged_in == 1) echo '--- <a href="logout.php">Déconnexion</a>'; ?></div>		

				<?php /* Vérification que l'utilisateur est bien connecté */
					if ($logged_in == 0) {
						echo 'Désolé vous n\'êtes pas connecté, cette page nécessite que vous vous <a href="login.php">identifiez</a>';
    					echo '</div></body></html>';
    					exit;
					}
				?>
				
    			<?php
    				$qry = 'SELECT
    							matchs.ID_EQUIPE_A,
    							matchs.ID_EQUIPE_B,
    							eA.NOM as NOM_EQUIPE_A,
    							eB.NOM as NOM_EQUIPE_B
    						FROM moi_betonpet_matchs matchs
    						LEFT JOIN moi_betonpet_equipes as eA on eA.ID = matchs.ID_EQUIPE_A 
    						LEFT JOIN moi_betonpet_equipes as eB on eB.ID = matchs.ID_EQUIPE_B 
    						WHERE EN_COURS=1';
					$res_qry = mysql_query($qry);
			
					if (mysql_num_rows($res_qry) == 0) {
						echo 'Il n\'y a pas de match en cours.';
    					echo '</div></body></html>';
    					exit;
					}

					if (mysql_num_rows($res_qry) > 1) {
						echo 'Plus d\'un match en cours. C\'est bizarre... Prévenir le naze qui a codé ça !';
    					echo '</div></body></html>';
    					exit;
					}

					if (mysql_num_rows($res_qry) == 1) {
						$match = mysql_fetch_object($res_qry);				
						echo '<p> Le match en cours oppose <a href="./page_equipes.php?id_equipe=', $match->ID_EQUIPE_A,'">', $match->NOM_EQUIPE_A, '</a>';
						echo ' à <a href="./page_equipes.php?id_equipe=', $match->ID_EQUIPE_B,'">', $match->NOM_EQUIPE_B, '</a>.</p>';
					
						$qry_comm = 'SELECT 
										*
									FROM moi_betonpet_commentaires comm
									LEFT JOIN moi_betonpet_matchs matchs ON matchs.ID = comm.ID_MATCH
									LEFT JOIN moi_betonpet_joueurs joueurs ON joueurs.ID = comm.ID_JOUEUR
									WHERE matchs.EN_COURS = 1 ORDER BY DATE_COMMENTAIRE ASC';
						$res_comm = mysql_query($qry_comm);
					
						if (mysql_num_rows($res_comm) == 0) {
							echo '<p>Aucun commentaire. </p>';
						} else {
							echo '<div class="commentaires"> <div class="titre">Commentaires</div>';
							while ($comm = mysql_fetch_object($res_comm)){
								echo '<div class="commentaire">', $comm->NOM, ' a dit : "', $comm->COMMENTAIRE,'" </div>';		
							}
							echo '</div>';
						}
					}
					
								
    			?>
			<p> Retour au <a href="./accueil.php">menu</a></p>			
		</div>
	</body>
</html>