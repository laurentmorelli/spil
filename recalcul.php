<?php 

    include('fonctions.php');
	if(isset($_GET['actionType'])&& isset($_GET['id_match']))
	{
	if ($_GET['actionType'] == "calcul" && $_GET['id_match'] > -1)
	{
		calcul_elo_by_match($_GET['id_match']);
	}
	else if ( $_GET['actionType'] == "delete" && $_GET['id_match'] > -1)
	{
		delete_elo_by_match($_GET['id_match']);
	}
	}

?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
    	<title>recalcul de elo par match</title>
    	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
    	<link rel="stylesheet" type="text/css" href="template.css" media="all" />
    </head>
    <body>
        <?php include 'menu.html'; ?>
        <form method='GET' action='recalcul.php'>
            <p>
                <br/><br/>
                <table border="0" align="center">
                    <tr><th>Methode</th><th>numero match</th></tr>
                    <tr>
                    	<td><select name="actionType"> 
        <option VALUE="calcul" selected="selected">calcul</option>
        <option VALUE="delete">delete</option>
    </select></td>
                    	<td><input name="id_match" type='text' value=''/></td>
                    </tr>
                </table>
                <input type=submit />
            </p>
        </form>
    </body>
</html>
