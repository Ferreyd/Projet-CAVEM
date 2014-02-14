<?php

include_once'bd/RequeteAdministration.php';
include_once 'bd/RequeteRecherche.php';
include_once 'bd/RequeteNouveau.php';
include_once 'bd/RequeteSuite.php';
include_once 'includes/connexion.inc';
include_once 'bandeau.php';

session_start();
if(! isset($_SESSION[ 'user' ]) || ! isset($_SESSION[ 'prenomNom' ]) || $_SESSION[ 'privilege' ] != 'super-admin'){
	header('location:connexion.php');
}


//TODO Theme, type de rue, ville, GEstion des droits , type intervention, type document

/**
 *
 * Fonction d'affichage de la gestion d'une ville ( ajout / modification / Suppression ).
 *
 */
function gestionVille()
{
	$erreur = "";
	$page   = "<div class='gestionVille'>";
	$page .= "Gestion de la ville";

	//Si on ajoute une ville
	if(isset($_POST[ "ajouterVille" ])){
		$page .= "<form action='' method='post'>";
		$page .= "<table><tr><th>Nom de la ville </th>";
		$page .= "<th>Initiales de la Ville</th></tr><tr>";
		$page .= "<td><input class='input-large' type='text' class='input-large' name='ajoutNomVille'/></td>";
		$page .= "<td><input type='text' class='input-large' class='input-large' maxlength='3' name='ajoutInitialVille'/></td></tr></table>";
		$page .= "<input type='submit' class='btn btn-success' name='ajoutVille' value='Ajouter la ville'/>";
		$page .= "</form>";
	}
	// si ajoutVille est initilialisé et non vide
	else if(isset($_POST[ "ajoutVille" ])){
		if(! empty($_POST[ 'ajoutNomVille' ]) && ! empty($_POST[ 'ajoutInitialVille' ])){
			$foundVille        = TRUE;
			$foundInitialVille = TRUE;
			$ville             = RequeteRecherche::getInstance()->getTouteVilles();
			foreach($ville as $v){
				if($v->getVille() == RequeteNouveau::getInstance()->gereQuote(RequeteNouveau::getInstance()->stripAccents($_POST[ 'ajoutNomVille' ]))){
					$foundVille = FALSE;
				}
				elseif($v->getInitialVille() == RequeteNouveau::getInstance()->gereQuote(RequeteNouveau::getInstance()->stripAccents($_POST[ 'ajoutInitialVille' ]))){
					$foundInitialVille = FALSE;
				}
			}
			if(! $foundVille || ! $foundInitialVille){
				if(! $foundVille){
					$erreur .= "La ville que vous avez saisie existe déjà.";
				}
				if(! $foundInitialVille){
					$erreur .= "Les initiales de la ville que vous avez saisie existent déjà.";
				}
				$page .= "<form action='' method='post'>";
				$page .= "<table><tr><th>Nom de la ville </th>";
				$page .= "<th>Initiales de la Ville</th></tr><tr>";
				$page .= "<td><input class='input-large' type='text' class='input-large' name='ajoutNomVille'/></td>";
				$page .= "<td><input type='text' class='input-large' maxlength='3' name='ajoutInitialVille'/></td></tr></table>";
				$page .= "<input type='submit' class='btn btn-primary'  name='ajoutVille' value='Ajouter la ville'/>";
				$page .= "</form>";
			}
			else{
				$maxID = "select max(id) from Villes";

				$localResultID = sqlsrv_query(constant('CONNEXION'), $maxID);

				if($localResultID == FALSE){
					echo "Error in query preparation/execution. MAXID\n";
					die(print_r(sqlsrv_errors(), TRUE));
				}

				while($row = sqlsrv_fetch_array($localResultID, SQLSRV_FETCH_NUMERIC)){
					$nbr = intval($row[ 0 ]) + 1;

				}
				$id           = $nbr;
				$nomVille     = $_POST[ 'ajoutNomVille' ];
				$initialVille = $_POST[ 'ajoutInitialVille' ];
				$sql          = "INSERT INTO Villes (id, ville, initialeVille) VALUES ('$id', '".RequeteNouveau::getInstance()->stripAccents(RequeteNouveau::getInstance()->gereQuote($nomVille))."', '".RequeteNouveau::getInstance()->stripAccents($initialVille)."')";
				$localResult  = sqlsrv_query(constant('CONNEXION'), $sql);
				if($localResult == FALSE){
					echo "Error in query preparation/execution. Tout Type\n";
					die(print_r(sqlsrv_errors(), TRUE));
				}
				$erreur .= "La ville a bien été ajouté à la base.";
				$page .= "<form action='' method='post'><input type='submit' class='btn btn-primary' name='ajouterVille' value='Ajouter une Ville'/></form>";
			}
		}
		else{
			$erreur .= "Veuillez saisir le nom et les initiales de la ville.<br/>";
			$page .= "<form action='' method='post'>";
			$page .= "<table><tr><th>Nom de la ville </th>";
			$page .= "<th>Initiales de la Ville</th></tr><tr>";
			$page .= "<td><input type='text' class='input-large' name='ajoutNomVille'/></td>";
			$page .= "<td><input type='text' class='input-large' maxlength='3' name='ajoutInitialVille'/></td></tr></table>";
			$page .= "<input type='submit' class='btn btn-success' name='ajoutVille' value='Ajouter la ville'/>";
			$page .= "</form>";
		}
	}
	else{
		$page .= "<form action='' method='post'><input type='submit' class='btn btn-primary' name='ajouterVille' value='Ajouter une Ville'/></form>";
	}


	//Si on modifie une ville
	if(isset($_POST[ "modifierVille" ])){
		$page .= "<form action = 'administration.php' method = 'post'>
					<select name = \"villeAModif\">
					<option value = \"\" >---Choisissez une ville---</option >";
		$villes = RequeteRecherche::getInstance()->getTouteVilles();

		foreach($villes as $v){
			$page .= "<option value='".$v->getId()."'>".utf8_encode($v->getVille())."</option>";
		}
		$page .= "</select><br/>";
		$page .= "<input type='submit' class='btn btn-success' name='modifieVille' value='Modifier la Ville'/>";
		$page .= "</form>";
	}
	// si ajoutVille est initilialisé et non vide
	else if(isset($_POST[ "modifieVille" ])){
		if($_POST[ 'villeAModif' ] != ""){
			$ville         = "";
			$initialeVille = "";
			$sql           = "SELECT ville,initialeVille FROM Villes WHERE id='".$_POST[ 'villeAModif' ]."'";
			$localResult   = sqlsrv_query(constant('CONNEXION'), $sql);
			if($localResult == FALSE){
				echo "Error in query preparation/execution INFO VILLES.\n";
				die(print_r(sqlsrv_errors(), TRUE));
			}

			while($row = sqlsrv_fetch_array($localResult, SQLSRV_FETCH_ASSOC)){
				$ville         = $row[ "ville" ];
				$initialeVille = $row[ "initialeVille" ];
			}


			$page .= "<form action='administration.php?id=".$_POST[ 'villeAModif' ]."' method='post'>";
			$page .= "<table><tr><th>Nom de la ville </th>";
			$page .= "<th>Initiales de la Ville</th></tr><tr>";
			$page .= "<td><input type=\"text\" class=\"input-large\" name=\"modifNomVille\" value=\"$ville\" /></td>";
			$page .= "<td><input type='text' class='input-large' maxlength='3' name='modifInitialVille' value='".$initialeVille."' /></td></tr></table>";
			$page .= "<input type='submit' class='btn btn-success' name='confModifieVille' value='Modifier la ville'/>";
			$page .= "</form>";
		}
		else{
			$erreur .= "Veuillez insérer une ville à modifier.<br/>";
			$page .= "<form action = 'administration.php' method = 'post'>
					<select name = \"villeAModif\">
					<option value = \"\" >---Choisissez une ville---</option >";
			$villes = RequeteRecherche::getInstance()->getTouteVilles();

			foreach($villes as $v){
				$page .= "<option value='".$v->getId()."'>".utf8_encode($v->getVille())."</option>";
			}
			$page .= "</select><br/>";
			$page .= "<input type='submit' class='btn btn-success' name='modifieVille' value='Modifier la Ville'/>";
			$page .= "</form>";
		}
	}
	else if(isset($_POST[ "confModifieVille" ])){
		if($_POST[ 'modifNomVille' ] != "" && $_POST[ 'modifInitialVille' ] != ""){
			$foundVille        = TRUE;
			$foundInitialVille = TRUE;
			$ville             = RequeteRecherche::getInstance()->getTouteVilles();
			foreach($ville as $v){
				if($v->getVille() == RequeteNouveau::getInstance()->gereQuote(RequeteNouveau::getInstance()->stripAccents($_POST[ 'modifNomVille' ]))){
					$foundVille = FALSE;
				}
				elseif($v->getInitialVille() == RequeteNouveau::getInstance()->gereQuote(RequeteNouveau::getInstance()->stripAccents($_POST[ 'modifInitialVille' ]))){
					$foundInitialVille = FALSE;
				}
			}
			if(! $foundVille || ! $foundInitialVille){
				if(! $foundVille){
					$erreur .= "La ville que vous avez saisie existe déjà.";
				}
				if(! $foundInitialVille){
					$erreur .= "Les initiales de la ville que vous avez saisie existent déjà.";
				}
				$nomVille      = "";
				$initialeVille = "";
				$sql           = "SELECT ville,initialeVille FROM Villes WHERE id='".$_GET[ 'id' ]."'";
				$localResult   = sqlsrv_query(constant('CONNEXION'), $sql);
				if($localResult == FALSE){
					echo "Error in query preparation/execution INFO VILLES.\n";
					die(print_r(sqlsrv_errors(), TRUE));
				}

				while($row = sqlsrv_fetch_array($localResult, SQLSRV_FETCH_ASSOC)){
					$nomVille      = $row[ "ville" ];
					$initialeVille = $row[ "initialeVille" ];
				}
				$page .= "<form action='' method='post'>";
				$page .= "<table><tr><th>Nom de la ville </th>";
				$page .= "<th>Initiales de la Ville</th></tr><tr>";
				$page .= "<td><input type=\"text\" class=\"input-large\" name=\"modifNomVille\" value=\"$nomVille\" /></td>";
				$page .= "<td><input type='text' class='input-large' maxlength='3' name='modifInitialVille' value='".$initialeVille."' /></td></tr></table>";
				$page .= "<input type='submit' class='btn btn-success' name='confModifieVille' value='Modifier la ville'/>";
				$page .= "</form>";
			}
			else{
				$modifNomVille     = $_POST[ 'modifNomVille' ];
				$modifInitialVille = $_POST[ 'modifInitialVille' ];
				$sql               = "UPDATE Villes SET ville = '".RequeteNouveau::getInstance()->stripAccents(RequeteNouveau::getInstance()->gereQuote($modifNomVille))."', initialeVille = '".RequeteNouveau::getInstance()->stripAccents($modifInitialVille)."' WHERE id = '".$_GET[ 'id' ]."'";
				$localResult       = sqlsrv_query(constant('CONNEXION'), $sql);
				if($localResult == FALSE){
					echo "Error in query preparation/execution. Tout Type\n";
					die(print_r(sqlsrv_errors(), TRUE));
				}
				$erreur .= "La ville a bien été modifiée.";
				$page .= "<form action='' method='post'><input type='submit' class='btn btn-warning' name='modifierVille' value='Modifier une Ville'/></form>";
			}
		}
		else{
			$erreur .= "Veuillez insérer le nouveau nom de la ville ainsi que ses initiales.<br/>";
			$page .= "<form action='' method='post'><input type='submit' class='btn btn-success' name='modifierVille' value='Modifier une Ville'/></form>";
		}
	}
	else{
		$page .= "<form action='' method='post'><input type='submit' class='btn btn-warning' name='modifierVille' value='Modifier une Ville'/></form>";
	}


	//Si on supprime une ville
	if(isset($_POST[ "supprimerVille" ])){
		$villes = RequeteRecherche::getInstance()->getTouteVilles();
		$page .= "<form action = 'administration.php' method = 'post'>
				<select name = \"villeASuppr\">
				<option value = \"\" >---Choisissez une ville---</option >";
		foreach($villes as $v){
			$page .= "<option value='".$v->getId()."'>".utf8_encode($v->getVille())."</option>";
		}
		$page .= "</select><br/>";
		$page .= "<input type='submit' class='btn btn-danger' name='supprimeVille' value='Supprimer la Ville'/>";
		$page .= "</form>";
	}
	// si ajoutVille est initilialisé et non vide
	else if(isset($_POST[ "supprimeVille" ])){

		$sql         = "DELETE FROM Villes WHERE id='".$_POST[ 'villeASuppr' ]."'";
		$localResult = sqlsrv_query(constant('CONNEXION'), $sql);
		if($localResult == FALSE){
			echo "Error in query preparation/execution. Suppr Ville\n";
			die(print_r(sqlsrv_errors(), TRUE));
		}
		$erreur .= "La ville a bien été supprimé.<br/>";
		$page .= "<form action='' method='post'><input type='submit' class='btn btn-danger' name='supprimerVille' value='Supprimer une Ville'/></form>";
	}
	else{
		$page .= "<form action='' method='post'><input type='submit' class='btn btn-danger' name='supprimerVille' value='Supprimer une Ville'/></form>";
	}
	$page .= "</div>";
	echo $erreur;
	echo $page;
}

/**
 *
 * Fonction d'affichage de gestion des types de rues ( ajout / modification ).
 *
 *
 */
function gestionTypeRues()
{
	$erreur = "";
	$page   = "<div class='gestionType'>";
	$page .= "Gestion des types de rue";

	//Si on ajoute un type de rues
	if(isset($_POST[ "ajouterRue" ])){
		$page .= "<form action='' method='post'>";
		$page .= "<table><tr>";
		$page .= "<td><input type='text' class='input-large' name='ajoutTypeRue'/></td>";
		$page .= "</tr></table>";
		$page .= "<input type='submit' class='btn btn-success' name='ajoutRue' value='Ajouter un type de rue'/>";
		$page .= "</form>";
	}
	// si ajoutVille est initilialisé et non vide
	else if(isset($_POST[ "ajoutRue" ])){
		if(! empty($_POST[ 'ajoutTypeRue' ])){
			$foundRue = TRUE;
			$rue      = RequeteRecherche::getInstance()->getToutTypeRue();
			foreach($rue as $r){
				if(utf8_encode($r->getTypeRues()) == RequeteNouveau::getInstance()->gereQuote(RequeteNouveau::getInstance()->stripAccents($_POST[ 'ajoutTypeRue' ]))){
					$foundRue = FALSE;
				}
			}
			if(! $foundRue){
				if(! $foundRue){
					$erreur .= "Le type de rue que vous avez saisie existe déjà.";
				}
				$page .= "<form action='' method='post'>";
				$page .= "<table><tr>";
				$page .= "<td><input type='text' class='input-large' name='ajoutTypeRue'/></td>";
				$page .= "</tr></table>";
				$page .= "<input type='submit' class='btn btn-success' name='ajoutRue' value='Ajouter un type de rue'/>";
				$page .= "</form>";
			}
			else{
				$maxID = "select max(id) from typeRues";

				$localResultID = sqlsrv_query(constant('CONNEXION'), $maxID);

				if($localResultID == FALSE){
					echo "Error in query preparation/execution. MAXID\n";
					die(print_r(sqlsrv_errors(), TRUE));
				}

				while($row = sqlsrv_fetch_array($localResultID, SQLSRV_FETCH_NUMERIC)){
					$nbr = intval($row[ 0 ]) + 1;

				}
				$id           = $nbr;
				$ajoutTypeRue = $_POST[ 'ajoutTypeRue' ];
				$sql          = "INSERT INTO typeRues (id, typeRues) VALUES ('$id', '".RequeteNouveau::getInstance()->stripAccents(RequeteNouveau::getInstance()->gereQuote($ajoutTypeRue))."')";
				$localResult  = sqlsrv_query(constant('CONNEXION'), $sql);
				if($localResult == FALSE){
					echo "Error in query preparation/execution. Ajout T-rue\n";
					die(print_r(sqlsrv_errors(), TRUE));
				}
				$erreur .= "Le type de rue a bien été ajouté à la base.";
				$page .= "<form action='' method='post'><input type='submit' class='btn btn-primary' name='ajouterRue' value='Ajouter un type de rue'/></form>";
			}
		}
		else{
			$erreur .= "Veuillez saisir le nom et les initiales de la ville.<br/>";
			$page .= "<form action='' method='post'>";
			$page .= "<table><tr>";
			$page .= "<td><input type='text' class='input-large' name='ajoutTypeRue'/></td>";
			$page .= "</tr></table>";
			$page .= "<input type='submit' class='btn btn-success' name='ajoutRue' value='Ajouter un type de rue'/>";
			$page .= "</form>";
		}
	}
	else{
		$page .= "<form action='' method='post'><input type='submit' class='btn btn-primary' name='ajouterRue' value='Ajouter un type de rue'/></form>";
	}


	//Si on modifie un type de rues
	if(isset($_POST[ "modifierRue" ])){
		$page .= "<form action = 'administration.php' method = 'post'>
					<select name = \"rueAModif\">
					<option value = \"\" >---Choisissez un type de rue---</option >";
		$rue = RequeteRecherche::getInstance()->getToutTypeRue();

		foreach($rue as $r){
			$page .= "<option value='".$r->getId()."'>".utf8_encode($r->getTypeRues())."</option>";
		}
		$page .= "</select><br/>";
		$page .= "<input type='submit' class='btn btn-success' name='modifieRue' value='Modifier le type de rue'/>";
		$page .= "</form>";
	}
	// si ajoutVille est initilialisé et non vide
	else if(isset($_POST[ "modifieRue" ])){
		if($_POST[ 'rueAModif' ] != ""){
			$rue         = "";
			$sql         = "SELECT typeRues FROM TypeRues WHERE id='".$_POST[ 'rueAModif' ]."'";
			$localResult = sqlsrv_query(constant('CONNEXION'), $sql);
			if($localResult == FALSE){
				echo "Error in query preparation/execution INFO VILLES.\n";
				die(print_r(sqlsrv_errors(), TRUE));
			}

			while($row = sqlsrv_fetch_array($localResult, SQLSRV_FETCH_ASSOC)){
				$rue = $row[ "typeRues" ];
			}


			$page .= "<form action='administration.php?id=".$_POST[ 'rueAModif' ]."' method='post'>";
			$page .= "<table><tr><th>Type de rue </th>";
			$page .= "</tr><tr>";
			$page .= "<td><input type=\"text\" class=\"input-large\" name=\"modifTypeRue\" value=\"$rue\" /></td>";
			$page .= "</tr></table>";
			$page .= "<input type='submit' class='btn btn-success' name='confModifieTypeRue' value='Modifier le type de rue'/>";
			$page .= "</form>";
		}
		else{
			$erreur .= "Veuillez insérer un type de rue à modifier.<br/>";
			$page .= "<form action = 'administration.php' method = 'post'>
					<select name = \"rueAModif\">
					<option value = \"\" >---Choisissez un type de rue---</option >";
			$rue = RequeteRecherche::getInstance()->getToutTypeRue();

			foreach($rue as $r){
				$page .= "<option value='".$r->getId()."'>".utf8_encode($r->getTypeRues())."</option>";
			}
			$page .= "</select><br/>";
			$page .= "<input type='submit' class='btn btn-success' name='modifieRue' value='Modifier le type de rue'/>";
			$page .= "</form>";
		}
	}
	else if(isset($_POST[ "confModifieTypeRue" ])){
		if($_POST[ 'modifTypeRue' ] != ""){
			$foundRue = TRUE;
			$rue      = RequeteRecherche::getInstance()->getToutTypeRue();
			foreach($rue as $r){
				if(utf8_encode($r->getTypeRues()) == RequeteNouveau::getInstance()->gereQuote(RequeteNouveau::getInstance()->stripAccents($_POST[ 'modifTypeRue' ]))){
					$foundRue = FALSE;
				}
			}
			if(! $foundRue){
				if(! $foundRue){
					$erreur .= "Le type de rue que vous avez saisie existe déjà.";
				}
				$page .= "<form action='' method='post'>";
				$page .= "<table><tr>";
				$page .= "<td><input type='text' class='input-large' name='ajoutTypeRue'/></td>";
				$page .= "</tr></table>";
				$page .= "<input type='submit' class='btn btn-success' name='ajoutRue' value='Ajouter un type de rue'/>";
				$page .= "</form>";
			}
			else{
				$modifTypeRue = $_POST[ 'modifTypeRue' ];
				$sql          = "UPDATE TypeRues SET typeRues = '".RequeteNouveau::getInstance()->stripAccents(RequeteNouveau::getInstance()->gereQuote($modifTypeRue))."' WHERE id = '".$_GET[ 'id' ]."'";
				$localResult  = sqlsrv_query(constant('CONNEXION'), $sql);
				if($localResult == FALSE){
					echo "Error in query preparation/execution. Tout Type\n";
					die(print_r(sqlsrv_errors(), TRUE));
				}
				$erreur .= "Le type de rue a bien été modifiée.";
				$page .= "<form action='' method='post'><input type='submit' class='btn btn-warning' name='modifierRue' value='Modifier un type de rue'/></form>";
			}
		}
		else{
			$erreur .= "Veuillez insérer le nouveau type de rue.<br/>";
			$page .= "<form action='' method='post'><input type='submit class='btn btn-success'' name='modifierRue' value='Modifier un type de rue'/></form>";
		}
	}
	else{
		$page .= "<form action='' method='post'><input type='submit' class='btn btn-warning' name='modifierRue' value='Modifier un type de rue'/></form>";
	}
	$page .= "</div>";
	echo $erreur;
	echo $page;
}

/**
 *
 * Fonction d'affichage de gestion des types d'interventions ( ajout / modification )
 *
 *
 */
function gestionTypeIntervention()
{
	$erreur = "";
	$page   = "<div class='gestionInter'>";
	$page .= "Gestion des types d'interventions";

	//Si on ajoute un type d'intervention
	if(isset($_POST[ "ajouterInter" ])){
		$page .= "<form action='' method='post'>";
		$page .= "<table><tr>";
		$page .= "<td><input type='text' class='input-large' name='ajouteInter'/></td>";
		$page .= "</tr></table>";
		$page .= '<input type="submit" class="btn btn-success" name="ajoutInter"  value="Ajouter un type d\'intervention"/>';
		$page .= "</form>";
	}
	// si est initilialisé et non vide
	else if(isset($_POST[ "ajoutInter" ])){
		if(! empty($_POST[ 'ajouteInter' ])){
			$foundInter = TRUE;
			$inter      = RequeteRecherche::getInstance()->getToutTypeIntervention();
			foreach($inter as $i){
				if(utf8_encode($i->getType()) == RequeteNouveau::getInstance()->gereQuote(RequeteNouveau::getInstance()->stripAccents($_POST[ 'ajouteInter' ]))){
					$foundInter = FALSE;
				}
			}
			if(! $foundInter){
				if(! $foundInter){
					$erreur .= "Le type d'intervention que vous avez saisie existe déjà.";
				}
				$page .= "<form action='' method='post'>";
				$page .= "<table><tr>";
				$page .= "<td><input type='text' class='input-large' name='ajouteInter'/></td>";
				$page .= "</tr></table>";
				$page .= '<input type="submit" class="btn btn-success" name="ajoutInter"  value="Ajouter un type d\'intervention"/>';
				$page .= "</form>";
			}
			else{
				$maxID = "select max(id) from TypeIntervention";

				$localResultID = sqlsrv_query(constant('CONNEXION'), $maxID);

				if($localResultID == FALSE){
					echo "Error in query preparation/execution. MAXID\n";
					die(print_r(sqlsrv_errors(), TRUE));
				}

				while($row = sqlsrv_fetch_array($localResultID, SQLSRV_FETCH_NUMERIC)){
					$nbr = intval($row[ 0 ]) + 1;

				}
				$id          = $nbr;
				$ajouteInter = $_POST[ 'ajouteInter' ];
				$sql         = "INSERT INTO TypeIntervention (id, typeIntervention) VALUES ('$id', '".RequeteNouveau::getInstance()->gereQuote($ajouteInter)."')";
				$localResult = sqlsrv_query(constant('CONNEXION'), $sql);
				if($localResult == FALSE){
					echo "Error in query preparation/execution. Ajout T-rue\n";
					die(print_r(sqlsrv_errors(), TRUE));
				}
				$erreur .= "Le type d'intervention a bien été ajouté à la base.";
				$page .= '<form action="" method="post"><input type="submit"  name="ajouterInter" class="btn btn-primary" value="Ajouter un type d\'intervention"/></form>';
			}
		}
		else{
			$erreur .= "Veuillez saisir une intervention à ajouter.<br/>";
			$page .= "<form action='' method='post'>";
			$page .= "<table><tr>";
			$page .= "<td><input type='text' class='input-large' name='ajouteInter'/></td>";
			$page .= "</tr></table>";
			$page .= '<input type="submit" class="btn btn-success" name="ajoutInter"  value="Ajouter un type d\'intervention"/>';
			$page .= "</form>";
		}
	}
	else{
		$page .= '<form action="" method="post"><input type="submit" class="btn btn-primary" name="ajouterInter" value="Ajouter un type d\'intervention"/></form>';
	}


	//Si on modifie un type de rues
	if(isset($_POST[ "modifierInter" ])){
		$page .= "<form action = 'administration.php' method = 'post'>
					<select name = \"InterAModif\">
					<option value = \"\" >---Choisissez un type d'intervention---</option >";
		$Inter = RequeteRecherche::getInstance()->getToutTypeIntervention();

		foreach($Inter as $i){
			$page .= "<option value='".$i->getId()."'>".utf8_encode($i->getType())."</option>";
		}
		$page .= "</select><br/>";
		$page .= '<input type="submit" class="btn btn-success" name="modifieInter" value="Modifier le type d\'intervention"/>';
		$page .= "</form>";
	}
	// si ajoutVille est initilialisé et non vide
	else if(isset($_POST[ "modifieInter" ])){
		if($_POST[ 'InterAModif' ] != ""){
			$Inter       = "";
			$sql         = "SELECT typeIntervention FROM TypeIntervention WHERE id='".$_POST[ 'InterAModif' ]."'";
			$localResult = sqlsrv_query(constant('CONNEXION'), $sql);
			if($localResult == FALSE){
				echo "Error in query preparation/execution Modif Inter.\n";
				die(print_r(sqlsrv_errors(), TRUE));
			}

			while($row = sqlsrv_fetch_array($localResult, SQLSRV_FETCH_ASSOC)){
				$Inter = $row[ "typeIntervention" ];
			}


			$page .= "<form action='administration.php?id=".$_POST[ 'InterAModif' ]."' method='post'>";
			$page .= "<table><tr><th>Type d'intervention</th>";
			$page .= "</tr><tr>";
			$page .= "<td><input type=\"text\" class=\"input-large\" name=\"modifInter\" value=\"".RequeteNouveau::getInstance()->stripAccents($Inter)."\" /></td>";
			$page .= "</tr></table>";
			$page .= '<input type="submit" class="btn btn-success" name="confModifieInter" value="Modifier le type d\'intervention"/>';
			$page .= "</form>";
		}
		else{
			$erreur .= "Veuillez insérer un type d'intervention à modifier.<br/>";
			$page .= "<form action = 'administration.php' method = 'post'>
					<select name = \"InterAModif\">
					<option value = \"\" >---Choisissez un type d'intervention---</option >";
			$Inter = RequeteRecherche::getInstance()->getToutTypeIntervention();

			foreach($Inter as $i){
				$page .= "<option value='".$i->getId()."'>".utf8_encode($i->getType())."</option>";
			}
			$page .= "</select><br/>";
			$page .= '<input type="submit" class="btn btn-warning" name="ModifieInter" value="Modifier le type d\'intervention"/>';
			$page .= "</form>";
		}
	}
	else if(isset($_POST[ "confModifieInter" ])){
		if($_POST[ 'modifInter' ] != ""){
			$foundInter = TRUE;
			$inter      = RequeteRecherche::getInstance()->getToutTypeIntervention();
			foreach($inter as $i){
				if(utf8_encode($i->getType()) == RequeteNouveau::getInstance()->gereQuote(RequeteNouveau::getInstance()->stripAccents($_POST[ 'modifInter' ]))){
					$foundInter = FALSE;
				}
			}
			if(! $foundInter){
				if(! $foundInter){
					$erreur .= "Le type d'intervention que vous avez saisie existe déjà.";
				}
				$page .= "<form action='' method='post'>";
				$page .= "<table><tr>";
				$page .= "<td><input type='text' class='input-large' name='ajouteInter'/></td>";
				$page .= "</tr></table>";
				$page .= '<input type="submit" class="btn btn-success" name="ajoutInter"  value="Ajouter un type d\'intervention"/>';
				$page .= "</form>";
			}
			else{
				$modifInter  = $_POST[ 'modifInter' ];
				$sql         = "UPDATE TypeIntervention SET typeIntervention = '".RequeteNouveau::getInstance()->gereQuote(RequeteNouveau::getInstance()->stripAccents($modifInter))."' WHERE id = '".$_GET[ 'id' ]."'";
				$localResult = sqlsrv_query(constant('CONNEXION'), $sql);
				if($localResult == FALSE){
					echo "Error in query preparation/execution. Tout Type\n";
					die(print_r(sqlsrv_errors(), TRUE));
				}
				$erreur .= "Le type d'intervention a bien été modifiée.";
				$page .= '<form action="" method="post"><input type="submit" class="btn btn-warning" name="modifierInter" value="Modifier un type d\'intervention"/></form>';
			}
		}
		else{
			$erreur .= "Veuillez insérer le nouveau type d'intervention.<br/>";
			$page .= '<form action="" method="post"><input type="submit" class="btn btn-warning" name="modifierInter" value="Modifier un type d\'intervention"/></form>';
		}
	}
	else{
		$page .= '<form action="" method="post"><input type="submit" class="btn btn-warning" name="modifierInter" value="Modifier un type d\'intervention"/></form>';
	}
	$page .= "</div>";
	echo $erreur;
	echo $page;
}


/**
 *
 * Fonction d'affichage de gestion des types de suites
 *
 *
 */
function gestionTypeSuite()
{
	$erreur = "";
	$page   = "<div class='gestionSuite'>";
	$page .= "Gestion des types de suite";


	if(isset($_POST[ "ajouterSuite" ])){
		$page .= "<form action='' method='post'>";
		$page .= "<table><tr>";
		$page .= "<td><input type='text' class='input-large' name='ajouteSuite'/></td>";
		$page .= "</tr></table>";
		$page .= '<input type="submit" class="btn btn-success" name="ajoutSuite"  value="Ajouter un type de suite"/>';
		$page .= "</form>";
	}
	// si est initilialisé et non vide
	else if(isset($_POST[ "ajoutSuite" ])){
		if(! empty($_POST[ 'ajouteSuite' ])){
			$foundSuite = TRUE;
			$suite      = RequeteSuite::getInstance()->getToutTypeSuite();
			foreach($suite as $s){
				if(utf8_encode($s->getType()) == RequeteNouveau::getInstance()->gereQuote(RequeteNouveau::getInstance()->stripAccents($_POST[ 'ajouteSuite' ]))){
					$foundSuite = FALSE;
				}
			}
			if($foundSuite == FALSE){
				$erreur .= "Le type de suite que vous avez saisie existe déjà.";
				$page .= "<form action='' method='post'>";
				$page .= "<table><tr>";
				$page .= "<td><input type='text' class='input-large' name='ajouteSuite'/></td>";
				$page .= "</tr></table>";
				$page .= '<input type="submit" class="btn btn-success" name="ajoutSuite"  value="Ajouter un type de suite"/>';
				$page .= "</form>";
			}
			else{
				RequeteAdministration::getInstance()->ajouteSuite($_POST[ 'ajouteSuite' ]);
				$erreur .= "Le type de suite a bien été ajouté à la base.";
				$page .= '<form action="" method="post"><input type="submit" name="ajouterSuite" class="btn btn-primary" value="Ajouter un type de suite"/></form>';
			}
		}
		else{
			$erreur .= "Veuillez saisir une suite à ajouter.<br/>";
			$page .= "<form action='' method='post'>";
			$page .= "<table><tr>";
			$page .= "<td><input type='text' class='input-large' name='ajouteSuite'/></td>";
			$page .= "</tr></table>";
			$page .= '<input type="submit" class="btn btn-success" name="ajoutSuite"  value="Ajouter un type de suite"/>';
			$page .= "</form>";
		}
	}
	else{
		$page .= '<form action="" method="post"><input type="submit" class="btn btn-primary" name="ajouterSuite" value="Ajouter un type de suite"/></form>';
	}


	//Si on modifie un type de rues
	if(isset($_POST[ "modifierSuite" ])){
		$page .= "<form action = 'administration.php' method = 'post'>
					<select name = \"SuiteAModif\">
					<option value = \"\" >---Choisissez un type de suite---</option >";
		$Suite = RequeteSuite::getInstance()->getToutTypeSuite();

		foreach($Suite as $s){
			$page .= "<option value='".$s->getId()."'>".utf8_encode($s->getType())."</option>";
		}
		$page .= "</select><br/>";
		$page .= '<input type="submit" class="btn btn-success" name="modifieSuite" value="Modifier le type de suite"/>';
		$page .= "</form>";
	}
	// si ajoutVille est initilialisé et non vide
	else if(isset($_POST[ "modifieSuite" ])){
		if($_POST[ 'SuiteAModif' ] != ""){
			$Suite       = "";
			$sql         = "SELECT typeSuite FROM TypeSuite WHERE id='".$_POST[ 'SuiteAModif' ]."'";
			$localResult = sqlsrv_query(constant('CONNEXION'), $sql);
			if($localResult == FALSE){
				echo "Error in query preparation/execution Modif Suite.\n";
				die(print_r(sqlsrv_errors(), TRUE));
			}

			while($row = sqlsrv_fetch_array($localResult, SQLSRV_FETCH_ASSOC)){
				$Suite = $row[ "typeSuite" ];
			}


			$page .= "<form action='administration.php?id=".$_POST[ 'SuiteAModif' ]."' method='post'>";
			$page .= "<table><tr>";
			$page .= "<td><input type=\"text\" class=\"input-large\" name=\"modifSuite\" value=\"".RequeteNouveau::getInstance()->stripAccents($Suite)."\" /></td>";
			$page .= "</tr></table>";
			$page .= '<input type="submit" class="btn btn-success" name="confModifieSuite" value="Modifier le type de suite"/>';
			$page .= "</form>";
		}
		else{
			$erreur .= "Veuillez insérer un type de suite à modifier.<br/>";
			$page .= "<form action = 'administration.php' method = 'post'>
					<select name = \"SuiteAModif\">
					<option value = \"\" >---Choisissez un type de suite---</option >";
			$Suite = RequeteRecherche::getInstance()->getToutTypeSuite();

			foreach($Suite as $s){
				$page .= "<option value='".$s->getId()."'>".utf8_encode($s->getType())."</option>";
			}
			$page .= "</select><br/>";
			$page .= '<input type="submit" class="btn btn-warning" name="modifieSuite" value="Modifier le type de suite"/>';
			$page .= "</form>";
		}
	}
	else if(isset($_POST[ "confModifieSuite" ])){
		if($_POST[ 'modifSuite' ] != ""){
			$foundSuite = TRUE;
			$suite      = RequeteSuite::getInstance()->getToutTypeSuite();
			foreach($suite as $s){
				if(utf8_encode($s->getType()) == RequeteNouveau::getInstance()->gereQuote(RequeteNouveau::getInstance()->stripAccents($_POST[ 'modifSuite' ]))){
					$foundSuite = FALSE;
				}
			}
			if($foundSuite == FALSE){
				$erreur .= "Le type de suite que vous avez saisie existe déjà.";
				$page .= "<form action = 'administration.php' method = 'post'>
						<select name = \"SuiteAModif\">
						<option value = \"\" >---Choisissez un type de suite---</option >";
				$Suite = RequeteSuite::getInstance()->getToutTypeSuite();

				foreach($Suite as $s){
					$page .= "<option value='".$s->getId()."'>".utf8_encode($s->getType())."</option>";
				}
				$page .= "</select><br/>";
				$page .= '<input type="submit" class="btn btn-success" name="modifieSuite" value="Modifier le type de suite"/>';
				$page .= "</form>";
			}
			else{
				$id         = $_GET[ 'id' ];
				$modifSuite = $_POST[ 'modifSuite' ];
				RequeteAdministration::getInstance()->modifieSuite($id, $modifSuite);
				$erreur .= "Le type de suite a bien été modifiée.";
				$page .= '<form action="" method="post"><input type="submit" class="btn btn-warning" name="modifierSuite" value="Modifier un type de suite"/></form>';
			}
		}
		else{
			$erreur .= "Veuillez insérer le nouveau type de suite.<br/>";
			$page .= '<form action="" method="post"><input type="submit" class="btn btn-warning" name="modifierSuite" value="Modifier un type de suite"/></form>';
		}
	}
	else{
		$page .= '<form action="" method="post"><input type="submit" class="btn btn-warning" name="modifierSuite" value="Modifier un type de suite"/></form>';
	}
	$page .= "</div>";
	echo $erreur;
	echo $page;
}
?>


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
	<link href = "css/administration.css"
	      rel = "stylesheet"
	      media = "screen" >
    <title >Base For SIHS</title >
	<?php
	bandeau();
	?>
</head >
<body ><center ><div class = 'corp' >
	<?php
		gestionVille();
		gestionTypeRues();
		gestionTypeIntervention();
		gestionTypeSuite();
		?>
</div ></center ></body >

</html >