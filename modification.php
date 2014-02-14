﻿<?php

include_once 'bd/RequeteModification.php';
include_once 'bd/RequeteNouveau.php';
include_once 'bd/RequeteAffaire.php';
include_once 'bd/RequeteAdresseDossier.php';
include_once 'bd/RequeteIntervention.php';
include_once 'bd/RequeteRecherche.php';
include_once 'bd/RequeteSuite.php';

/*
 *
 *
 * LISTE DES MODIFICATIONS
 *
 * 3 = raison intervention
 * 4 = ajout contact
 * 5 = ajout affaire
 * 6 = ajout affaire
 * 7 = ajout intervention
 * 8 = ajout document
 * 9 = modifie intervention
 * 10 = suprimer intervention
 * 11 = supprimer affaire
 * 12 = supprimer affaire
 * 13 = modif date fermeture
 * 14 = supprimer intervenant affaire
 * 15 = ajout suite
 * 16 = modifier suite
 * 17 = supprimer suite
 */

include 'bandeau.php';

session_start();
if(! isset($_SESSION[ 'user' ]) || ! isset($_SESSION[ 'prenomNom' ])){
	header('location:connexion.php');
}

/**
 *
 * User: Nicolas CORDINA et Florian HULOT
 * Date: 14/06/13
 * Time: 10:56
 * To change this template use File | Settings | File Templates.
 */

/**
 *
 * Fonction d'affichage de modification d'adresse dans l'affaire courante.
 *
 * @return string
 */
function modifAdresse()
{
	$id      = $_GET[ "id" ];
	$ville   = RequeteModification::getInstance()->getTouteVilles();
	$type    = RequeteModification::getInstance()->getToutTypeRue();
	$adresse = RequeteAdresseDossier::getInstance()->getAdresse($id);
	//$affaire = RequeteAffaire::getInstance()->getAffaire($id);

	if(isset($_POST[ "okAdresse" ])){

		$a = new Adresse();
		if(isset($_POST[ "cadastre" ])){
			$a->setCadastre($_POST[ "cadastre" ]);
		}
		if(isset($_POST[ "ville" ])){
			$a->setVille($_POST[ "ville" ]);
		}
		if(isset($_POST[ "type" ])){
			$a->setType($_POST[ "type" ]);
		}
		if(isset($_POST[ "numeroRue" ])){
			$a->setNumeroRue($_POST[ "numeroRue" ]);
		}
		if(isset($_POST[ "complementAdresse" ])){
			$a->setcomplementAdresse($_POST[ "complementAdresse" ]);
		}
		if(isset($_POST[ "nomRue" ])){
			$a->setNomRue($_POST[ "nomRue" ]);
		}
		if(isset($_POST[ "nomResidence" ])){
			$a->setNomResidence($_POST[ "nomResidence" ]);
		}
		if(isset($_POST[ "nomBatiment" ])){
			$a->setNomBatiment($_POST[ "nomBatiment" ]);
		}
		$a->setId(RequeteModification::getInstance()->getIdAdresse($_GET[ 'id' ]));
		RequeteModification::getInstance()->modifieAdresse($id, $a, $adresse);
		header("location:affaire.php?id=$id");
	}

	$id      = $_GET[ "id" ];
	$ville   = RequeteModification::getInstance()->getTouteVilles();
	$type    = RequeteModification::getInstance()->getToutTypeRue();
	$adresse = RequeteAdresseDossier::getInstance()->getAdresse($id);

	$page = "<h3>Modification de l'adresse du dossier :</h3><br/>";
	$page .= "<form action ='' method='post'>";
	$page .= "<table>";
	$page .= "<thead><tr>";
	$page .= "<th><label for='cadastre'>Numéro Cadastral</label></th>";
	$page .= "<th><label for='ville'>Ville</label></th>";
	$page .= "<th><label for='type'>Type</label></th>";
	$page .= "<th><label for='numeroRue'>Numéro de la rue</label></th>";
	$page .= "<th><label for='nomRue'>Nom de rue</label></th>";
	$page .= "<th><label for='nomResidence'>Nom de la résidence</label></th>";
	$page .= "<th><label for='nomBatiment'>Bâtiment</label></th>";
	$page .= "</tr></thead>";

	$page .= "<tr><td><input type = 'text' class = 'input-large' name='cadastre' value= '".RequeteAffaire::getInstance()->getNumeroCadastral($id)."'/></td>";
	$page .= "<td><select name='ville'>";
	foreach($ville as $v){
		if(utf8_encode($v->getVille()) == $adresse->getVille()){
			$page .= "<option value ='".$v->getId()."' selected>".utf8_encode($v->getVille())."</option>";
		}
		else{
			$page .= "<option value ='".$v->getId()."'>".utf8_encode($v->getVille())."</option>";
		}
	}
	$page .= "</select></td>";
	$page .= "<td><select name='type'>";
	foreach($type as $t){
		if(utf8_encode($t->getTypeRues()) == $adresse->getType()){
			$page .= "<option value ='".$t->getId()."' selected>".utf8_encode($t->getTypeRues())."</option>";
		}
		else{
			$page .= "<option value ='".$t->getId()."'>".utf8_encode($t->getTypeRues())."</option>";
		}
	}
	$page .= "</select></td>";
	$page .= "<td><input type = 'number' class = 'input-large' name='numeroRue' value= '".$adresse->getNumeroRue()."'/></td>";
	$page .= "<td><input type = 'text' class = 'input-large' name='nomRue' value= '".htmlentities($adresse->getNomRue(), ENT_QUOTES)."'/></td>";
	$page .= "<td><input type = 'text' class = 'input-large' name='nomResidence' value= '".htmlentities($adresse->getNomResidence(), ENT_QUOTES)."'/></td>";
	$page .= "<td><input type = 'text' class = 'input-large' name='nomBatiment' value= '".htmlentities($adresse->getNomBatiment(), ENT_QUOTES)."'/></td></tr>";
	$page .= "<td><input type='submit'class='btn btn-success' value='Modifier le Dossier' name ='okAdresse'/></td>";
	$page .= "</form>";
	$page .= "</table><br/><br/>";


	$affaire = RequeteAffaire::getInstance()->getAdresse($id);

	if(isset($_POST[ "okAdresseAffaire" ])){
		$a = new Affaire();
		if(isset($_POST[ "etage" ])){
			$a->setEtage($_POST[ "etage" ]);
		}
		if(isset($_POST[ "cote" ])){
			$a->setCote($_POST[ "cote" ]);
		}
		if(isset($_POST[ "numAppartVilla" ])){
			$a->setNumAppartVilla($_POST[ "numAppartVilla" ]);
		}
		if(isset($_POST[ "nomEtablissement" ])){
			$a->setNomEtablissement($_POST[ "nomEtablissement" ]);
		}
		RequeteModification::getInstance()->modifieAdresseAffaire($id, $a, $affaire);
	}

	$affaire = RequeteAffaire::getInstance()->getAdresse($id);

	$page .= "<h3>Modification de l'adresse de l'affaire :</h3><br/>";
	$page .= "<form action ='' method='post'>";
	$page .= "<table>";
	$page .= "<thead><tr>";
	$page .= "<th><label for='etage'>Etage</label></th>";
	$page .= "<th><label for='cote'>Côté</label></th>";
	$page .= "<th><label for='numAppartVilla'>Numéro d'Appartement, de Villa.</label></th>";
	$page .= "<th><label for='nomEtablissement'>Nom de l'établissement</label></th>";
	$page .= "</tr></thead>";

	$page .= "<tr><td><input type = 'text' class = 'input-large' name='etage' value= '".htmlentities($affaire->getEtage(), ENT_QUOTES)."'/></td>";
	$page .= "<td><input type = 'text' class = 'input-large' name='cote' value= '".htmlentities($affaire->getCote(), ENT_QUOTES)."' /></td>";
	$page .= "<td><input type = 'text' class = 'input-large' name='numAppartVilla' value= '".htmlentities($affaire->getNumAppartVilla(), ENT_QUOTES)."'/></td>";
	$page .= "<td><input type = 'text' class = 'input-large' name='nomEtablissement' value= '".htmlentities($affaire->getNomEtablissement(), ENT_QUOTES)."'/></td></tr>";
	$page .= "<td><input type='submit' class='btn btn-success' value='Modifier Affaire' name ='okAdresseAffaire'/></td>";
	$page .= "</form>";
	$page .= "</table>";

	echo $page;
}

/**
 *
 * Fonction d'affichage de modification de l'intervention courante.
 *
 * @return string
 */
function modifIntervention($idAffaire)
{

	$idIntervention = $_GET[ "intervention" ];

	$type = RequeteModification::getInstance()->getToutTypeInformation();

	$int = RequeteIntervention::getInstance()->getIntervention($idIntervention);

	$agents = RequeteRecherche::getInstance()->getAgentsNomPrenom();


	if(isset($_POST[ "modifIntervention" ])){
		$intervention = new Intervention();

		if(isset($_POST[ "modifIntDate" ]) && ! empty($_POST[ "modifIntDate" ])){
			$intervention->setDate($_POST[ "modifIntDate" ]);
		}
		if(isset($_POST[ "modifIntTexte" ]) && ! empty($_POST[ "modifIntTexte" ])){
			$intervention->setTexte($_POST[ "modifIntTexte" ]);
		}
		if(isset($_POST[ "typeInt" ]) && ! empty($_POST[ "typeInt" ]) && $_POST[ "typeInt" ]){
			$intervention->setType($_POST[ "typeInt" ]);
		}

		if(isset($_POST[ "ModifAgentInter" ]) && ! empty($_POST[ "ModifAgentInter" ])){
			$intervention->setAgent($_POST[ "ModifAgentInter" ]);
		}

		$intervention->setId($_GET[ "intervention" ]);

		RequeteModification::getInstance()->modifieIntervention($intervention);

		header("location:affaire.php?id=$idAffaire&intervention=$idIntervention");

	}

	$page = "* champs obligatoire requis</br>";
	$page .= "<form action = '' method = 'POST'>";
	$page .= "<table class='modifIntervention'>";

	$page .= "<tr><th><label for='typeInfo'> Type d'intervention </label></th>";
	$page .= "<tbody><td><select name = 'typeInt'>";

	foreach($type as $t){
		if($t->getType() == $int->getType()){
			$page .= "<option value = '".$t->getId()."'selected>".utf8_encode($t->getType())."</option>";
		}
		$page .= "<option value = '".$t->getId()."'>".utf8_encode($t->getType())."</option>";
	}
	$page .= "</select>";
	$page .= "<tr><th><label for='texteInfo'> Date de l'intervention *</label></th></tr>";
	$page .= "<tr><td><input type = 'text' class = 'input-large' id = 'datepicker' name = 'modifIntDate' value='".$int->getDate()."' /></td></tr>";

	$page .= "<th><label for='texteInfo'> Information de l'intervention *</label></th>";
	$page .= "<tr><td class='texte'><textarea  col='20' rows='20' name='modifIntTexte'>".$int->getTexte()."</textarea></td></tr>";

	$page .= "<th><label for='texteInfo'> Agent affecte a l'intervention *</label></th>";
	$page .= "<tr><td><select name='ModifAgentInter'>";
	foreach($agents as $a){
		$agTest = utf8_encode($a->getPrenom()." ".$a->getNom());
		$ag = utf8_encode($a->getNom()." ".$a->getPrenom());
		if($int->getAgent() == $agTest){
			$page .= "<option = '$ag' selected>$ag</option>";
		}
		else{
			$page .= "<option = '$ag'>$ag</option>";
		}

	}
	$page .= "</td></tr>";


	$page .= "<tr><td><input type='submit'class='btn btn-success' value='Valider' name = 'modifIntervention'/></td></tr></table></form>";

	echo $page;

}

/**
 *
 * Fonction d'affichage d'ajout d'un contact dans l'affaire courante.
 *
 * @return string
 */
function ajouteIntervenant()
{
	$id   = $_GET[ "id" ];
	$page = "* champs obligatoire.</br>";

	$idAdresse = RequeteModification::getInstance()->getIdAdresse($id);

	if(isset($_POST[ "okIntervenant" ])){
		if(! empty($_POST[ "nomIntervenant" ]) && ! empty($_POST[ "qualiteeIntervenant" ])){
			$i = new Contact();

			if(isset($_POST[ "nomIntervenant" ])){
				$i->setNom($_POST[ "nomIntervenant" ]);
			}
			if(isset($_POST[ "prenomIntervenant" ])){
				$i->setPrenom($_POST[ "prenomIntervenant" ]);
			}
			if(isset($_POST[ "adresse" ])){
				$i->setAdresse($_POST[ "adresse" ]);
			}
			if(isset($_POST[ "ville" ])){
				$i->setVille($_POST[ "ville" ]);
			}
			if(isset($_POST[ "codePostal" ])){
				$i->setCodePostal($_POST[ "codePostal" ]);
			}
			if(isset($_POST[ "qualiteeIntervenant" ])){
				$i->setQualite($_POST[ "qualiteeIntervenant" ]);
			}
			if(isset($_POST[ "telephoneIntervenant" ])){
				$i->setTelephone($_POST[ "telephoneIntervenant" ]);
			}
			if(isset($_POST[ "mailIntervenant" ])){
				$i->setMail($_POST[ "mailIntervenant" ]);
			}
			if(isset($_POST[ "faxIntervenant" ])){
				$i->setFax($_POST[ "faxIntervenant" ]);
			}
			if(isset($_POST[ "syndicIntervenant" ])){
				$i->setSyndicProprioRegie($_POST[ "syndicIntervenant" ]);
			}

			RequeteModification::getInstance()->ajoutIntervenant($id, $i);

			header("location:affaire.php?id=$id");
		}
		else{
			$page .= "Veuillez saisir au moins le nom et la qualité du contact.<br/>";
		}
	}


	$id = $_GET[ "id" ];

	$idAdresse = RequeteModification::getInstance()->getIdAdresse($id);

	$page .= "<form action ='' method='POST'>";
	$page .= "<table>";

	$page .= "<tr><th><label for='nomIntervenant'><b>Nom ou personne morale *</b></label></th>";
	$page .= "<td><input type = 'text' class = 'input-large' name='nomIntervenant'/></td></tr>";

	$page .= "<tr><th><label for='prenomIntervenant'>Prenom</label></th>";
	$page .= "<td><input type = 'text' class = 'input-large' name='prenomIntervenant'/></td></tr>";

	$page .= "<tr><th><label for='adresse'>Adresse</label></th>";
	$page .= "<td><input type = 'text' class = 'input-large' name='adresse'/></td></tr>";

	$page .= "<tr><th><label for='ville'>Ville</label></th>";
	$page .= "<td><input type = 'text' class = 'input-large' name='ville'/></td></tr>";

	$page .= "<tr><th><label for='codePostal'>Code Postal</label></th>";
	$page .= "<td><input type = 'number' class = 'input-large' name='codePostal'/></td></tr>";

	$page .= "<tr><th><label for='qualiteeIntervenant'><b>Qualité *</b></label></th>";
	$page .= "<td><input type = 'text' class = 'input-large' name='qualiteeIntervenant'/></td></tr>";

	$page .= "<tr><th><label for='telephoneIntervenant'>Telephone</label></th>";
	$page .= "<td><input type = 'text' class = 'input-large' name='telephoneIntervenant'/></td></tr>";

	$page .= "<tr><th><label for='mailIntervenant'>Mail</label></th>";
	$page .= "<td><input type = 'text' class = 'input-large' name='mailIntervenant'/></td></tr>";

	$page .= "<tr><th><label for='faxIntervenant'>Fax</label></th>";
	$page .= "<td><input type = 'text' class = 'input-large' name='faxIntervenant'/></td></tr>";

	$page .= "<tr><th><label for='syndicIntervenant'>Complément</label></th>";
	$page .= "<td><input type = 'text' class = 'input-large' name='syndicIntervenant'/></td></tr>";

	$page .= "<tr><td><input type='submit' class='btn btn-success' name = 'okIntervenant' value = 'Valider'/></td></tr>";
	$page .= "</table></form>";

	echo $page;


}

/**
 *
 * Fonction d'affichage d'ajout d'intervention de l'affaire courante.
 *
 * @return string
 */
function ajoutIntervention($idAffaire)
{
	$id = $_GET[ "id" ];

	$type   = RequeteModification::getInstance()->getToutTypeInformation();
	$agents = RequeteRecherche::getInstance()->getAgentsNomPrenom();

	$page = "* champs obligatoire.</br>";
	$page .= "<form action = '' method = 'POST'>";
	$page .= "<table>";

	$page .= "<tr><th><label for='typeInfo'><b> Type d'intervention *</b></label></th>";
	$page .= "<th><label for='texteInfo'><b> Date de l'intervention *</b></label></th>";
	$page .= "<th><label for='texteInfo'><b> Information de l'intervention *</b></label></th>";
	$page .= "<th><label for='texteInfo'><b> Agent affecte à l'intervention *</b></label></th>";

	$page .= "<tbody><td><select name = 'typeInfo'>";

	foreach($type as $t){
		$page .= "<option value = '".$t->getId()."'>".utf8_encode($t->getType())."</option>";
	}

	$page .= "</select>";
	$page .= "<td><input type = 'text' class = 'input-large' id = 'datepicker' name = 'date' /></td>";
	$page .= "<td><textarea col='20' rows='6' name='texteInfo'></textarea></td>";

	$page .= "<td><select name='choixAgentInter'>";
	$page .= "<option value = '-1'>CHOISSISEZ UN AGENT</option>";
	foreach($agents as $a){
		$page .= "<option value = '".$a->getNom()." ".utf8_encode($a->getPrenom())."'>".utf8_encode($a->getNom())." ".utf8_encode($a->getPrenom())."</option>";
	}
	$page .= "</select></td>";
	$page .= "</tbody><tr><td><input type='submit' class='btn btn-success' name='okIntervention' value='valider' /></td></tr>";
	$page .= "</table></form>";

	if(isset($_POST[ "okIntervention" ])){

		if(! empty($_POST[ "texteInfo" ]) && ! empty($_POST[ "typeInfo" ])){

			$inf = new Intervention();

			date_default_timezone_set('Europe/Paris');

			$inf->setTexte($_POST[ "texteInfo" ]);
			$inf->setType($_POST[ "typeInfo" ]);
			if($_POST[ 'date' ] == ""){
				$inf->setDate(date("Y-m-d"));
			}
			else{
				$inf->setDate($_POST[ 'date' ]);
			}

			if($_POST[ "choixAgentInter" ] == - 1){
				$agent = explode( " ", $_SESSION[ "prenomNom" ]);
				$inf->setAgent($agent[1]." ".$agent[0]);
			}
			else{
				$inf->setAgent($_POST[ "choixAgentInter" ]);
			}

			RequeteModification::getInstance()->ajoutIntervention($id, $inf);
			echo "L'intervention a bien été ajoutée.";
			header("location:affaire.php?id=$idAffaire");

		}


	}


	return $page;


}

/**
 *
 * Fonction d'affichage de l'ajout de document dans l'intervention courante.
 *
 * @param $idIntervention
 *
 * @return string
 */
function ajoutDocument($idIntervention)
{
	$id = $_GET[ "id" ];

	if(isset($_POST[ "upload" ])){
		// define the posted file into variables
		$name     = $_FILES[ 'fichier' ][ 'name' ];
		$tmp_name = $_FILES[ 'fichier' ][ 'tmp_name' ];
		$type     = $_FILES[ 'fichier' ][ 'type' ];
		$size     = $_FILES[ 'fichier' ][ 'size' ];


		if(! empty($_FILES[ 'fichier' ][ 'error' ])){
			echo "erreur = ->".$_FILES[ 'fichier' ][ 'error' ]."</br>";
		}

		// if your server has magic quotes turned off, add slashes manually
		if(! get_magic_quotes_gpc()){
			$name = addslashes($name);
		}

		// open up the file and extract the data/content from it
		$extract = fopen($tmp_name, 'r');
		$content = fread($extract, $size);
		$content = addslashes($content);
		fclose($extract);

		//$lien = realpath($content);
		$lien = realpath($tmp_name);

		//echo "fichier : " . $lien;
		date_default_timezone_set("Europe/Paris");
		$date = Date("Ymd");


		$document = new Document();
		$document->setDocument($lien);
		$document->setTaille($size);
		$document->setNom($name);
		$document->setDate($date);
		$document->setType($type);


		RequeteModification::getInstance()->ajoutDocument($idIntervention, $document);
		echo "Fichier hébergé avec succès.</br>";

	}

	$page = "<form action=''method='POST' enctype='multipart/form-data'>
                <input name='fichier' type='file'>
                <input name='upload' type='submit' class='btn btn-success' value='Hebergement'>
                </form>";


	return $page;

}

/**
 *
 * Fonction d'affichage de suppression de documents dans l'intervention courante.
 *
 * @param $idIntervention
 * @return string
 */
function supprimeDocument($idIntervention)
{

	if(isset($_POST[ "SupprimerDoc" ]) && $_POST[ "doc" ] != - 1){
		RequeteModification::getInstance()->supprimeDocument($_POST[ "doc" ]);
	}


	$documents = RequeteModification::getInstance()->getDocuments($idIntervention);

	$page = "<p>Suppression d'un document</p>";
	$page .= "<form action = '' method='post'>";
	$page .= "<select name='doc'>";
	$page .= "<option value = '-1'>----------CHOISSISSEZ UN DOCUMENT A SUPPRIMER--------------</option>";
	foreach($documents as $d){
		$page .= "<option value='".$d->getId()."'>".$d->getNom()."</option>";
	}
	$page .= "</select>";
	$page .= "<input type='submit' value='Supprimer Document' name='SupprimerDoc' class='btn btn-danger'/>";
	$page .= "</form></div>";

	return $page;
}





/**
 *
 * Fonction d'affichage d'un bouton pour retourner à l'affaire courante.
 *
 * @return string
 */
function retourneAffaire()
{
	$id = $_GET[ "id" ];

	return "<br/><a href='affaire.php?id=$id' class='btn btn-info'>Retourner à l'affaire</a>";
}

/**
 *
 * Fonction d'affichage d'ajout suites dans l'intervention courante.
 *
 * @param $idIntervention
 * @param $idAffaire
 * @return string
 */
function ajoutSuite($idIntervention, $idAffaire)
{

	$types = RequeteSuite::getInstance()->getToutTypeSuite();
	$suite = new Suite();

	$page = "";

	if(isset($_POST[ "okSuiteAjout" ])){
		if(isset($_POST[ "typeSuite" ]) && $_POST[ "typeSuite" ] != - 1 && isset($_POST[ "texteSuite" ])){
			if(empty($_POST[ "dateSuite" ])){
				$date = Date('Ymd');
				$suite->setDate($date);
			}
			else{
				$suite->setDate($_POST[ "dateSuite" ]);
			}

			$suite->setType($_POST[ "typeSuite" ]);
			$suite->setTexte($_POST[ "texteSuite" ]);
			$suite->setNombre($_POST[ "nombreSuite" ]);

			$suite->setIntervention($_GET[ "intervention" ]);

			RequeteSuite::getInstance()->ajouteSuite($suite);
			header("location:intervention.php?id=$idAffaire&intervention=$idIntervention");

		}
		else{
			$page .= "Veuillez renseigner tout les champs";
		}
	}


	$page .= "<form action = '' method='post'>";
	$page .= "<label>Date Suite</label><input type='text' id='datepicker' name='dateSuite'/>";
	$page .= "<label>Type Suite</label><select name='typeSuite'>";
	$page .= "<option value='-1'>---SELECTIONNEZ UN TYPE</option>";
	foreach($types as $t){
		$page .= "<option value='".$t->getId()."'>".$t->getType()."</option>";
	}
	$page .= "</select>";
	$page .= "<label>Nombre</label><input type='number' value='1' class='input-large' name='nombreSuite'/>";
	$page .= "<label>Texte Suite</label><textarea col='20' rows='6' name='texteSuite'></textarea>";
	$page .= "<br/><input type='submit' class='btn btn-success' value='Valider' name='okSuiteAjout'/>";
	$page .= "</form>";

	return $page;
}


/**
 *
 * Fonction d'affichage de modification suite dans l'intervention courante.
 *
 * @param $id
 * @param $idIntervention
 * @param $idAffaire
 * @return string
 */
function modifieSuite($id, $idIntervention, $idAffaire)
{

	$types = RequeteSuite::getInstance()->getToutTypeSuite();

	$suite = RequeteSuite::getInstance()->getSuite($id);

	$texte  = $suite->getTexte();
	$type   = $suite->getType();
	$date   = $suite->getDate();
	$nombre = $suite->getNombre();

	$page = "";

	if(isset($_POST[ "okSuiteModif" ])){

		if(isset($_POST[ "typeSuite" ]) && $_POST[ "typeSuite" ] != - 1 && isset($_POST[ "texteSuite" ]) && isset($_POST[ "nombreSuite" ])){
			if(empty($_POST[ "dateSuite" ])){
				$date = new Date('Ymd');
				$suite->setDate($date);
			}
			else{
				$suite->setDate($_POST[ "dateSuite" ]);
			}

			$suite->setType($_POST[ "typeSuite" ]);
			$suite->setTexte($_POST[ "texteSuite" ]);
			$suite->setNombre($_POST[ "nombreSuite" ]);

			$suite->setIntervention($_GET[ "intervention" ]);
			$suite->setId($_GET[ "idSuite" ]);


			RequeteSuite::getInstance()->modifieSuite($suite);
			header("location:intervention.php?id=$idAffaire&intervention=$idIntervention");
		}
		else{
			$page .= "Veuillez renseigner tout les champs";
		}

	}

	$page .= "<form action = '' method='post'>";
	$page .= "<label>Date</label><input type='text' id='datepicker' name='dateSuite' value='$date' />";
	$page .= "<label>Type de suite</label><select name='typeSuite'>";
	$page .= "<option value='-1'>---SELECTIONNEZ UN TYPE</option>";
	foreach($types as $t){
		if($t->getId() == $type){
			$page .= "<option value='".$t->getId()."'selected>".$t->getType()."</option>";
		}
		else{
			$page .= "<option value='".$t->getId()."'>".$t->getType()."</option>";
		}
	}
	$page .= "</select>";
	$page .= "<label>Nombre</label><input type='number' value='$nombre' class='input-large' name='nombreSuite'/>";
	$page .= "<label>Texte</label><textarea col='20' rows='6' name='texteSuite'>".$texte."</textarea>";
	$page .= "<br/><input type='submit' class='btn btn-success' value='Valider' name='okSuiteModif'/>";
	$page .= "</form>";
	$page .= "<a href=intervention.php?id=$idAffaire&intervention=$idIntervention class='btn btn-info'>Retourner à l'intervention</a>";

	return $page;

}

?>

<script language = "JavaScript" >

    function modifDateOuverture() {
	    window.open('', 'modifDateOuverture');
	    window.document.write("<form name='' action='' method='POST'>");
	    window.document.write("<p><input id='datepicker'/></p>")
	    $(function () {
		    $("#datepicker").datepicker({ dateFormat: "yy-mm-dd"});
	    });
	    window.document.write("<input type='submit' name='okDateOuverture'/>");
	    window.document.write("</form>");
	    var id = <?php echo $_GET["id"]?>;
	    document.location.href = "affaire.php?id=id";

    }

    function modifDatefermeture() {
	    window.open('', 'modifDateFermeture');
	    window.document.write("<form name='' action='' method='POST'>");
	    window.document.write("<p><input id='datepicker'/></p>")
	    $(function () {
		    $("#datepicker").datepicker({ dateFormat: "yy-mm-dd"});
	    });
	    window.document.write("<input type='submit' name='okDateFermeture'/>");

	    window.document.write("</form>");
	    var id = <?php echo $_GET["id"]?>;
	    document.location.href = "affaire.php?id=id";

    }




</script >

<html lang = "fr" >
<head >
  <meta charset = "utf-8" />
  <script src = "jquery/js/jquery-1.9.1.js" ></script >
<script src = "jquery/development-bundle/ui/jquery-ui.custom.js" ></script >
  <link rel = "stylesheet"
        href = "jquery/css/ui-lightness/jquery-ui-1.10.3.custom.css" />
  <link rel = "stylesheet"
        href = "/resources/demos/style.css" />
    <link href = "bootstrap/css/bootstrap.min.css"
          rel = "stylesheet"
          media = "screen" >
    <link href = "css/modification.css"
          rel = "stylesheet"
          media = "screen" >

</head >
<body >
<?php
bandeau();

if($_GET[ "mod" ] == 4){


	echo "<h2><p>Modifie adresse</p></h2>";
	modifAdresse();
	echo  retourneAffaire();
}
if($_GET[ "mod" ] == 5){
	echo "<p>Ajout du contact</p>";
	ajouteIntervenant();
	echo  retourneAffaire();

}

if($_GET[ "mod" ] == 7){
	echo "<p>Ajout Intervention</p>";
	echo ajoutIntervention($_GET[ "id" ]);

	echo  retourneAffaire();

}

if($_GET[ "mod" ] == 8){

	echo "<p>Ajout Document</p>";
	echo ajoutDocument($_GET[ "intervention" ]);
	echo supprimeDocument($_GET[ "intervention" ]);


	$idIntervention = $_GET[ "intervention" ];
	$id             = $_GET[ "id" ];
	echo "<br/><a href='intervention.php?id=$id&intervention=$idIntervention' class='btn btn-info'>Retourner à l'intervention</a>";

}

if($_GET[ "mod" ] == 9){
	echo "<p>Modifie Intervention</p>";
	echo modifIntervention($_GET[ "id" ]);

	$idIntervention = $_GET[ "intervention" ];
	$id             = $_GET[ "id" ];

	echo "<br/><a href='intervention.php?id=$id&intervention=$idIntervention' class='btn btn-info'>Retourner à l'intervention</a>";
}

if($_GET[ "mod" ] == 10){

	$id = $_GET[ "id" ];

	RequeteAffaire::getInstance()->supprimerIntervention($_GET[ "intervention" ]);
	header("location:affaire.php?id=$id");
}
if($_GET[ "mod" ] == 11){

	$id = $_GET[ "id" ];

	RequeteAffaire::getInstance()->supprimerAffaire($_GET[ "affaire" ]);
	header("location:affaire.php?id=$id");

}

if($_GET[ "mod" ] == 12){

	$id = $_GET[ "id" ];

	RequeteAffaire::getInstance()->supprimeAffaire($id);
	header("location:activite.php");

}

if($_GET[ "mod" ] == 13){

	echo "<form name='' action='' method='POST'>
    <p>Modifier la date de fermeture: <input type = 'text' class = 'input-large' id='datepicker' name='dateFermeture'/></p>
    <input type='submit' name='okDateFermeture'/>
    </form>";
	if(isset($_POST[ "dateFermeture" ]) && isset($_POST[ "okDateFermeture" ])){

		modifDateFermeture($_POST[ "dateFermeture" ]);
		unset($_POST);
	}
	echo  retourneAffaire();
}

if($_GET[ "mod" ] == 14){
	$id          = $_GET[ "id" ];
	$intervenant = $_GET[ "Contact" ];

	RequeteAffaire::getInstance()->supprimerContact($intervenant);
	header("location:affaire.php?id=$id");
}

if($_GET[ "mod" ] == 15){

	$id = $_GET[ "id" ];

	$intervention = $_GET[ "intervention" ];

	echo ajoutSuite($intervention, $id);

	echo "<a href =intervention.php?id=$id&intervention=$intervention class='btn btn-info'>Retourner a l'intervention</a>";


}
if($_GET[ "mod" ] == 16){


	$intervention = $_GET[ "intervention" ];
	$idSuite      = $_GET[ "idSuite" ];


	echo modifieSuite($idSuite, $intervention, $_GET[ "id" ]);


}
if($_GET[ "mod" ] == 17){

	$idSuite      = $_GET[ "idSuite" ];
	$id           = $_GET[ "id" ];
	$intervention = $_GET[ "intervention" ];
	RequeteAffaire::getInstance()->supprimerSuite($idSuite);
	header("location:intervention.php?id=$id&intervention=$intervention");


}

if($_GET[ "mod" ] == 18){
	$id          = $_GET[ "id" ];
	$intervenant = $_GET[ "intervenant" ];

	modifieContact();

	header("location:contact.php?id=$id&int=$intervenant");
}

?>

<script >
      $(function () {
	      $("#datepicker").datepicker({ dateFormat: "dd-mm-yy"});  //Jquerry
      });
 </script >
</body >
</html >