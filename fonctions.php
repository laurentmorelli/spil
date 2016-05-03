<?php

// Connection à la base de données
function connect_bdd()
{
	if (! $bdd = mysqli_connect('127.0.0.1','root','spil','spil')) { echo "Erreur de connexion à la BD.";}
	return $bdd ;
}

// Test si un match existe déjà
// (cad si un match avec la même diff de score en valeur abs a déjà été réentrée dans la base récement)
function match_already_exists ($score1,$score2,$datetime,$ecart)
{
	$bdd = connect_bdd();
	$diff_score = abs($score1-$score2);
	$sql = "select * from matchs where ABS(`score_team1` - `score_team2`) = $diff_score AND ABS(DATEDIFF('$datetime',`date`)) < $ecart;";
	$resultat = mysqli_query($bdd,$sql);
	$nombre_resultat = mysqli_num_rows($resultat);
	if ( $nombre_resultat > 0) { return 1 ;}
	return 0;
}


// Création d'une matrice initialisée avec la valeur $init
function create_matrix ($rows, $cols, $init) {
    $mx = array();
    for ($i=0; $i<$rows; $i++) {
		for ($j=0; $j<$cols; $j++) {
			$mx[$i][$j] = $init;
		}
    }
    return($mx);
}

// Addition de 2 matrices
function add_matrix ($a,$b,$rows,$cols) {
    $mx = array();
    for ($i=0; $i<$rows; $i++) {
		for ($j=0; $j<$cols; $j++) {
			$mx[$i][$j] = $a[$i][$j]+$b[$i][$j];
		}
    }
    return($mx);
}

// Multiplication de 2 matrices
// $rows and $cols are $a size
function multiply_matrix ($a,$b,$rowsA,$colsA,$colsB) {
    $mx = array();
    for ($i=0; $i<$rowsA; $i++) {
		for ($j=0; $j<$colsB; $j++) {
			$x = 0;
			for ($k=0; $k<$colsA; $k++) {
				$x += ($a[$i][$k]) * ($b[$k][$j]);
			}
			$mx[$i][$j] = $x;
		}
    }
    return($mx);
}

// MoorePenrose Pseudo Invert
function pseudoinvert_matrix($a)
{

	$url = "http://calculator.vhex.net/c/moore-penrose-pseudo-inverse";
	$data_string = json_encode($a);
	$invert_string = curl_post($url,"[$data_string]");
	return json_decode($invert_string);
	
}

// TIPS :  To use curl library with wamp : uncomment lines with curl in php.ini (in php binaries and apache binaries)
function curl_post($url,$data) 
{ 
    $defaults = array( 
        CURLOPT_POST => 1, 
        CURLOPT_HEADER => 0, //application/json
        CURLOPT_URL => $url, 
        CURLOPT_FRESH_CONNECT => 1, 
        CURLOPT_RETURNTRANSFER => 1, 
        CURLOPT_FORBID_REUSE => 1, 
        CURLOPT_TIMEOUT => 4, 
        CURLOPT_POSTFIELDS => $data 
    ); 

    $ch = curl_init(); 
    curl_setopt_array($ch, ($defaults)); 
    if( ! $result = curl_exec($ch)) 
    { 
        trigger_error(curl_error($ch)); 
    } 
    curl_close($ch); 
    return $result; 
} 

//nb rows for a matrix
function get_nb_rows($a) {return count($a,0);}

//nb cols for a matrix
function get_nb_cols($a) {return count($a[0],0);}

//affiche une matrice, pour le debug
function print_matrix($a)
{
	$rows=get_nb_rows($a);
	$cols=get_nb_cols($a);
	for($i=0;$i<$rows;$i++){
		for($j=0;$j<$cols;$j++){
			echo "|".$a[$i][$j] ;
		}
		echo "</br>";
	}
}

// calcul le elo d'une méthode donnée
function compute_elo_by_methode($objet_methode)
{
	//on travaille sur le dernier match par default
	$bdd = connect_bdd();
	$sql = "select max(id) id from matchs;";
	$resultat = mysqli_query($bdd,$sql);
	$data = mysqli_fetch_assoc($resultat);	
	
	return compute_elo_by_methode_by_match($objet_methode,$data['id']);
}

// calcul le elo d'une méthode donnée
function compute_elo_by_methode_by_match($objet_methode,$match_id)
{
	$bdd = connect_bdd();
	$sql = "select * from matchs;";
	if ($match_id != -1) // pas de gestion de parametre par defaut dans php, ce qui nous eviterait cette mochetee
	{
		$sql = "select * from matchs where `id` <= '$match_id';";
	}
	
	$objet_matchs = mysqli_query($bdd,$sql);
	$objet_joueurs = get_joueurs();
	$nb_joueurs = get_nb_joueurs($objet_joueurs);
	$nb_matchs = get_nb_matchs($objet_matchs);
	
// $objet_methode peut etre le numero associe a la methode
// (dans ce cas on contruit le vector de poids),
// ou alors directement le poids (on fait rien)	
	if (is_numeric($objet_methode))
	{
	$weight_matchs = create_weight($objet_methode,$nb_matchs);
	}
	else
	{$weight_matchs = $objet_methode;
	};
		
	$matrice_participants = create_matrix($nb_matchs,$nb_joueurs,0);
	$vecteur_resultats = create_matrix($nb_matchs,1,0);
	$vecteur_elo_initial = create_matrix($nb_matchs,1,1500);
	
	$count = 0;
	while ($data = mysqli_fetch_assoc($objet_matchs))
		{
		$w=$weight_matchs[$count][0];
		
		$matrice_participants[$count][$data['team1_player1']] += $w ;
		$matrice_participants[$count][$data['team1_player2']] += $w ;
		$matrice_participants[$count][$data['team1_player3']] += $w ;
		$matrice_participants[$count][$data['team1_player4']] += $w ;
		$matrice_participants[$count][$data['team2_player1']] += -$w ;
		$matrice_participants[$count][$data['team2_player2']] += -$w ;
		$matrice_participants[$count][$data['team2_player3']] += -$w ;
		$matrice_participants[$count][$data['team2_player4']] += -$w ;
//		$vecteur_resultats[$count][0] = $w*1000*($data['score_team1']-$data['score_team2'])/($data['score_team1']+$data['score_team2']);
		$vecteur_resultats[$count][0] = $w*500*(log($data['score_team1'])-log($data['score_team2']));
		$count ++;
		} 

	//echo "</br>";print_matrix($vecteur_resultats);echo "</br>";

	$pseudoinverted_matrice = pseudoinvert_matrix($matrice_participants) ;
	$elos = multiply_matrix($pseudoinverted_matrice,$vecteur_resultats,$nb_joueurs,$nb_matchs,1);
	$elos = add_matrix($elos,$vecteur_elo_initial,$nb_joueurs,1);

	
	mysqli_free_result($objet_matchs);
	mysqli_free_result($objet_joueurs);
	
	//print_matrix($elos);
	return $elos ; // array : elos[id][0] = elo;
}

function create_weight($methode_id,$nb_matchs)
{	
	$weight_matchs = create_matrix($nb_matchs,1,1);
	for($i=0;$i<$nb_matchs;$i++)
		{
		if ($methode_id==0)
			{$weight_matchs[$i][0]=1;};
		if ($methode_id==1)
			{if ($nb_matchs-$i<=20)
				$weight_matchs[$i][0]=1;
				else
				$weight_matchs[$i][0]=0;
			};
		if ($methode_id==2)
			{$weight_matchs[$i][0]=round(1/sqrt($nb_matchs-$i),3);};
			// sans arrondi, le pseudo-invert fait n'imp
		if ($methode_id==3)
			{$weight_matchs[$i][0]=rand(0,1000)/1000;};
		};
	return $weight_matchs ;
}
	
//put elos in database
function put_elo_in_db ($elos,$methode_id)
{
	return put_elo_in_db_by_match($elos,$methode_id,-1);	
}

//put elos in database by match
function put_elo_in_db_by_match ($elos,$methode_id,$match_id)
{
	$bdd = connect_bdd();
	$nb_elos = get_nb_rows($elos);
	$date_now = date('Y-m-d H:i:s');
	for($i=0;$i<$nb_elos;$i++)
	{
		$sql="insert into calcul (`id`,`id_joueur`,`id_methode`,`date`,`elo`,`id_match`) values ('',$i,$methode_id,'$date_now',".$elos[$i][0].",$match_id );";
		//echo $sql;
		$resultat = mysqli_query($bdd,$sql);
		// test pour vérifier l'insertion ?
	}
	return 1;
}

// pour le reset eventuel des elo en cas de server distant inaccessible
function delete_elo_by_match($match_id)
{
	$bdd = connect_bdd();	
	$sql="delete from calcul where `id_match` = $match_id;";
	$resultat = mysqli_query($bdd,$sql);
}


//calcul le dernier elo
function calcul_elo_last_match()
{
	//on travaille sur le dernier match
	$bdd = connect_bdd();
	$sql = "select max(id) id from matchs;";
	$resultat = mysqli_query($bdd,$sql);
	$data = mysqli_fetch_assoc($resultat);	
	
	return calcul_elo_by_match($data['id']);
}


function calcul_elo_by_match($match_id)
{
	// on boucle sur toutes les méthodes actives
//while ($methode = mysqli_fetch_assoc($objet_methodes))
for($methode=0;$methode<4;$methode++){
    // on calcule le elo
    $elos = compute_elo_by_methode_by_match($methode,$match_id);
    // on insère les résultats dans la base
   put_elo_in_db_by_match ($elos,$methode,$match_id);
    
}
	return 1;
}

// retourne le pseudo d'un joueur connaissant son id
function get_pseudo_by_id($objet_joueurs,$id)
{
	while ($data = mysqli_fetch_assoc($objet_joueurs)) {
		if ($data['id'] = $id) { return $data['pseudo'] ; };
	}
	echo "erreur pour récupérer le pseudo depuis l'id";
	exit;
}

// retourne l'id d'un joueur connaissant son pseudo
function get_id_by_pseudo($objet_joueurs,$pseudo)
{
	while ($data = mysqli_fetch_assoc($objet_joueurs)) {
		if ($data['pseudo'] == $pseudo) { return $data['id'] ; };
	}
	echo "le joueur n'existe pas.";
	exit;
}

// retourne la liste des joueurs au format mysqli (objet retourné par mysqli_query)
function get_joueurs() {
	$bdd = connect_bdd();
	$sql = "select * from joueurs;";
	$resultat = mysqli_query($bdd,$sql);
	return $resultat;
}

// retourne la methode souhaitée sous forme d'objet mysqli_query
function get_methode($methode_name)
{
	$bdd = connect_bdd();
	$sql = "select * from methode_de_calcul where nom='$methode_name' limit 1;";
	$resultat = mysqli_query($bdd,$sql);
	if ( mysqli_num_rows($resultat) == 0) {
			echo "erreur, méthode incorrecte;";
			exit;
	}
	return $resultat;
}

// retourne les methodes actives sous forme d'objet mysqli_query
function get_active_methodes()
{
	$bdd = connect_bdd();
	$date_now = date('Y-m-d H:i:s');
	$sql = "select * from methode_de_calcul where date_debut < '$date_now' AND date_fin > '$date_now';";
	$resultat = mysqli_query($bdd,$sql);
	if ( mysqli_num_rows($resultat) == 0) {
			echo "erreur, aucune methode trouvee;";
			exit;
	}
	return $resultat;
}

// retourne la liste des matchs au format mysqli (objet retourné par mysqli_query)
function get_matchs($objet_methode) {
	$bdd = connect_bdd();
	$data = mysqli_fetch_assoc($objet_methode);
	$sql = "select * from matchs where date > '".$data['date_debut']."' AND date < '".$data['date_fin']."' limit ".$data['nb_matchs']." ;";
	$resultat = mysqli_query($bdd,$sql);
	if ( mysqli_num_rows($resultat) == 0) {
			echo "erreur, aucun match recueilli par la méthode choisie : ".$data['nom'].";";
			echo "requete SQL : [$sql]";
			exit;
	}
	return $resultat;
}

// retourne la liste de tous les matchs au format mysqli (objet retourné par mysqli_query)
// mais avec les pseudos au lieu des id
function get_all_matchs() {
	$bdd = connect_bdd();
	$sql = "select * from matchs order by id desc;";
	$resultat = mysqli_query($bdd,$sql);
	return $resultat;
}


// retourne le nb de joueurs
function get_nb_joueurs($objet_joueurs){ return mysqli_num_rows($objet_joueurs);}

// retourne le nb de matchs
function get_nb_matchs($objet_matchs){return mysqli_num_rows($objet_matchs);}

// retourne les equipes avec les bons id pour les bots sous forme de tableaux
// en entree : les 4 ids, en sortie un tableau
function get_real_team ($a,$b,$c,$d)
{
	$bot_nb = 0;
	$real_team = array();
	if ($a == 'bot') {$bot_nb ++;};
	if ($b == 'bot') {$bot_nb ++;};
	if ($c == 'bot') {$bot_nb ++;};
	if ($d == 'bot') {$bot_nb ++;};
	$bot_id = $bot_nb - 1 ;
	if ($a == 'bot') {$real_team[0] =$bot_id;} else {$real_team[0] = $a;};
	if ($b == 'bot') {$real_team[1] =$bot_id;} else {$real_team[1] = $b;};
	if ($c == 'bot') {$real_team[2] =$bot_id;} else {$real_team[2] = $c;};
	if ($d == 'bot') {$real_team[3] =$bot_id;} else {$real_team[3] = $d;};
	return $real_team;
}


// Select a random file in a directory
function select_random_image($dir_path)
{
    $dir = opendir($dir_path); 
    $images_name = array();
    while($file = readdir($dir))
    {
	    if($file != '.' && $file != '..' && !is_dir($dir_path.$file))
	    {
	    	$images_name[] = $file;
	    }
    }
    closedir($dir);
    shuffle($images_name);
    return $dir_path.$images_name[0]; 
}

// for a given method
// return an array : elos[id]->[pseudo,elo n, elo n-1]
function get_elos ($methode_id)
{
	$bdd = connect_bdd();
    $objet_joueurs = get_joueurs();
//    $methode_id = get_methode_id_from_nom($methode_name);
    $elos = create_matrix(mysqli_num_rows($objet_joueurs),2,1500); // elos[id]->[elo n, elo n-1]
    while($joueur = mysqli_fetch_assoc($objet_joueurs))
    {
        $id = $joueur['id'];
        $sql="select elo from calcul where id_joueur = '$id' and id_methode = '$methode_id' order by id_match desc;";
		//echo $sql;
       	$resultat = mysqli_query($bdd,$sql);
       	$count = 0;
       	while($data = mysqli_fetch_assoc($resultat))
       	{
		    $elos[$id][$count] = $data['elo'];
		    $count ++;
        }
		mysqli_free_result($resultat);
    }
	return $elos;
}

function get_joueur_pseudo_from_id ($id)
{
	$bdd = connect_bdd();
	$sql = "select pseudo from joueurs where id = '$id' limit 1;";
	$resultat = mysqli_query($bdd,$sql);
	$data = mysqli_fetch_assoc($resultat);
	return $data['pseudo'];
}

//function get_methode_id_from_nom ($nom)
//{
//	$bdd = connect_bdd();
//	$sql = "select id from methode_de_calcul where nom = '$nom' limit 1;";
//	$resultat = mysqli_query($bdd,$sql);
//	$data = mysqli_fetch_assoc($resultat);
//	return $data['id'];
//}
?>
