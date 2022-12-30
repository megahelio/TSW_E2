<?php
//file: view/layouts/default.php

$view = ViewManager::getInstance();
$currentuser = $view->getVariable("currentusername");

?><!DOCTYPE html>
<html>
<head>
	<title><?= $view->getVariable("title", "no title") ?></title>
	<meta charset="utf-8">
	<link rel="stylesheet" href="css/Style.css" type="text/css">
	<!-- enable ji18n() javascript function to translate inside your scripts -->
	<script src="index.php?controller=language&amp;action=i18njs">
	</script>
	<?= $view->getFragment("css") ?>
	<?= $view->getFragment("javascript") ?>
	<script>
		function mostrarBloque(id){
			document.getElementById(id).style.display="block";
		}
	</script>
</head>
<body>
	<!-- header -->
	<header>
        <div id="menu">
            <div id="logoCompleto" class="imagenLogo"><img src="img/LogoNombreAzul.png" alt="LogoCompleto"></div>
            <div id="logoReducido" class="imagenLogo"><img src="img/LogoAzul.png" alt="LogoCompleto"></div>
            <?php if (isset($currentuser)): ?>
				<div class="seccion-usuario">
					<span><img src="img/cuenta.png" alt="imgUser"></span>
					<p><?php echo($currentuser) ?> </p>
				</div>
				<div id="hamburguesa">
					<nav>
						<input type="checkbox" id="check">
						<label for="check"> ☰ </label>
						<ul>
							<a href="index.php?controller=expenses&amp;action=index">
								<li><?=i18n("Análisis")?></li>    
							</a>
							<a href="index.php?controller=expenses&amp;action=crud" >
								<li><?=i18n("Tabla de Gastos")?></li>
							</a>
							<a href="index.php?controller=users&amp;action=logout">
								<li class="opcion-salida"><?=i18n("Cerrar Sesión")?></li>
							</a> 
							<a onclick="mostrarBloque('contenedor-eliminar-usuario')">
								<li class="opcion-salida"><?=i18n("Eliminar Perfil")?></li>
							</a>
						</ul>
					</nav>
				</div>
			<?php else: ?>
				<div id="sesionMenu">
					<a href="index.php?controller=users&amp;action=login"><button id="iniciarSesion"><h3><?=i18n("Iniciar Sesión")?></h3></button></a>

					<a href="index.php?controller=users&amp;action=register"><button id="registrarse"><h3><?=i18n("Registrarse")?></h3></button></a>
				</div>

			<?php endif ?>
        </div>
        <div class="separador"></div>
    </header>

	<main>
		<div id="flash">
		<span class="successfull"><?= $view->popFlash() ?></span>
		</div>

		<?= $view->getFragment(ViewManager::DEFAULT_FRAGMENT) ?>
	</main>

	<footer>
		
		<div id="footerLogo">
            <img src="img/LogoNombreBlanco.png" alt="LogoFooter">
            <p>Edificio Politécnico s/n, 32004 Ourense</p>
        </div>

        <div id="footer-idioma">
			<h5>TSW_2 2022/2023</h5>
			<?php
			include(__DIR__."/language_select_element.php");
			?>
		</div>

        <div id="footerNombres">
            <p>
                Antón Canzobre Martínez <br> Cibrán Cores Cabaleiro <br> Noel Fabello Quintana <br>
            </p>
        </div>
	</footer>

	<div id="contenedor-eliminar-usuario" class="contenedor-emergente">
		<div id="eliminar-usuario" class="generico-eliminar">
			<h3><?=i18n("¿Estas seguro de eliminar tu usuario?")?></h3>
			<div class="botones-eliminar-generico">
				<button class="boton-eliminar-generico" id="cancelar-eliminar-usuario" onclick="location.reload();"><?=i18n("Cancelar")?></button>
				<form method="POST" action="index.php?controller=users&amp;action=deleteProfile">
					<button type="submit" class="boton-eliminar-generico aceptar-eliminar-generico" id="aceptar-eliminar-usuario" ><?=i18n("Eliminar")?></button>
				</form>
			</div>
		</div>
	</div>

</body>
</html>
