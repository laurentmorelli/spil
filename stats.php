<!DOCTYPE html>
<html>
    <head>
        <title>ELO SPIL : Stats</title>
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
            <h1>La méthode : </h1>
            <form method='GET' action='stats.php'>
                <select name="methode" >
                    <option value="0">Compte tous les matchs</option>
                    <option value="1">Compte les 20 derniers matchs</option>
                    <option value="2">Poids décroissant en fonction du temps</option>
                    <option value="3">Poids aléatoire (wait... what?)</option>    
                </select>
            <input type=submit value=GO! />
        	</form>
        
        <h1>Le Classement : </h1>
            <table id="classement">
                <thead> 
                    <tr> 
                        <th class='header'>Joueur</th> 
                        <th class='header'>ELO n matchs</th> 
                        <th class='header'>ELO n-1 matchs</th> 
                        <th class='header'>etc. </th> 
                    </tr> 
                </thead>
                <tbody>
                    <?php 
                        include('fonctions.php');
                        include('html_helpers.php');
                        $bdd = connect_bdd();
                        //$methode_name = "season-2013";
						$methode_id = $_GET['methode'];
                        $elos = get_elos($methode_id);
                        for($i=0;$i<get_nb_rows($elos);$i++){
                            // Output a row
                            echo "<tr>";
                            echo "<td>".get_joueur_pseudo_from_id($i)."</td>"; //pseudo
						for($j=0;$j<get_nb_cols($elos);$j++){				
                            echo "<td>".round($elos[$i][$j],4)."</td>";
						}
                            echo "</tr>";
                            
                        }
                    ?>
                </tbody>
            </table>
            
            
    </body>
</html>
