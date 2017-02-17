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
			<div class="titre">BetOnPet : With great power comes great responsibility <?php if ($logged_in == 1) echo '--- <a href="logout.php">Déconnexion</a>'; ?></div>		
				<?php /* Vérification que l'utilisateur est bien connecté */
					if ($logged_in == 0) {
						echo 'Désolé vous n\'êtes pas connecté, cette page nécessite que vous vous <a href="./login.php">identifiez</a>';
    					echo '</div></body></html>';
    					exit;
					}
				?>
				
				<?php
					if($_SESSION['username'] != 'M. Shirt' AND $_SESSION['username'] != 'Lulu'){
						echo 'Cette partie du site est réservée.';
    					echo '</div></body></html>';
    					exit;						
					}

/****************************************************************************************
*							 Clic - Créer une équipe
****************************************************************************************/

					if (isset($_POST['btnSubmitJoueurs']) AND strlen(trim(isset($_POST['txt_equipe'])))){

	/****************************************************************************************
	*							Cause de l'arrêt du match							 
	****************************************************************************************/
						echo '<p>Création de l\'équipe "', $_POST['txt_equipe'],'"  </p>';
						
						$qry_insert ='INSERT INTO moi_betonpet_equipes
									(`NOM`) VALUES ('.$_POST['txt_equipe'].')';
						mysql_query($qry_insert);
						
						$qry = 'SELECT * FROM moi_betonpet_equipes WHERE NOM = "'.$_POST['txt_equipe'].'"';
						$qry_res = mysql_query($qry);	
						
						$id_equipe = mysql_fetch_object($qry_res);
						$id_equipe = $id_equipe->ID;
						
						$qry_update = 'UPDATE moi_betonpet_joueurs
										SET ID_EQUIPE = '.$id_equipe.' WHERE ID IN ('.$_POST['listeJoueurs1'].', '.$_POST['listeJoueurs2'].', '.$_POST['listeJoueurs3'].')';
						
						mysql_query($qry_update);
					
/****************************************************************************************
*							 Clic - Initialiser la BDD
****************************************************************************************/

					} elseif (isset($_GET['init']) AND $_GET['init'] == 1){
						INIT_table_Tableau();					
						echo '<p>Le tableau des équipes est créé. </p>';
					
/****************************************************************************************
*							 Clic - Arrêter le match en cours
****************************************************************************************/
					} elseif (isset($_POST['btnArreter'])){
						if(!is_numeric($_POST['txt_score_equipe_A']) OR !is_numeric($_POST['txt_score_equipe_B'])){
							echo 'Impossible d\'arrêter avec ces scores. Retour à la <a href="./page_admin.php">page d\'administration</a>.';
    						echo '</div></body></html>';
    						exit;										
						} elseif (max($_POST['txt_score_equipe_A'], $_POST['txt_score_equipe_B']) > 13 AND min($_POST['txt_score_equipe_A'], $_POST['txt_score_equipe_B']) + 2 < max($_POST['txt_score_equipe_A'], $_POST['txt_score_equipe_B'])) {
							echo 'Il y a plus de deux points d\'écart alors qu\'une équipe a plus de 13 points. Retour à la <a href="./page_admin.php">page d\'administration</a>.';
    						echo '</div></body></html>';
    						exit;																
						} else {
						
	/****************************************************************************************
	*							Cause de l'arrêt du match							 
	****************************************************************************************/
							echo '<p>Cause de l\'arrêt du match...';
							if ( (max($_POST['txt_score_equipe_A'], $_POST['txt_score_equipe_B']) >= 13 AND abs($_POST['txt_score_equipe_A']-$_POST['txt_score_equipe_B']) >= 2)){
								$match_stoppe = false;
							} else {
								$match_stoppe = true;
							}
							echo ' OK</p>';							
							
	/****************************************************************************************
	*							 Trouver le vainqueur
	****************************************************************************************/
							echo '<p>Trouver le vainqueur...';			
							$res_qry = mysql_query('SELECT * FROM moi_betonpet_matchs WHERE ID = '.$_POST['id_match_en_cours']);
							$match = mysql_fetch_object($res_qry);
							if ( $_POST['txt_score_equipe_A'] > $_POST['txt_score_equipe_B']){
								$id_vainqueur = $match->ID_EQUIPE_A;
							} elseif ( $_POST['txt_score_equipe_A'] < $_POST['txt_score_equipe_B']){
								$id_vainqueur = $match->ID_EQUIPE_B;
							} else { // Match nul
								$id_vainqueur = 0;
							}
							echo ' OK</p>';

	/****************************************************************************************
	*							 Arrêter le match
	****************************************************************************************/
							echo '<p>Arrêter le match en BDD...';
							$qry='	UPDATE moi_betonpet_matchs
									SET EN_COURS = 0, 
										EST_TERMINE=1, 
										SCORE_A='.$_POST['txt_score_equipe_A'].',
										SCORE_B='.$_POST['txt_score_equipe_B'].'
									WHERE EN_COURS=1';

							mysql_query($qry); // Modifie la table des matchs
							echo ' OK </p>';

	/****************************************************************************************
	*							 Gérer les paris
	****************************************************************************************/
							echo '<p>Gestion des paris...';	
							$qry = 'SELECT 
										paris.*,
										type.COTE
									FROM moi_betonpet_paris paris
									LEFT JOIN moi_betonpet_type_pari type ON type.ID = paris.ID_TYPE_PARI
									WHERE ID_MATCH = '.$_POST['id_match_en_cours'].'';
							$res_qry = mysql_query($qry); // Modifie la table des matchs

							
							while($pari = mysql_fetch_object($res_qry)){
								if ($pari->ID_TYPE_PARI == 2){ // Pari sur match stoppé
									if (($pari->IS_TEMPS_ECOULE == 1 AND !$match_stoppe) OR ($pari->IS_TEMPS_ECOULE == O AND $match_stoppe)){ // Pari juste
										// Récompenser le joueur
										mysql_query('UPDATE moi_betonpet_joueurs
													SET SOLDE=SOLDE+'.(round($pari->MONTANT_TEMPS_ECOULE * $pari->COTE)).'
													WHERE ID = '.$pari->ID_JOUEUR);
									} else { // Pari loupé
										// On ne fait rien, les capitaux étaient déjà prélevés au moment du pari
									}
								} elseif ($pari->ID_TYPE_PARI == 1){ // Pari sur issue du match
									if ($pari->ID_EQUIPE_VAINQUEUR == $id_vainqueur){ // Le joueur a trouvé le bon vainqueur
										mysql_query('UPDATE moi_betonpet_joueurs
													SET SOLDE=SOLDE+'.(round($pari->MONTANT_VAINQUEUR * $pari->COTE)).'
													WHERE ID = '.$pari->ID_JOUEUR);
									} else { // Pari loupé
										// On ne fait rien, les capitaux étaient déjà prélevés au moment du pari
									}
								}
							}
							echo ' OK </p>';
														
	/****************************************************************************************
	*							 Mettre à jour le tableau des scores des équipes
	****************************************************************************************/
							echo '<p>MàJ du tableau des scores des équipes...';
							MAJ_table_Tableau();
							echo ' OK </p>';
						
							echo '<p>Le match en cours a bien été arrêté. Retour à la <a href="./page_admin.php">page d\'administration</a>.</p>';
    						echo '</div></body></html>';
    						exit;					
    					}
    					
    					
/****************************************************************************************
*							 Clic - Choisir le match suivant
****************************************************************************************/
    					
					} elseif(isset($_POST['btnMatchSuivant'])){
						$qry = 'UPDATE moi_betonpet_matchs
								SET EN_COURS = 1, DATE_DEBUT = NOW()
								WHERE ID = '.$_POST['match_suivant'];
						mysql_query($qry);
						echo '<p>Un nouveau match est en cours. Retour à la <a href="./page_admin.php">page d\'administration</a>.</p>';
    					echo '</div></body></html>';
    					exit;					
						
					
					} else { // Entrée dans la page sans action du formulaire


/****************************************************************************************
*							 Menu - Affecter les joueurs aux équipes
****************************************************************************************/
						$qry = 'SELECT * FROM moi_betonpet_joueurs WHERE ID_EQUIPE IS NULL';
						$res_qry = mysql_query($qry);
						if (mysql_num_rows($res_qry) != 0) { // Cas où il y a des joueurs
							echo '<form method="post" action="./page_admin.php">';
							$options = "";
							while($joueur = mysql_fetch_object($res_qry)){
								$options = $options.'<option value="'.($joueur->ID).'">'.($joueur->NOM).'</option>';
							}
							echo '<p>Joueur 1  <select name="listeJoueurs1"><option value="0" selected>Pas de joueur</option>';
							echo $options;
							echo '</select></p>';
							
							echo '<p>Joueur 2  <select name="listeJoueurs2"><option value="0" selected>Pas de joueur</option>';
							echo $options;
							echo '</select></p>';

							echo '<p>Joueur 3  <select name="listeJoueurs3"><option value="0" selected>Pas de joueur</option>';
							echo $options;
							echo '</select></p>';
							
							echo '<p>Nom de l\'équipe <input type="text" size="40" name="txt_equipe"> <input type="submit" name="btnSubmitJoueurs" value="Créer l\'équipe"></p>';
							echo '</form>';
							
						}
						
						



	
/****************************************************************************************
*							 Menu - Initialiser la base
****************************************************************************************/
						$qry = 'SELECT * FROM moi_betonpet_tableau';
						$res_qry = mysql_query($qry);
						if (mysql_num_rows($res_qry) == 0) {
							echo '<p><a href="./page_admin.php?init=1">Initialiser</a> la base de données</p>';
						}
						
/****************************************************************************************
*							 Menu - Arrêter le match en cours
****************************************************************************************/
						$qry='SELECT 
								matchs.*,
								eA.NOM as NOM_EQUIPE_A,
								eB.NOM as NOM_EQUIPE_B						
							FROM moi_betonpet_matchs matchs 
							LEFT JOIN moi_betonpet_equipes eA ON eA.ID = matchs.ID_EQUIPE_A
							LEFT JOIN moi_betonpet_equipes eB ON eB.ID = matchs.ID_EQUIPE_B
							WHERE EN_COURS = 1';
												
						$res_qry = mysql_query($qry);
						$match_en_cours = false;
						if (mysql_num_rows($res_qry) == 0) {
							echo '<p>Pas de match en cours.</p>';
						} elseif (mysql_num_rows($res_qry) > 1) { 
							echo 'Problème, il y a plus d\'un match en cours.';
						} else {     			
							$match_en_cours=true;	
							$match = mysql_fetch_object($res_qry);
							echo '<form method="post" action="./page_admin.php"> Match en cours : <a href="./page_equipes.php?id_equipe=',
								 $match->ID_EQUIPE_A,'">', $match->NOM_EQUIPE_A, '</a> <input type="text" name="txt_score_equipe_A" size="2">
								 - <input type="text" name="txt_score_equipe_B" size="2"> <a href="./page_equipes.php?id_equipe=', 
								 $match->ID_EQUIPE_B,'">', $match->NOM_EQUIPE_B, '</a> --- <input type="hidden" name="id_match_en_cours" value="', $match->ID,'">
								 <input type="submit" name="btnArreter" value="Arrêter"></form>';
						}	

/****************************************************************************************
*							 Menu - Choisir le prochain match à jouer
****************************************************************************************/

						$qry='SELECT 
								matchs.*,
								eA.NOM as NOM_EQUIPE_A,
								eB.NOM as NOM_EQUIPE_B						
							FROM moi_betonpet_matchs matchs 
							LEFT JOIN moi_betonpet_equipes eA ON eA.ID = matchs.ID_EQUIPE_A
							LEFT JOIN moi_betonpet_equipes eB ON eB.ID = matchs.ID_EQUIPE_B
							WHERE EST_TERMINE = 0 AND EN_COURS = 0';
												
						$res_qry = mysql_query($qry);
				
						if (mysql_num_rows($res_qry) == 0) {
							echo 'Aucun match à jouer dans la liste.';
						} else {     				
							echo '<form method="post" action="./page_admin.php">';
							echo '<p>Choisir le prochain match :<ul>';
							while ($match = mysql_fetch_object($res_qry)){
							echo '<li> <a href="./page_equipes.php?id_equipe=', $match->ID_EQUIPE_A,'">', $match->NOM_EQUIPE_A, '</a> vs. <a href="./page_equipes.php?id_equipe=', 
								 $match->ID_EQUIPE_B,'">', $match->NOM_EQUIPE_B, '</a> <input type="radio" name="match_suivant" value="', $match->ID,'"></li>';
							}
							echo '</ul></p>'.((!$match_en_cours) ? '<input type="submit" name="btnMatchSuivant" value="Choisir">' : '').'</form>';
						}	
					}				
				?>

			<p> Retour au <a href="./accueil.php">menu</a></p>			
		</div>
	</body>
</html>