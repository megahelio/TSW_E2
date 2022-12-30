<?php
//file: view/users/register.php

require_once(__DIR__."/../../core/ViewManager.php");
$view = ViewManager::getInstance();

$errors = $view->getVariable("errors");
$user = $view->getVariable("user");

$view->setVariable("title", "Register");
?>

<div id="contenedor-registro">
	<form id="formulario-registro" action="index.php?controller=users&amp;action=register" method="POST">       
		<h2 id="titulo-registro"><?= i18n("Registrarse")?></h2>
		<input type="text" class="form-control" name="username" placeholder="Username"  >
        <div class = 'texto-error'><?= isset($errors["username"])?i18n($errors["username"]):"" ?></div>
		<input type="text" class="form-control" name="email" placeholder="Email" >
        <div class = 'texto-error'><?= isset($errors["email"])?i18n($errors["email"]):"" ?></div>
		<input type="password" class="form-control" name="password" placeholder="Password">      
        <div class = 'texto-error'><?= isset($errors["passwd"])?i18n($errors["passwd"]):"" ?></div>
		
		<button id="boton-registro" type="submit"><?= i18n("Registrarse")?></button>   
	</form>

</div>
<?php $view->moveToFragment("css");?>
<link rel="stylesheet" type="text/css" href="css/RegistroStyle.css">
