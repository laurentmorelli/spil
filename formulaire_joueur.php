<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
    	<title>Formulaire nouveau joueur</title>
    	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
    	<link rel="stylesheet" type="text/css" href="template.css" media="all" />
    </head>
    <body>
        <?php include 'menu.html'; ?>
        <form method='GET' action='insert_new_joueur.php'>
            <p>
                <br/><br/>
                <table border="0" align="center">
                    <tr><th>Prenom</th><th>Pseudo</th></tr>
                    <tr>
                    	<td><input name="prenom" type='text' value=''/></td>
                    	<td><input name="pseudo" type='text' value=''/></td>
                    </tr>
                </table>
                <input type=submit />
            </p>
        </form>
    </body>
</html>
