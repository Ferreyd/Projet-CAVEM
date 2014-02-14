<?php

include_once "bd/RequeteModification.php";
include_once "data/Document.php";
include_once "includes/connexion.inc";

?>


<html >

<?php

if(isset($_POST[ "doc" ]) && $_POST[ "doc" ] != - 1){

	$idDoc = $_POST[ "doc" ];

	$d = new Document();

	$d = RequeteModification::getInstance()->getDocument($idDoc);

	$nom    = $d->getNom();
	$doc    = $d->getDocument();
	$taille = $d->getTaille();
	$type   = $d->getType();

	$fd = fopen("./Documents/".$nom, 'w');
	$a  = fwrite($fd, $doc);
	fclose($fd);


	/**
	 * Header
	 */
	if($a != NULL){
		header("Content-Description: File Transfer");
		header("Content-Type: ".$type."");
		header("Content-Disposition: attachment; filename=".$nom."");
		header("Content-Transfer-Encoding: binary");
		header("Expires: 0");
		header("Cache-Control: must-revalidate");
		header("Pragma: public");
		header("Content-Length:".$taille."");
		ob_clean();
		flush();
		readfile("./Documents/".$nom);
		unlink("./Documents/".$nom);


		exit;

	}

}
else{
	$id           = $_GET[ "id" ];
	$intervention = $_GET[ "intervention" ];
	header("location:intervention.php?id=$id&intervention=$intervention");
}




?>




</html >