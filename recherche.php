﻿<?php

include_once 'bandeau.php';
include_once 'bd/RequeteRecherche.php';
include_once 'data/Adresse.php';
include_once 'data/Reponse.php';
include_once 'data/Agent.php';
include_once 'bd/RequeteAdresseDossier.php';
include_once 'bd/RequeteNouveau.php';

session_start();
if(! isset($_SESSION[ 'user' ]) || ! isset($_SESSION[ 'prenomNom' ]) || ! isset($_SESSION[ 'privilege' ])){
	header('location:connexion.php');
}

/**
 *
 * Fonction d'affichage du formulaire des adresses dans la recherche courante.
 *
 * @return string
 */
function rechercheAdresse()
{
	$numeroRueP    = "";
	$nomBatimentP  = "";
	$nomRueP       = "";
	$villeP        = "";
	$typeP         = "";
	$cadastreP     = "";
	$nomResidenceP = "";
	$dateDebutP    = "";
	$dateFinP      = "";
	$etatP         = "";
	$agentP        = "";
	$themeP        = "";
	$nomAffaireP   = "";

	if(isset($_POST[ "numeroRue" ])){
		$numeroRueP = $_POST[ "numeroRue" ];
	}
	if(isset($_POST[ "nomBatiment" ])){
		$nomBatimentP = $_POST[ "nomBatiment" ];
	}
	if(isset($_POST[ "nomRue" ])){
		$nomRueP = $_POST[ "nomRue" ];
	}
	if(isset($_POST[ "ville" ])){
		$villeP = $_POST[ "ville" ];
	}
	if(isset($_POST[ "type" ])){
		$typeP = $_POST[ "type" ];
	}
	if(isset($_POST[ "cadastre" ])){
		$cadastreP = $_POST[ "cadastre" ];
	}
	if(isset($_POST[ "nomResidence" ])){
		$nomResidenceP = $_POST[ "nomResidence" ];
	}
	if(isset($_POST[ "dateDebut" ])){
		$dateDebutP = $_POST[ "dateDebut" ];
	}
	if(isset($_POST[ "dateFin" ])){
		$dateFinP = $_POST[ "dateFin" ];
	}
	if(isset($_POST[ "etat" ])){
		$etatP = $_POST[ "etat" ];
	}
	if(isset($_POST[ "agent" ])){
		$agentP = $_POST[ "agent" ];
	}
	if(isset($_POST[ "theme" ])){
		$themeP = $_POST[ "theme" ];
	}
	if(isset($_POST[ "nomAffaire" ])){
		$nomAffaireP = $_POST[ "nomAffaire" ];
	}


	$ville  = RequeteRecherche::getInstance()->getTouteVilles();
	$type   = RequeteRecherche::getInstance()->getToutTypeRue();
	$agents = RequeteRecherche::getInstance()->getAgents();
	$theme  = RequeteRecherche::getInstance()->getThemes();

	$touteRues        = RequeteRecherche::getInstance()->getTouteRues();
	$toutNomBatiment  = RequeteRecherche::getInstance()->getToutNomBatiment();
	$toutNomResidence = RequeteRecherche::getInstance()->getToutNomResidence();

	$page = "<form action ='' method='post'>";
	$page .= "<table>";
	$page .= "<thead><tr>";
	$page .= "<th><label for='ville'>Ville</label></th>";
	$page .= "<th><label for='nomRue'>Nom de rue</label></th>";
	$page .= "<th><label for='type'>Type</label></th>";
	$page .= "<th><label for='numeroRue'>Numéro de la rue</label></th>";
	$page .= "<th><label for='nomResidence'>Nom de la résidence</label></th>";
	$page .= "<th><label for='nomBatiment'>Nom du Bâtiment</label></th>";
	$page .= "<th><label for='cadastre'>Numéro Cadastral</label></th>";
	$page .= "</tr></thead>";
	$page .= "<tr><td><select name='ville'>";
	$page .= "<option value = -1>CHOISSISSEZ UNE VILLE</option>";
	foreach($ville as $v){
		if($villeP == $v->getId()){
			$page .= "<option value ='".$v->getId()."' selected>".utf8_encode($v->getVille())."</option>";
		}
		else{
			$page .= "<option value ='".$v->getId()."'>".utf8_encode($v->getVille())."</option>";
		}
	}
	$page .= "</select></td>";

	$page .= "<td><select name='nomRue'>
				<option value = -1>----SELECTIONNEZ UN NOM DE RUE-----</option>";
	foreach($touteRues as $tr){
		if($nomRueP == $tr){
			$page .= "<option value=\"".$tr."\" selected>".$tr."</option>";
		}
		else{
			$page .= "<option value=\"".$tr."\">".$tr."</option>";
		}
	}
	$page .= "</select></td>";

	$page .= "<td><select name='type'>";
	$page .= "<option value = -1>CHOISSISSEZ UN TYPE DE RUE</option>";
	foreach($type as $t){
		if($typeP == $t->getId()){
			$page .= "<option value ='".$t->getId()."' selected>".utf8_encode($t->getTypeRues())."</option>";
		}
		else{
			$page .= "<option value ='".$t->getId()."'>".utf8_encode($t->getTypeRues())."</option>";
		}
	}
	$page .= "</select></td>";
	$page .= "<td><input type='number' class='input-large' name='numeroRue' value='".$numeroRueP."'/></td>";

	$page .= "<td><select name='nomResidence'>
				<option value = -1>----SELECTIONNEZ UNE RESIDENCE-----</option>";
	foreach($toutNomResidence as $tre){
		if($nomResidenceP == $tre){
			$page .= "<option value=\"".$tre."\" selected>$tre</option>";
		}
		else{
			$page .= "<option value=\"".$tre."\">$tre</option>";
		}
	}

	$page .= "<td><select name='nomBatiment'>
				<option value = -1>----SELECTIONNEZ UN DE BATIMENT-----</option>";
	foreach($toutNomBatiment as $tb){
		if($nomBatimentP == $tb){
			$page .= "<option value=\"".$tb."\" selected>$tb</option>";
		}
		else{
			$page .= "<option value=\"".$tb."\">$tb</option>";
		}
	}
	$page .= "</select></td>";

	$page .= "</select></td>";
	$page .= "<td><input type='text' class='input-large' class='input-large' name='cadastre' value='".$cadastreP."'/></td>";
	$page .= "</tr></table>";

	//----------------------------------------------------------------------------------------------------------------

	$page .= "<table><th><label for='nomAffaire'>Nom de l'affaire</label></th>";
	$page .= "<th><label for='dateDebut'>Date de début</label></th>";
	$page .= "<th><label for='dateFin'>Date de fin</label></th>";
	$page .= "<th><label for='etat'>Etat</label></th>";
	$page .= "<th><label for='agent'>Agent</label></th>";
	$page .= "<th><label for='theme'>Thème</label></th></tr>";

	$page .= "<td><input type='text' class='input-large' name='nomAffaire' value='".$nomAffaireP."'/></td>";

	//-------------------------------------------------------------------------------------------------------------------
	$page .= "<td><input type='text' class='input-large' id='datepickerDebut' name='dateDebut' value='".$dateDebutP."'/></td>";
	//-------------------------------------------------------------------------------------------------------------------
	$page .= "<td><input type='text' class='input-large' id='datepickerFin' name='dateFin' value='".$dateFinP."'/></td>";


	$page .= "<td><select name = 'etat'>";
	$page .= "<option value = -1>CHOISSISSEZ UN ETAT</option>";
	if($etatP == 'OUVERT'){
		$page .= "<option value = 'OUVERT' selected>OUVERT</option>";
	}
	else{
		$page .= "<option value = 'OUVERT'>OUVERT</option>";
	}

	if($etatP == 'FERME'){
		$page .= "<option value = 'FERME' selected>FERME</option>";
	}
	else{
		$page .= "<option value = 'FERME'>FERME</option>";
	}

	if($etatP == 'EN ATTENTE'){
		$page .= "<option value = 'EN ATTENTE' selected>EN ATTENTE</option>";
	}
	else{
		$page .= "<option value = 'EN ATTENTE'>EN ATTENTE</option>";
	}
	$page .= "</td>";

	$page .= "<td><select name='agent'>";
	$page .= "<option value = -1>CHOISSISSEZ UN AGENT</option>";
	foreach($agents as $a){
		if($agentP == $a){
			$page .= "<option value='$a' selected>$a</option>";
		}
		else{
			$page .= "<option value='$a'>$a</option>";
		}
	}
	$page .= "</select></td>";

	$page .= "<td><select name = 'theme'>";
	$page .= "<option value = -1>CHOISSISSEZ UN THEME</option>";
	foreach($theme as $th){
		if($themeP == $th->getId()){
			$page .= "<option value ='".$th->getId()."' selected>".$th->getTheme()."</option>";
		}
		else{
			$page .= "<option value ='".$th->getId()."'>".$th->getTheme()."</option>";
		}
	}
	$page .= "</select></td>";

	$page .= "</select></td>";
	$page .= "<td><input type='submit' class='btn btn-primary' value='Valider'/></td></tr></table></form>";


	return $page;

}

/**
 *
 * Fonction d'affichage des affaires dans la recherche courante.
 *
 * @return reponse
 */
function effectueRecherche()
{
	$page = "";

	$a = new Adresse();
	$d = new Affaire();
	;

	if(isset($_POST[ "numeroRue" ]) && ! empty($_POST[ "numeroRue" ])){
		$a->setNumeroRue($_POST[ "numeroRue" ]);
	}
	if(isset($_POST[ "nomBatiment" ]) && ! empty($_POST[ "nomBatiment" ]) && $_POST[ "nomBatiment" ] != - 1){
		$a->setnomBatiment($_POST[ "nomBatiment" ]);
	}
	if(isset($_POST[ "nomRue" ]) && $_POST[ "nomRue" ] != - 1){
		$a->setNomRue($_POST[ "nomRue" ]);
	}
	if(isset($_POST[ "ville" ]) && ! empty($_POST[ "ville" ]) && $_POST[ "ville" ] != - 1){
		$a->setVille($_POST[ "ville" ]);
	}
	if(isset($_POST[ "type" ]) && ! empty($_POST[ "type" ]) && $_POST[ "type" ] != - 1){
		$a->setType($_POST[ "type" ]);
	}
	if(isset($_POST[ "cadastre" ]) && ! empty($_POST[ "cadastre" ])){
		$a->setCadastre($_POST[ "cadastre" ]);
	}
	if(isset($_POST[ "nomResidence" ]) && ! empty($_POST[ "nomResidence" ]) && $_POST[ "nomResidence" ] != - 1){
		$a->setNomResidence($_POST[ "nomResidence" ]);
	}
	if(isset($_POST[ "dateDebut" ]) && ! empty($_POST[ "dateDebut" ])){
		$d->setDateDebut($_POST[ "dateDebut" ]);
	}
	if(isset($_POST[ "dateFin" ]) && ! empty($_POST[ "dateFin" ])){
		$d->setDateFin($_POST[ "dateFin" ]);
	}
	if(isset($_POST[ "etat" ]) && ! empty($_POST[ "etat" ]) && $_POST[ "etat" ] != - 1){
		$d->setEtat($_POST[ "etat" ]);
	}
	if(isset($_POST[ "agent" ]) && ! empty($_POST[ "agent" ]) && $_POST[ "agent" ] != - 1){
		$d->setNomAgent($_POST[ "agent" ]);
	}
	if(isset($_POST[ "theme" ]) && ! empty($_POST[ "theme" ]) && $_POST[ "theme" ] != - 1){
		$d->setTheme($_POST[ "theme" ]);
	}
	if(isset($_POST[ "nomAffaire" ]) && ! empty($_POST[ "nomAffaire" ])){
		$d->setNomAffaire($_POST[ "nomAffaire" ]);
	}

	$requete = RequeteRecherche::getInstance()->rechercheAdresse($a, $d);

	$i = 0;
	$page .= "<div class=\"scrollBar\"><table border=0 class=\"contenuRecherche\" >";
	foreach($requete as $r){


		$d = $r->getAffaire();
		$a = $r->getAdresse();

		$id  = $d->getId();
		$nom = $d->getNomAffaire();

		// $nom = $d->nommeAffaire($a, $id , RequeteAdresseDossier::getInstance()->getInitialeVille($a->getVille()));

		if(($i % 2) == 0){

			$page .= "<tr class='paire'>";
			$page .= "<td class=\"nomAffaire\"><a href='affaire.php?id=$id'> $nom</a></td>";
			$page .= "<td class=\"theme\">".$d->getTheme()."</td>";
			$page .= "<td class=\"numRue\">".$a->getNumeroRue()."</td>";
			$page .= "<td class=\"typeRue\">".utf8_encode($a->getType())."</td>";
			$page .= "<td class=\"nomRue\">".$a->getNomRue()."</td>";
			if(utf8_encode($a->getVille()) == 'ROQUEBRUNE-SUR-ARGENS'){
				$page .= "<td class=\"ville\">ROQUEBRUNE</td>";
			}
			elseif(utf8_encode($a->getVille()) == 'PUGET-SUR-ARGENS'){
				$page .= "<td class=\"ville\">PUGET</td>";
			}
			else{
				$page .= "<td class=\"ville\">".utf8_encode($a->getVille())."</td>";
			}
			$page .= "<td class=\"nomResidence\">".$a->getNomResidence()."</td>";
			$page .= "<td class=\"nomBatiment\">".$a->getNomBatiment()."</td>";
			$page .= "<td class=\"numCadastre\">".$a->getCadastre()."</td>";
			if($d->getEtat() == 'EN ATTENTE'){
				$page .= "<td class=\"etat\">ATTENTE</td>";
			}
			else{
				$page .= "<td class=\"etat\">".$d->getEtat()."</td>";
			}
			$page .= "<td class=\"agent\">".utf8_encode($d->getNomAgent())."</td>";
			$page .= "<td class=\"dateDebut\">".$d->getDateDebut()."</td>";
			$page .= "<td class=\"dateFin\">".$d->getDateFin()."</td>";
			$page .= "</tr>";

		}
		else{

			$page .= "<tr class='impaire'>";
			$page .= "<td class=\"nomAffaire\"><a href='affaire.php?id=$id'> $nom</a></td>";
			$page .= "<td class=\"theme\">".$d->getTheme()."</td>";
			$page .= "<td class=\"numRue\">".$a->getNumeroRue()."</td>";
			$page .= "<td class=\"typeRue\">".utf8_encode($a->getType())."</td>";
			$page .= "<td class=\"nomRue\">".$a->getNomRue()."</td>";
			if(utf8_encode($a->getVille()) == 'ROQUEBRUNE-SUR-ARGENS'){
				$page .= "<td class=\"ville\">ROQUEBRUNE</td>";
			}
			elseif(utf8_encode($a->getVille()) == 'PUGET-SUR-ARGENS'){
				$page .= "<td class=\"ville\">PUGET</td>";
			}
			else{
				$page .= "<td class=\"ville\">".utf8_encode($a->getVille())."</td>";
			}
			$page .= "<td class=\"nomResidence\">".$a->getNomResidence()."</td>";
			$page .= "<td class=\"nomBatiment\">".$a->getNomBatiment()."</td>";
			$page .= "<td class=\"numCadastre\">".$a->getCadastre()."</td>";
			if($d->getEtat() == 'EN ATTENTE'){
				$page .= "<td class=\"etat\">ATTENTE</td>";
			}
			else{
				$page .= "<td class=\"etat\">".$d->getEtat()."</td>";
			}
			$page .= "<td class=\"agent\">".utf8_encode($d->getNomAgent())."</td>";
			$page .= "<td class=\"dateDebut\">".$d->getDateDebut()."</td>";
			$page .= "<td class=\"dateFin\">".$d->getDateFin()."</td>";
			$page .= "</tr>";

		}
		$i ++;
	}
	$page .= "</table></div>";

	return $page;
}


?>

<!DOCTYPE HTML>
<html lang = "en" >
<head >
   <meta charset = "utf-8" />
  <script src = "jquery/js/jquery-1.9.1.js" ></script >
<script src = "jquery/development-bundle/ui/jquery-ui.custom.js" ></script >
  <link rel = "stylesheet"
        href = "jquery/css/ui-lightness/jquery-ui-1.10.3.custom.css" />
    <link href = "bootstrap/css/bootstrap.min.css"
          rel = "stylesheet"
          media = "screen" />
    <link href = "css/recherche.css"
          rel = "stylesheet"
          media = "screen" >
	<?php bandeau(); ?>
</head >
<body >
<center >
<?php


	echo rechercheAdresse();



	?>

	<div class = "row-fluid" >
    <div class = "span12" >
        <table border = 0
               class = "table table-bordered" >
            <tr class = "entete" >
                <th class = "nomAffaireT" >
                    Nom de l'affaire
                </th >
                <th class = "themeT" >
                    Thème
                </th >
                <th class = "numRueT" >
                    N° rue
                </th >
                <th class = "typeRueT" >
                    Type
                </th >
                <th class = "nomRueT" >
                    Nom de rue
                </th >
                <th class = "villeT" >
                    Ville
                </th >
                <th class = "nomResidenceT" >
                    Nom résidence
                </th >
                <th class = "nomBatimentT" >
                   Nom du Bâtiment
                </th >
                <th class = "numCadastreT" >
                    N° Cadastre
                </th >
                <th class = "etatT" >
                    Etat
                </th >
                <th class = "agentT" >
                    Agent(s)
                </th >
                <th class = "dateDebutT" >
                    Date Début
                </th >
                <th class = "dateFinT" >
                    Date Fin
                </th >
            </tr >
			</table >
	    <?php

	    echo effectueRecherche();

	    ?>
    </div >
</div >
    <script >
      $(function () {
	      $("#datepickerDebut").datepicker({ dateFormat: "dd-mm-yy"});  //Jquerry
      });
      $(function () {
	      $("#datepickerFin").datepicker({ dateFormat: "dd-mm-yy"});  //Jquerry
      });
 </script >
</center >
</body >


</html >