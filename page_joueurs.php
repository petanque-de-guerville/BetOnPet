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
				<?php /* Vérification que l'utilisateur est bien connecté */
					if ($logged_in == 0) {
						echo '<div class="titre">BetOnPet : Les joueurs </div>';
						echo 'Désolé vous n\'êtes pas connecté, cette page nécessite que vous vous <a href="login.php">identifiez</a>';
    					echo '</div></body></html>';
    					exit;
					}
				?>
						
				<?php
					if (isset($_GET['id_joueur']) AND is_numeric($_GET['id_joueur'])) { /* La fiche d'une équipe a été demandée*/
	    				$qry = 'SELECT
    								joueurs.*,
    								@prev := @curr,
    								@curr := joueurs.SOLDE,
    								@rank := IF(@prev = @curr, @rank, @rank+1) as RANK
       							FROM moi_betonpet_joueurs joueurs, (SELECT @curr := null, @prev := null, @rank := 0) init
    							ORDER BY joueurs.SOLDE DESC';
    							
    					$qry_complete = 'SELECT 
    										CLASSEMENT.*,
    										equipes.NOM as NOM_EQUIPE
    									FROM ('.$qry.') AS CLASSEMENT
    									LEFT JOIN moi_betonpet_equipes equipes ON equipes.ID = CLASSEMENT.ID_EQUIPE
    									WHERE CLASSEMENT.ID='.$_GET['id_joueur'];
    					

						$res_qry = mysql_query($qry_complete);
			
						if (mysql_num_rows($res_qry) == 0) {
							echo '<div class="titre">BetOnPet : Fiche de joueur';
							if ($logged_in == 1){
								echo ' --- <a href="logout.php">Déconnexion</a></div>';		
							}
							echo 'Aucune fiche pour ce joueur.';
    						echo '</div></body></html>';
    						exit;
    					} elseif (mysql_num_rows($res_qry) == 1) {
    						$joueur = mysql_fetch_object($res_qry);				
							echo '<div class="titre">BetOnPet : Fiche de "', $joueur->NOM,'" ';
							if ($logged_in == 1) {
								echo ' --- <a href="logout.php">Déconnexion</a></div>';		
							}
							
							if ($joueur->NOM_EQUIPE == NULL){
								echo '<p>', $joueur->NOM, ' est simple parieur, ', ($joueur->SEXE == 'F') ? 'elle' : 'il' ,' ne joue pas à la pétanque.</p>';
							} else {
								echo '<p>', $joueur->NOM, ' est parieur et joueur. Son équipe est <a href="./page_equipes?id_equipe=', $joueur->ID_EQUIPE,'">', $joueur->NOM_EQUIPE, '</a>.</p>';
							}
							echo '<p> Capital : ', $joueur->SOLDE, ' caps.</p>';
							echo '<p> Rang parmi les parieurs : ', $joueur->RANK, '.</p>';							
							
							echo '<p>Voir la <a href="./page_joueurs.php">liste</a> des joueurs/parieurs.</p>'; 
    					}
    					
					} else { /* Afficher tous les joueurs */
	    				$qry = 'SELECT
    								*
    							FROM moi_betonpet_joueurs joueurs
    							ORDER BY NOM';
						$res_qry = mysql_query($qry);

						if (mysql_num_rows($res_qry) == 0) {
							echo '<div class="titre">BetOnPet : Les joueurs';
							if ($logged_in == 1){
								echo ' --- <a href="logout.php">Déconnexion</a></div>';		
							}
							echo 'Aucun joueur inscrit.';
    						echo '</div></body></html>';
    						exit;
    					} elseif (mysql_num_rows($res_qry) >= 1) {
							echo '<div class="titre">BetOnPet : Les joueurs';
							if ($logged_in == 1) {
								echo ' --- <a href="logout.php">Déconnexion</a></div>';		
							}
							
							echo 'Les ', mysql_num_rows($res_qry), ' joueurs sont :';
							echo '<ul>';
							while ($joueur = mysql_fetch_object($res_qry)) {
								echo '<li><a href="./page_joueurs?id_joueur=', $joueur->ID,'">', $joueur->NOM, '</a>';
								if ($joueur->ID_EQUIPE == NULL){
									echo ' (parieur)';
								}
								echo '</li>';
							}
							echo '</ul>';
    					}
					
					} 
				?>

			<p> Retour au <a href="./accueil.php">menu</a></p>			
		</div>
	</body>
</html>