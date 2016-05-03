<?php 

    include('fonctions.php');
    $bdd = connect_bdd();
    $date_now =  date('Y-m-d H:i:s');
    $nb_joueurs = get_nb_joueurs(get_joueurs()); // nb de joueurs doit etre egal -> id

	$sql = "INSERT INTO `spil`.`joueurs`
	(`id`, `prenom`, `nom`, `pseudo`, `bot`, `comment`) 
	 VALUES
	 ('$nb_joueurs',
	 '".$_GET['prenom']."',
	 '',
	 '".$_GET['pseudo']."',
	 '0','Via le site, le $date_now');";
	 //echo $sql;
	 if ( ! $resultat = mysqli_query($bdd,$sql) )
	 {
		echo "Erreur lors de l'insertion des donn&eacute;es dans la base.";
	 }
	 else
	 {
	 echo "L'insertion des donn&eacute;es dans la base est un succ&egrave;s.";
	 };

echo " Vous allez &ecirc;tre redirig&eacute; vers le formulaire.";

?>

<!DOCTYPE html>
<html>
    <head>
	    <title>Formulaire nouveau joueur</title>
	    <meta http-equiv="refresh" content="3;URL=liste_joueurs.php" />
	    <link rel="stylesheet" type="text/css" href="template.css" media="all" />
    </head>
    <body>
        <?php include 'header.html'; ?>
    </body>
</html>
