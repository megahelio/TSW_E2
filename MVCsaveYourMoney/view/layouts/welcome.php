<?php
// file: view/layouts/welcome.php

$view = ViewManager::getInstance();

?>
<!DOCTYPE html>
<html>

<head>
	<title><?= $view->getVariable("title", "no title") ?></title>
	<meta charset="utf-8">
	<link rel="stylesheet" href="./view/CSS/headerFooter.css" type="text/css">
	
	<?= $view->getFragment("css") ?>
	<?= $view->getFragment("javascript") ?>
	
</head>

<body>
	<header>
		<div class="header">
			<?php
			include(__DIR__ . "/linksWelcome.php");
			?>
			<?php
			include(__DIR__ . "/language_select_element.php");
			?>

		</div>
	</header>
	<main>
		<!-- flash message -->
		<div id="flash">
			<?= $view->popFlash() ?>
		</div>
		<?= $view->getFragment(ViewManager::DEFAULT_FRAGMENT) ?>
	</main>
	<footer>

	</footer>
</body>

</html>