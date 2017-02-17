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
			<div class="titre">BetOnPet : Il est où le bookie ?! <?php if ($logged_in == 1) echo '--- <a href="logout.php">Déconnexion</a>'; ?></div>		
				<?php /* Vérification que l'utilisateur est bien connecté */
					if ($logged_in == 0) {
						echo 'Désolé vous n\'êtes pas connecté, cette page nécessite que vous vous <a href="login.php">identifiez</a>';
    					echo '</div></body></html>';
    					exit;
					}
				?>

				<?php
/****************************************************************************************
*							 Clic - Parier
****************************************************************************************/
					if (isset($_POST['btnParier'])){
						if(!is_numeric($_POST['txt_mise']) OR $_POST['id_equipe_vainqueur'] == ''){
							echo 'Impossible d\'enregistrer ce pari. Retour à la page des <a href="./page_pari.php">paris</a>.';
    						echo '</div></body></html>';
    						exit;										
						} 	
						
						$mise = $_POST['txt_mise'];
						$res = mysql_query('SELECT SOLDE FROM moi_betonpet_joueurs WHERE ID = '.$_SESSION['id_joueur']);
						$res = mysql_fetch_object($res);
						$solde = $res->SOLDE;
												
						if ( $solde < $mise){
							echo 'La mise est plus élevée que votre capital (', $solde,' caps). Retour à la page des <a href="./page_pari.php">paris</a>.';
    						echo '</div></body></html>';
    						exit;																
						}
					
						echo 'Tout est ok. Retour à la <a href="./page_pari.php">page des paris</a>.';
						
/****************************************************************************************
*							 Accès direct à la page des paris
****************************************************************************************/
						
					} else {						

					
						$qry='SELECT 
								matchs.*,
								eA.NOM as NOM_EQUIPE_A,
								eB.NOM as NOM_EQUIPE_B,
								COALESCE(paris_tous_joueurs.MONTANT_TOTAL_TOUS, 0) as MONTANT_TOTAL_TOUS,
								COALESCE(paris_tous_joueurs.NB_PARIS_TEMPS_ECOULE_TOUS, 0) as NB_PARIS_TEMPS_ECOULE_TOUS,
								COALESCE(paris_tous_joueurs.NB_PARIS_VAINQUEUR_TOUS, 0) as NB_PARIS_VAINQUEUR_TOUS,
								COALESCE(paris_tous_joueurs.NB_PARIS_EQUIPE_A, 0) as NB_PARIS_EQUIPE_A,
								COALESCE(paris_tous_joueurs.NB_PARIS_EQUIPE_B, 0) as NB_PARIS_EQUIPE_B,
								COALESCE(paris_ce_joueur.MONTANT_TOTAL_CE_JOUEUR, 0) as MONTANT_TOTAL_CE_JOUEUR,
								COALESCE(paris_ce_joueur.NB_PARIS_TEMPS_ECOULE_CE_JOUEUR, 0) as NB_PARIS_TEMPS_ECOULE_CE_JOUEUR,
								COALESCE(paris_ce_joueur.NB_PARIS_VAINQUEUR_CE_JOUEUR, 0) as NB_PARIS_VAINQUEUR_CE_JOUEUR							
							FROM moi_betonpet_matchs matchs 
							LEFT JOIN moi_betonpet_equipes eA ON eA.ID = matchs.ID_EQUIPE_A
							LEFT JOIN moi_betonpet_equipes eB ON eB.ID = matchs.ID_EQUIPE_B
							LEFT JOIN (
								SELECT
									ID_MATCH,
									SUM(MONTANT_VAINQUEUR) + SUM(MONTANT_TEMPS_ECOULE) as MONTANT_TOTAL_TOUS,
									SUM(IS_TEMPS_ECOULE) as NB_PARIS_TEMPS_ECOULE_TOUS,
									COUNT(*)-SUM(IS_TEMPS_ECOULE) as NB_PARIS_VAINQUEUR_TOUS,
									SUM(ID_EQUIPE_VAINQUEUR = ID_EQUIPE_A) as NB_PARIS_EQUIPE_A,
									SUM(ID_EQUIPE_VAINQUEUR = ID_EQUIPE_B) as NB_PARIS_EQUIPE_B								
								FROM moi_betonpet_paris paris
								LEFT JOIN moi_betonpet_matchs matchs ON matchs.ID = paris.ID_MATCH
								GROUP BY ID_MATCH) AS paris_tous_joueurs ON paris_tous_joueurs.ID_MATCH = matchs.ID
							LEFT JOIN (
								SELECT
									ID_MATCH,
									SUM(MONTANT_VAINQUEUR) + SUM(MONTANT_TEMPS_ECOULE) as MONTANT_TOTAL_CE_JOUEUR,
									SUM(IS_TEMPS_ECOULE) as NB_PARIS_TEMPS_ECOULE_CE_JOUEUR,
									COUNT(*)-SUM(IS_TEMPS_ECOULE) as NB_PARIS_VAINQUEUR_CE_JOUEUR
								FROM moi_betonpet_paris
								WHERE ID_JOUEUR = '.$_SESSION['id_joueur'].'
								GROUP BY ID_MATCH) AS paris_ce_joueur ON paris_ce_joueur.ID_MATCH = matchs.ID
							WHERE 
								EST_TERMINE = 0 
								AND EN_COURS = 0';
						
						$res_qry = mysql_query($qry);
				
						if (mysql_num_rows($res_qry) == 0) {
							echo 'Aucun match sur lequel parier.';
							echo '</div></body></html>';
							exit;
						} else {
							echo 'Vous pouvez parier sur ', (mysql_num_rows($res_qry) > 1) ? 'les matchs suivants' : 'le match suivant', ' :';
							//echo '<ul>';
							while ($match = mysql_fetch_object($res_qry)) {
								$a_parie = $match->MONTANT_TOTAL_CE_JOUEUR != 0;
								if ($match->NB_PARIS_EQUIPE_A * $match->NB_PARIS_EQUIPE_B == 0){
									$coteA = $match->NB_PARIS_EQUIPE_A;
									$coteB = $match->NB_PARIS_EQUIPE_B;
								} else {
									$coteA = round($match->NB_PARIS_EQUIPE_A / min($match->NB_PARIS_EQUIPE_A, $match->NB_PARIS_EQUIPE_B), 1);
									$coteB = round($match->NB_PARIS_EQUIPE_B / min($match->NB_PARIS_EQUIPE_A, $match->NB_PARIS_EQUIPE_B), 1);
								}
								//echo ($a_parie) ? '<li class="a_parie">' : '<li>' ,' <form><a href="./page_equipes?id_equipe=', $match->ID_EQUIPE_A,'">', 
								//		$match->NOM_EQUIPE_A, '</a> ', $coteA, ' - ', $coteB , ' <a href="./page_equipes?id_equipe=', 
								//		$match->ID_EQUIPE_B,'">', $match->NOM_EQUIPE_B, '</a> ',($a_parie) ? '' : '<input type="hidden" value="'.($match->ID).'"><input type="text" name="txt_pari" size="2"> <input type="submit" name="btnParier" value="Parier">','</form></li>';
								
								echo '<div class="section">	<div class="titre"><a href="./page_equipes?id_equipe=', $match->ID_EQUIPE_A,'">', 
										$match->NOM_EQUIPE_A, '</a> ', $coteA, ' - ', $coteB , ' <a href="./page_equipes?id_equipe=', 
										$match->ID_EQUIPE_B,'">', $match->NOM_EQUIPE_B, '</a> </div> ';
								if (!$a_parie){
										echo '<form method="post" action="./page_pari.php"> Vainqueur : <select name="id_equipe_vainqueur"> ',
											'<option value=""> Pas de pari</option>',
											'<option value="', $match->ID_EQUIPE_A,'"> ', $match->NOM_EQUIPE_A, '</option>',
											'<option value="', $match->ID_EQUIPE_B,'"> ', $match->NOM_EQUIPE_B, '</option>',									
											'</select>  Mise : <input type="text" name="txt_mise" size="2"> ',
											'<input type="hidden" name="id_match" value="'.($match->ID).'">',
											'<input type="submit" name="btnParier" value="Parier"> </form>';
								} else {
										echo 'Vous avez déjà parié sur ce match.';
								}
								
								echo '</div>';		
	
							}
							//echo '</ul>';
						}
					}
				?>

			
		</div>
	</body>
</html>