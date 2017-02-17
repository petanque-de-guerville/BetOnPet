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
				<?php /* Vérification que l'utilisateur est bien connecté */
					if ($logged_in == 0) {
						echo '<div class="titre">BetOnPet : Les équipes </div>';
						echo 'Désolé vous n\'êtes pas connecté, cette page nécessite que vous vous <a href="login.php">identifiez</a>';
    					echo '</div></body></html>';
    					exit;
					}
				?>
				
				<?php
					if (isset($_GET['id_equipe']) AND is_numeric($_GET['id_equipe'])) { /* La fiche d'une équipe a été demandée*/
	    				$qry = 'SELECT
    								tableau.*,
    								@prev := @curr,
    								@curr := tableau.NB_POINTS_AUXILIAIRES,
	    							@rank := IF(@prev = @curr, @rank, @rank+1) as RANK
    	   							FROM moi_betonpet_tableau tableau, (SELECT @curr := null, @prev := null, @rank := 0) init
    								ORDER BY NB_POINTS_AUXILIAIRES DESC';
    					$qry_complete = 'SELECT 
    										CLASSEMENT.*,
    										equipes.NOM as NOM_EQUIPE,
    										equipes.DEVISE,
    										joueurs.NOM,
    										joueurs.ID   									
    									FROM ('.$qry.') AS CLASSEMENT
    									LEFT JOIN moi_betonpet_equipes equipes ON equipes.ID = CLASSEMENT.ID_EQUIPE
    									LEFT JOIN moi_betonpet_joueurs joueurs ON joueurs.ID_EQUIPE = equipes.ID
    									WHERE equipes.ID ='.$_GET['id_equipe'];

 						$res_qry = mysql_query($qry_complete);
			
						if (mysql_num_rows($res_qry) == 0) {
							echo '<div class="titre">BetOnPet : Fiche d\'équipe';
							if ($logged_in == 1){
								echo '--- <a href="logout.php">Déconnexion</a></div>';		
							}
							echo 'Aucune fiche pour cette équipe.';
    					} elseif (mysql_num_rows($res_qry) >= 1) {
    						$joueur = mysql_fetch_object($res_qry);	
    						$devise = $joueur->DEVISE;		
    						$classement = $joueur->RANK;
    						$points = $joueur->NB_POINTS;
    						$matchs_joues = $joueur->NB_MATCHS_JOUES;	
							echo '<div class="titre">BetOnPet : Fiche de "', $joueur->NOM_EQUIPE,'" ';
							if ($logged_in == 1) {
								echo '--- <a href="logout.php">Déconnexion</a></div>';		
							}
							
							if ($joueur->ID == NULL) {
								echo 'Cette équipe n\'a pas de joueurs !';
							} else {
								echo 'Les joueurs de l\'équipe sont <a href="./page_joueurs?id_joueur=', $joueur->ID,'">', $joueur->NOM, '</a>';
								while ($joueur = mysql_fetch_object($res_qry)) {
									echo ', <a href="./page_joueurs?id_joueur=', $joueur->ID,'">', $joueur->NOM, '</a>';
								}
								echo '.';
							}
							echo '<p>Classement : ', $classement, ' (', $points,' points, ', $matchs_joues,' matchs joués)</p>';
							echo '<p>Devise : ', $devise,'</p>';							
							echo '<p>Voir la <a href="./page_equipes.php">liste</a> des équipes.</p>';
    					}
    					
    					
					} else { /* Afficher toutes les équipes */
	    				$qry = 'SELECT
    								*
    							FROM moi_betonpet_equipes
    							ORDER BY NOM';
						$res_qry = mysql_query($qry);

						if (mysql_num_rows($res_qry) == 0) {
							echo '<div class="titre">BetOnPet : Les équipes';
							if ($logged_in == 1){
								echo '--- <a href="logout.php">Déconnexion</a></div>';		
							}
							echo 'Aucune équipe inscrite.';
    						echo '</div></body></html>';
    						exit;
    					} elseif (mysql_num_rows($res_qry) >= 1) {
							echo '<div class="titre">BetOnPet : Les équipes';
							if ($logged_in == 1) {
								echo '--- <a href="logout.php">Déconnexion</a></div>';		
							}
							
							echo 'Les ', mysql_num_rows($res_qry), ' équipes sont :';
							echo '<ul>';
							while ($equipe = mysql_fetch_object($res_qry)) {
								echo '<li><a href="./page_equipes?id_equipe=', $equipe->ID,'">', $equipe->NOM, '</a></li>';
							}
							echo '</ul>';
    					}
						
					} 
				?>
			<p> Retour au <a href="./accueil.php">menu</a></p>
			
		</div>
	</body>
</html>