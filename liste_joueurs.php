<!DOCTYPE html>
<html>
    <head>
      <title>Liste des joueurs</title>
      <meta http-equiv="content-type" content="text/html; charset=utf-8" />
      <link rel="stylesheet" type="text/css" href="template.css" media="all" />
    </head>
    <body>
        <?php include 'menu.html'; ?>
        <h1> Liste des joueurs : </h1>
        <table border="1">
        <?php 
            include('fonctions.php');
            $joueurs = get_joueurs();
            while ($row = mysqli_fetch_array($joueurs))
            {
                echo "<tr>" ;
                echo "<td>".$row[0]."</td>";
                echo "<td>".$row[1]."</td>";
                //echo "<td>".$row[2]."</td>";
                echo "<td>".$row[3]."</td>";
                echo "<td>".$row[5]."</td>";
                echo "<td><a href='fiche_joueur.php?pseudo=".$row[3]."'>fiche</a></td>";
                echo "<tr>" ;
            }
        ?>
        </table>
 <!-- 
        <h2>Nouveau Joueur</h2>
        <form method='GET' action='insert_new_joueur.php'>
           <table border="0" align="center">
              <tr><th>Prenom</th><th>Pseudo</th></tr>
              <tr>
                <td><input name="prenom" type='text' value=''/></td>
                <td><input name="pseudo" type='text' value=''/></td>
              </tr>
           </table>
          <input type=submit />
        </form>-->
  
    </body>
</html>
