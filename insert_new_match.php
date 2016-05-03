<?php 

include('fonctions.php');

$bdd = connect_bdd();
$date_now =  date('Y-m-d H:i:s');

# check if the match already exist (same abs(score1-score) for the same day
if ( $_GET['team1_score'] == '0' || $_GET['team2_score'] == '0' || match_already_exists($_GET['team1_score'],$_GET['team2_score'],$date_now,2) ) 
{
	echo "Erreur : la match semble déjà exister (ou un des scores est 0).";
	exit;
};


$team1 = get_real_team($_GET['team1_player1'],$_GET['team1_player2'],$_GET['team1_player3'],$_GET['team1_player4']);
$team2 = get_real_team($_GET['team2_player1'],$_GET['team2_player2'],$_GET['team2_player3'],$_GET['team2_player4']);

$sql = "INSERT INTO `spil`.`matchs`
	(`id`,
	 `team1_player1`, `team1_player2`, `team1_player3`, `team1_player4`,
	 `team2_player1`, `team2_player2`, `team2_player3`, `team2_player4`,
	 `score_team1`, `score_team2`, `date`, `map`, `game_type`) 
	 VALUES
	 (NULL,
	 '".$team1[0]."',
	 '".$team1[1]."',
	 '".$team1[2]."',
	 '".$team1[3]."',
	 '".$team2[0]."',
	 '".$team2[1]."',
	 '".$team2[2]."',
	 '".$team2[3]."',
	 '".$_GET['team1_score']."',
	 '".$_GET['team2_score']."',
	 '$date_now', '".$_GET['map']."', '".$_GET['game_type']."');";
	 //echo $sql;
	 
if ( ! $resultat = mysqli_query($bdd,$sql) )
{
	echo "Erreur lors de l'insertion des donn&eacute;es dans la base.";
 } else {
    echo "L'insertion des donn&eacute;es dans la base est un succ&egrave;s.";
}

// on récupère les méthodes actives
// active => date_debut < date_now < date_fin
//$objet_methodes = get_active_methodes();

calcul_elo_last_match();


echo " Vous allez &ecirc;tre redirig&eacute; vers le classement.";
?>

<!DOCTYPE html>
<html>
    <head>
	    <title>Formulaire nouveau match</title>
	    <meta http-equiv="refresh" content="3;URL=index.php" />
	    <link rel="stylesheet" type="text/css" href="template.css" media="all" />
    </head>
    <body>
        <?php include 'header.html'; ?>
    </body>
</html>
