﻿<script xmlns = "http://www.w3.org/1999/html" >
	function verifSuite() {
		if (confirm("/!\\ ATTENTION /!\\\n Vous allez supprimer une suite, êtes-vous certains de bien vouloir la supprimer?.")) {

			return true;
		}
	}


</script >

<?php
include_once 'bd/RequeteIntervention.php';
include_once 'bd/RequeteAffaire.php';
include_once 'bandeau.php';
include_once 'data/Intervention.php';
include_once 'data/Affaire.php';
include_once 'bd/RequeteModification.php';
include_once 'data/Suite.php';
include_once 'bd/RequeteSuite.php';

session_start();
if(! isset($_SESSION[ 'user' ]) || ! isset($_SESSION[ 'prenomNom' ]) || ! isset($_SESSION[ 'privilege' ])){
	header('location:connexion.php');
}

$droitUserModif = FALSE;
$prenomNom = explode(" ", $_SESSION[ 'prenomNom' ]);
$nom = strtoupper($prenomNom[ 1 ]);

$affaireAgent = RequeteAffaire::getInstance()->getAgents($_GET[ 'id' ]);
foreach($affaireAgent as $d){
	if($d == $nom){
		$droitUserModif = TRUE;
	}
}

/**
 *
 * Fonction d'affichage de suites dans l'intervention courante.
 *
 * @param $droitUserModif
 *
 * @return string
 */
function afficheSuites($droitUserModif)
{
	$id           = $_GET[ "id" ];
	$intervention = $_GET[ "intervention" ];

	$tab = RequeteSuite::getInstance()->getSuitesByIntervention($intervention);

	$page = "";
	if($_SESSION[ 'privilege' ] == 'super-admin' || $_SESSION[ 'privilege' ] == 'admin' || $droitUserModif == TRUE)
		$page .= "<a href=modification.php?mod=15&id=$id&intervention=$intervention class='btn btn-primary'>Ajouter une Suite</a><br/><br/>";
		$page .= "<table class='table table-bordered tableInfo titre'>
		<tr class='entete'>
		<th class='iDateT'>Date</th>
		<th class='iTypeT'>Type</th>
		<th class='iNombreT'>Nombre</th>
		<th class='iTexteT'>Texte</th>
		<th class='iModifT'>Modif/Ajout</th>
		</tr></table><div class='contenuSuite'><table class='table table-bordered tableInfo'>";

	$do = new Suite();

	$i = 0;

	foreach($tab as $t){

		$idIntervention = $_GET[ "intervention" ];
		$idAffaire      = $_GET[ "id" ];
		$idSuite        = $t->getId();

		if($i % 2 == 0){


			$date = $do->parseDate($t->getDate());
			$page .= "<tr class='paire'>";
			$page .= "<td class='iDate'>".$date."</td><td class='iType'>".utf8_encode(RequeteSuite::getInstance()->getTypeSuiteById($t->getType()))."</td><td class='iNombre'>".$t->getNombre()."</td><td class='iTexte'><div class='info'>".$t->getTexte()."</div></td>";
			$page .= "</td><td class='iModif'>";
			$page .= "<table><form action = 'modification.php?modification.php?id=$idAffaire&mod=17&intervention=$idIntervention&idSuite=$idSuite' method='post' name='supprimer' onsubmit='if(verifSuite()) return true; else return false;'>";
			if($_SESSION[ 'privilege' ] == 'super-admin' || $_SESSION[ 'privilege' ] == 'admin' || $droitUserModif == TRUE)
				$page .= "<a href=modification.php?mod=16&intervention=$idIntervention&idSuite=$idSuite&id=$idAffaire class='btn btn-primary'>Modifier la suite</a>";
			if($_SESSION[ 'privilege' ] == 'super-admin'){
				$page .= "<input name='supprimerSuite' type='submit' value='Supprimer la suite' class='btn btn-danger'/>";
			}
			$page .= "</form></table></td></tr>";

		}
		else{
			$date = $do->parseDate($t->getDate());
			$page .= "<tr class='impaire'>";
			$page .= "<td class='iDate'>".$date."</td><td class='iType'>".utf8_encode(RequeteSuite::getInstance()->getTypeSuiteById($t->getType()))."</td><td class='iNombre'>".$t->getNombre()."</td><td class='iTexte'><div class='info'>".$t->getTexte()."</div></td>";
			$page .= "</td><td class='iModif'>";
			$page .= "<table><form action = 'modification.php?modification.php?id=$idAffaire&mod=17&intervention=$idIntervention&idSuite=$idSuite' method='post' name='supprimer' onsubmit='if(verifSuite()) return true; else return false;'>";
			if($_SESSION[ 'privilege' ] == 'super-admin' || $_SESSION[ 'privilege' ] == 'admin' || $droitUserModif == TRUE)
				$page .= "<a href=modification.php?mod=16&intervention=$idIntervention&idSuite=$idSuite&id=$idAffaire class='btn btn-primary'>Modifier la suite</a>";
			if($_SESSION[ 'privilege' ] == 'super-admin'){
				$page .= "<input name='supprimerSuite' type='submit' value='Supprimer la suite' class='btn btn-danger'/>";
			}
			$page .= "</form></table></td></tr>";

		}
		$i ++;


	}
	$page .= "</table></div><br/>";

	return $page;
}


/**
 *
 * Fonction d'affichage de l'intervention dans l'intervention courante
 *
 * @param $droitUserModif
 */
function afficheIntervention($droitUserModif)
{
	$id      = $_GET[ "intervention" ];
	$affaire = $_GET[ "id" ];

	$page = "";


	$intervention = RequeteIntervention::getInstance()->getIntervention($id);

	$documents = RequeteModification::getInstance()->getDocuments($id);


	$a = new Affaire();

	$typeIntervention = RequeteIntervention::getInstance()->nommeTypeIntervention($intervention->getType());
	$date             = $a->parseDate($intervention->getDate());

	$page .= "<div class='agentDate'><p>".$intervention->getAgent()." / ".$date."</p></div>";
	$page .= "<br/><div class='typeInter'><p>".utf8_encode($typeIntervention)."</p></div>";
	$page .= "<div class='texte container'><p>".$intervention->getTexte()."</p></div>";
	$page .= "<div class='suite'><p>".$intervention->getSuite()."</p></div>";
	$page .= afficheSuites($droitUserModif);
	$page .= "<div class='doc'><form action = 'download.php?id=$affaire&intervention=$id' method='post'>";
	$page .= "<select name='doc'>";
	$page .= "<option value = '-1'>--DOCUMENT A AFFICHER--</option>";
	foreach($documents as $d){
		$page .= "<option value='".$d->getId()."'>".$d->getNom()."</option>";
	}
	$page .= "</select>";
	$page .= "<input type='submit' value='Choisir Document' name='ValiderDoc' class='btn btn-success'/>";
	$page .= "<a href=modification.php?id=$affaire&mod=8&intervention=$id class='btn btn-inverse'>Gestion des Documents</a>";
	$page .= "</form>";
	$page .= "</div>";
	$page .= "<div class='bouton'>";

	if($_SESSION[ 'privilege' ] == 'super-admin' || $_SESSION[ 'privilege' ] == 'admin' || $droitUserModif == TRUE){
		$page .= "<a href=modification.php?id=$affaire&mod=9&intervention=$id class='btn btn-warning'>Modifier l'intervention</a>";
	}
	$page .= "<div class='retourAffaire'><a href=affaire.php?id=$affaire class='btn btn-info'>Retourner à l'affaire</a></div>";

	$page .= "</div>";


	echo $page;


}




?>


<html >
<head >
<link href = "bootstrap/css/bootstrap.min.css"
      rel = "stylesheet"
      media = "screen" >
    <link href = "css/intervention.css"
          rel = "stylesheet"
          media = "screen" >
	<?php bandeau(); ?>
</head >
<body >

<?php


afficheIntervention($droitUserModif);

?>

</body >

</html >