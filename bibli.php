<?php require 'db_connect.php'; ?>

<?php

	if (function_exists('mysql_real_escape_string')) {
		define('FP_PROTECT', 'mysql_real_escape_string');
	} elseif (function_exists('mysql_escape_string')) {
		define('FP_PROTECT', 'mysql_escape_string');
	} else if (get_magic_quotes_gpc() == 0) {
		define('FP_PROTECT', 'addslashes');
	}

	function fp_protectSQL($Texte) {
		if (get_magic_quotes_gpc() == 1) {
			$Texte = stripslashes($Texte);
		}
		// On est obligÈ de passer par une variable car on ne peut pas faire de 
		// "constantes fonctions" comme on peut faire des "variables fonctions".
		$Fonction = FP_PROTECT;
		return $Fonction($Texte);
	}
	
	function fp_protectHTML($Texte, $BR = FALSE) {
		return ($BR) ? nl2br(htmlspecialchars($Texte)) : htmlspecialchars($Texte);
	}


	function INIT_table_Tableau(){
		$qry = 'SELECT * FROM moi_betonpet_tableau';
		$res_qry = mysql_query($qry);
		if (mysql_num_rows($res_qry) == 0) {
			$qry = 'SELECT ID FROM moi_betonpet_equipes';
			$res_qry = mysql_query($qry);
			while ($equipe = mysql_fetch_object($res_qry)){
				mysql_query('INSERT INTO moi_betonpet_tableau
							(`ID_EQUIPE`, `NB_MATCHS_JOUES`, `NB_VICTOIRES`, `NB_DEFAITES`, `NB_NULS`, `GOAL_AVERAGE`, `NB_POINTS`, `NB_POINTS_AUXILIAIRES`)
							VALUES ('.$equipe->ID.', 0, 0, 0, 0, 0, 0, 0)');
			}
		}
	}		


	function MAJ_table_Tableau(){
		$qry = 'SELECT ID FROM moi_betonpet_equipes';
		$res_qry = mysql_query($qry);
		
		if (mysql_num_rows($res_qry) == 0) {
			return(-1);
		}
				
		$qry = 'SELECT
					matchs.*,
					IF(SCORE_A = SCORE_B, 1, 0) as EST_MATCH_NUL,
					IF(SCORE_A > SCORE_B, ID_EQUIPE_A, ID_EQUIPE_B) as ID_VAINQUEUR,
					IF(SCORE_A > SCORE_B, ID_EQUIPE_B, ID_EQUIPE_A) as ID_PERDANT
				FROM moi_betonpet_matchs matchs
				WHERE EN_COURS = 0
				AND EST_TERMINE = 1';
		
		$res_qry = mysql_query($qry);
		if (mysql_num_rows($res_qry) == 0) {
			return(0);
		}
		
		/* Remise à zéro du tableau avant de le recalculer */
		$qry = 'UPDATE moi_betonpet_tableau
			SET NB_MATCHS_JOUES = 0,
			NB_VICTOIRES = 0, 
			NB_DEFAITES = 0, 
			NB_NULS = 0, 
			NB_POINTS = 0,
			GOAL_AVERAGE = 0,
			NB_POINTS_AUXILIAIRES = 0';
		mysql_query($qry);
		
		/* Recalcul du tableau*/
		while ($match = mysql_fetch_object($res_qry)) {
			if ($match->EST_MATCH_NUL == 1){
				$qry = 'UPDATE moi_betonpet_tableau
						SET NB_NULS = NB_NULS+1, NB_POINTS = NB_POINTS +1, NB_MATCHS_JOUES = NB_MATCHS_JOUES+1, NB_POINTS_AUXILIAIRES = NB_POINTS_AUXILIAIRES +1
						WHERE ID_EQUIPE IN ('.$match->ID_EQUIPE_A.', '.$match->ID_EQUIPE_B.')';
				mysql_query($qry);			
			} else {
				// Mise à jour de la ligne du vainqueur
				$qry = 'UPDATE moi_betonpet_tableau
						SET NB_VICTOIRES = NB_VICTOIRES +1, 
							NB_POINTS = NB_POINTS +3, 
							NB_MATCHS_JOUES = NB_MATCHS_JOUES+1, 
							GOAL_AVERAGE = GOAL_AVERAGE + '.(max($match->SCORE_A, $match->SCORE_B) - min($match->SCORE_A, $match->SCORE_B)).',
							NB_POINTS_AUXILIAIRES = NB_POINTS + ATAN(GOAL_AVERAGE)/PI()
						WHERE ID_EQUIPE ='.$match->ID_VAINQUEUR;
				mysql_query($qry);			

				// Mise à jour de la ligne du perdant
				$qry = 'UPDATE moi_betonpet_tableau
						SET NB_VICTOIRES = NB_DEFAITES +1, 
							NB_MATCHS_JOUES = NB_MATCHS_JOUES+1, 
							GOAL_AVERAGE = GOAL_AVERAGE + '.(min($match->SCORE_A, $match->SCORE_B) - max($match->SCORE_A, $match->SCORE_B)).',
							NB_POINTS_AUXILIAIRES = NB_POINTS + ATAN(GOAL_AVERAGE)/PI()
						WHERE ID_EQUIPE ='.$match->ID_PERDANT;
				mysql_query($qry);			
			
			}
		
		}	
	}
?>