﻿<script xmlns = "http://www.w3.org/1999/html" >

    function verifAffaire() {
	    if (confirm("/!\\ ATTENTION /!\\\n Si vous confirmez, les données des Affaires, des Interventions, des Intervenants et les Documents associés à ce Affaire seront perdus.\nVoulez-vous supprimer ce Affaire?")) {
		    if (confirm("Êtes-vous bien certain(e) de vouloir supprimer ce Affaire et tout ce qu'il contient ?\n Cet Acte est irréversible.")) {
			    if (confirm("Dernière vérification avant suppression.\n La suppression sera définitive.")) {
				    return true;
			    }
		    }
	    }
    }
    function verifAffaire() {
	    if (confirm("/!\\ ATTENTION /!\\\n Si vous confirmez, des Interventions, des Intervenants et les Documents associés à ce Affaire seront perdus.\nVoulez-vous supprimer cette Affaire?")) {

		    return true;
	    }
    }
    function verrifIntervenant() {
	    if (confirm("/!\\ ATTENTION /!\\\n Si vous confirmez, les données des Affaires, des Interventions, des Intervenants et les Documents associés à ce Affaire seront perdus.\nVoulez-vous supprimer ce Affaire?")) {

		    return true;
	    }
    }
    function verifIntervention() {
	    if (confirm("/!\\ ATTENTION /!\\\n Si vous confirmez, des Interventions et les Documents associés à cette Intervention seront perdus.\nVoulez-vous supprimer cette Intervention?")) {

		    return true;
	    }
    }
    function verifDocument() {
	    if (confirm("/!\\ ATTENTION /!\\\n Si vous confirmez, les données des Affaires, des Interventions, des Intervenants et les Documents associés à ce Affaire seront perdus.\nVoulez-vous supprimer ce Affaire?")) {

		    return true;
	    }
    }

    function modifDateOuverture() {
	    window.open('', 'modifDateOuverture');
	    window.document.write("<form name='' action='' method='POST'>");
	    window.document.write("<p><input id='datepicker'/></p>")
	    $(function () {
		    $("#datepicker").datepicker({ dateFormat: "yy-mm-dd"});
	    });
	    window.document.write("<input type='submit' name='okDateOuverture'/>");
	    window.document.write("</form>");


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


    }
	
	

</script >

<?php
include_once 'bandeau.php';
include_once 'bd/RequeteAffaire.php';
include_once 'bd/RequeteAdresseDossier.php';
include_once 'bd/RequeteActivite.php';
include_once 'data/Contact.php';
include_once 'data/Document.php';
include_once 'bd/RequeteModification.php';
include_once 'bd/RequeteNouveau.php';


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
 * User: Nicolas CORDINA et Florian HULOT
 * Date: 14/06/13
 * Time: 10:56
 * To change this template use File | Settings | File Templates.
 */

/**
 *
 * Fonction d'affichage de la modification de la date d'ouverture de l'affaire courante.
 *
 * @param $date
 */

function modifDateOuverture($date)
{
	$id = $_GET[ "id" ];
	if($date == ""){
		echo "<br/>Vous devez remplir le champ pour changer la date.<br/>";
	}
	elseif(RequeteModification::getInstance()->isDateDebutValide($id, $date)){
		RequeteModification::getInstance()->modifieDateOuverture($id, $date);
		echo '<script language="Javascript">document.location.replace("affaire.php?id='.$id.'");</script>';
	}
	else{
		echo "<br/>La date de début doit être antérieur à la date de fin de l'affaire.<br/>";
	}
}

/**
 *
 * Fonction d'affichage de la modification de la date de fermeture ( désactivée )
 *
 * @param $date
 */

function modifDateFermeture($date)
{
	$id = $_GET[ "id" ];
	if($date == ""){
		echo "<br/>Vous devez remplir le champ pour changer la date.<br/>";
	}
	elseif(RequeteModification::getInstance()->isDateFinValide($id, $date)){
		RequeteModification::getInstance()->modifieDateFermeture($id, $date);
		//	header("location:affaire.php?id=$id");
	}
	else{
		echo "La date de début doit être antérieur à la date de fin de l'affaire.";
	}
}

/**
 *
 * Fonction d'affichage de la gestion des agents afféctés à l'affaire courante.
 *
 * @return string
 */
function modifAffaireSuivis()
{
	$page = "";
	if(isset($_POST[ 'boutonAjoutAgent' ])){
		if($_POST[ 'choixAgent' ] != ''){
			$id = $_GET[ "id" ];
			RequeteModification::getInstance()->ajouteAgent($_POST[ "choixAgent" ], $id);
			$page = "l'Agent a bien été ajouté à l'affaire.<br/>";
			echo '<script language="Javascript">document.location.replace("affaire.php?id='.$id.'");</script>';
		}
		else{
			$page = "Aucun agent n'a été sélectionné";
		}

	}
	elseif(isset($_POST[ 'boutonSupprAgent' ])){
		if($_POST[ 'choixAgent' ] != ''){
			$id = $_GET[ "id" ];
			RequeteModification::getInstance()->enleveAgent($_POST[ "choixAgent" ], $id);
			$page = "l'Agent a bien été supprimé à l'affaire.<br/>";
			echo '<script language="Javascript">document.location.replace("affaire.php?id='.$id.'");</script>';
		}
		else{
			$page = "Aucun agent n'a été sélectionné";
		}

	}
	elseif(isset($_POST[ 'ajouterAgent' ])){
		$id = $_GET[ "id" ];
		echo RequeteModification::getInstance()->reqAjoutAgentAffaire($id);
	}
	elseif(isset($_POST[ 'supprimerAgent' ])){
		$id = $_GET[ "id" ];
		echo RequeteModification::getInstance()->reqSupprAgentAffaire($id);
	}
	else{
		$page = "<form action='' method='post'><input type='submit' class='btn btn-primary btn-small' name='ajouterAgent' value='Ajouter un Agent'/></form>";
		$page .= "<form action='' method='post'><input type='submit' class='btn btn-danger btn-small' name='supprimerAgent' value='Supprimer un Agent'/></form>";
	}
	echo $page;
}

/**
 *
 * Fonction d'affichage de modification du thème de l'affaire courante.
 *
 *
 */
function modifTheme()
{
	$page = "";
	if(isset($_POST[ 'boutonChoixTheme' ])){
		if($_POST[ 'choixTheme' ] != ""){
			RequeteModification::getInstance()->modifieTheme($_GET[ 'id' ], $_POST[ 'choixTheme' ]);
			$page = "le theme a bien été modifié.<br/>";
		}
	}
	else{
		$themes = RequeteActivite::getInstance()->getThemes();
		$page   = "<form action='' method='POST'>";
		$page .= "<select name = 'choixTheme'>";
		$page .= "<option value=''>-----CHOISIR THEME----</option>";
		foreach($themes as $ths){
			$page .= "<option value='".$ths."'>".$ths."</option>";
		}
		$page .= "</select>";
		$page .= "<input type='submit' class='btn btn-success btn-small' name='boutonChoixTheme' value='Modifier le theme'/>";
		$page .= "</form>";
	}
	echo $page;
}

/**
 *
 * Fonction d'affichage de modification de l'adresse de l'affaire courante.
 *
 * @return string
 */
function modifAdresse()
{
	$id      = $_GET[ "id" ];
	$ville   = RequeteModification::getInstance()->getTouteVilles();
	$type    = RequeteModification::getInstance()->getToutTypeRue();
	$adresse = RequeteAdresseDossier::getInstance()->getAdresse($id);

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
		if(isset($_POST[ "nomBatiment" ])){
			$a->setnomBatiment($_POST[ "nomBatiment" ]);
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
	}

	$id      = $_GET[ "id" ];
	$ville   = RequeteModification::getInstance()->getTouteVilles();
	$type    = RequeteModification::getInstance()->getToutTypeRue();
	$adresse = RequeteAdresseDossier::getInstance()->getAdresse($id);

	$page = "Modification de l'adresse du dossier :<br/>";
	$page .= "<form action ='' method='post'>";
	$page .= "<table>";
	$page .= "<thead><tr>";
	$page .= "<th><label for='cadastre'>Numero Cadastral</label></th>";
	$page .= "<th><label for='ville'>Ville</label></th>";
	$page .= "<th><label for='type'>Type</label></th>";
	$page .= "<th><label for='numeroRue'>Numero de la rue</label></th>";
	$page .= "<th><label for='nomBatiment'>Complement d'Adresse</label></th>";
	$page .= "<th><label for='nomRue'>Nom de rue</label></th>";
	$page .= "<th><label for='nomResidence'>Nom de la residence</label></th>";
	$page .= "<th><label for='nomBatiment'>Batiment</label></th>";
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
	$page .= "<td><input type = 'text' class = 'input-large' name='numeroRue' value= '".$adresse->getNumeroRue()."'/></td>";
	$page .= "<td><input type = 'text' class = 'input-large' name='nomBatiment' value= '".$adresse->getnomBatiment()."' /></td>";
	$page .= "<td><input type = 'text' class = 'input-large' name='nomRue' value= '".$adresse->getNomRue()."'/></td>";
	$page .= "<td><input type = 'text' class = 'input-large' name='nomResidence' value= '".$adresse->getNomResidence()."'/></td>";
	$page .= "<td><input type = 'text' class = 'input-large' name='nomBatiment' value= '".$adresse->getNomBatiment()."'/></td></tr>";
	$page .= "<td><input type='submit' class='btn btn-warning' value='Modifier le Dossier' name ='okAdresse'/></td>";
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

	$page .= "Modification de l'adresse de l'affaire : :<br/>";
	$page .= "<form action ='' method='post'>";
	$page .= "<table>";
	$page .= "<thead><tr>";
	$page .= "<th><label for='etage'>Etage</label></th>";
	$page .= "<th><label for='cote'>Côté</label></th>";
	$page .= "<th><label for='numAppartVilla'>Numéro d'Appartement, de Villa.</label></th>";
	$page .= "<th><label for='nomEtablissement'>Nom de l'établissement</label></th>";
	$page .= "</tr></thead>";

	$page .= "<tr><td><input type = 'text' class = 'input-large' name='etage' value= '".$affaire->getEtage()."'/></td>";
	$page .= "<td><input type = 'text' class = 'input-large' name='cote' value= '".$affaire->getCote()."' /></td>";
	$page .= "<td><input type = 'text' class = 'input-large' name='numAppartVilla' value= '".$affaire->getNumAppartVilla()."'/></td>";
	$page .= "<td><input type = 'text' class = 'input-large' name='nomEtablissement' value= '".$affaire->getNomEtablissement()."'/></td></tr>";
	$page .= "<td><input type='submit' class='btn btn-warning' value='Modifier affaire' name ='okAdresseAffaire'/></td>";
	$page .= "</form>";
	$page .= "</table>";

	echo $page;
}

/**
 *
 * Fonction d'affichage de modification de l'intervention l'affaire courante ( désactivée )
 *
 * @return string
 */
function modifIntervention()
{

	$types = RequeteModification::getInstance()->getToutTypeInformation();

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

		$intervention->setId($_GET[ "intervention" ]);

		RequeteModification::getInstance()->modifieIntervention($intervention);

	}


	$page = "<table><form name='modifIntervention' action='' method='POST'>";
	$page .= "<tr><th><label for='modifIntTexte'>Texte</label></th>";
	$page .= "<th><label for='modifIntDate'>Date</label></th>";
	$page .= "<th><label for='modifIntType'>Type</label></th></tr>";
	$page .= "<tr><td><input type = 'text' class = 'input-large' name='modifIntTexte'/></td>";
	$page .= "<td><input type = 'text' class = 'input-large' id='datepicker' name='modifIntDate'/></td>";
	$page .= "<td><select name='typeInt'>";
	foreach($types as $t){
		$page .= "<option value='".$t->getId()."'>".$t->getType()."</option>";
	}

	$page .= "</select></td>";
	$page .= "<tr><td><input type='submit'class='btn btn-mini btn-warning ' name = 'modifIntervention'/></td></tr></table></form>";


	echo $page;

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

	return "<a href='affaire.php?id=$id' class='btn btn-info btn-mini'>Retourner à l'affaire</a>";
}

/**
 *
 * Fonction d'affichage de la date d'ouverture de l'affaire courante.
 *
 * @param $id
 *
 * @return string
 *
 */
function afficheDateOuverture($id)
{

	$d = new Affaire();

	$requete = RequeteAffaire::getInstance()->getDateOuverture($id);

	$date = $d->parseDate($requete);

	return $date;
}

/**
 *
 * Fonction d'affichage de la date d'ouverture de l'affaire courante.
 *
 * @param $id
 *
 * @return string
 *
 */
function afficheDateFermeture($id)
{

	$d = new Affaire();

	$requete = RequeteAffaire::getInstance()->getDatefermeture($id);
	if($requete != "--"){
		$date = $d->parseDate($requete);

		return $date;
	}

	return $requete;
}

/**
 *
 * Fonction d'affichage des agents suivants l'affaire courante.
 *
 * @param $id
 *
 * @return string
 */
function afficheAffaireSuivisPar($id)
{
	$requete = RequeteAffaire::getInstance()->getAgents($id);
	$affiche = "";
	foreach($requete as $r){
		$affiche .= $r." ";
	}

	return $affiche;
}

/**
 *
 * Fonction d'affichage de l'adresse "Dossier" de l'affaire courante.
 *
 * @param $id
 *
 * @return string
 */
function afficheAdresse($id)
{
	$adresse = RequeteAdresseDossier::getInstance()->getAdresse($id);

	$page = $adresse->getNumeroRue()." ".$adresse->getType()." ".$adresse->getNomRue()."</br>";
	$page .= $adresse->getNomResidence()." ".$adresse->getnomBatiment()."</br>";
	$page .= $adresse->getCodePostal()." ".$adresse->getVille()."</br>";


	return $page;
}

/**
 *
 * Fonction d'affichage de l'adresse "Affaire" de l'affaire courante.
 *
 * @param $id
 *
 * @return string
 */
function afficheAdresseAffaire($id)
{
	$adresse = RequeteAffaire::getInstance()->getAdresseAffaire($id);

	$page        = "<br/>";
	$etage       = $adresse->getEtage();
	$cote        = $adresse->getCote();
	$numAppVilla = $adresse->getNumAppartVilla();
	$nomEtab     = $adresse->getNomEtablissement();
	if(! empty($etage)){
		$page .= "<br/>Etage : ".$adresse->getEtage();
	}
	if(! empty($cote)){
		$page .= "<br/>Cote : ".$adresse->getCote();
	}
	if(! empty($numAppVilla)){
		$page .= "<br/>Num App/Villa : ".$adresse->getNumAppartVilla();
	}
	if(! empty($nomEtab)){
		$page .= "<br/><b>Nom Etablissement : ".$adresse->getNomEtablissement()."</b>";
	}


	return $page;
}

/**
 *
 * Fonction d'affichage du numéro cadastral de l'affaire courante.
 *
 * @param $id
 *
 * @return string
 */
function afficheNumeroCadastral($id)
{
	$requete = RequeteAffaire::getInstance()->getNumeroCadastral($id);

	return $requete;
}

/**
 *
 * Fonction d'affichage des contacts de l'affaire courante.
 *
 * @param $id
 *
 * @return string
 */
function  afficheIntervenant($id)
{

	$page        = "";
	$intervenant = RequeteAffaire::getInstance()->getTousContact($id);

	foreach($intervenant as $i){
		$page .= "<a href = contact.php?id=$id&int=".$i->getId()." class='btn btn-info btn-small btn-contact'>".$i->afficheprenomNomQualitee()."</a></br>";
	}

	return $page;

}

/**
 *
 * Fonction d'affichage des agents de l'affaire courante.
 *
 * @param $id
 *
 * @return string
 */
function afficheAgent($id)
{
	$affaire = RequeteAffaire::getInstance()->getAgents($id);
	$affiche = "";
	foreach($affaire as $d){
		$affiche .= $d."</br>";
	}

	return $affiche;


}

/**
 *
 * Fonction d'affichage des interventions de l'affaire courante.
 *
 * @param $idAffaire
 *
 * @return string
 */
function afficheAffaire($idAffaire)
{
	$tab = RequeteAffaire::getInstance()->getInterventionParAffaire($idAffaire);

	$id = $_GET[ "id" ];

	$page = "<table class='table table-bordered tableInfo titre'><tr class='entete'><th class='iDateT'>Date</th><th class='iTypeT'>Type</th><th class='iInfoT'>Information</th><th class='iModifT'>Modif/Ajout</th></tr></table><div class='contenuInter'><table class='table table-bordered tableInfo'>";

	$do = new Affaire();

	$i = 0;

	foreach($tab as $t){

		$idIntervention = $t->getId();

		if($i % 2 == 0){


			$date = $do->parseDate($t->getDate());
			$page .= "<tr class='paire'>";
			$page .= "<td class='iDate'>".$date."</td><td class='iType'>".utf8_encode(RequeteAffaire::getInstance()->getTypeInterventionById($t->getType()))."</td><td class='iInfo'><div class='info'>".$t->getTexte()."</div></td>";
			$page .= "</td><td class='iModif'>";
			$page .= "<table><form action = 'modification.php?id=$id&mod=10&intervention=$idIntervention' method='post' name='supprimer' onsubmit='if(verifIntervention()) return true; else return false;'>";
			$page .= "<a href=intervention.php?id=$id&intervention=$idIntervention class='btn btn-primary'>Visualiser</a>";
			if($_SESSION[ 'privilege' ] == 'super-admin'){
				$page .= "<input name='supprimerIntervention' type='submit' value='Supprimer l intervention' class='btn btn-danger'/>";
			}
			$page .= "</form></table></td></tr>";

		}
		else{
			$date = $do->parseDate($t->getDate());
			$page .= "<tr class='impaire'>";
			$page .= "<td class='iDate'>".$date."</td><td class='iType'>".utf8_encode(RequeteAffaire::getInstance()->getTypeInterventionById($t->getType()))."</td><td class='iInfo'><div class='info'>".$t->getTexte()."</div></td>";
			$page .= "</td><td class='iModif'>";
			$page .= "<table><form action = 'modification.php?id=$id&mod=10&intervention=$idIntervention' method='post' name='supprimer' onsubmit='if(verifIntervention()) return true; else return false;'>";
			$page .= "<a href=intervention.php?id=$id&intervention=$idIntervention class='btn btn-primary'>Visualiser</a>";
			if($_SESSION[ 'privilege' ] == 'super-admin'){
				$page .= "<input name='supprimerIntervention' type='submit' value='Supprimer l intervention' class='btn btn-danger'/>";
			}
			$page .= "</form></table></td></tr>";

		}
		$i ++;


	}
	$page .= "</table></div>";

	return $page;
}

/**
 *
 * Fonction d'affichage de modification de l'état de l'affaire courante.
 *
 * @param $id
 */
function modifieEtat($id)
{
	$page = "";
	$etat = RequeteAffaire::getInstance()->getEtat($id);

	if(isset($_POST[ "choixEtat" ])){
		$page .= "L'état de l'affaire a bien été modifié.";

		//if($etat == 'FERME' && $_POST['choixEtat'] == 'OUVERT') {
		RequeteAffaire::getInstance()->modifieEtat($_POST[ "choixEtat" ], $etat, $id);
		//}

		/*
		if($_POST['choixEtat'] == 'FERME') {
			if(RequeteAffaire::getInstance()->getDatefermeture($id) == "--") {
				$date = date("Y-m-d");
				RequeteModification::getInstance()->modifieDateFermeture($id, $date);
			}
		else {
				RequeteModification::getInstance()->modifieDateFermeture($id, "--");
		}
		}
		*/
	}

	$etat  = RequeteAffaire::getInstance()->getEtat($id);
	$etats = array("OUVERT", "EN ATTENTE", "FERME");

	$page .= "<form name='choixEtat' action ='' method='post'>";
	$page .= "<select class='span5' name = 'choixEtat'>";
	foreach($etats as $e){
		if($etat == $e){
			$page .= "<option value='$e' selected>$e</option>";
		}
		else{
			$page .= "<option value='$e'>$e</option>";
		}

	}
	$page .= '</select><input type="submit" value="Changer l\'état" class="btn btn-success"/> </form>';


	echo $page;
}

/*
function supprimeIntervenant($idIntervenant)
{
	$id = $_GET["id"];

	$page = "";

	if(isset($_POST["supprimeIntervenant"]))
	{

		RequeteAffaire::getInstance()->supprimerIntervenant($idIntervenant);
		header('location:activite.php');

	}

	$page .= "<form action = 'affaire.php?id=$id' method='post' name='supprimer' onsubmit='if(verifIntervenant()) return true; else return false;'>";
	$page .= "<input name='supprimeIntervenant' type='submit' value='Supprimer le affaire' class='btn btn-danger'/>";
	$page .="</form>";

	echo $page;

}
 */
 
/**
 *
 * Fonction d'affichage du thème de l'affaire courante.
 *
 * @param $id
 * @return mixed
 */
function afficheTheme($id)
{
	return RequeteAffaire::getInstance()->getThemeParAffaire($id);
}

?>


<!DOCTYPE html>
<html lang = "en"
      xmlns = "http://www.w3.org/1999/html" >
	<head >
		<script src = "jquery/js/jquery-1.9.1.js" ></script >
		<script src = "jquery/development-bundle/ui/jquery-ui.custom.js" ></script >
		
		<link rel = "stylesheet"
		      href = "jquery/css/ui-lightness/jquery-ui-1.10.3.custom.css" />
		<link rel = "stylesheet"
		      href = "/resources/demos/style.css" />

        <link href = "bootstrap/css/bootstrap.min.css"
              rel = "stylesheet"
              media = "screen" />
        <link href = "css/affaire.css"
              rel = "stylesheet"
              media = "screen" />
		<?php
		bandeau();
		$id = $_GET[ "id" ]
		?>
	</head >
    <body >

    <!-- INFOS DOSSIER -->
<div class = "InfoAffaire" >
    <div class = "row-fluid " >
        <!-- Adresse dossier et affaire -->
        <div class = "span2 affaire" >
            <p >
                <p ><b >Adresse</b ></p >
	        <?php
	        echo afficheAdresse($id);
	        echo afficheNumeroCadastral($id);
	        echo afficheAdresseAffaire($id);
	        if($_SESSION[ 'privilege' ] == 'super-admin' || $_SESSION[ 'privilege' ] == 'admin' || $droitUserModif == TRUE){
		        echo "<br/><a href=modification.php?id=$id&mod=4 class='btn btn-warning btn btn-mini'>Modifier Adresse et numero cadastral</a>";
	        }
	        ?>
	        </p>
        </div >
        <!-- Theme de l'affaire -->
        <div class = "span2" >
             <p ><p ><b >Theme de l'affaire :</b >
		        <?php
		        if(isset($_GET[ 'mod' ]) && $_GET[ 'mod' ] == 15){
			        if($_SESSION[ 'privilege' ] == 'super-admin' || $_SESSION[ 'privilege' ] == 'admin' || $droitUserModif == TRUE){
				        echo "<p>Pour modifier le theme</p>";
				        modifTheme();
				        echo  retourneAffaire();
			        }
		        }
		        else{
			        echo "<h3>".afficheTheme($id)."</h3>";
			        if($_SESSION[ 'privilege' ] == 'super-admin' || $_SESSION[ 'privilege' ] == 'admin' || $droitUserModif == TRUE){
				        echo "<br/><a href=affaire.php?id=$id&mod=15 class='btn btn-warning btn btn-mini'>Modifier le theme</a>";
			        }
		        }
		        ?>
             </p ></p>
        </div >
        <!-- Date et Etat -->
        <div class = "span3 affaire" >
            <p >
                <p ><b >Date d'ouverture</b ></p >
	        <?php
	        if(isset($_GET[ 'mod' ]) && $_GET[ 'mod' ] == 1){
		        if($_SESSION[ 'privilege' ] == 'super-admin' || $_SESSION[ 'privilege' ] == 'admin' || $droitUserModif == TRUE){
			        echo '<form name="" action="" method="POST">
								<p>Modifier la date d\'ouverture: <input type = "text" class = "input-large" id="datepicker" name="dateOuverture"/></p>
								<input type="submit" class="btn btn-success btn-small" name="okDateOuverture" value="Modifier la date d\'ouverture"/>
							</form>';
			        if(isset($_POST[ "dateOuverture" ]) && isset($_POST[ "okDateOuverture" ])){
				        modifDateOuverture($_POST[ "dateOuverture" ]);
				        unset($_POST);
			        }
			        echo  retourneAffaire();
		        }
	        }
	        else{
		        echo afficheDateOuverture($id);
		        if($_SESSION[ 'privilege' ] == 'super-admin' || $_SESSION[ 'privilege' ] == 'admin' || $droitUserModif == TRUE){
			        echo " <a href=affaire.php?id=$id&mod=1 class='btn btn-warning btn-mini'>Modifier date d'ouverture</a><br/><br/>";
		        }
	        }
	        ?>
	        <p ><b >Date de fermeture</b ></p >
	        <?php
	        if(isset($_GET[ 'mod' ]) && $_GET[ 'mod' ] == 13){
		        if($_SESSION[ 'privilege' ] == 'super-admin' || $_SESSION[ 'privilege' ] == 'admin' || $droitUserModif == TRUE){
			        echo "<form name='' action='' method='POST'>
						<p>Modifier la date de fermeture: <input type = 'text' class = 'input-large' id='datepicker' name='dateFermeture'/></p>
						<input type='submit' class='btn btn-warning' name='okDateFermeture' value='Modifier la date d ouverture'/>
						</form>";
			        if(isset($_POST[ "dateFermeture" ]) && isset($_POST[ "okDateFermeture" ])){
				        modifDateFermeture($_POST[ "dateFermeture" ]);
				        unset($_POST);
			        }
			        echo  retourneAffaire();
		        }
	        }
	        else{
		        echo afficheDatefermeture($id);
		        /*
		if($_SESSION['privilege']=='super-admin' || $_SESSION['privilege']=='admin' || $droitUserModif == TRUE ){
			echo " <a href=affaire.php?id=$id&mod=13 class='btn btn-mini'>Modifier date de fermeture</a><br/><br/>";
		}
		*/
	        }
	        ?>
	        </p>
	        <div class = "choixEtat" >
            <?php
		        if($_SESSION[ 'privilege' ] == 'super-admin' || $_SESSION[ 'privilege' ] == 'admin' || $droitUserModif == TRUE){
			        echo modifieEtat($id);
		        }
		        ?>
            </div >
        </div >
        <!-- affairesuivisPar -->
        <div class = "span2 affaire  " >
            <p >
                <p ><b >Affaire suivis par :</b > </p >
	        <?php
	        if(isset($_GET[ 'mod' ]) && $_GET[ 'mod' ] == 2){
		        if($_SESSION[ 'privilege' ] == 'super-admin' || $_SESSION[ 'privilege' ] == 'admin' || $droitUserModif == TRUE){
			        echo "<p>Pour modifier les agents</p>";
			        modifAffaireSuivis();
			        echo  retourneAffaire();
		        }
	        }
	        else{
		        echo  "<div class = 'affaireSuivisPar'>".afficheAgent($id)."</div>";
		        if($_SESSION[ 'privilege' ] == 'super-admin' || $_SESSION[ 'privilege' ] == 'admin' || $droitUserModif == TRUE){
			        echo "<br/><a href=affaire.php?id=$id&mod=2 class='btn btn-warning btn btn-mini'>Modifier Agent</a>";
		        }
	        }
	        ?>
	        </p>
        </div >
        <!-- contact -->
        <div class = "span3 affaire" >
            <p >
                <p ><b >Contact(s) sur l'affaire : </b ></p >
                <div class = "divContact" >
            <?php
	                echo afficheIntervenant($id);
	                echo "</div>";
	                if($_SESSION[ 'privilege' ] == 'super-admin' || $_SESSION[ 'privilege' ] == 'admin' || $droitUserModif == TRUE){
		                echo "<br/><a href=modification.php?id=$id&mod=5 class='btn btn-primary btn btn-mini'>Ajouter un contact</a>";
	                }
	                ?>
                </div >
	        </p>
        </div >
    </div >
</div >

    <!-- Affaire et Intervention -->
    <div class = "row-fluid affaire" >
        <!-- Menu de selection d'affaires -->
        <div class = "span3" >
        </div >
       </div >
        <div class = "row-fluid" >
            <div class = "span12 boutonAjoutIntervention" >
                <?php
	            if($_SESSION[ 'privilege' ] == 'super-admin' || $_SESSION[ 'privilege' ] == 'admin' || $droitUserModif == TRUE ) {
	            echo "<a href=modification.php?id=$id&mod=7 class='btn btn-primary btn-large'>Ajout Intervention</a>";
	            ?>
            </div >
            <div class = "tableauInter" >
                <?php
	            }
	            echo afficheAffaire($_GET[ "id" ]);


	            ?>

            </div >

            

        </div >


    </div>

    <div class = "supprimerAffaire" >
                <?php

	    if($_SESSION[ 'privilege' ] == 'super-admin'){
		    echo "<div class='boutonSupprimerAffaire'>";
		    echo "<form action = 'modification.php?id=$id&mod=12' method='post' name='supprimer' onsubmit='if(verifAffaire()) return true; else return false;'>";
		    echo '<br/><input name="supprimerAffaire " type="submit" value="Supprimer l\'affaire" class="btn btn-danger btn-large"/>';
		    echo  "</form>";
		    echo "</div>";
	    }

	    ?></div >



	<script >
      $(function () {
	      $("#datepicker").datepicker({ dateFormat: "dd-mm-yy"});  //Jquerry
      });
	</script >
    </body >
	

</html >