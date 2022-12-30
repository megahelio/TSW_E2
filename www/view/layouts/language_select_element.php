<?php
// file: view/layouts/language_select_element.php
?>
<ul id="languagechooser" >
	<li><a href="index.php?controller=language&amp;action=change&amp;lang=es">
		<span class="bandera-texto"><img src="img/spain.png" alt="imgSpainFlag"><p><?= i18n("Español") ?></p></span>
	</a></li>
	<li><a href="index.php?controller=language&amp;action=change&amp;lang=en">
		<span class="bandera-texto"><img src="img/united-kingdom.png" alt="imgEnglishFlag"><p><?= i18n("Inglés") ?></p></span>
	</a></li>
</ul>

