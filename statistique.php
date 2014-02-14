<?php
include 'bandeau.php';
include_once 'bd/RequeteStatistique.php';
include_once 'bd/RequeteActivite.php';

session_start();
if(! isset($_SESSION[ 'user' ]) || ! isset($_SESSION[ 'prenomNom' ]) || ! isset($_SESSION[ 'privilege' ])){
	header('location:connexion.php');
}

/**
 *
 * Fonction d'affichage d'un tableau du nombre d'affaire par thème.
 *
 * @param $annee
 *
 * @return string
 */
function afficheNombreAffaireParTheme($annee)
{
	$theme = RequeteStatistique::getInstance()->getThemes();

	$html = "<table border=1 class='nombreAffaireParTheme'>";
	$html .= "<tr class='titre'>";
	$html .= "<th>Thèmes</th><th>Nombre d'affaires</th>";
	$html .= "</tr>";
	$i = 2;
	foreach($theme as $thm){
		if($i % 2 == 0){
			$html .= "<tr class='paire'>";
			$html .= "<td>".$thm."</td>";
			$html .= "<td>".RequeteStatistique::getInstance()->getNombreAffaireParTheme($thm, $annee)."</td>";
			$html .= "</tr>";
			$i ++;
		}
		else{
			$html .= "<tr class='impaire'>";
			$html .= "<td>".$thm."</td>";
			$html .= "<td>".RequeteStatistique::getInstance()->getNombreAffaireParTheme($thm, $annee)."</td>";
			$html .= "</tr>";
			$i ++;
		}
	}
	$html .= "</table>";

	return $html;
}

/**
 *
 * Fonction d'affichage d'un tableau du nombre d'affaire par agent par thème.
 *
 * @param $annee
 *
 * @return string
 */
function afficheNombreAffaireParAgentParTheme($annee)
{
	$agents = RequeteStatistique::getInstance()->getAgents();
	$theme  = RequeteStatistique::getInstance()->getThemes();

	$html = "<table border=1 class='nombreAffaireParAgentParTheme'>";
	$html .= "<tr class='titre'>";
	$html .= "<th>Agent</th>";
	foreach($theme as $thm){
		$html .= "<th>".$thm."</th>";
	}
	$html .= "<th>Nombre d'affaires de l'agent</th>";
	$html .= "</tr>";
	$i = 2;
	foreach($agents as $agts){
		if($i % 2 == 0){
			$html .= "<tr class='paire'>";
			$html .= "<td>".$agts."</td>";
			foreach($theme as $thm){
				$html .= "<td>".RequeteStatistique::getInstance()->getNombreAffaireParAgentParTheme($agts, $thm, $annee)."</td>";
			}
			$html .= "<td>".RequeteStatistique::getInstance()->getNombreAffaireParAgent($agts, $annee)."</td>";
			$html .= "</tr>";
			$i ++;
		}
		else{
			$html .= "<tr class='impaire'>";
			$html .= "<td>".$agts."</td>";
			foreach($theme as $thm){
				$html .= "<td>".RequeteStatistique::getInstance()->getNombreAffaireParAgentParTheme($agts, $thm, $annee)."</td>";
			}
			$html .= "<td>".RequeteStatistique::getInstance()->getNombreAffaireParAgent($agts, $annee)."</td>";
			$html .= "</tr>";
			$i ++;
		}
	}
	$html .= "</table>";

	return $html;
}

/**
 *
 * Fonction d'affichage d'un tableau du nombre de type de suite.
 *
 * @param $annee
 *
 * @return string
 */
function afficheNombreTypeSuite($annee)
{
	$typeSuite = RequeteStatistique::getInstance()->getNomTypeSuite();

	$html = "<table border=1 class='nombreTypeSuite'>";
	$html .= "<tr class='titre'>";
	$html .= "<th>Type de Suite</th>";
	$html .= "<th>Nombres de Suites</th>";
	$html .= "</tr>";
	$i = 2;
	foreach($typeSuite as $tps){
		if($i % 2 == 0){
			$html .= "<tr class='paire'>";
			$html .= "<td>".$tps."</td>";
			$html .= "<td>".RequeteStatistique::getInstance()->getNombreAffaireParType($tps, $annee)."</td>";
			$html .= "</tr>";
			$i ++;
		}
		else{
			$html .= "<tr class='impaire'>";
			$html .= "<td>".$tps."</td>";
			$html .= "<td>".RequeteStatistique::getInstance()->getNombreAffaireParType($tps, $annee)."</td>";
			$html .= "</tr>";
			$i ++;
		}
	}
	$html .= "</table>";

	return $html;
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
	<link href = "css/statistique.css"
	      rel = "stylesheet"
	      media = "screen" >
    <title >Base For SIHS</title >
	<?php
	bandeau();
	?>
</head >


<body >
<center >
<?php
	date_default_timezone_set('UTC');
	$annee = "";
	if(! isset($_POST[ 'annee' ]))
		$annee = date('Y');
	elseif(isset($_POST[ 'validerTouteAnnees' ])){
	}
	else
		$annee = $_POST[ 'annee' ];


	$html = "<div class='corp'><div class='formulaire'><form action='' method='POST'>";
	$html .= "<select name = 'annee'>";
	for($i = date('Y'); $i > 1989; $i --){
		if(isset($_POST[ 'annee' ]) && $i == $_POST[ 'annee' ])
			$html .= "<option value='$i' selected>$i</option>";
		else
			$html .= "<option value='$i'>$i</option>";
	}
	$html .= "</select><br/>";
	$html .= '<input type="submit" name="validerAnnee" value="Charger l\'année" /><hr/>';
	$html .= '<input type="submit" name="validerTouteAnnees" value="Charger toutes les l\'années" />';
	$html .= "</form></div><br/>";


	$html .= afficheNombreAffaireParTheme($annee);
	$html .= "<br/>";
	$html .= afficheNombreTypeSuite($annee);
	$html .= "<br/>";
	$html .= afficheNombreAffaireParAgentParTheme($annee);
	$html .= "</div>";


	echo $html;

	?>
</center >
</body >
</html >