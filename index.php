<!DOCTYPE html>
<html>
    <head>
        <title>ELO SPIL : Le Classement</title>
        <meta http-equiv="content-type" content="text/html; charset=utf-8" />
        <link rel="stylesheet" type="text/css" href="template.css" media="all" />
        <script type="text/javascript" src="javascript/jquery-1.10.2.min.js"></script> 
        <script type="text/javascript" src="javascript/jquery.tablesorter.min.js"></script> 
    </head>
    <body>
        <?php include 'menu.html'; ?>
        <script>
            $(document).ready(function(){$("#classement").tablesorter( { sortList: [[1,1]] });}); 
        </script>
            <h1>Le Classement : </h1>
            <table id="classement">
                <thead> 
                    <tr> 
                        <th class='header'>Joueur</th> 
                        <th class='header'>ELO</th> 
                        <th class='header'>Progression</th> 
                    </tr> 
                </thead>
                <tbody>
                    <?php 
                        include('fonctions.php');
                        include('html_helpers.php');
                        $bdd = connect_bdd();
                        //$methode_name = "season-2013";
						$methode_id = 2;
						if (isset($_GET['methode']))
						{
							$methode_id = $_GET['methode'];
						}
                        $elos = get_elos($methode_id);
                        for($i=0;$i<get_nb_rows($elos);$i++){
                            // Output a row
                            echo "<tr>";
                            echo "<td>".get_joueur_pseudo_from_id($i)."</td>"; //pseudo

                            echo "<td>".round($elos[$i][0],4).histogram_bar($elos[$i][0]/6)."</td>";

                            $progression = round($elos[$i][0] - $elos[$i][1],4); // elo n - elo n-1
                            echo "<td class='progression'>".$progression.histogram_bar($progression, 40)."</td>" ;
                            echo "</tr>";
                        }
                    ?>
                </tbody>
            </table>
			<!--<img src="pchart2.1.3/examples/L4DGraph?Seed=0.345456006238237" > -->
			<!--<img src="images/L4DGraph?Seed=0.345456006238237" > -->
			
<div style="display: none">

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
					echo "<span id='l4dMatchContent".$row[0]."'>" ;
					echo $row[11]." : ".$row[12];
					echo "<br/>";
					echo $array_joueurs[$row[1]];
                    if ($row[2]>2) { echo ",   ".$array_joueurs[$row[2]]; }
                    if ($row[3]>2) { echo ",   ".$array_joueurs[$row[3]]; }
                    if ($row[4]>2) { echo ",   ".$array_joueurs[$row[4]]; }
					echo " : <strong>".$row[9]."</strong>";
					echo "<br/>";
					echo $array_joueurs[$row[5]];                  
                    if ($row[6]>2) { echo ",   ".$array_joueurs[$row[6]]; }
                    if ($row[7]>2) { echo ",   ".$array_joueurs[$row[7]]; }
                    if ($row[8]>2) { echo ",   ".$array_joueurs[$row[8]]; }
					echo " : <strong>".$row[10]."</strong>";
					echo "<br/>";
					echo "<br/>";
					echo "</span>" ;
                }    
?>
</div>
			 <script src="javascript/highcharts.js"></script>
<script src="javascript/exporting.js"></script>

<div id="container" style="min-width: 310px; height: 800px; margin: 0 auto"></div>

<h2>La m&eacute;thode : </h2>
            <form method='GET' action='index.php'>
                <select name="methode" onchange="this.options[this.selectedIndex].value && (window.location = window.location.pathname + '?methode=' + this.options[this.selectedIndex].value);">
                    <option <?php if(isset($_GET['methode']) and $_GET['methode'] == '0'){echo("selected");}?> value="0">Compte tous les matchs</option>
                    <option <?php if(isset($_GET['methode']) and $_GET['methode'] == '1'){echo("selected");}?> value="1">Compte les 20 derniers matchs</option>
                    <option <?php if(isset($_GET['methode']) == false or $_GET['methode'] == '2'){echo("selected");}?> value="2">Poids d&eacute;croissant en fonction du temps</option>
                    <option <?php if(isset($_GET['methode']) and $_GET['methode'] == '3'){echo("selected");}?> value="3">Poids al&eacute;atoire (wait... what?)</option>    
                </select>
        	</form>
<script>

$(function () {
        $('#container').highcharts({
            title: {
                text: 'Lestate le vampire !',
                x: -20 //center
            },
            subtitle: {
                text: 'Official SPIL L4D elo',
                x: -20
            },
            xAxis: {
                categories: [<?php   
 /* left4Dead style !!!!! */
	$idMethod = 2;
	if (isset($_GET['methode']))
	{
		$idMethod = $_GET['methode'];
	}
    $matchs = mysqli_query($bdd,"select distinct id_match from calcul where id_match is not null and id_methode = $idMethod order by id_match;"); 
    $isfirst = true;
    while($data = mysqli_fetch_assoc($matchs))
    {
		if ($isfirst)
		{
		echo "'".$data['id_match']."'";
		$isfirst = false;
		}
		else
		{
		echo ", '".$data['id_match']."'";
		}
    }
    mysqli_free_result($matchs);?>]
	
            },
            yAxis: {
                title: {
                    text: 'Elo (point duche)'
                },
                plotLines: [{
                    value: 0,
                    width: 1,
                    color: '#808080'
                }]
            },
            tooltip: {
                valueSuffix: ' '
            },
            legend: {
                layout: 'vertical',
                align: 'right',
                verticalAlign: 'middle',
                borderWidth: 0
            },
            series: [<?php
 
 $joueurs = mysqli_query($bdd,"select distinct pseudo,calcul.id_joueur id_joueur  from calcul, joueurs where id_match is not null and id_methode = $idMethod and calcul.id_joueur = joueurs.id order by id_match;"); 
 $isfirst = true;
 while($data = mysqli_fetch_assoc($joueurs))
    {
		if ($isfirst)
		{
		echo "{";
		$isfirst = false;
		}
		else
		{
		echo ", {";
		}
		echo "
		";
		echo"name: '".$data['pseudo']."',";
		echo "
		";
		echo "data: [";
		echo "
		";
		//recup des elos
		$scores = mysqli_query($bdd,"select elo from calcul where id_match is not null and id_methode = $idMethod and id_joueur = '".$data['id_joueur']."' order by id_match;"); 
		 $isfirst2 = true;
		 while($data2 = mysqli_fetch_assoc($scores))
		{
			if ($isfirst2)
			{
			echo $data2['elo'];
			$isfirst2 = false;
			}
			else
			{
			echo ", ".$data2['elo'];;
			}
		
		}
		echo "
		";
		echo "] }";
		
		/*
		 $scores = mysqli_query($bdd,"select elo, pseudo from calcul, joueurs where id_match is not null and id_methode = 0 and calcul.id_joueur = joueurs.id order by id_match;"); 
 
 while($data = mysqli_fetch_assoc($scores))
    {
		$MyData->addPoints($data['elo'],$data['pseudo']);
    } 
    mysqli_free_result($scores);*/
		
    } 
    mysqli_free_result($joueurs);
			
			?>]
			
			
        });
    });
    
</script>
			
			
    </body>
</html>
