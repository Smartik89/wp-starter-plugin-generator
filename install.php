<?php 

if( file_exists( dirname(__FILE__) . "/db-config.php" ) ) {
	header('Location: index.php');
	exit;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
	
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	
	<title>Page</title>
	<link rel="stylesheet" type="text/css" href="bootstrap/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="bootstrap/css/bootstrap-theme.css">
	<link rel="stylesheet" type="text/css" href="css/style.css">
	<script type="text/javascript" src="js/jquery-2.2.4.min.js"></script>

</head>
<body>
<div class="page-install">

<?php

$db_host        = 'localhost';
$db_username    = '';
$db_password    = '';
$db_name        = '';
$errors         = array();


if( !empty($_POST) ){ 

	/* Verificam datele introduse
	------------------------------------------------*/
	if( !empty($_POST['db-host']) ){
		$db_host = strip_tags($_POST['db-host']);
	}
	else{
		$errors['db-host'] = '<div class="alert alert-danger">Introduceti DB Host.</div>';
	}

	if( !empty($_POST['db-username']) ){
		$db_username = strip_tags($_POST['db-username']);
	}
	else{
		$errors['db-username'] = '<div class="alert alert-danger">Introduceti DB username.</div>';
	}

	// DB poate fi fara parola, de aceea nu vom afisa nici o eroare
	$db_password = strip_tags($_POST['db-password']);

	if( !empty($_POST['db-name']) ){
		$db_name = strip_tags($_POST['db-name']);
	}
	else{
		$errors['db-name'] = '<div class="alert alert-danger">Introduceti DB name.</div>';
	}

	if( !empty($db_host) && !empty($db_username) && !empty($db_name) ){

		/* Conectare la BD
		------------------------------------------------*/
		$conn = mysqli_connect($db_host, $db_username, $db_password, $db_name);

		// Verifica conexiunea
		if (!$conn) {
			$errors['conexiune-imposibila'] = '<div class="alert alert-danger">Conexiune imposibila!</div>';
		}
		else{
			$success_msg = array();
			$error_msg = array();

			/* Creaza tabela pentru optiuni
			------------------------------------------------*/
			$sql = "CREATE TABLE options(
			id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
			option VARCHAR(255),
			value LONGTEXT
			)";
			if( mysqli_query($conn, $sql) ){
				$success_msg[] = 'Tabelul "options" a fost creat.';
			}
			else{
				$error_msg[] = 'Eroare la crearea tabelului "options".';
			}

			/* Inchidem conexiunea
			------------------------------------------------*/
			mysqli_close($conn);

/* Creaza fisierul db-config.php
------------------------------------------------*/
$content = "<?php
define('DB_HOST',     '" . $db_host ."');
define('DB_USERNAME', '" . $db_username ."');
define('DB_PASSWORD', '" . $db_password ."');
define('DB_NAME',     '" . $db_name ."');";

			// Creaza fisierul si adauga continutul
			file_put_contents('db-config.php', $content);

			echo '
			<div class="install-success">
				<img src="img/success.png" alt="" />
				<h1>Instalare reușită</h1><ul class="install-log-list">';
				
				if( !empty($success_msg) ){
					foreach ($success_msg as $msg) {
						echo '<li class="bg-success">'. $msg .'</li>';
					}
				}

				if( !empty($error_msg) ){
					foreach ($error_msg as $msg) {
						echo '<li class="bg-danger">'. $msg .'</li>';
					}
				}

			echo '</ul>
			<a href="index.php" class="btn btn-primary">Continuare</a>
			</div>
			';

		}
	}

}

// Daca forma nu a fost trimisa sau sunt errori afiseaza forma
if( empty($_POST) || !empty($errors) ) :
	
	if( !empty($errors['conexiune-imposibila']) ) echo $errors['conexiune-imposibila'];

?>

	<form method="post">
		<h3 class="form-section">Server details</h3>
		<div class="form-group">
			<label>DB Host</label>
			<input class="form-control" type="text" name="db-host" value="<?php echo $db_host; ?>"/>
			<?php if( !empty($errors['db-host']) ) echo $errors['db-host']; ?>
		</div>
		<div class="form-group">
			<label>DB Username</label>
			<input class="form-control" type="text" name="db-username" value="<?php echo $db_username; ?>"/>
			<?php if( !empty($errors['db-username']) ) echo $errors['db-username']; ?>
		</div>
		<div class="form-group">
			<label>DB Password</label>
			<input class="form-control" type="password" name="db-password" value="<?php echo $db_password; ?>"/>
			<?php if( !empty($errors['db-password']) ) echo $errors['db-password']; ?>
		</div>
		<div class="form-group">
			<label>DB Name</label>
			<input class="form-control" type="text" name="db-name" value="<?php echo $db_name; ?>"/>
			<?php if( !empty($errors['db-name']) ) echo $errors['db-name']; ?>
		</div>

		<button type="submit" class="btn btn-primary">Trimite</button>
	</form>

<?php 
endif;

?>
</div>

	<script type="text/javascript" src="bootstrap/js/bootstrap.min.js"></script>
	<script type="text/javascript" src="js/scripts.js"></script>

</body>
</html>