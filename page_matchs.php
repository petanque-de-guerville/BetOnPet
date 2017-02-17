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
			<div class="titre">BetOnPet : Demandez le programme... <?php if ($logged_in == 1) echo '--- <a href="logout.php">Déconnexion</a>'; ?></div>
				<?php /* Vérification que l'utilisateur est bien connecté */
					if ($logged_in == 0) {
						echo 'Désolé vous n\'êtes pas connecté, cette page nécessite que vous vous <a href="login.php">identifiez</a>';
    					echo '</div></body></html>';
    					exit;
					}
				?>
				
				<?php
						$qry='SELECT 
								matchs.*,
								eA.NOM as NOM_EQUIPE_A,
								eB.NOM as NOM_EQUIPE_B						
							FROM moi_betonpet_matchs matchs 
							LEFT JOIN moi_betonpet_equipes eA ON eA.ID = matchs.ID_EQUIPE_A
							LEFT JOIN moi_betonpet_equipes eB ON eB.ID = matchs.ID_EQUIPE_B';
												
						$res_qry = mysql_query($qry);
				
						if (mysql_num_rows($res_qry) == 0) {
							echo 'Erreur lors du chargement de la liste des matchs.';
							echo '</div></body></html>';
							exit;
						} else {     				
							echo '<p><ul>';
							while ($match = mysql_fetch_object($res_qry)){
								if ($match->EN_COURS == 1 OR $match->EST_TERMINE == 0){
									$a_jouer = true;
									$scores = ' vs. ';
								} else {
									$a_jouer = false;
									$scores = ' '.$match->SCORE_A.' - '.$match->SCORE_B.' '; 
								}
								echo '<li> <a href="./page_equipes.php?id_equipe=', $match->ID_EQUIPE_A,'">', $match->NOM_EQUIPE_A,
									 '</a>', $scores,'<a href="./page_equipes.php?id_equipe=', $match->ID_EQUIPE_B,'">', $match->NOM_EQUIPE_B, 
									 '</a> ', ($a_jouer) ? '(à venir)' : '',' </li>';
							}
							echo '</ul></p>';
						}	
				?>
			<p> Aller <a href="./page_pari.php">parier</a> </p>
			<p> Retour au <a href="./accueil.php">menu</a></p>			
		</div>
	</body>
</html>