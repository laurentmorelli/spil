<!DOCTYPE html>
<html>
    <head>
    	<title>Historique</title>
    	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
    	<link rel="stylesheet" type="text/css" href="template.css" media="all" />
    </head>
    <body>
        <?php include 'menu.html'; ?>
            <h1> L'historique des combats : </h1>
            <table>
            <?php 
               include('fonctions.php');
               $objet_joueurs = get_joueurs();
               $array_joueurs = array();
               while ($row = mysqli_fetch_array($objet_joueurs))
               {
                    $array_joueurs[$row['id']]=$row['pseudo'];
               }
               $objet_matchs = get_all_matchs();// les matchs de ouf ! ! !             
               while ($row = mysqli_fetch_array($objet_matchs))
               {
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
            ?>
            </table>
    </body>
</html>
