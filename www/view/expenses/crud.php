<?php
//file: view/gastos/index.php

require_once(__DIR__."/../../core/ViewManager.php");
$view = ViewManager::getInstance();

$expenses = $view->getVariable("expenses");
$currentuser = $view->getVariable("currentusername");

$view->setVariable("title", "Crud");
$errors = $view->getVariable("errors");

$totalExpenses = 0;

?>


<?= isset($errors["general"])?i18n($errors["general"]):"" ?>

<div class="contenedor" id="controlador-tabla">
    <button id="añadir-gasto" class="boton-controlador" onclick="mostrarBloque('contenedor-ventana-añadir')"><?=i18n("Añadir")?></button>
    <form method="POST" action="index.php?controller=expenses&amp;action=createCSV" id="downloadCRUD">
        <button type="submit" id="descargar" class="boton-controlador"><?=i18n("Descargar")?></button>
    </form>
</div>

<div id="contenedor-tabla" class="contenedor">
    <table id="tabla" >
        <tr>
            <th class="priority-2"><?=i18n("Tipo")?></th><th class="priority-3"><?=i18n("Fecha")?></th><th class="priority-1"><?=i18n("Cantidad")?></th><th class="priority-4"><?=i18n("Descripción")?></th><th class="priority-5"><?=i18n("Fichero")?></th><th></th>
        </tr>
        
        <?php foreach ($expenses as $expense): ?>
            <?php
            //show actions ONLY for the author of the post (if logged)
            if (isset($currentuser) && $currentuser == $expense->getOwner()->getUsername()): ?>
                <tr>
                    <td class="priority-2">
                        <a><?= i18n(htmlentities($expense->getTipo())) ?></a>
                    </td>
                    <td class="priority-3">
                        <?= htmlentities($expense->getDate()) ?>
                    </td>
                    <td class="priority-1">
                        <?= htmlentities($expense->getAmount()) ?>
                        <?php $totalExpenses += $expense->getAmount(); ?>
                    </td>
                    <td class="priority-4">
                        <?= htmlentities($expense->getDescription()) ?>
                    </td>
                    <td class="priority-5">
                    <?php if(strlen($expense->getFile()) > 0) {
                            $nombreSinId = explode("_",$expense->getFile())[1];
                        } else {
                            $nombreSinId = "";
                        }
                        ?>
                        <a href="/uploads/<?= $currentuser."/".htmlentities($expense->getFile()) ?>"
                        download="<?= htmlentities($nombreSinId) ?>">
                        <?= htmlentities($nombreSinId) ?></a>
                    </td>

                    <td class="td-boton">
                        <?php
                        // 'Edit Button'
                        ?>
                        <button class="boton-edit" 
                        onclick="mostrarBloque('contenedor-ventana-editar');
                        cargarEdit(<?= $expense->getId(); ?>, '<?= $expense->getTipo() ?>', '<?= $expense->getDate() ?>', <?= $expense->getAmount() ?>, '<?= $expense->getDescription() ?>', '<?= $expense->getFile() ?>');" 
                        ><?= i18n("Editar") ?></button>
                    
                        <?php
                        // 'Delete Button': show it as a button, but do POST in order to preserve
                        // the good semantic of HTTP
                        ?>
                        <button type="button" href="#" class="boton-eliminar" onclick="mostrarBloque('contenedor-eliminar-gasto-<?= $expense->getId(); ?>')">
                            <?= i18n("Delete") ?>
                        </button>

                        <div id="contenedor-eliminar-gasto-<?= $expense->getId(); ?>" class="contenedor-emergente">
                            <div id="eliminar-gasto" class="generico-eliminar">
                                <h3><?=i18n("¿Estas seguro de eliminar el gasto?")?></h3>
                                <div class="botones-eliminar-generico">
                                    <button class="boton-eliminar-generico" id="cancelar-eliminar-gasto" onclick="location.reload();"><?=i18n("Cancelar")?></button>
                                    <form method="POST" action="index.php?controller=expenses&amp;action=delete"
                                        id="delete_expenses_<?= $expense->getId(); ?>"> 
                                        <input type="text" name="id" value="<?= $expense->getId(); ?>" style="display:none;">
                                        <input type="submit" name="submit" class="boton-eliminar-generico aceptar-eliminar-generico" id="aceptar-eliminar-gasto" value="<?= i18n("Eliminar")?>"></input>
                                    </form>
                                </div>
                            </div>
                        </div>

                    </td>
                </tr>
            <?php endif; ?>
        <?php endforeach; ?>

        <tr>
            <td ><b><?=i18n("Total:")?></b></td><td><?= htmlentities($totalExpenses) ?></td><td colspan="5"></td>
        </tr>

    </table>
</div>
<div id="contenedor-ventana-añadir" class="contenedor-emergente" style="display:<?php echo((sizeof((array)$errors))>0 ? 'block' : 'none') ?>">
    <div id="ventana-añadir" class="contenedor ventana-emergente" >
        <div id="añadir-header" class="header-ventana-emergente">
            <h3><?=i18n("Añadir Gasto")?></h3>
        </div>

        <form id="fomulario-añadir" class="formulario-emergente" action="index.php?controller=expenses&amp;action=add" enctype="multipart/form-data" method="POST">
            <div class="seccion-emergente">
                <label for="tipo"><?=i18n("Tipo")?></label>
                <select name="type_exp" id="tipo">
                    <option selected="selected" value="COMBUSTIBLE"><?=i18n("Combustible")?></option>
                    <option value="ALIMENTACION"><?=i18n("Alimentacion")?></option>
                    <option value="COMUNICACIONES"><?=i18n("Comunicaciones")?></option>
                    <option value="SUMINISTRO"><?=i18n("Suministros")?></option>
                    <option value="OCIO"><?=i18n("Ocio")?></option>
                </select>
                <div class = 'texto-error'><?= isset($errors["type_exp"])?i18n($errors["type_exp"]):"" ?></div>
            </div>
            <div class="seccion-emergente">
                <label for="fecha"><?=i18n("Fecha")?></label>
                <input type="date" name="date_exp" id="fecha" value=<?=date("Y-m-d")?>>
                <div class = 'texto-error'><?= isset($errors["date_exp"])?i18n($errors["date_exp"]):"" ?></div>
            </div>
            <div class="seccion-emergente">
                <label for="cantidad"><?=i18n("Cantidad")?></label>
                <input type="number" name="amount" id="cantidad" value="0">
                <div class = 'texto-error'><?= isset($errors["amount"])?i18n($errors["amount"]):"" ?></div>
            </div>
            <div class="seccion-emergente">
                <label for="descripcion"><?=i18n("Descripción")?></label>
                <input type="text" name="description_exp" id="descripcion">
            </div>
            <div class = 'texto-error'><?= isset($errors["description"])?i18n($errors["description"]):"" ?></div>
            <div class="seccion-emergente">
                <label for="fichero"><?=i18n("Fichero")?></label>
                <div class="styled-file-select">
                        <input type="text" disabled placeholder="Choose file (jpg, png, pdf)" id="mostrar"/>
                        
                        <input type="file" name="file_exp" id="fichero" onchange="mostrarArchivo('mostrar','fichero')"/>
                </div>
                <div class = 'texto-error'><?= isset($errors["file_exp"])?i18n($errors["file_exp"]):"" ?></div>                
            </div>

            <div class="seccion-añadir margen-top">
                <button class="boton-añadir boton-eliminar" type="button" onclick="location.reload();"><?=i18n("Cerrar")?></button>
                <input class="boton-añadir" name="submit" id="enviar-añadir" type="submit" value="<?=i18n("Aceptar")?>"/>
            </div>

        </form>
    </div>
</div>

<div id="contenedor-ventana-editar" class="contenedor-emergente" style="display:none">
    <div id="ventana-editar" class="contenedor ventana-emergente">
        <div id="editar-header" class="header-ventana-emergente">
            <h3><?=i18n("Editar Gasto")?></h3>

        </div>
                            
        <form id="fomulario-editar" class="formulario-emergente" action="index.php?controller=expenses&amp;action=edit" enctype="multipart/form-data" method="POST">
        
            <input type="text" name="id_exp" id="id-editar" style="display:none;">
            <div class="seccion-emergente">
                <label for="tipo"><?=i18n("Tipo")?></label>
                <select name="type_exp" id="tipo-editar" >
                    <option value="ALIMENTACION"><?=i18n("Alimentación")?></option>
                    <option value="COMUNICACIONES"><?=i18n("Comunicaciones")?></option>
                    <option value="COMBUSTIBLE"><?=i18n("Combustible")?></option>
                    <option value="SUMINISTRO"><?=i18n("Suministros")?></option>
                    <option value="OCIO"><?=i18n("Ocio")?></option>
                </select>
            </div>
            <div class="seccion-emergente">
                <label for="fecha"><?=i18n("Fecha")?></label>
                <input type="date" name="date_exp" id="fecha-editar" >
            </div>
            <div class="seccion-emergente">
                <label for="cantidad"><?=i18n("Cantidad")?></label>
                <input type="number" name="amount" id="cantidad-editar">
            </div>
            <div class="seccion-emergente">
                <label for="descripcion"><?=i18n("Descripción")?></label>
                <input type="text" name="description_exp" id="descripcion-editar">
            </div>
            <div class="seccion-emergente">
                <label for="fichero"><?=i18n("Fichero")?></label>
                <div class="styled-file-select">
                        <input type="text" disabled placeholder="Choose file (jpg, png, pdf)" id="mostrar-editar"/>
                        
                        <input type="file" name="file_exp" id="fichero-editar" onchange="mostrarArchivo('mostrar-editar','fichero-editar')"/>
                </div>
                <div class = 'texto-error'><?= isset($errors["file_exp"])?i18n($errors["file_exp"]):"" ?></div> 
            </div>

            <div class="seccion-emergente margen-top">
                <button class="boton-editar boton-eliminar" type="button" onclick="location.reload();"><?=i18n("Cerrar")?></button>
                <button class="boton-añadir" id="enviar-editar" type="submit"><?=i18n("Editar")?></button>
            </div>

        </form>
    </div>
</div>



<?php $view->moveToFragment("css");?>
<link rel="stylesheet" href="css/CrudStyle.css" type="text/css">

<?php $view->moveToFragment("javascript");?>
<script>

    function mostrarArchivo(idMostrar ,idArchivo) {
        document.getElementById(idMostrar).value = document.getElementById(idArchivo).files[0].name;
    }
    
    function cargarEdit(id, tipo, date, amount, description, file){
        document.getElementById("id-editar").value = id;
        document.getElementById("tipo-editar").value = tipo;
        document.getElementById("fecha-editar").value = date;
        document.getElementById("cantidad-editar").value = amount;
        document.getElementById("descripcion-editar").value = description;
        if(file.length > 0){
            file = file.split("_")[1];
        }
        document.getElementById("mostrar-editar").value = file;

    }

</script>