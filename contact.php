<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nicolas
 * Date: 05/07/13
 * Time: 10:59
 * To change this template use File | Settings | File Templates.
 */
include_once 'data/Contact.php';
include_once 'bd/RequeteAffaire.php';
include_once 'bd/RequeteModification.php';
include "bandeau.php";

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
 * Fonction d'affichage d'un contact en fonction de l'affaire courante.
 *
 * @param $idContact
 * @param $erreur
 * @param $droitUserModif
 */
function afficheContact($idContact, $erreur, $droitUserModif)
{


	$idAffaire = $_GET[ "id" ];
	$idContact = $_GET[ "int" ];

	$Contact = RequeteAffaire::getInstance()->getContactParId($idContact);

	$nom = ($Contact->getNom());

	$page = $erreur."<br/>";
	$page .= " * champs obligatoire.</br>";
	$page .= "<form action ='contact.php?id=$idAffaire&int=$idContact' method='post'>";
	$page .= "<table>";

	$page .= "<tr><th><label for='nomContact'><b>Nom ou personne morale *</b></label></th>";
	$page .= "<td><input type = 'text' class = 'input-large' name='nomContact' value='".htmlentities(utf8_decode($nom), ENT_QUOTES)."'/></td></tr>";

	$page .= "<tr><th><label for='prenomContact'>Prenom</label></th>";
	$page .= "<td><input type = 'text' class = 'input-large' name='prenomContact' value='".htmlentities(utf8_decode($Contact->getPrenom()), ENT_QUOTES)."'/></td></tr>";

	$page .= "<tr><th><label for='adresse'>Adresse</label></th>";
	$page .= "<td><input type = 'text' class = 'input-large' name='adresse' value='".htmlentities(utf8_decode($Contact->getAdresse()), ENT_QUOTES)."'/></td></tr>";

	$page .= "<tr><th><label for='ville'>Ville</label></th>";
	$page .= "<td><input type = 'text' class = 'input-large' name='ville' value='".htmlentities(utf8_decode($Contact->getVille()), ENT_QUOTES)."'/></td></tr>";

	$page .= "<tr><th><label for='codePostal'>Code Postal</label></th>";
	$page .= "<td><input type = 'number' class = 'input-large' name='codePostal' value='".utf8_decode($Contact->getCodePostal())."'/></td></tr>";

	$page .= "<tr><th><label for='qualiteeContact'><b>Qualité *</b></label></th>";
	$page .= "<td><input type = 'text' class = 'input-large' name='qualiteeContact' value='".htmlentities(utf8_decode($Contact->getQualite()), ENT_QUOTES)."'/></td></tr>";

	$page .= "<tr><th><label for='telephoneContact'>Telephone</label></th>";
	$page .= "<td><input type = 'text' class = 'input-large' name='telephoneContact' value='".htmlentities(utf8_decode($Contact->getTelephone()), ENT_QUOTES)."'/></td></tr>";

	$page .= "<tr><th><label for='mailContact'>Mail</label></th>";
	$page .= "<td><input type = 'text' class = 'input-large' name='mailContact' value='".htmlentities(utf8_decode($Contact->getMail()), ENT_QUOTES)."'/></td></tr>";

	$page .= "<tr><th><label for='faxContact'>Fax</label></th>";
	$page .= "<td><input type = 'text' class = 'input-large' name='faxContact' value='".htmlentities(utf8_decode($Contact->getFax()), ENT_QUOTES)."'/></td></tr>";

	$page .= "<tr><th><label for='syndicContact'>Complement</label></th>";
	$page .= "<td><input type = 'text' class = 'input-large' name='syndicContact' value='".htmlentities(utf8_decode($Contact->getSyndicProprioRegie()), ENT_QUOTES)."'/></td></tr>";

	$page .= "</form></table>";

	if($_SESSION[ 'privilege' ] == 'super-admin' || $_SESSION[ 'privilege' ] == 'admin' || $droitUserModif == TRUE){
		$page .= "<input type='submit' name='modifierContact'  value='Modifier le contact' class='btn btn-success'/>";
	}

	if($_SESSION[ 'privilege' ] == 'super-admin'){
		$page .= "<form action = 'modification.php?mod=14&id=$idAffaire&Contact=$idContact' method='post' name='supprimer' onsubmit='if(verifContact()) return true; else return false;'>";
		$page .= "<input name='supprimerContact' type='submit' value='Supprimer le contact' class='btn btn-danger'/>";
		$page .= "</form>";
	}

	echo $erreur;
	echo $page;

}

?>
<html >
<head >
<link href = "bootstrap/css/bootstrap.min.css"
      rel = "stylesheet"
      media = "screen" >
    <link href = "css/intervenant.css"
          rel = "stylesheet"
          media = "screen" >
	<?php bandeau(); ?>
</head >
<body >

<?php
$idContact = $_GET[ "int" ];
$idAffaire = $_GET[ "id" ];
$Contact = RequeteAffaire::getInstance()->getContactParId($idContact);
$erreur = "";



if(isset($_POST[ 'modifierContact' ])){
	if($_POST[ 'nomContact' ] != "" && $_POST[ 'qualiteeContact' ] != ""){
		if($_POST[ 'nomContact' ] != $Contact->getNom()){
			$nom         = $_POST[ 'nomContact' ];
			$sql         = "UPDATE Contact SET nom='$nom' WHERE id='$idContact'";
			$localResult = sqlsrv_query($con, $sql);
			$erreur .= "Le nom ou la personne morale du contact a bien été modifié.<br/>";
		}
		if($_POST[ 'prenomContact' ] != $Contact->getPrenom()){
			$prenom      = $_POST[ 'prenomContact' ];
			$sql         = "UPDATE Contact SET prenom='$prenom' WHERE id='$idContact'";
			$localResult = sqlsrv_query($con, $sql);
			$erreur .= "Le prénom du contact a bien été modifié.<br/>";
		}
		if($_POST[ 'adresse' ] != $Contact->getAdresse()){
			$adresse     = $_POST[ 'adresse' ];
			$sql         = "UPDATE Contact SET adresse='$adresse' WHERE id='$idContact'";
			$localResult = sqlsrv_query($con, $sql);
			$erreur .= "L'adresse du contact a bien été modifié.<br/>";
		}
		if($_POST[ 'ville' ] != $Contact->getVille()){
			$ville       = $_POST[ 'ville' ];
			$sql         = "UPDATE Contact SET ville='$ville' WHERE id='$idContact'";
			$localResult = sqlsrv_query($con, $sql);
			$erreur .= "La ville du contact a bien été modifié.<br/>";
		}
		if($_POST[ 'codePostal' ] != $Contact->getCodePostal()){
			$codePostal  = $_POST[ 'codePostal' ];
			$sql         = "UPDATE Contact SET codePostal='$codePostal' WHERE id='$idContact'";
			$localResult = sqlsrv_query($con, $sql);
			$erreur .= "Le nom du contact a bien été modifié.<br/>";
		}
		if($_POST[ 'qualiteeContact' ] != $Contact->getQualite()){
			$qualiteeContact = $_POST[ 'qualiteeContact' ];
			$sql             = "UPDATE Contact SET qualite='$qualiteeContact' WHERE id='$idContact'";
			$localResult     = sqlsrv_query($con, $sql);
			$erreur .= "La qualité du contact a bien été modifié.<br/>";
		}
		if($_POST[ 'telephoneContact' ] != $Contact->getTelephone()){
			$telephoneContact = $_POST[ 'telephoneContact' ];
			$sql              = "UPDATE Contact SET telephone='$telephoneContact' WHERE id='$idContact'";
			$localResult      = sqlsrv_query($con, $sql);
			$erreur .= "Le numéro de téléphone du contact a bien été modifié.<br/>";
		}
		if($_POST[ 'mailContact' ] != $Contact->getMail()){
			$mailContact = $_POST[ 'mailContact' ];
			$sql         = "UPDATE Contact SET mail='$mailContact' WHERE id='$idContact'";
			$localResult = sqlsrv_query($con, $sql);
			$erreur .= "Le mail du contact a bien été modifié.<br/>";
		}
		if($_POST[ 'faxContact' ] != $Contact->getFax()){
			$faxContact  = $_POST[ 'faxContact' ];
			$sql         = "UPDATE Contact SET fax='$faxContact' WHERE id='$idContact'";
			$localResult = sqlsrv_query($con, $sql);
			$erreur .= "Le fax du contact a bien été modifié.<br/>";
		}
		if($_POST[ 'syndicContact' ] != $Contact->getSyndicProprioRegie()){
			$syndicContact = $_POST[ 'syndicContact' ];
			$sql           = "UPDATE Contact SET syndicProprioRegie='$syndicContact' WHERE id='$idContact'";
			$localResult   = sqlsrv_query($con, $sql);
			$erreur .= "Le complément ou la Personne Morale du contact a bien été modifié.<br/>";
		}

	}
	else{
		$erreur .= "Veuillez saisir au moins le nom et la qualité du contact.<br/>";
	}
}

echo afficheContact($idContact, $erreur, $droitUserModif);
echo "<a href=affaire.php?id=$idAffaire class='btn btn-info'>Pour retourner à l'affaire</a>";

?>
</body >



</html >

