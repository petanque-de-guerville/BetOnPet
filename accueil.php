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
			<div class="titre">BetOnPet : Où l'on peut parier sur les boules de ses adversaires !  <?php if ($logged_in == 1) echo '--- <a href="logout.php">Déconnexion</a>'; ?></div>		
	
				<?php
					if ($logged_in == 0) {
						echo 'Désolé vous n\'êtes pas connecté, cette page nécessite que vous vous <a href="login.php">identifiez</a>';
    					echo '</div></body></html>';
    					exit;
					}
				?>
			<div class="item_menu"> <a href="./page_match_en_cours.php">Match</a> en cours </div>
			<div class="item_menu"> Le <a href="./page_matchs.php">programme</a> des matchs </div>
			<div class="item_menu"> <a href="./page_pari.php">Parier</a> sur un match</div>		
			<div class="item_menu"> <a href="./page_historique_paris.php">Historique</a> de vos paris</div>			
			<div class="item_menu"> <a href="./page_classement_parieurs.php">Classement</a> des parieurs</div>
			<div class="item_menu"> <a href="./page_classement_equipes.php">Fiches et classement</a> des équipes </div>
			<div class="item_menu"> Fiches des <a href="./page_joueurs.php">joueurs</a>  </div>
			<div class="item_menu"> <a href="./page_photos.php">Les photos</a> des années précédentes </div>
			<div class="item_menu"> <a href="./page_admin.php">Gérer</a> l'application</div>			
			<div class="item_menu"> <a href=""></a>  </div>
			<div class="item_menu"> <a href=""></a>  </div>
			<div class="item_menu"> <a href=""></a>  </div>
		</div>

	</body>
</html>


