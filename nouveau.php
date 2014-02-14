<?php

session_start();
if(! isset($_SESSION[ 'user' ]) || ! isset($_SESSION[ 'prenomNom' ]) || ! isset($_SESSION[ 'privilege' ])){
	header('location:connexion.php');
}

include_once 'bandeau.php';
include_once 'bd/RequeteNouveau.php';
include_once'data/Adresse.php';
include_once 'data/Affaire.php';
include_once 'bd/RequeteRecherche.php';


/**
 *
 * Fonction spécifique à la gestion de quote dans le code.
 *
 * @param $data
 *
 * @return int|mixed|string
 */
function gereQuote($data)
{
	if(! isset($data) or empty($data))
		return '';
	if(is_numeric($data))
		return $data;

	$non_displayables = array('/%0[0-8bcef]/', // url encoded 00-08, 11, 12, 14, 15
		'/%1[0-9a-f]/', // url encoded 16-31
		'/[\x00-\x08]/', // 00-08
		'/\x0b/', // 11
		'/\x0c/', // 12
		'/[\x0e-\x1f]/' // 14-31
	);
	foreach($non_displayables as $regex){
		$data = preg_replace($regex, '', $data);
	}
	$data = str_replace("'", "''", $data);

	return $data;
}

if(! isset($d)){
	$d = new Affaire();
}
if(! isset($_SESSION[ 'ville' ])){
	$_SESSION[ 'ville' ] = "";
}
if(! isset($_SESSION[ 'type' ])){
	$_SESSION[ 'type' ] = "";
}
if(! isset($_SESSION[ 'nomRue' ])){
	$_SESSION[ 'nomRue' ] = "";
}
if(! isset($_SESSION[ 'numeroRue' ])){
	$_SESSION[ 'numeroRue' ] = "";
}
$erreur = "";

?>

<html >
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
		<link href = "./css/nouveau.css"
		      rel = "stylesheet"
		      media = "screen" >
	</head >
	<body >
		<?php
		bandeau();
		$html = "<center><table><tr><h1>Ajout d'une nouvelle Affaire :</h1></tr>";
		
		// Si l'utilisateur valide son affaire.
		if(isset($_POST[ "ok" ])){
			if($_POST[ "dateDebut" ] == ""){
				$erreur .= "Vous Devez mettre une date de debut pour l'affaire.<br/>";
			}
			/*
			if($_POST["dateFin"] != ""){

				//Transforme la date en object date afin de pouvoir le comparer

				$dateDebut = new DateTime($_POST["dateDebut"] , new DateTimeZone('Europe/Paris'));
				$dateDebut = $dateDebut->format('Ymd');
				$dateFin = new DateTime($_POST["dateFin"] , new DateTimeZone('Europe/Paris'));
				$dateFin = $dateFin->format('Ymd');

				if ($dateDebut > $dateFin) {
					$erreur .= "La date de début doit être antérieur à la date de fin de l'affaire.<br/>";
				}
			}
				*/
			if($_POST[ "etat" ] == - 1){
				$erreur .= "Vous devez selectionner un Etat pour l'affaire.</br>";
			}
			if($_POST[ "theme" ] == - 1){
				$erreur .= "Vous devez selectionner un Theme pour l'affaire.<br/>";
			}
			if($erreur == ""){ //Si l'adresse est deja dans la BD
				$a = new Adresse();
				if(isset($_SESSION[ "ville" ])){ // Si on cree une nouvelle Adresse
					$a->setVille($_SESSION[ "ville" ]);
					$a->setType($_SESSION[ "type" ]);
					$a->setNomRue($_SESSION[ "nomRue" ]);

					$a->setNumeroRue($_SESSION[ "numeroRue" ]);


					if(isset($_SESSION[ "residence" ])){ // AJout nom de residence
						$a->setNomResidence($_SESSION[ "residence" ]);
						if(isset($_SESSION[ "nomBatiment" ])){
							$a->setNomBatiment($_SESSION[ "nomBatiment" ]);
						}
					}
					if(isset($_SESSION[ "cadastre" ]) && ! empty($_SESSION[ "cadastre" ])){
						$a->setCadastre($_SESSION[ "cadastre" ]);
					}
					//Si on l'adresse existe deja on prend juste son ID
					//TODO Mettre theme dans getAdresseFromID
					if(! RequeteNouveau::getInstance()->isDoublonAdresse($a->getVille(), $a->getType(), $a->getNomRue(), $a->getNumeroRue(), $a->getNomResidence(), $a->getNomBatiment()) == TRUE){
						$a = RequeteNouveau::getInstance()->ajouteAdresse($a);
					}
					$a->setId(RequeteNouveau::getInstance()->getIdAdresse($a));
				}
				else{
					$a->setVille($_SESSION[ "villeInst" ]);
					$a->setType($_SESSION[ "typeInst" ]);
					$a->setNomRue($_SESSION[ "nomRueInst" ]);
					$a->setNumeroRue($_SESSION[ "numeroRueInst" ]);
					if(isset($_SESSION[ "residenceInst" ])){ // AJout nom de residence
						$a->setNomResidence($_SESSION[ "residenceInst" ]);
						if(isset($_SESSION[ "nomBatimentInst" ])){
							$a->setNomBatiment($_SESSION[ "nomBatimentInst" ]);
						}
					}
					$a->setId(RequeteNouveau::getInstance()->getIdAdresse($a)); // On recupere l id de l'adresse
				}

				$d->setDateDebut($_POST[ "dateDebut" ]);
				//$d->setDateFin($_POST["dateFin"]);
				$d->setEtat($_POST[ "etat" ]);
				$d->setTheme($_POST[ "theme" ]);
				$d->setEtage($_POST[ "etage" ]);
				$d->setCote($_POST[ "cote" ]);
				$d->setNumAppartVilla($_POST[ "numAppartVilla" ]);
				$d->setNomEtablissement($_POST[ "nomEtablissement" ]);
				$d->setAdresse($a->getId());
				
				// tout est bon, on ajoute l'affaire et on détruit les variables de session
				if(RequeteNouveau::getInstance()->isDoublonAffaire($d) == FALSE){
					RequeteNouveau::getInstance()->ajouteAffaire($a, $d, $_SESSION[ 'prenomNom' ]);
					$id = RequeteNouveau::getInstance()->getIdAffaire($d);
					//session_destroy();
					unset($_SESSION[ "villeInst" ]);
					unset($_SESSION[ "typeInst" ]);
					unset($_SESSION[ "nomRueInst" ]);
					unset($_SESSION[ "numeroRueInst" ]);
					unset($_SESSION[ "residenceInst" ]);
					unset($_SESSION[ "nomBatimentInst" ]);
					unset($_SESSION[ "ville" ]);
					unset($_SESSION[ "type" ]);
					unset($_SESSION[ "nomRue" ]);
					unset($_SESSION[ "numeroRue" ]);
					unset($_SESSION[ "residence" ]);

					unset($_SESSION[ "cadastre" ]);

					echo "<script>location.href='affaire.php?id=$id';</script>";

				}
				// Si l'affaire existe déjà, on retourne la même page avec un lien sur l'affaire.
				else{
					$id = RequeteNouveau::getInstance()->getIdAffaire($d);
					$erreur .= "L'affaire existe déjà. Pour y accéder, <a href='affaire.php?id=".$id."'>cliquez ici</a>";
					$html .= $erreur;
					$themeAffaire = RequeteRecherche::getInstance()->getThemes();
					$html .= "</table><table><tr><h3>Saisie de l'affaire :</h3></tr>
					  <tr><th>Theme de l'affaire</th>
					  <form action = '' method = 'post'>
					  <td><select name = 'theme' >
					  <option value='-1'>----SELECTIONNEZ UN THEME----</option>";

					foreach($themeAffaire as $t){
						if($t->getId() == $d->getTheme()){
							$html .= "<option value='".$t->getId()."' selected>".$t->getTheme()."</option>";
						}
						else{
							$html .= "<option value='".$t->getId()."'>".$t->getTheme()."</option>";
						}
					}
					$html .= "</select ></td></tr>

						  <tr><th>Date de debut</th>";


					$html .= "<td><input type = 'text' class = 'input-large' id = 'datepickerDebut' name = 'dateDebut' value= '".$d->getDateDebut()."'/></td></tr>";

					$html .= "<tr><th>Etat</th>
						  <td><select name = 'etat' >
							  <option value = '-1'>----SELECTIONNEZ UN ETAT----</option>";
					if($d->getEtat() == 'OUVERT'){
						$html .= "<option value = 'OUVERT' selected>OUVERT</option >";
						$html .= "<option value = 'EN ATTENTE' >EN ATTENTE</option >";
					}
					else{
						$html .= "<option value = 'OUVERT' >OUVERT</option >";
						$html .= "<option value = 'EN ATTENTE' selected>EN ATTENTE</option >";
					}

					$html .= "</select ></td></tr>

						  <tr><th>Etage ou se situe l'affaire</th>
						  <td><input type = 'text' class = 'input-large' class = 'input-large' name = 'etage' value= '".$d->getEtage()."'/></td></tr>

						  <tr><th>Coté ou se situe l'affaire</th>
						  <td><input type = 'text' class = 'input-large' class = 'input-large' name = 'cote' value= '".$d->getCote()."'/></td></tr>

						  <tr><th>Numéro d'appartement ou de villa</th>
						  <td><input type = 'text' class = 'input-large' class = 'input-large' name = 'numAppartVilla' value= '".$d->getNumAppartVilla()."'/></td></tr>

						  <tr><th>Nom de l'établissement Commercial</th>
						  <td><input type = 'text' class = 'input-large' class = 'input-large' name = 'nomEtablissement' value= '".$d->getNomEtablissement()."'/></td></tr></table>
						  <input type = 'submit' name = 'ok' class='btn btn-success' value = 'Valider'/ ></form></center>
					<script>
						$(function () {
							$(\"#datepickerDebut\").datepicker({ dateFormat: \"dd-mm-yy\"});  //Jquerry
						});
						$(function () {
							$(\"#datepickerFin\").datepicker({ dateFormat: \"dd-mm-yy\"});  //Jquerry
						});
					</script >";
				}

			}
			// Si il y a une erreur dans la saisie de l'affaire, on retourne l'erreur et le formulaire
			else{
				$html .= $erreur;
				$themeAffaire = RequeteRecherche::getInstance()->getThemes();
				$html .= "</table><table><tr><h3>Saisie de l'affaire :</h3></tr>
					  <tr><th>Theme de l'affaire</th>
					  <form action = '' method = 'post'>
					  <td><select name = 'theme' >
					  <option value='-1'>----SELECTIONNEZ UN THEME----</option>";

				foreach($themeAffaire as $t){
					$html .= "<option value='".$t->getId()."'>".$t->getTheme()."</option>";
				}
				$html .= "</select ></td></tr>

					  <tr><th>Date de debut</th>
					  <td><input type = 'text' class = 'input-large' id = 'datepickerDebut' name = 'dateDebut' /></td></tr>

					  <tr><th>Etat</th>
					  <td><select name = 'etat' >
						  <option value = '-1'>----SELECTIONNEZ UN ETAT----</option>
						  <option value = 'OUVERT' >OUVERT</option >
						  <option value = 'EN ATTENTE' >EN ATTENTE</option >
					  </select ></td></tr>

					  <tr><th>Etage ou se situe l'affaire</th>
					  <td><input type = 'text' class = 'input-large' class = 'input-large' name = 'etage' /></td></tr>

					  <tr><th>Coté ou se situe l'affaire</th>
					  <td><input type = 'text' class = 'input-large' class = 'input-large' name = 'cote' /></td></tr>

					  <tr><th>Numéro d'appartement ou de villa</th>
					  <td><input type = 'text' class = 'input-large' class = 'input-large' name = 'numAppartVilla' /></td></tr>

					  <tr><th>Nom de l'établissement Commercial</th>
					  <td><input type = 'text' class = 'input-large' class = 'input-large' name = 'nomEtablissement' /></td></tr></table>
					  <input type = 'submit' name = 'ok' class='btn btn-success' value = 'Valider'/ ></form></center>
				<script>
					$(function () {
						$(\"#datepickerDebut\").datepicker({ dateFormat: \"dd-mm-yy\"});  //Jquerry
					});
					$(function () {
						$(\"#datepickerFin\").datepicker({ dateFormat: \"dd-mm-yy\"});  //Jquerry
					});
				</script >";
			}
		}
		// Si l'utilisateur valide son adresse.
		elseif(isset($_POST[ 'saisieAdresseExistePas' ]) || isset($_POST[ 'saisieAdresseExiste' ])){
			if(isset($_POST[ 'saisieAdresseExistePas' ])){
				if($_POST[ 'ville' ] == ""){
					$erreur .= "Vous devez saisir une ville.<br/>";
				}
				if(($_POST[ 'numeroRue' ] == "") || is_numeric($_POST[ 'numeroRue' ]) == FALSE){
					$erreur .= "Vous devez saisir un numéro de rue ou mettre un nombre en numéro de rue.<br/>";
				}
				if($_POST[ 'type' ] == ""){
					$erreur .= "Vous devez saisir un type de rue.<br/>";
				}
				if($_POST[ 'nomRue' ] == ""){
					$erreur .= "Vous devez saisir un nom de rue.<br/>";
				}
				/*
				if(RequeteNouveau::getInstance()->isDoublonAdresse($_POST["ville"], $_POST["type"], $_POST["nomRue"], $_POST["numeroRue"], $_POST["residence"],$_POST["nomBatiment"], "")){
					$erreur .= "L'adresse saisie existe déjà.";
				}
				*/
				if($erreur == ""){
					$_SESSION[ 'ville' ]     = $_POST[ "ville" ];
					$_SESSION[ 'type' ]      = $_POST[ "type" ];
					$_SESSION[ 'nomRue' ]    = $_POST[ "nomRue" ];
					$_SESSION[ 'numeroRue' ] = $_POST[ "numeroRue" ];

					if(isset($_POST[ "residence" ])){ // AJout nom de residence
						$_SESSION[ 'residence' ] = $_POST[ "residence" ];
						if(isset($_POST[ "nomBatiment" ])){
							$_SESSION[ 'nomBatiment' ] = $_POST[ "nomBatiment" ];
						}
					}

					if(isset($_POST[ "cadastre" ]) && ! empty($_POST[ "cadastre" ])){
						$_SESSION[ 'cadastre' ] = $_POST[ "cadastre" ];
					}
				}
			}
			if(isset($_POST[ 'saisieAdresseExiste' ]) && ($_POST[ 'villeInst' ] == - 1 || $_POST[ 'typeInst' ] == - 1 || $_POST[ 'nomRueInst' ] == - 1 || $_POST[ 'numeroRueInst' ] == - 1)){
				$erreur .= "Veuillez saisir tous les champs de l'adresse.<br/>";
			}
			// Si l'adresse est complète, on saisie l'affaire
			if($erreur == ""){
				$themeAffaire = RequeteRecherche::getInstance()->getThemes();
				$html .= "</table><table><tr><h3>Saisie de l'affaire :</h3></tr>
					  <tr><th>Theme de l'affaire</th>
					  <form action = '' method = 'post'>
					  <td><select name = 'theme' >
					  <option value='-1'>----SELECTIONNEZ UN THEME----</option>";
				foreach($themeAffaire as $t){
					$html .= "<option value='".$t->getId()."'>".$t->getTheme()."</option>";
				}
				$html .= "</select ></td></tr>

					  <tr><th>Date de debut</th>
					  <td><input type = 'text' class = 'input-large' id = 'datepickerDebut' name = 'dateDebut' /></td></tr>

					  <tr><th>Etat</th>
					  <td><select name = 'etat' >
						  <option value = '-1'>----SELECTIONNEZ UN ETAT----</option>
						  <option value = 'OUVERT' >OUVERT</option >
						  <option value = 'EN ATTENTE' >EN ATTENTE</option >
					  </select ></td></tr>

					  <tr><th>Etage ou se situe l'affaire</th>
					  <td><input type = 'text' class = 'input-large' class = 'input-large' name = 'etage' /></td></tr>

					  <tr><th>Coté ou se situe l'affaire</th>
					  <td><input type = 'text' class = 'input-large' class = 'input-large' name = 'cote' /></td></tr>

					  <tr><th>Numéro d'appartement ou de villa</th>
					  <td><input type = 'text' class = 'input-large' class = 'input-large' name = 'numAppartVilla' /></td></tr>

					  <tr><th>Nom de l'établissement Commercial</th>
					  <td><input type = 'text' class = 'input-large' class = 'input-large' name = 'nomEtablissement' /></td></tr></table>
					  <input type = 'submit' name = 'ok' class='btn btn-success' value = 'Valider'/ ></form></center>
				<script>
					$(function () {
						$(\"#datepickerDebut\").datepicker({ dateFormat: \"dd-mm-yy\"});  //Jquerry
					});
					$(function () {
						$(\"#datepickerFin\").datepicker({ dateFormat: \"dd-mm-yy\"});  //Jquerry
					});
				</script >";
			}
			// On affiche l'adresse avec la saisie précédente.
			else{
				//session_destroy();
				//session_start();
				/*
				unset($_SESSION["villeInst"]);
				unset($_SESSION["typeInst"]);
				unset($_SESSION["nomRueInst"]);
				unset($_SESSION["numeroRueInst"]);
				unset($_SESSION["residenceInst"]);

				unset($_SESSION["ville"]);
				unset($_SESSION["type"]);
				unset($_SESSION["nomRue"]);
				unset($_SESSION["numeroRue"]);
				unset($_SESSION["residence"]);
				unset($_SESSION["cadastre"]);
				*/
				$_SESSION[ 'ville' ]       = $_POST[ "ville" ];
				$_SESSION[ 'type' ]        = $_POST[ "type" ];
				$_SESSION[ 'nomRue' ]      = $_POST[ "nomRue" ];
				$_SESSION[ 'numeroRue' ]   = $_POST[ "numeroRue" ];
				$_SESSION[ 'residence' ]   = $_POST[ "residence" ];
				$_SESSION[ 'nomBatiment' ] = $_POST[ "nomBatiment" ];
				$html .= "</table><table><tr><h3>Saisie de l'adresse :</h3></tr>";
				$html .= $erreur;

				$html .= "<br/><br/><form action='' method='post'><tr><th>Ville</th>
						  <td><select name = 'ville' >
							<option value =''>-----SELECTIONNEZ UNE VILLE------</option>";
				$ville = RequeteNouveau::getInstance()->getTouteVilles();
				foreach($ville as $v){
					if(isset($_SESSION[ "villeInst" ]) && $_SESSION[ "villeInst" ] != - 1 && $_SESSION[ "villeInst" ] == $v->getId())
						$html .= "<option value='".$v->getId()."' selected>".$v->getVille()." </option>";
					else
						$html .= "<option value='".$v->getId()."'>".$v->getVille()." </option>";
				}
				$html .= "</select ></td></tr>";


				$html .= "</select ></td></tr>
						  <tr><th>Nom de la rue</th>";
				if(isset($_SESSION[ "nomRueInst" ]))
					$html .= "<td><input type = \"text\" class = \"input-large\" name = \"nomRue\" value=\"".$_SESSION[ "nomRueInst" ]."\"/>";
				elseif(isset($_SESSION[ "nomRue" ]))
					$html .= "<td><input type = \"text\" class = \"input-large\" name = \"nomRue\" value=\"".$_SESSION[ "nomRue" ]."\"/>";
				else
					$html .= "<td><input type = 'text' class = 'input-large' name = 'nomRue' />";
				$html .= "  <span style='font-style:italic;font-size:12px;'>Notation : Nom(Prenom)</span></td></tr>";


				$html .= "<tr><th>Type de rue</th>
						  <td><select name = 'type' >
							  <option value =''>-----SELECTIONNEZ UN TYPE DE RUE------</option>";

				$type = RequeteNouveau::getInstance()->getToutTypeRue();
				echo $_SESSION[ "type" ];
				foreach($type as $t){
					if(isset($_SESSION[ "typeInst" ]) && $_SESSION[ "typeInst" ] != - 1 && $_SESSION[ "typeInst" ] == $t->getId())
						$html .= "<option value='".$t->getId()."' selected>".$t->getTypeRues()." </option>";
					elseif(isset($_SESSION[ "type" ]) && $_SESSION[ "type" ] != - 1 && $_SESSION[ "type" ] == $t->getId())
						$html .= "<option value='".$t->getId()."' selected>".$t->getTypeRues()." </option>";
					else
						$html .= "<option value='".$t->getId()."' >".$t->getTypeRues()." </option>";
				}


				$html .= "<tr><th>Numero de rue</th>";
				if(isset($_SESSION[ "numeroRueInst" ]))
					$html .= "<td><input type = 'number' class = 'input-large' name = 'numeroRue' value='".$_SESSION[ "numeroRueInst" ]."'/></td></tr>";
				elseif(isset($_SESSION[ "numeroRueInst" ]))
					$html .= "<td><input type = 'number' class = 'input-large' name = 'numeroRue' value='".$_SESSION[ "numeroRue" ]."'/></td></tr>";
				else
					$html .= "<td><input type = 'number' class = 'input-large' name = 'numeroRue' /></td></tr>";


				$html .= "<tr><th>Nom de la residence</th>";
				if(isset($_SESSION[ "residenceInst" ]))
					$html .= "<td><input type = \"text\" class = \"input-large\" name = \"residence\" value=\"".$_SESSION[ "residenceInst" ]."\"/></td></tr>";
				elseif(isset($_SESSION[ "residence" ]))
					$html .= "<td><input type = \"text\" class = \"input-large\" name = \"residence\" value=\"".$_SESSION[ "residence" ]."\"/></td></tr>";
				else
					$html .= "<td><input type = 'text' class = 'input-large' name = 'residence'/></td></tr>";


				$html .= "<tr><th>Nom du Batiment</th>";
				if(isset($_SESSION[ "nomBatimentInst" ]))
					$html .= "<td><input type = \"text\" class = \"input-large\" name = \"nomBatiment\" value=\"".$_SESSION[ "nomBatimentInst" ]."\"/></td></tr>";
				elseif(isset($_SESSION[ "nomBatiment" ]))
					$html .= "<td><input type = \"text\" class = \"input-large\" name = \"nomBatiment\" value=\"".$_SESSION[ "nomBatiment" ]."\"/></td></tr>";
				else
					$html .= "<td><input type = 'text' class = 'input-large' name = 'nomBatiment' /></td></tr>";


				$html .= "<tr><th>Numéro Cadastral</th>";
				$html .= "<td>Pour ajouter un cadastre à une adresse,<br/> veuillez aller dans la modification d'adresse <br/> de l'affaire une fois ajoutée.</td></tr></table>";

				$html .= "<br/><input type='submit' name='saisieAdresseExistePas' class='btn btn-success' value='Valider Adresse'/></form><br/><br/>";
			}

			if(isset($_POST[ 'confAdresseExistePas' ])){
				/*
	unset($_SESSION["villeInst"]);
	unset($_SESSION["typeInst"]);
	unset($_SESSION["nomRueInst"]);
	unset($_SESSION["numeroRueInst"]);
	unset($_SESSION["residenceInst"]);
	unset($_SESSION["nomBatimentInst"]);
	*/
				unset($_SESSION[ "ville" ]);
				unset($_SESSION[ "type" ]);
				unset($_SESSION[ "nomRue" ]);
				unset($_SESSION[ "numeroRue" ]);
				unset($_SESSION[ "residence" ]);
				unset($_SESSION[ "cadastre" ]);

				$html .= "<br/><br/><form action='' method='post'><tr><th>Ville</th>
						  <td><select name = 'ville' >
							<option value =''>-----SELECTIONNEZ UNE VILLE------</option>";
				$ville = RequeteNouveau::getInstance()->getTouteVilles();
				foreach($ville as $v){
					if(isset($_SESSION[ "villeInst" ]) && $_SESSION[ "villeInst" ] != - 1 && $_SESSION[ "villeInst" ] == $v->getId())
						$html .= "<option value='".$v->getId()."' selected>".$v->getVille()." </option>";
					else
						$html .= "<option value='".$v->getId()."'>".$v->getVille()." </option>";
				}
				$html .= "</select ></td></tr>";


				$html .= "</select ></td></tr>
						  <tr><th>Nom de la rue</th>";
				if(isset($_SESSION[ "nomRueInst" ]))
					$html .= "<td><input type = \"text\" class = \"input-large\" name = \"nomRue\" value=\"".$_SESSION[ "nomRue" ]."\"/>";
				else
					$html .= "<td><input type = 'text' class = 'input-large' name = 'nomRue' />";
				$html .= "  <span style='font-style:italic;font-size:12px;'>Notation : Nom(Prenom)</span></td></tr>";

				$html .= "<tr><th>Type de rue</th>
						  <td><select name = 'type' >
							  <option value =''>-----SELECTIONNEZ UN TYPE DE RUE------</option>";

				$type = RequeteNouveau::getInstance()->getToutTypeRue();
				foreach($type as $t){
					if(isset($_SESSION[ "typeInst" ]) && $_SESSION[ "typeInst" ] != - 1 && $_SESSION[ "typeInst" ] == $v->getId()){
						$html .= "<option value='".$t->getId()."' selected>".$t->getTypeRues()." </option>";
					}
					else

						$html .= "<option value='".$t->getId()."' >".$t->getTypeRues()." </option>";
				}


				$html .= "<tr><th>Numero de rue</th>";
				if(isset($_SESSION[ "numeroRueInst" ]))
					$html .= "<td><input type = 'number' class = 'input-large' name = 'numeroRue' value='".$_SESSION[ "numeroRueInst" ]."'/></td></tr>";
				else
					$html .= "<td><input type = 'number' class = 'input-large' name = 'numeroRue' /></td></tr>";


				$html .= "<tr><th>Nom de la residence</th>";
				if(isset($_SESSION[ "residenceInst" ]))
					$html .= "<td><input type = \"text\" class = \"input-large\" name = \"residence\" value=\"".$_SESSION[ "residenceInst" ]."\"/></td></tr>";
				else
					$html .= "<td><input type = 'text' class = 'input-large' name = 'residence'/></td></tr>";


				$html .= "<tr><th>Nom du Batiment</th>";
				if(isset($_SESSION[ "nomBatimentInst" ]))
					$html .= "<td><input type = \"text\" class = \"input-large\" name = \"nomBatiment\" value=\"".$_SESSION[ "nomBatiment" ]."\"/></td></tr>";
				else
					$html .= "<td><input type = 'text' class = 'input-large' name = 'nomBatiment' /></td></tr>";


				$html .= "<tr><th>Numéro Cadastral</th>";
				$html .= "<td>Pour ajouter un cadastre à une adresse,<br/> veuillez aller dans la modification d'adresse <br/> de l'affaire une fois ajoutée.</td></tr></table>";

				$html .= "<br/><input type='submit' name='saisieAdresseExistePas' class='btn btn-success' value='Valider Adresse'/></form><br/><br/>";

			}
			elseif(isset($_POST[ 'confAdresseExiste' ]) || isset($_POST[ 'villeInst' ]) || isset($_POST[ 'typeInst' ]) || isset($_POST[ 'nomRueInst' ]) || isset($_POST[ 'numeroRueInst' ]) || isset($_POST[ 'residenceInst' ])){

				$html .= '<br/><tr><form action="" method="post"><input type = "submit" name = "confAdresseExistePas" class="btn btn-info" value = "Saisie de l\'adresse"/ ></form></tr><br/>';
			}

		}
		// l'utilisateur saisie l'adresse
		else{
			//session_destroy();
			//session_start();

			$html .= "</table><table><tr><h3>Saisie de l'adresse :</h3></tr>";
			if(! isset($_POST[ 'confAdresseExistePas' ])){

				unset($_SESSION[ "villeInst" ]);
				unset($_SESSION[ "typeInst" ]);
				unset($_SESSION[ "nomRueInst" ]);
				unset($_SESSION[ "numeroRueInst" ]);
				unset($_SESSION[ "residenceInst" ]);
				unset($_SESSION[ "nomBatimentInst" ]);
				unset($_SESSION[ "ville" ]);
				unset($_SESSION[ "type" ]);
				unset($_SESSION[ "nomRue" ]);
				unset($_SESSION[ "numeroRue" ]);
				unset($_SESSION[ "residence" ]);
				unset($_SESSION[ "cadastre" ]);

				$html .= "<tr><form action = 'nouveau.php' method = 'post' id = 'formInstRecherche'>
					<select name = \"villeInst\" id = \"villeInst\" 
							onchange = \"document.forms['formInstRecherche'].submit();\" >
						<option value = \"-1\" >---Choisissez une ville---</option >";

				$villes = RequeteNouveau::getInstance()->getTouteVilles();

				foreach($villes as $v){
					if(isset($_POST[ "villeInst" ]) && $v->getId() == $_POST[ "villeInst" ]){
						$html .= "<option selected value='".$v->getId()."'>".$v->getVille()."</option>";
					}
					else{
						$html .= "<option value='".$v->getId()."'>".$v->getVille()."</option>";
					}
				}
				$html .= "</select>";

				if(isset($_POST[ "villeInst" ]) && $_POST[ "villeInst" ] != - 1){
					$_SESSION[ 'villeInst' ] = $_POST[ "villeInst" ];
					$nomRue                  = RequeteNouveau::getInstance()->getNomRueInstancie($_POST[ "villeInst" ]);
					$html .= "<select name = \"nomRueInst\" id = \"nomRueInst\"
									 onchange = \"document.forms['formInstRecherche'].submit();\" >
							<option value = \"-1\" >---Choisissez un nom de rue---</option>";
					foreach($nomRue as $n){
						if(isset($_POST[ "nomRueInst" ]) && $n == $_POST[ "nomRueInst" ]){
							$html .= "<option selected value=\"".$n."\">$n</option>";
						}
						else{
							$html .= "<option value=\"".$n."\">$n</option>";
						}
					}

				}
				$html .= "</select>";

				if(isset($_POST[ "nomRueInst" ]) && $_POST[ "nomRueInst" ] != - 1){

					$_SESSION[ 'nomRueInst' ] = $_POST[ "nomRueInst" ];
					$nomRue                   = $_POST[ "nomRueInst" ];
					$types                    = RequeteNouveau::getInstance()->getTypeInstancie($_POST[ "villeInst" ], $nomRue);

					$html .= "<select name = \"typeInst\" id = \"typeInst\"
									onchange = \"document.forms['formInstRecherche'].submit();\" >
								<option value = \"-1\" >---Choisissez un type---</option >";

					foreach($types as $t){
						if(isset($_POST[ "typeInst" ]) && $t->getId() == $_POST[ "typeInst" ]){
							$html .= "<option selected value='".$t->getId()."'>".$t->getTypeRues()."</option>";
						}
						else{
							$html .= "<option value='".$t->getId()."'>".$t->getTypeRues()."</option>";
						}
					}
					$html .= "</select>";

					if(isset($_POST[ "typeInst" ]) && $_POST[ "typeInst" ] != - 1){
						$_SESSION[ 'typeInst' ] = $_POST[ "typeInst" ];
						$numeroRue              = RequeteNouveau::getInstance()->getNumeroRueInstancie($_POST[ "villeInst" ], $_POST[ "typeInst" ], $_POST[ "nomRueInst" ]);
						$html .= "<select name = \"numeroRueInst\" id = \"numeroRueInst\"
									  onchange = \"document.forms['formInstRecherche'].submit();\" >
								<option value = \"-1\" >---Choisissez un numero de rue---</option >";

						foreach($numeroRue as $nr){
							if(isset($_POST[ "numeroRueInst" ]) && $nr == $_POST[ "numeroRueInst" ]){
								$html .= "<option selected value='$nr'>$nr</option>";
							}
							else{
								$html .= "<option value='$nr'>$nr</option>";
							}
						}
						$html .= "</select>";

						if(isset($_POST[ "numeroRueInst" ]) && $_POST[ "numeroRueInst" ] != - 1){
							$_SESSION[ 'numeroRueInst' ] = $_POST[ "numeroRueInst" ];
							$residence                   = RequeteNouveau::getInstance()->getResidenceInstancie($_POST[ "villeInst" ], $_POST[ "typeInst" ], $_POST[ "nomRueInst" ], $_POST[ "numeroRueInst" ]);
							if(count($residence) > 0){
								$html .= "<select name = \"residenceInst\" id = \"residenceInst\"
											  onchange = \"document.forms['formInstRecherche'].submit();\" >
										<option value = \"-1\" >---Choisissez la residence---</option >";

								foreach($residence as $r){
									if(isset($_POST[ "residenceInst" ]) && $r == $_POST[ "residenceInst" ]){
										$html .= "<option selected value=\"$r\">$r</option>";
									}
									else{
										$html .= "<option value=\"$r\">$r</option>";
									}
								}
								$html .= "</select>";
							}
						}
						if(isset($_POST[ "residenceInst" ]) && $_POST[ "residenceInst" ] != - 1){
							$_SESSION[ 'residenceInst' ] = $_POST[ "residenceInst" ];
							$batiment                    = RequeteNouveau::getInstance()->getNomBatimentInstancie($_POST[ "villeInst" ], $_POST[ "typeInst" ], $_POST[ "nomRueInst" ], $_POST[ "numeroRueInst" ], $_POST[ "residenceInst" ]);
							if(count($residence) > 0){
								$html .= "<select name = \"nomBatimentInst\" id = \"nomBatimentInst\"
											  onchange = \"document.forms['formInstRecherche'].submit();\" >
										<option value = \"-1\" >---Choisissez le nom du batiment---</option >";

								foreach($batiment as $b){
									if(isset($_POST[ "nomBatimentInst" ]) && $b == $_POST[ "nomBatimentInst" ]){
										$html .= "<option selected value='$b'>$b</option>";
										$_SESSION[ 'nomBatimentInst' ] = $_POST[ "nomBatimentInst" ];
									}
									else{
										$html .= "<option value='$b'>$b</option>";
									}
								}
								$html .= "</select>";
							}
						}
					}
				}
				$html .= "</select></tr>";
				/*
				$html.= "<br/><input type='submit' name='saisieAdresseExiste' value='Valider Adresse'/></form><br/><br/>";
				*/
			}
			else{
				$html .= "<br/><tr><form action='nouveau.php' method='post'><input type = 'submit' class='btn btn-info' name = 'confAdresseExiste' value = 'Adresse Existante'/ ></form></tr><br/>";
			}

			if(isset($_POST[ 'confAdresseExistePas' ])){
				/*
				unset($_SESSION["villeInst"]);
				unset($_SESSION["typeInst"]);
				unset($_SESSION["nomRueInst"]);
				unset($_SESSION["numeroRueInst"]);
				unset($_SESSION["residenceInst"]);
				unset($_SESSION["nomBatimentInst"]);
				*/
				unset($_SESSION[ "ville" ]);
				unset($_SESSION[ "type" ]);
				unset($_SESSION[ "nomRue" ]);
				unset($_SESSION[ "numeroRue" ]);
				unset($_SESSION[ "residence" ]);
				unset($_SESSION[ "cadastre" ]);

				$html .= "<br/><br/><form action='' method='post'><tr><th>Ville</th>
						  <td><select name = 'ville' >
							<option value =''>-----SELECTIONNEZ UNE VILLE------</option>";
				$ville = RequeteNouveau::getInstance()->getTouteVilles();
				foreach($ville as $v){
					if(isset($_SESSION[ "villeInst" ]) && $_SESSION[ "villeInst" ] != - 1 && $_SESSION[ "villeInst" ] == $v->getId())
						$html .= "<option value='".$v->getId()."' selected>".$v->getVille()." </option>";
					else
						$html .= "<option value='".$v->getId()."'>".$v->getVille()." </option>";
				}

				$html .= "</select ></td></tr>";

				$html .= "</select ></td></tr>
						  <tr><th>Nom de la rue</th>";
				if(isset($_SESSION[ "nomRueInst" ]))
					$html .= "<td><input type = \"text\" class = \"input-large\" name = \"nomRue\" value=\"".$_SESSION[ "nomRueInst" ]."\"/>";
				else
					$html .= "<td><input type = 'text' class = 'input-large' name = 'nomRue' />";
				$html .= "  <span style='font-style:italic;font-size:12px;'>Notation : Nom(Prenom)</span></td></tr>";


				$html .= "<tr><th>Type de rue</th>
						  <td><select name = 'type' >
							  <option value =''>-----SELECTIONNEZ UN TYPE DE RUE------</option>";

				$type = RequeteNouveau::getInstance()->getToutTypeRue();
				foreach($type as $t){
					if(isset($_SESSION[ "typeInst" ]) && $_SESSION[ "typeInst" ] != - 1 && $_SESSION[ "typeInst" ] == $t->getId()){
						$html .= "<option value='".$t->getId()."' selected>".$t->getTypeRues()." </option>";
					}
					else
						$html .= "<option value='".$t->getId()."' >".$t->getTypeRues()." </option>";
				}


				$html .= "<tr><th>Numero de rue</th>";
				if(isset($_SESSION[ "numeroRueInst" ]))
					$html .= "<td><input type = 'number' class = 'input-large' name = 'numeroRue' value='".$_SESSION[ "numeroRueInst" ]."'/></td></tr>";
				else
					$html .= "<td><input type = 'number' class = 'input-large' name = 'numeroRue' /></td></tr>";


				$html .= "<tr><th>Nom de la residence</th>";
				if(isset($_SESSION[ "residenceInst" ]))
					$html .= "<td><input type = \"text\" class = \"input-large\" name = \"residence\" value=\"".$_SESSION[ "residenceInst" ]."\"/></td></tr>";
				else
					$html .= "<td><input type = 'text' class = 'input-large' name = 'residence'/></td></tr>";


				$html .= "<tr><th>Nom du Batiment</th>";
				if(isset($_SESSION[ "nomBatimentInst" ]))
					$html .= "<td><input type = \"text\" class = \"input-large\" name = \"nomBatiment\" value=\"".$_SESSION[ "nomBatimentInst" ]."\"/></td></tr>";
				else
					$html .= "<td><input type = 'text' class = 'input-large' name = 'nomBatiment' /></td></tr>";


				$html .= "<tr><th>Numéro Cadastral</th>";
				$html .= "<td>Pour ajouter un cadastre à une adresse,<br/> veuillez aller dans la modification d'adresse <br/> de l'affaire une fois ajoutée.</td></tr></table>";


				$html .= "<br/><input type='submit' name='saisieAdresseExistePas' class='btn btn-success' value='Valider Adresse'/></form><br/><br/>";
			}
			elseif(isset($_POST[ 'confAdresseExiste' ]) || isset($_POST[ 'villeInst' ]) || isset($_POST[ 'typeInst' ]) || isset($_POST[ 'nomRueInst' ]) || isset($_POST[ 'numeroRueInst' ]) || isset($_POST[ 'residenceInst' ])){

				$html .= '<br/><tr><form action="" method="post"><input type = "submit" name = "confAdresseExistePas" class="btn btn-info" value = "Saisie de l\'adresse"/ ></form></tr><br/>';
			}

		}
		echo $html;
		?>
	</body >
</html >