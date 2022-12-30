<?php
//file: view/users/login.php

require_once(__DIR__."/../../core/ViewManager.php");
$view = ViewManager::getInstance();
$view->setVariable("title", "Login");
$errors = $view->getVariable("errors");
?>



<div id="contenedor-login">
    <form id="formulario-inicio" action="index.php?controller=users&amp;action=login" method="POST">       
        <h2 id="titulo-login"><?= i18n("Iniciar Sesión") ?></h2>
        <span class="texto-error"><?= isset($errors["general"])?i18n($errors["general"]):"" ?></span>
        <input type="text" class="form-control" name="username" placeholder="Username" required="" autofocus="">
        <input type="password" class="form-control" name="password" placeholder="Password" required="">      
        <div id="mantener"><input type="checkbox" name="mantener-sesion" id="mantener-sesion"><label for="mantener-sesion"><?= i18n("Mantener sesión iniciada") ?></label></div>
        
        <button id="boton-inicio" type="submit"><?= i18n("Login") ?></button>   
    </form>

</div>

<?php $view->moveToFragment("css");?>
<link rel="stylesheet" type="text/css" href="css/LoginStyle.css">
