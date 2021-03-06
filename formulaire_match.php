<?php 

    include('fonctions.php');

    $bdd = connect_bdd();
    $resultat = mysqli_query($bdd,"select id,pseudo from joueurs where bot=0 order by pseudo;"); 
    $liste_joueur_alpha = array();
    $liste_joueurs_options = "<option value='bot'> bot</option>";
    while($data = mysqli_fetch_assoc($resultat))
    {
        $liste_joueurs_options .= "<option value='".$data['id']."'>".$data['pseudo']."</option>";
        array_push( $liste_joueur_alpha, $data['id'] );
    } 
    mysqli_free_result($resultat);
    $methode_id = 2;
	if (isset($_GET['methode']))
	{
		$methode_id = $_GET['methode'];
	}
    $elos = get_elos($methode_id);
?> 

<!DOCTYPE html>
<html>
    <head>
        <title>Formulaire Nouveau Match</title>
        <meta http-equiv="content-type" content="text/html; charset=utf-8" />
        <link rel="stylesheet" type="text/css" href="template.css" media="all" />
        <script type="text/javascript" src="javascript/jquery-1.10.2.min.js"></script> 
        <script>

    var PLAYERS=[ <?php
            foreach ($elos as $i=>$j) {
              if ($i>0) echo ",";
              echo "{ id: ".$i.", ";
              echo " elo: ".$elos[$i][0].", ";
              echo " nick: \"".get_joueur_pseudo_from_id($i)."\" }";
            }
          ?>
    ];
    var ELO_BOT=[ 0, PLAYERS[0].elo, PLAYERS[1].elo, PLAYERS[2].elo, 0. ];


    $(document).ready( function() {
      compare_teams(team_elo(1),team_elo(2));

      $("#joueurs input[type=checkbox]").change( split_players );
      $("select[name^=team]").change(function(){ compare_teams(team_elo(1),team_elo(2)); });
      $("input[name^=team]").keyup(function(){ compare_teams(team_elo(1),team_elo(2)); });
    });


   function team_elo(n) {
     elo = 0;
     bot_nb = 4;
     $("select[name^=team"+n+"]").each( function() {
       p_id = $(this).val();
       if ( p_id != "bot" )
       {
         elo += PLAYERS[p_id].elo;
         --bot_nb;
       }
     });

     elo += bot_nb * ELO_BOT[bot_nb];
     return elo;
   }



   /* mise � jour de la vue de comparaison des �quipe */

   function compare_teams(elo1,elo2)
   {
     var delta = elo2 - elo1;
     var coef1 = (1000 - delta)/(1000 + delta);
     var coef2 = (1000 + delta)/(1000 - delta);

     var score1 = $('input[name="team1_score"]').val();
     var score2 = $('input[name="team2_score"]').val();
     if (score1 == "") score1=0;
     if (score2 == "") score2=0;

     $('input[name="team2_score"]').attr("placeholder",   Math.round(score1*coef2)   );
     $('input[name="team1_score"]').attr("placeholder",   Math.round(score2*coef1)   );

     $("#team1").html( Math.round(elo1) );
     $("#team2").html( Math.round(elo2) );

     if (elo1==0 || elo2==0)
     {
        $("#ecart1").html("");
        $("#ecart2").html("");
     }
     else
     {
       $("#team"+ (elo1<=elo2 ? "1" : "2")).append( "&nbsp;<small><abbr title='Cette &eacute;quipe doit commencer infect&eacute;e.'>(Infect&eacute;s)</abbr></small>" );
       $("#ecart1").html( "Si l'&eacute;quipe 1 marque 100 points, l'&eacute;quipe 2 doit en marquer " + Math.round(100*coef2) + ".");
       $("#ecart2").html( "Si l'&eacute;quipe 2 marque 100 points, l'&eacute;quipe 1 doit en marquer " + Math.round(100*coef1) + ".");

     }
   }



    /*   ALGO D'EQUILIBRAGE DES EQUIPES     */
    /*

        notations :

        Soit N le nombre de joueurs humains.
        Soit X et Y le nombre de bots dans les �quipes 1 et 2.
        Remarque : Y = 8-N-X

        Soit E la somme des elos des 8 joueurs, dont (X+Y) bots.
        Soit e1 l'elo de l'�quipe 1 : X botX, plus (4-X) joueurs humains.

        On cherche donc � minimiser la distance entre e1 et l'�quilibre parfait : E/2



        * algo1 : on �quilibre d'abord le nombre d'humains par �quipe (peut etre pas le meilleur choix) 

        avantage : on retrouve facilement quels botX et botY vont jouer (en choisissant X >= Y) :
        X = 4-round(N/2)


        X �tant fixe, les possibiit�s se limitent � piocher 4-X humains parmis N
        et la somme e1 des elo doit etre au plus proche de la constante E/2  

        On peut essayer toutes les possibilit�s... 
        methode bourrine sans eliminer les doublons pour 8 humains : 2^8 = 256 quadruplets 



        * algo2 : pas de contrainte 

        cons�quence : on ne connait pas les bots � l'avance, et donc on ne connait pas E

        pas impl�ment�.


    */


    function split_players()
    {

      var MAX_PLAYERS = 8;

      /* init */

      var E = 0.0; 
      var players = [];

      $("#joueurs input[type=checkbox]:checked").each( function() {
        var p = PLAYERS[parseInt($(this).attr("data-id"))];
        players.push( p );
        E += p.elo;
      } );

      var X = 4 - Math.round(players.length / 2);
      var Y = MAX_PLAYERS - players.length - X;
      E += X * ELO_BOT[X];
      E += Y * ELO_BOT[Y];


      $("select[name^=team]").val("bot");
      if ( players.length < 2 || players.length > MAX_PLAYERS )
      {
        if ( players.length > MAX_PLAYERS ) {
          $("#info").html("Vous avez selectionn&eacute; trop de joueurs.");
        }
        compare_teams(0,0);
        return;
      }


      /* algo : tous les cas possibles... */

      var solution = 0;
      var solution_distance = 10000000.;

      // on boucle sur les 2^N cas possibles
      for (var p=0; p< (1<<(players.length)); ++p)
      {

        var team_size = X;
        var team_elo = X*ELO_BOT[X];

        for (var i=0; i<players.length; ++i)
        {
          // les bits de p en binaire codent la pr�sence ou non du joueur i dans l'�quipe.
          if ( p & (1<<i) ) {
            ++team_size;
            team_elo += players[i].elo;
          }
        }

        var distance = Math.abs(team_elo-E/2);
        if (team_size == 4 && distance<solution_distance )
        {
          solution = p;
          solution_distance = distance;
        }

      }


      /* D�code la solution */

      var team1_count = 0;
      var team2_count = 0;

      var elo1 = X*ELO_BOT[X];
      var elo2 = Y*ELO_BOT[Y];

      for (var i=0; i<players.length; ++i)
      {
        if ( solution & (1<<i) )
        {
          elo1 += players[i].elo;

          ++team1_count;
          $("select[name=team1_player"+team1_count+"]").val(players[i].id);
        }
        else
        {
          elo2 += players[i].elo;

          ++team2_count;
          $("select[name=team2_player"+team2_count+"]").val(players[i].id);
        }
      }


      if (solution == 0)
      {
        // ne devrait pas arriver
        $("#info").html( "ERREUR ! Pas de solution !" );
      }
      else
      {
        $("#info").html("");
        compare_teams(elo1,elo2);
      }


    }



    


        </script>
    </head>
    <body>	
        <?php include 'menu.html'; ?>
		<h2>La m&eacute;thode : </h2>
            <form method='GET' action='index.php'>
                <select name="methode" onchange="this.options[this.selectedIndex].value && (window.location = window.location.pathname + '?methode=' + this.options[this.selectedIndex].value);">
                    <option <?php if(isset($_GET['methode']) and $_GET['methode'] == '0'){echo("selected");}?> value="0">Compte tous les matchs</option>
                    <option <?php if(isset($_GET['methode']) and $_GET['methode'] == '1'){echo("selected");}?> value="1">Compte les 20 derniers matchs</option>
                    <option <?php if(isset($_GET['methode']) == false or $_GET['methode'] == '2'){echo("selected");}?> value="2">Poids d&eacute;croissant en fonction du temps</option>
                    <option <?php if(isset($_GET['methode']) and $_GET['methode'] == '3'){echo("selected");}?> value="3">Poids al&eacute;atoire (wait... what?)</option>    
                </select>
        	</form>
        <h2>Equilibrer les &eacute;quipes <small>(facultatif)</small></h2>
        <div style='float:right; '>
          <p id=info></p>
          <p id=ecart1></p>
          <p id=ecart2></p>
        </div>
        <form id="joueurs">
          <?php
            foreach ($liste_joueur_alpha as $i=>$j) {
              echo "<label><input type='checkbox' ";
              echo "data-id='".$j."'>"; 
              echo get_joueur_pseudo_from_id($j)." (".round($elos[$j][0]).")";
              echo "</input></label><br />";
            }
          ?>
        </form>

        <h2>Enregistrer le match</h2>
        <form method='GET' action='insert_new_match.php'>
                <table>
               <tbody><tr><th>Player 1</th><th>Player 2</th><th>Player 3</th><th>Player 4</th><th>Elo</th><th>Score</th></tr>
                    <tr>
                        <td><select name="team1_player1" ><?php echo $liste_joueurs_options ?></select></td>
                        <td><select name="team1_player2" ><?php echo $liste_joueurs_options ?></select></td>
                        <td><select name="team1_player3" ><?php echo $liste_joueurs_options ?></select></td>
                        <td><select name="team1_player4" ><?php echo $liste_joueurs_options ?></select></td>
                        <td><span id="team1"></span></td>
                        <td><input name="team1_score" type="text" style="width:3em" placeholder="0" autocomplete="off" /></td>
                    </tr>
                    <tr>
                        <td><select name="team2_player1" ><?php echo $liste_joueurs_options ?></select></td>
                        <td><select name="team2_player2" ><?php echo $liste_joueurs_options ?></select></td>
                        <td><select name="team2_player3" ><?php echo $liste_joueurs_options ?></select></td>
                        <td><select name="team2_player4" ><?php echo $liste_joueurs_options ?></select></td>
                        <td><span id="team2"></span></td>
                        <td><input name="team2_score" type="text" style="width:3em" placeholder="0" autocomplete="off" /></td>
                    </tr>
                </table>
            <p>
                Map :
                <select name="map" >
                    <option>Blood_Harvest</option>
                    <option>Cold_Stream</option>
                    <option>Crash_Course</option>
                    <option>Dark_Carnival</option>
                    <option>Dead_Air</option>
                    <option>Dead_Center</option>
                    <option>Death_Toll</option>
                    <option>No_Mercy</option>
                    <option>Swamp_Fever</option>
                    <option>The_Parish</option>
                    <option>The_Passing</option>
                    <option>The_Sacrifice</option>
                    <option>Hard_Rain</option>          
                </select>
            </p>
            <p>
                Game Type  : 
                <select name="game_type" >
                    <option>Versus_Realisme</option>
                </select>
            </p>
            <input type=submit value=Enregistrer />
        </form>
    </body>
</html>
