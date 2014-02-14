<?php

include 'includes/connexion.inc';
if(isset($_SESSION[ 'user' ]) || isset($_SESSION[ 'prenomNom' ])){
	session_destroy();
}
/**
 *
 * Affichage et gestion de l'authentification.
 *
*/
$erreur = "";

// Si on a cliquer sur connecter
if(isset($_POST[ 'boutonAuth' ])){
	if($_POST[ 'nomUser' ] == ""){
		$erreur .= "Veuillez saisir votre nom d'utilisateur.<br/>";
	}
	if($_POST[ 'mdpUser' ] == ""){
		$erreur .= "Veuillez saisir votre mot de passe d'utilisateur.<br/>";
	}
	// Si il n'y a pas d'erreur grossières, on tente de se connecter sur le ldap.
	if($erreur == ""){
		$server = "172.20.201.100";
		$port   = "389";
		$racine = "o=priv, c=agglo";
		$rootdn = $_POST[ 'nomUser' ]."@agglo.priv";
		$rootpw = $_POST[ 'mdpUser' ];

		$ds = ldap_connect($server) or die("Connexion impossible");
		if($ds){
			ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
			$r = @ldap_bind($ds, $rootdn, $rootpw);
			// Si l'utilisateur existe bien sur la base ldap, il passe.
			if($r){
				session_start();
				$found  = FALSE;
				$filtre = "userprincipalname=".$_POST[ 'nomUser' ]."@agglo.priv";

				$dnBH   = "ou=Bureau Hygiene, ou=CAFSR, dc=agglo, dc=priv";
				$srBH   = ldap_search($ds, $dnBH, $filtre);
				$infoBH = ldap_get_entries($ds, $srBH);
				if(ldap_count_entries($ds, $srBH) != 0){
					for($i = 0; $i < $infoBH [ "count" ]; $i ++){
						$_SESSION[ 'prenomNom' ] = $infoBH[ $i ][ "cn" ][ 0 ];
						$_SESSION[ 'user' ]      = utf8_decode($infoBH[ $i ][ "userprincipalname" ][ 0 ]);

						$prenomNom   = explode(" ", $_SESSION[ 'prenomNom' ]);
						$prenom      = $prenomNom[ 0 ];
						$nom         = strtoupper($prenomNom[ 1 ]);
						$sql         = "SELECT qualite FROM Agent WHERE nom='".utf8_decode($nom)."' AND prenom='".utf8_decode($prenom)."'";
						$localResult = sqlsrv_query($con, $sql);

						if($localResult == FALSE){
							echo "Error in query preparation/execution. Agent\n";
							die(print_r(sqlsrv_errors(), TRUE));
						}
						$qualite = "";
						while($row = sqlsrv_fetch_array($localResult, SQLSRV_FETCH_ASSOC)){
							$qualite = $row[ 'qualite' ];
						}
						$_SESSION[ 'privilege' ] = $qualite;
						$found                   = TRUE;
					}
				}


				$dnI   = "ou=Informatique, ou=CAFSR, dc=agglo, dc=priv";
				$srI   = ldap_search($ds, $dnI, $filtre);
				$infoI = ldap_get_entries($ds, $srI);
				if(ldap_count_entries($ds, $srI) != 0){
					for($i = 0; $i < $infoI [ "count" ]; $i ++){

						$_SESSION[ 'prenomNom' ] = utf8_decode($infoI[ 0 ][ "cn" ][ 0 ]);
						$_SESSION[ 'user' ]      = utf8_decode($infoI[ 0 ][ "userprincipalname" ][ 0 ]);

						$prenomNom   = explode(" ", $_SESSION[ 'prenomNom' ]);
						$prenom      = $prenomNom[ 0 ];
						$nom         = strtoupper($prenomNom[ 1 ]);
						$sql         = "SELECT qualite FROM Agent WHERE nom='".utf8_decode($nom)."' AND prenom='".utf8_decode($prenom)."'";
						$localResult = sqlsrv_query($con, $sql);


						if($localResult == FALSE){
							echo "Error in query preparation/execution. Agent\n";
							die(print_r(sqlsrv_errors(), TRUE));
						}
						$qualite = "";
						while($row = sqlsrv_fetch_array($localResult, SQLSRV_FETCH_ASSOC)){
							$qualite = $row[ 'qualite' ];
						}
						$_SESSION[ 'privilege' ] = $qualite;
						$found                   = TRUE;
					}
				}
				// Si l'utilisateur fait bien partit du service informatique ou d'hygiène, on le connecte.
				if($found){
					$prenomNom   = explode(" ", $_SESSION[ 'prenomNom' ]);
					$prenom      = $prenomNom[ 0 ];
					$nom         = strtoupper($prenomNom[ 1 ]);
					$sql         = "SELECT nom,prenom FROM Agent WHERE nom='".utf8_decode($nom)."' AND prenom='".utf8_decode($prenom)."'";
					$localResult = sqlsrv_query($con, $sql);

					if($localResult == FALSE){
						echo "Error in query preparation/execution. Agent\n";
						die(print_r(sqlsrv_errors(), TRUE));
					}
					$existe = FALSE;
					while($row = sqlsrv_fetch_array($localResult, SQLSRV_FETCH_ASSOC)){
						$existe = TRUE;
					}
					// Si l'utilisateur n'existe pas dans la base du site, on le rajoute.
					if(! $existe){
						$maxID         = "select max(id) from Agent";
						$localResultID = sqlsrv_query($con, $maxID);

						if($localResultID == FALSE){
							echo "Error in query preparation/execution. MAXID\n";
							die(print_r(sqlsrv_errors(), TRUE));
						}

						while($row = sqlsrv_fetch_array($localResultID, SQLSRV_FETCH_NUMERIC)){
							$nbr = intval($row[ 0 ]) + 1;
						}

						$user = explode('@', $_SESSION[ 'user' ]);
						$mail = $user[ 0 ]."@cavem.fr";

						$sql         = "INSERT INTO Agent(id,nom,prenom,qualite,mail) VALUES('$nbr','$nom','$prenom','user','$mail')";
						$localResult = sqlsrv_query($con, $sql);
						if($localResult == FALSE){
							echo "Error in query preparation/execution. Ajout\n";
							die(print_r(sqlsrv_errors(), TRUE));
						}
					}
					ldap_close($ds);
					header('location:activite.php');
				}
				else{
					$erreur .= "Vous n'appartenez pas au bon service pour pouvoir vous connecter.<br/>";
				}
			}
			else{
				$erreur .= "Le login et/ou le mot de passe sont incorrect.<br/>";
			}
			ldap_close($ds);
		}
		else{
			echo  "Impossible de se connecter au serveur LDAP";
		}
	}
}
$html = "<center><div class='corp'><div class='erreur'>".$erreur."</div><br/>";
$html .= "<div class='centrage'>";
$html .= "<form action='' method='post' name='auth'>";
$html .= "<div class='contenu'><table>";
$html .= "<tr><th class='nomUtil'>Nom d'utilisateur   </th><td class='saisieNomUtil'><input type='text' name='nomUser' id='input1' class='input-large' value=''/></td></tr>";
$html .= "<tr><th class='nomUtil'>Mot de passe  </th><td class='saisieNomUtil'><input type='password' name='mdpUser' class='input-large' value=''/></td></tr>";
$html .= "<tr><td></td><td><div class='bouton'><input type='submit' name='boutonAuth' class='btn btn-primary' value='Connecter'/></div></td></tr>";
$html .= "</table></div>";
$html .= "</form>";
$html .= "</div></div></center>";
?>

<script >

	window.onload = init;

	function init() {
		document.getElementById("input1").focus();
	}

</script >

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
		<link href = "css/connexion.css"
		      rel = "stylesheet"
		      media = "screen" >
	</head >
	<?php
	echo $html;
	?>
	<body >
	</body >
</html >