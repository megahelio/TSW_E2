<?php

require_once(__DIR__ . "/../../core/ViewManager.php");
$view = ViewManager::getInstance();
$gastos = $view->getVariable("gastos");

$currentuser = $view->getVariable("currentusername");

$view->setVariable("title", "Gastos");
//i18n()
?>



<html>

<body>
    <div class="w3-container">
        <h1>Gastos</h1>
        <? print_r($gastos) ?>
        <a href="index.php?controller=gastos&amp;action=add">
            <img src="../view/Imgs/mas.png">
        </a>
        <a href="index.php?controller=gastos&amp;action=DownloadCSV">
            <img src="../view/Imgs/descargar.png">
        </a>

        <div class="tabla">
            <div class="w3-responsive">
                <table class="w3-table-all w3-card-4">
                    <thead>
                        <tr class="headerTabla">
                            <th><?= i18n("Description") ?></th>
                            <th><?= i18n("cost") ?></th>
                            <th><?= i18n("date") ?></th>
                            <th><?= i18n("type") ?></th>
                            <th><?= i18n("File") ?></th>
                            <th><?= i18n("Options") ?></th>
                        </tr>
                    </thead>
                    <?php foreach ($gastos as $gasto) : ?>
                        <tr class="colorLetraTabla">
                            <td>
                                <?php echo $gasto["descripcion"] ?>
                            </td>
                            <td>
                                <?= $gasto["cantidad"] ?>
                            </td>
                            <td>
                                <?php echo $gasto["fecha"] ?>
                            </td>
                            <td>
                                <?= i18n($gasto["tipo"])  ?>
                            </td>
                            <td>
                                <a href="<?= "uploads/" . $gasto["fichero"] ?>" target="blank"><?= $gasto["fichero"] ?></a>
                            </td>

                            <td>&nbsp;
                                <a href="index.php?controller=gastos&amp;action=edit&amp;id=<?= $gasto["id"] ?>">
                                    <img src="../view/Imgs/editar.png">
                                </a>
                                <a onclick=confirmSpendDelete(<?=$gasto["id"]?>)>
                                    <img src="../view/Imgs/3221845.png">
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                </table>
            </div>
        </div>
    </div>
</body>

</html>


<?php $view->moveToFragment("css"); ?>
<link rel="stylesheet" type="text/css" href="https://www.w3schools.com/w3css/4/w3.css">
<link rel="stylesheet" type="text/css" href="./view/CSS/listarGastos.css">

<!-- Idealmente esta funcion JS debería estar en el controlador y no en la vista -->
<?php $view->moveToFragment("javascript"); ?>
<script>
    function confirmSpendDelete(id) {
        if (confirm("<?= i18n("Confirm Delete") ?>")) {
            window.location.href = "index.php?controller=gastos&action=delete&id="+id;
        }
    }

</script>
<?php $view->moveToDefaultFragment(); ?>