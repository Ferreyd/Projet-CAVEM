<?php
	include 'bd/requeteBandeau.php';
	/**
	 *
     * Fonction d'affichage du menu.
     *
	 *
	 */
	function bandeau()
	{
		$host = gethostbyaddr($_SERVER[ 'REMOTE_ADDR' ]);
		$page = "<div class='navbar'>";
		$page .= "<div class='navbar-inner'>";
		$page .= "<a class='brand' href='#'>Base for SIHS</a>";

		$cheminImage       = "Images/Logo_cavem.png";
		$cheminRealisation = "Images/Realisation.gif";


		if($_SESSION[ 'privilege' ] == 'super-admin'){
			$tableau = array("<a class='bandeau' href = 'activite.php'> Activites</a>", "<a class='bandeau' href = 'nouveau.php'>Ajouter une nouvelle Affaire</a>", "<a class='bandeau' href = 'recherche.php'>Recherche Dossier / Affaire</a>", "<a class='bandeau' href = 'statistique.php'>Statistique</a>", "<a class='bandeau' href = 'administration.php'>Administration</a>", "<a class='bandeau' href = 'connexion.php'>Déconnexion</a>", "<img class='realisation' src='$cheminRealisation'>");
		}
		elseif($_SESSION[ 'privilege' ] == 'admin'){
			$tableau = array("<a class='bandeau' href = 'activite.php'> Activites</a>", "<a class='bandeau' href = 'nouveau.php'>Ajouter une nouvelle Affaire</a>", "<a class='bandeau' href = 'recherche.php'>Recherche Dossier / Affaire</a>", "<a class='bandeau' href = 'statistique.php'>Statistique</a>", "<a class='bandeau' href = 'connexion.php'>Déconnexion</a>", "<img class='realisation' src='$cheminRealisation'>");
		}
		else{
			$tableau = array("<a class='bandeau' href = 'activite.php'> Activites</a>", "<a class='bandeau' href = 'nouveau.php'>Ajouter une nouvelle Affaire</a>", "<a class='bandeau' href = 'recherche.php'>Recherche Dossier / Affaire</a>", "<a class='bandeau' href = 'connexion.php'>Déconnexion</a>", "<img class='realisation' src='$cheminRealisation'>");
		}

		$page .= "<ul class='nav'>";

		foreach($tableau as $element){
			$page .= "<li class='active'>";
			$page .= $element;
			$page .= "</a></li>";
		}
		if(! isset($_SESSION[ 'prenomNom' ])){
			$_SESSION[ 'prenomNom' ];
		}
		$prenomNom = explode(" ", $_SESSION[ 'prenomNom' ]);
		$prenom    = $prenomNom[ 0 ];
		$nom       = $prenomNom[ 1 ];

		$page .= "</ul>";
		$page .= "<div class='nomBandeau'>Bienvenue ".$prenom." ".$nom."</div>";
		$page .= "<img class = 'logo'src='$cheminImage'>";
		$page .= "</div>";
		$page .= "</div>";
		echo $page;
	}

?>