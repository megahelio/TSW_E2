<?php
//file: view/layouts/inicio.php

require_once(__DIR__."/../../core/ViewManager.php");
$view = ViewManager::getInstance();


$view->setVariable("title", "Inicio");

?>


<div id="estructura">
    <div id="contentEstructura">
        <div id="textoContent">
            <h1><?=i18n("Ahorra, Planifica y Visualiza")?></h1>
            <h3><?=i18n("Alcanza tus metas financieras más rápido")?></h3>
        </div>
        <h5 id="graficaTexto"><?=i18n("Observa por donde se va tu dinero en lapsos de tiempo personalizados")?></h5>
        <img src="img/imagenGrafica.png" alt="Visualizacion De Graficas" id="imagenGrafica">
        <h5 id="tablaTexto"><?=i18n("Actualiza tus gastos y guarda tus archivos relacionados")?></h5>
        <img src="img/imagenTabla.png" alt="Visualizacion de Tabla" id="imagenTabla">
    </div>
</div>

<?php $view->moveToFragment("css");?>
<link rel="stylesheet" href="css/InicioStyle.css" type="text/css">