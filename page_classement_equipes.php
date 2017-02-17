<?php require 'db_connect.php'; include 'bibli.php' ?>

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
			<div class="titre">BetOnPet : Le classement des équipes <?php if ($logged_in == 1) echo '--- <a href="logout.php">Déconnexion</a>'; ?></div>		
				<?php /* Vérification que l'utilisateur est bien connecté */
					if ($logged_in == 0) {
						echo 'Désolé vous n\'êtes pas connecté, cette page nécessite que vous vous <a href="login.php">identifiez</a>';
    					echo '</div></body></html>';
    					exit;
					}
				?>
				
				<?php
					MAJ_table_Tableau();
	    			$qry = 'SELECT
    							tableau.*,
    							@prev := @curr,
    							@curr := tableau.NB_POINTS_AUXILIAIRES,
    							@rank := IF(@prev = @curr, @rank, @rank+1) as RANK
       							FROM moi_betonpet_tableau tableau, (SELECT @curr := null, @prev := null, @rank := 0) init
    							ORDER BY NB_POINTS_AUXILIAIRES DESC';
    				$qry_complete = 'SELECT 
    									CLASSEMENT.*,
    									equipes.NOM as NOM_EQUIPE   									
    									FROM ('.$qry.') AS CLASSEMENT
    									LEFT JOIN moi_betonpet_equipes equipes ON equipes.ID = CLASSEMENT.ID_EQUIPE';
    									  				
					$res_qry = mysql_query($qry_complete);
			
					if (mysql_num_rows($res_qry) == 0) {
						echo 'Erreur lors du classement des équipes.';
 						echo '</div></body></html>';
   						exit;
    				} else {
						echo 'Le classement (temporaire) des équipes est :';
						echo '<ul>';
						while ($equipe = mysql_fetch_object($res_qry)) {
							echo '<li class="classement">', $equipe->RANK, '. <a href="./page_equipes.php?id_equipe=', $equipe->ID_EQUIPE,'">', $equipe->NOM_EQUIPE, '</a> avec ', $equipe->NB_POINTS, ' points (goal average : ', $equipe->GOAL_AVERAGE,')</li>';
						}
						echo '</ul>';
    				}
				?>
				
			<p> Retour au <a href="./accueil.php">menu</a></p>				
		</div>
	</body>
</html>