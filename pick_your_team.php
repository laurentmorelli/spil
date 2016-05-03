<!DOCTYPE html>
<html>
    <head>
    	<title>Team Spliter</title>
    	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
    	<link rel="stylesheet" type="text/css" href="template.css" media="all" />
      <script type="text/javascript" src="javascript/jquery-1.10.2.min.js"></script> 
      <script>


    $(document).ready( function() { 
        $("#joueurs input[type=checkbox]").change( split_players );
        split_players();
    }); 

    
    /*
             notations :

             Soit N le nombre de joueurs humains.
             Soit X et Y le nombre de bots dans les équipes 1 et 2.
             Remarque : Y = 8-N-X

             Soit E la somme des elos des 8 joueurs, dont (X+Y) bots.
             Soit e1 l'elo de l'équipe 1 : X botX, plus (4-X) joueurs humains.

             On cherche donc à minimiser la distance entre e1 et l'équilibre parfait : E/2 
            



             * algo1 : on équilibre d'abord le nombre d'humains par équipe (peut etre pas le meilleur choix) 

             avantage : on retrouve facilement quels botX et botY vont jouer (en choisissant X >= Y) :
             X = 4-round(N/2)


             X étant fixe, les possibiités se limitent à piocher 4-X humains parmis N
             et la somme e1 des elo doit etre au plus proche de la constante E/2  

             On peut essayer toutes les possibilités... 
             methode bourrin sans eliminer les doublons pour 8 humains : 2^8 = 256 quadruplets 
            



             * algo2 : pas de contrainte 

             conséquence : on ne connait pas les bots à l'avance, et donc on ne connait pas E 

             pas implémenté. 


    */ 

    <?php 
      include('fonctions.php');
      $bdd = connect_bdd();
      $elos = get_elos("season-2013");
    ?>

    function split_players() {

      var ELO_BOT=[ 0, <?php echo $elos[0][0].", ".$elos[1][0].", ".$elos[2][0] ?> ];
      var MAX_PLAYERS = 8;

      /* init */

      var E = 0.0; 
      var players = [];

      $("#joueurs input[type=checkbox]:checked").each( function() { 
        var p = {};
        p.nick = $(this).attr("data-nick");
        p.elo  = parseFloat( $(this).attr("data-elo") );
        players.push( p );

        E += p.elo;
      } );

      var X = 4 - Math.round(players.length / 2);
      var Y = MAX_PLAYERS - players.length - X;
      E += X * ELO_BOT[X];
      E += Y * ELO_BOT[Y];


      if ( players.length < 2 || players.length > MAX_PLAYERS )
      {
        $("#info").html("");
		$("#ecart1").html("");
		$("#ecart2").html("");
        $("#team1").html("");
        $("#team2").html("");
        return;
      }


      /* algo : tous les cas possibles... */
      
      var solution = 0;
      var solution_distance = 10000000.;

      for (var p=0; p< (1<<(players.length)); ++p)
      {

        var nb = X;
        var e = X*ELO_BOT[X];

        for (var i=0; i<players.length; ++i)
        {
          if ( p & (1<<i) ) {
            ++nb;
            e += players[i].elo;
          }
        }

        var distance = Math.abs(e-E/2);
        if (nb == 4 && distance<solution_distance )
        {
          solution = p;
          solution_distance = distance;
        }

      }


      /* Display */

      var list1 = "";
      var list2 = "";

      var elo1 = X*ELO_BOT[X];
      var elo2 = Y*ELO_BOT[Y];

      for (var i=0; i<players.length; ++i)
      {
        if ( solution & (1<<i) ) 
        {
          list1 += players[i].nick + ", ";
          elo1  += players[i].elo;
        }
        else
        {
          list2 += players[i].nick + ", ";
          elo2  += players[i].elo;
        }
      }


      if (solution == 0)
      {
        $("#info").html( "ERREUR ! Pas de solution !" );
      }
      else
      {
		if ( X == 0) {
		    $("#team1").html( list1 + " => " + Math.round(elo1) );
		} else {
			$("#team1").html( list1 + "Bot" + X  + " => " + Math.round(elo1) );
		}
		if ( Y == 0) {
			$("#team2").html( list2  + " => " + Math.round(elo2) );
		} else {
			$("#team2").html( list2 + "Bot" + Y  + " => " + Math.round(elo2) );
		}
        $("#info").html( "L'équipe "+ (elo1<=elo2 ? "1" : "2") +" est infectée." );
		var delta = elo2 - elo1;
		var score1 = 100 * (1000 - delta)/(1000 + delta);
		var score2 = 100 * (1000 + delta)/(1000 - delta);
		$("#ecart1").html( "Si l'équipe 1 marque 100 points, l'équipe 2 doit en marquer " + Math.round(score2) + ".");
		$("#ecart2").html( "Si l'équipe 2 marque 100 points, l'équipe 1 doit en marquer " + Math.round(score1) + ".");
      }


    }




      </script>
    </head>
    <body>
      <?php include 'menu.html'; ?>
      <h1>Former les équipes&nbsp;:</h1>
      <form id="joueurs">
        <?php
          for ($i=3; $i<get_nb_rows($elos); $i++) {
            echo "<label><input type='checkbox' ";
            echo "data-nick='".get_joueur_pseudo_from_id($i)."' "; 
            echo "data-elo='".$elos[$i][0]."'>";
            echo get_joueur_pseudo_from_id($i)." (".round($elos[$i][0]).")";
            echo "</input></label><br />";
          }
        ?>
      </form>
      <p>Equipe 1 : <span id="team1"></span></p>
      <p>Equipe 2 : <span id="team2"></span></p>
      <p id=ecart1></p>
	  <p id=ecart2></p>
	  <p id=info></p>
    </body>
</html>
