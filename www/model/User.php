<?php
// file: model/User.php

require_once(__DIR__."/../core/ValidationException.php");

class User {

	private $username;
	private $passwd;
	private $email;

	public function __construct($username=NULL, $passwd=NULL , $email=NULL) {
		$this->username = $username;
		$this->passwd = $passwd;
		$this->email = $email;
	}

	public function getUsername() {
		return $this->username;
	}

	public function setUsername($username) {
		$this->username = $username;
	}
	
	public function getPasswd() {
		return $this->passwd;
	}

	public function setPassword($passwd) {
		$this->passwd = $passwd;
	}

	public function getEmail() {
		return $this->email;
	}

	public function setEmail($email) {
		$this->email = $email;
	}

	public function checkIsValidForRegister() {
		$errors = array();
		if (strlen($this->username) < 5) {
			$errors["username"] = "El nombre de usuario debe más de 5 caracteres";

		}
		if (strlen($this->username) > 20) {
			$errors["username"] = "El nombre de usuario debe ser menor de 20 caracteres";

		}
		if (strlen($this->passwd) < 5) {
			$errors["passwd"] = "La contraseña debe ser mayor a 5 caracteres";
		}

		if (strlen($this->passwd) > 200) {
			$errors["passwd"] = "Contraseña demasiado larga";
		}

		if (strlen($this->email) < 5) {
			$errors["email"] = "Email demasiado corto";
		}
		if (strlen($this->email) > 200) {
			$errors["email"] = "Email demasiado largo";
		}
		if (sizeof($errors)>0){
			throw new ValidationException($errors, "Usuario no válido");
		}
	}
}
