<?php
// file: index.php

define("DEFAULT_CONTROLLER", "expenses");

define("DEFAULT_ACTION", "index");

function run() {
	// invoke action!
	try {
		if (!isset($_GET["controller"])) {
			$_GET["controller"] = DEFAULT_CONTROLLER;
		}

		if (!isset($_GET["action"])) {
			$_GET["action"] = DEFAULT_ACTION;
		}

		$controller = loadController($_GET["controller"]);

		$actionName = $_GET["action"];
		$controller->$actionName();
	} catch(Exception $ex) {
		die("An exception occured!!!!!".$ex->getMessage());
	}
}

function loadController($controllerName) {
	$controllerClassName = getControllerClassName($controllerName);

	require_once(__DIR__."/controller/".$controllerClassName.".php");
	return new $controllerClassName();
}

function getControllerClassName($controllerName) {
	return strToUpper(substr($controllerName, 0, 1)).substr($controllerName, 1)."Controller";
}

run();

?>
