<?php

require_once(__DIR__ . "/../../core/ViewManager.php");
$view = ViewManager::getInstance();
$gasto = $view->getVariable("gasto");

$currentuser = $view->getVariable("currentusername");

$view->setVariable("title", i18n("Add Spent"));

?>
<html>

<div class="formularioAddEdit">
<h1><?= i18n("Edit Spent") ?></h1>

<form enctype="multipart/form-data" action="index.php?controller=gastos&amp;action=edit&amp;id=<?= $gasto->getId() ?>" method="POST">

    <!-- TODO: Lenguaje

    $gasto->setUsuario($_POST["usuario"]);
            $gasto->setTipo($_POST["tipo"]);
            $gasto->setCantidad($_POST["cantidad"]);
            $gasto->setFecha($_POST["feecha"]);
            $gasto->setDescription($_POST["descripción"]);
            $gasto->setUuidFichero($_POST["uuidFichero"]);-->

    <div class="campoformulario">
        <p><?= i18n("Description") ?></p>
        <input type="text" name="descripcion" id="" value="<?= $gasto->getDescription() ?>">
        <?= isset($errors["descripcion"]) ? i18n($errors["descripcion"]) : "" ?><br>
    </div>

    <div class="campoformulario">
        <p><?= i18n("cost") ?></p>
        <input type="number" name="cantidad" id="" min="0" step="0.01" value="<?= $gasto->getCantidad() ?>" required>
        <?= isset($errors["cantidad"]) ? i18n($errors["cantidad"]) : "" ?><br>
    </div>

    <div class="campoformulario">
    <p><?= i18n("type") ?></p>

        <select name="tipo" id="lang" required>
            <?php
            $tipos = (new Tipos)->tipos;
            foreach ($tipos as $tipo) :
                $aux = ($gasto->getTipo() == ($tipo)) ?  "selected" : '';
                $str = "<option value=\"" . $tipo . "\" " .  i18n($aux) . ">" . $tipo . "</option>\n";
                echo $str;
            endforeach;
            ?>
        </select>
        <?= isset($errors["tipo"]) ? i18n($errors["tipo"]) : "" ?><br>
    </div>

    <div class="campoformulario">
        <p>fecha</p>
        <input type="date" name="fecha" id="" value="<?= $gasto->getFecha() ?>" required>
        <?= isset($errors["fecha"]) ? i18n($errors["fecha"]) : "" ?><br>
    </div>

    <div class="campoformulario">
        <p><?= i18n("File") ?></p>
        <input type="file" name="fichero" accept="application/pdf" id="" value="">
        <p><a href="index.php?controller=gastos&amp;action=removeFile&amp;id=<?= $gasto->getId() ?>"><?= i18n("Remove File") ?> </a></p>
        <?= isset($errors["file"]) ? i18n($errors["username"]) : "" ?><br>
    </div>



    <div class="botonSubmit">
        <input type="submit" value="<?= i18n("Edit Spent") ?>">
    </div>
</form>

</div>

</html>

<?php $view->moveToFragment("css"); ?>
            <link rel="stylesheet" type="text/css" href="./view/CSS/formAddEdit.css">
 <?php $view->moveToDefaultFragment(); ?>