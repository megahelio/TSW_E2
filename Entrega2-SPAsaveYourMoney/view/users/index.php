<?php
//file: view/users/index.php

require_once(__DIR__ . "/../../core/ViewManager.php");
$view = ViewManager::getInstance();
$view->setVariable("title", "Index");
?>

<div id="presentacion">
    <h1>Save Your Money</h1>
    
    <p><?=i18n("We help ordinary people to save money")?></p>
</div>

<?php $view->moveToFragment("css"); ?>
<link rel="stylesheet" href="./view/CSS/index.css">
<?php $view->moveToDefaultFragment(); ?>