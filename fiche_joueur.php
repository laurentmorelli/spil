<!DOCTYPE html>
<html>
    <head>
    	<title>Fiche individuelle</title>
    	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
    	<link rel="stylesheet" type="text/css" href="template.css" media="all" />
    </head>
    <body>
        <?php include 'menu.html'; 
        	include('fonctions.php'); 
        	$bdd = connect_bdd();
        	$joueur=$_GET["pseudo"];
        	$objet_joueurs = get_joueurs();
        	$id_joueur = get_id_by_pseudo($objet_joueurs,$joueur);
        ?>
        
	<h1> 
    	<?php 
        	echo 'Fiche du joueur ' . $joueur . ' :';        
		?>
	</h1>

	<h2> Evolution de son elo : </h2>
  <table>
  <?php 
      include('html_helpers.php'); 
      $methode_id = 0;
      $sql="select elo,date from calcul where id_joueur = '$id_joueur' and id_methode = '$methode_id' order by id_match ASC;";
      $resultat = mysqli_query($bdd,$sql);
      while($data = mysqli_fetch_assoc($resultat))
      {
        echo '<tr><td>'.$data['date'].'</td>';
        echo '<td>'.$data['elo'].histogram_bar($data['elo']/3-333).'</td></tr>';
      }
      mysqli_free_result($resultat);
  ?>
  </table>
    
    <h2> Historique de ses combats : </h2>
            <table>
            <?php 
               $objet_joueurs = get_joueurs();
               $array_joueurs = array();
               while ($row = mysqli_fetch_array($objet_joueurs))
               {
                    $array_joueurs[$row['id']]=$row['pseudo'];
               }
               $objet_matchs = get_all_matchs();// les matchs de ouf ! ! !             
               while ($row = mysqli_fetch_array($objet_matchs))
               {
               		// teste si le joueur est dans le match
               		$joueur_dans_match=0;
               		for ($i = 1; $i <= 8; $i++)  
               		{ 
              			if ( $row[$i]==$id_joueur )
						{$joueur_dans_match=1;
						};
						};
					// si le joueur est dans le match, alors...
					if ($joueur_dans_match==1)
					{
					// affiche le resultat du match               
                    echo "<tr>" ;
                    echo "<td>".$row[0]."</td>";
                    echo "<td>".$array_joueurs[$row[1]];
                    if ($row[2]>2) { echo ",   ".$array_joueurs[$row[2]]; }
                    if ($row[3]>2) { echo ",   ".$array_joueurs[$row[3]]; }
                    if ($row[4]>2) { echo ",   ".$array_joueurs[$row[4]]; }
                    echo "</td>";                                                    
                    if ($row[9]>$row[10])
                      echo "<td><strong>".$row[9]."</strong>&nbsp-&nbsp;".$row[10]."</td>";
                    else
                      echo "<td>".$row[9]."&nbsp-&nbsp;<strong>".$row[10]."</strong></td>";
                    echo "<td>".$array_joueurs[$row[5]];                  
                    if ($row[6]>2) { echo ",   ".$array_joueurs[$row[6]]; }
                    if ($row[7]>2) { echo ",   ".$array_joueurs[$row[7]]; }
                    if ($row[8]>2) { echo ",   ".$array_joueurs[$row[8]]; }
                    echo "</td>";
                    echo "<td>".$row[11]."</td>";
                    echo "<td>".$row[12]."</td>";
                    //echo "<td>".$row[13]."</td>";
                    echo "</tr>" ;
                	
                	}
                }
            ?>
            </table>
            

        </table>
    </body>
</html>
