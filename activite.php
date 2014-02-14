<?php
include 'bandeau.php';
include_once 'bd/RequeteActivite.php';
include_once 'bd/RequeteAffaire.php';

session_start();
if(! isset($_SESSION[ 'user' ]) || ! isset($_SESSION[ 'prenomNom' ]) || ! isset($_SESSION[ 'privilege' ])){
	header('location:connexion.php');
}

/**
 *
 * Fonction qui affiche les affaires ouvertes de l'agent en fonction du thème.
 *
 * @param $theme
 */
function afficheAffaire($theme)
{
	$page = "";

	$requete = RequeteActivite::getInstance()->activiteParTheme($theme);

	$cheminAffaireOuvert  = "Images/icone_dossier_ouvert.png";
	$cheminAffaireAttente = "Images/icone_dossier_attente.png";
	$cheminAffaireFerme   = "Images/icone_dossier_ferme.png";

	foreach($requete as $e){
		$id          = $e[ "id" ];
		$nom         = $e[ "nomAffaire" ];
		$etatAffaire = RequeteAffaire::getInstance()->getEtat($id);

		if($etatAffaire == 'OUVERT'){
			$page .= "<tr><td class='theme'>";
			$page .= "<img src='$cheminAffaireOuvert'><a href='affaire.php?id=$id'>$nom</a>";
			$page .= "</td></tr>";
		}
		if($etatAffaire == 'EN ATTENTE'){
			$page .= "<tr><td class='theme'>";
			$page .= "<img src='$cheminAffaireAttente'><a href='affaire.php?id=$id'>$nom</a>";
			$page .= "</td></tr>";
		}
		if($etatAffaire == 'FERME'){

		}
	}
	echo $page;
}

?>
<!DOCTYPE html>
<html xmlns = "http://www.w3.org/1999/html"
      xmlns = "http://www.w3.org/1999/html"
      xmlns = "http://www.w3.org/1999/html"
      xmlns = "http://www.w3.org/1999/html"
      xmlns = "http://www.w3.org/1999/html"
	>
<head >
    <link href = "bootstrap/css/bootstrap.min.css"
          rel = "stylesheet"
          media = "screen" >
	<link href = "css/activite.css"
	      rel = "stylesheet"
	      media = "screen" >
    <link href = "css/index.css"
          rel = "stylesheet"
          media = "screen" >
    <title >Base For SIHS</title >
	<?php
	bandeau();
	?>
</head >


<body >


<table class = "table" >
    <tr >
        <td >
            <table class = "table table-bordered" >
                <thead >
                <th >
                    Alimentaire
                </th >
                </thead >
            </table >
			
			<div class = "scrollbar" >
			<table class = "table table-bordered" >
                <tbody >
                <?php
                afficheAffaire("ALI");
                ?>
                </tbody >
            </table >
			</div >
        </td >
        <td >
            <table class = "table table-bordered" >
                <thead >
                <th >
                    Habitat
                </th >
                </thead >
            </table >
			
			<div class = "scrollbar" >
			<table class = "table table-bordered" >
                <tbody >
                <?php
                afficheAffaire("HAB");
                ?>
                </tbody >
            </table >
			</div >
        </td >
        <td >
            <table class = "table table-bordered" >
                <thead >
                <th >
                    Environnement
                </th >
                </thead >
            </table >
			
			<div class = "scrollbar" >
			<table class = "table table-bordered" >
                <tbody >
                <?php
                afficheAffaire("ENV");
                ?>
                </tbody >
            </table >
			</div >
        </td >
    </tr >

    <tr >
        <td >
            <table class = "table table-bordered" >
                <thead >
                <th >
                    Bruit
                </th >
                </thead >
            </table >
			
			<div class = "scrollbar" >
			<table class = "table table-bordered" >
                <tbody >
                <?php
                afficheAffaire("BR");
                ?>
                </tbody >
            </table >
			</div >
        </td >
        <td >
            <table class = "table table-bordered" >
                <thead >
                <th >
                    Eau
                </th >
                </thead >
            </table >
			
			<div class = "scrollbar" >
			<table class = "table table-bordered" >
                <tbody >
                <?php
                afficheAffaire("EAU");
                ?>
                </tbody >
            </table >
			</div >
        <td >
            <table class = "table table-bordered" >
                <thead >
                <th >
                    Assainissement
                </th >
                </thead >
            </table >
			
			<div class = "scrollbar" >
			<table class = "table table-bordered" >
                <tbody >
                <?php
                afficheAffaire("ASS");
                ?>
                </tbody >
            </table >
			</div >
        </td >
    </tr >
</table >




  <script src = "js/bootstrap.min.js" ></script >

</body >
</html >