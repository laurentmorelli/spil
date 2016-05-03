<!DOCTYPE html>
<html>
    <head>
	    <title>Compute elos</title>
	    <link rel="stylesheet" type="text/css" href="template.css" media="all" />
    </head>
    <body>
        <?php include 'menu.html'; ?>
        <h2>You've been warned</h2>
           <p> Cette page invoque des puissances encore mal comprises. 
            En appuyant sur le bouton "GO!", tu risques de faire revenir Lucifer sur terre et/ou une damnation éternelle.
            Au minimum, tu effaceras ou ajouteras un tas de scores dans la bdd, qui n'a rien demandé.
       	</p>
       	<br><br>
       	
       	<p>Je veux recalculer l'historique des scores depuis l'origine du SPIL, suivant la méthode suivante.
        <form method='GET' action='compute_elos.php'>
                <select name="methode" >
                    <option value="0">Compte tous les matchs</option>
                    <option value="1">Compte les 20 derniers matchs</option>
                    <option value="2">Poids décroissant en fonction du temps</option>
                    <option value="3">Poids aléatoire (wait... what?)</option>    
                </select>
            <input type=submit value=GO! />
        </form>
        </p> 
        
        <p>Je préfère supprimer des scores de la bdd.
        <form method='GET' action='compute_elos.php'>
                <select name="methode" >
                    <option value="reset">Remettre à zéro la bdd</option>      
                </select>
            <input type=submit value=GO! />
        </form>
        </p> 
        
                        
                
  <?php 
  	include('fonctions.php');

	$bdd = connect_bdd();
	$sql = "select * from matchs;";
	$objet_matchs = mysqli_query($bdd,$sql);
	$N = get_nb_matchs($objet_matchs);
	

		if(is_numeric($_GET['methode']))
		{ 
		// i=nmbre de matchs comptabilises
		for($n=1;$n<=$N;$n++){
			// on calcule le poids sur i match et on complete par 0
			$weight_matchs = create_weight ($_GET['methode'],$n);
			for($j=$n;$j<$N;$j++)
			{$weight_matchs[$j][0]=0;};
    		// on calcule le elo
    		$elos = compute_elo_by_methode($weight_matchs);
    		// on insère les résultats dans la base
    		put_elo_in_db ($elos,$_GET['methode']);
			}
		echo "I did it, master";
		}	
		elseif( $_GET['methode'] == 'reset')
		{
			$sql = "TRUNCATE TABLE `calcul`;";
			mysql_query($sql) or die("Something went wrong: ".mysql_error().". TRUNCATE requires ALTER permission on the table.");
		}
   ?>

    </body>
</html>
