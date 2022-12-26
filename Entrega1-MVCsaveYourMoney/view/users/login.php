<?php
//file: view/users/login.php

require_once(__DIR__ . "/../../core/ViewManager.php");
$view = ViewManager::getInstance();
$view->setVariable("title", i18n("Login"));
$errors = $view->getVariable("errors");
?>

<div class="formulario_login">
	<h1><?= i18n("Login") ?></h1>
	<form method="post" action="index.php?controller=users&amp;action=login">
		<div class="username">
			<input type="text" name="username" placeholder="<?= i18n("Username") ?>" required>
		</div>
		<div class="username">
			<input type="password" name="passwd" placeholder="<?= i18n("Password") ?>" required>

		</div>
		<div class="recordar2">
			<input type="checkbox" name="remember"> <?= i18n("Remember me") ?>
		</div>
		<!-- <div class="recordar">Recuperar contraseña</div> -->

		<input type="submit" value="<?= i18n("Log in") ?>">

		<div class="registrarse">
			<p><?= i18n("Not user?") ?><a href="index.php?controller=users&amp;action=register"><?= i18n("Register here!") ?></a></p>
		</div>
	</form>
</div>

<?php $view->moveToFragment("css"); ?>
<link rel="stylesheet" type="text/css" href="./view/CSS/stylesLogin.css">
<?php $view->moveToDefaultFragment(); ?>