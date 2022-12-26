<?php
//file: view/users/register.php

require_once(__DIR__ . "/../../core/ViewManager.php");
$view = ViewManager::getInstance();
$errors = $view->getVariable("errors");
$user = $view->getVariable("user");
$view->setVariable("title", "Register");
?>
<div class="formulario_registro">
	<h1><?= i18n("Join Us") ?></h1>
	<form action="index.php?controller=users&amp;action=register" method="POST">

		<div class="campoformulario">
			<input type="text" name="username" value="<?= $user->getUsername() ?>" placeholder="<?= i18n("Username") ?>">
			<?= isset($errors["username"]) ? i18n($errors["username"]) : "" ?><br>
		</div>
		<div class="campoformulario">

			<input type="text" name="email" value="<?= $user->getEmail() ?>" placeholder="<?= i18n("Email") ?>">
			<?= isset($errors["email"]) ? i18n($errors["email"]) : "" ?><br>
		</div>
		<div class="campoformulario">
			<input type="password" name="passwd" value="" placeholder="<?= i18n("Password") ?>">
			<?= isset($errors["passwd"]) ? i18n($errors["passwd"]) : "" ?><br>
		</div>
		<div class="campoformulario">
			<input type="password" name="passwdbis" value="" placeholder="<?= i18n("Repeat Password") ?>">
			<?= isset($errors["passwdbis"]) ? i18n($errors["passwdbis"]) : "" ?><br>

		</div>

		<div class="botonregistrarse">
			<input type="submit" value="<?= i18n("Start to save now") ?>">
		</div>
	</form>
</div>

<!-- inserto css -->
<?php $view->moveToFragment("css"); ?>
<link rel="stylesheet" type="text/css" href="./view/CSS/regstyles.css">
<?php $view->moveToDefaultFragment(); ?>