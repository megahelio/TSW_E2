<?php
// file: view/layouts/language_select_element.php
?>
<div id="languagechooser">
	<div class="dropdown">
   	 	<button class="dropbtn"><?=i18n("Lenguage")?> 
     		 <i class="fa fa-caret-down"></i>
    	</button>
		<div class="dropdown-content">
			<a href="index.php?controller=language&amp;action=change&amp;lang=es">
				<?= i18n("Spanish") ?>
			</a>
			<a href="index.php?controller=language&amp;action=change&amp;lang=en">
				<?= i18n("English") ?>
			</a>
		</div>
	</div>
</div>
