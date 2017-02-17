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
			<div class="titre">BetOnPet : Historique de vos paris <?php if ($logged_in == 1) echo '--- <a href="logout.php">Déconnexion</a>'; ?></div>		
				<?php /* Vérification que l'utilisateur est bien connecté */
					if ($logged_in == 0) {
						echo 'Désolé vous n\'êtes pas connecté, cette page nécessite que vous vous <a href="./login.php">identifiez</a>';
    					echo '</div></body></html>';
    					exit;
					}
				?>
				
				<?php
															
/****************************************************************************************
*							 Lister tous les paris de ce joueur par ordre de date
****************************************************************************************/
					$qry='SELECT 
							paris.*,
							matchs.*,
							eA.NOM as NOM_EQUIPE_A,
							eB.NOM as NOM_EQUIPE_B
						FROM moi_betonpet_paris paris
						LEFT JOIN moi_betonpet_matchs matchs ON matchs.ID = paris.ID_MATCH
						LEFT JOIN moi_betonpet_equipes eA ON eA.ID = matchs.ID_EQUIPE_A
						LEFT JOIN moi_betonpet_equipes eB ON eB.ID = matchs.ID_EQUIPE_B
						WHERE ID_JOUEUR = '.$_SESSION['id_joueur'].' ORDER BY DATE_PARI ASC';
												
					$res_qry = mysql_query($qry);
					if (mysql_num_rows($res_qry) == 0) {
						echo '<p>Vous n\'avez pas encore parié.</p>';
					} elseif (mysql_num_rows($res_qry) > 1) { 
						echo '<p>Vous avez parié : </p>';
						echo '<ul>';
						while ($match = mysql_fetch_object($res_qry)){
							if ($match->MONTANT_VAINQUEUR > 0){
															
								if ($match->ID_EQUIPE_VAINQUEUR = $match->ID_EQUIPE_A){
									$nom_vainqueur = $match->NOM_EQUIPE_A;
									$id_vainqueur = $match->ID_EQUIPE_VAINQUEUR;
									$nom_perdant = $match->NOM_EQUIPE_B;
									$id_perdant = $match->ID_EQUIPE_B;		
									
									if ($match->EST_TERMINE){
										if ($match->SCORE_A > $match->SCORE_B){
											$pari_gagne = true;
										} else {
											$pari_gagne = false;
										}
									}							
								} else {
									$nom_vainqueur = $match->NOM_EQUIPE_B;
									$id_vainqueur = $match->ID_EQUIPE_VAINQUEUR;									
									$nom_perdant = $match->NOM_EQUIPE_A;
									$id_perdant = $match->ID_EQUIPE_A;																		

									if ($match->EST_TERMINE){
										if ($match->SCORE_B > $match->SCORE_A){
											$pari_gagne = true;
										} else {
											$pari_gagne = false;
										}
									}							
								}
								echo '<li class="', ($pari_gagne) ? 'pari_gagne' : '','">Victoire de <a href="./page_equipes.php?id_equipe=', $id_vainqueur,'">', $nom_vainqueur,'</a> sur <a href="./page_equipes.php?id_equipe=', $id_perdant,'">', $nom_perdant,'</a> pour ', $match->MONTANT_VAINQUEUR, ' caps.</li>';
							} 
							
							if ($match->MONTANT_TEMPS_ECOULE > 0){
								if ($match->IS_TEMPS_ECOULE == 1){
									echo '<li>Fin du match <a href="./page_equipes.php?id_equipe=', $match->ID_EQUIPE_A,'">', $match->NOM_EQUIPE_A,'</a> vs. <a href="./page_equipes.php?id_equipe=', $match->ID_EQUIPE_B,'">', $match->NOM_EQUIPE_B,'</a> due au chrono pour ', $match->MONTANT_TEMPS_ECOULE, ' caps.</li>';							
								} else {
									echo '<li>Fin du match <a href="./page_equipes.php?id_equipe=', $match->ID_EQUIPE_A,'">', $match->NOM_EQUIPE_A,'</a> vs. <a href="./page_equipes.php?id_equipe=', $match->ID_EQUIPE_B,'">', $match->NOM_EQUIPE_B,'</a> due au score ', $match->MONTANT_TEMPS_ECOULE, ' caps.</li>';							
								}								
							}						
						}
						echo '</ul>';
					}									
				?>
			<p> Aller <a href="./page_pari.php">parier</a></p>			
			<p> Retour au <a href="./accueil.php">menu</a></p>			
		</div>
	</body>
</html>