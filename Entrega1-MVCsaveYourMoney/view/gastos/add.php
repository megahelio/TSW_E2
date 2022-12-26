<?php

require_once(__DIR__ . "/../../core/ViewManager.php");
require_once(__DIR__ . "/../../model/tipos.php");
$view = ViewManager::getInstance();
$errors = $view->getVariable("errors");
$gastos = $view->getVariable("gastos");

$currentuser = $view->getVariable("currentusername");

$view->setVariable("title", i18n("Add Spent"));

?>
<html>

<div class="formularioAddEdit">
    <h1><?= i18n("Add Spent") ?></h1>
    <form enctype="multipart/form-data" action="index.php?controller=gastos&amp;action=add" method="POST">


    <div class="campoformulario">
        <p><?= i18n("Description") ?></p>
        <input type="text" name="descripcion" id="">
        <?= isset($errors["descripcion"]) ? i18n($errors["descripcion"]) : "" ?><br>
    </div>

    <div class="campoformulario">
        <p><?= i18n("cost") ?></p>
        <input type="number" step="0.01" name="cantidad" id="" min="0"  required>
        <?= isset($errors["cantidad"]) ? i18n($errors["cantidad"]) : "" ?><br>
    </div>

    <div class="campoformulario">
        <p><?= i18n("type") ?></p>
        <select name="tipo" id="lang" required>
            <?php
            $tipos = (new Tipos)->tipos;
            foreach ($tipos as $tipo) :
                $str = "<option value=" . $tipo . ">" . i18n($tipo) . "</option>\n";
                echo $str;
            endforeach;
            ?>
        </select>
        <?= isset($errors["tipo"]) ? i18n($errors["tipo"]) : "" ?><br>
    </div>

    <div class="campoformulario">
        <p><?= i18n("date") ?></p>
        <input type="date" name="fecha" value="<?= date('Y-m-d') ?>" required>
        <?= isset($errors["fecha"]) ? i18n($errors["fecha"]) : "" ?><br>
    </div>

    <div class="campoformulario">
        <p><?= i18n("File") ?></p>
        <input type="file" name="fichero" accept="application/pdf" id="">
        <?= isset($errors["file"]) ? i18n($errors["username"]) : "" ?><br>
    </div>



    <div class="botonSubmit">
        <input type="submit" value="<?= i18n("Create Spent") ?>">
    </div>
    </form>

</div>

    </html>

<?php $view->moveToFragment("css"); ?>
            <link rel="stylesheet" type="text/css" href="./view/CSS/formAddEdit.css">
 <?php $view->moveToDefaultFragment(); ?>

