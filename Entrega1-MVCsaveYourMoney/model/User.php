<?php

require_once(__DIR__ . "/../core/ValidationException.php");


class User
{

	private $username;
	private $passwd;
	private $passwdbis;
	private $email;
	private $lastLogging;

	public function __construct($username = NULL, $email = NULL, $passwd = NULL, $passwdbis = NULL, $lastLogging = NULL)
	{
		$this->username = $username;
		$this->passwd = $passwd;
		$this->passwdbis = $passwdbis;
		$this->email = $email;
		$this->lastLogging = $lastLogging;
	}
	public function getUsername()
	{
		return $this->username;
	}


	public function setUsername($username)
	{
		$this->username = $username;
	}

	public function getPassword()
	{
		return $this->passwd;
	}

	public function setPassword($passwd)
	{
		$this->passwd = $passwd;
	}

	public function getPasswordbis()
	{
		return $this->passwdbis;
	}

	public function setPasswordbis($passwdbis)
	{
		$this->passwdbis = $passwdbis;
	}


	public function getEmail()
	{
		return $this->email;
	}

	public function setEmail($email)
	{
		$this->email = $email;
	}


	public function getLastLogging()
	{
		return $this->lastLogging;
	}


	public function setLastLogging($lastLogging)
	{
		$this->lastLogging = $lastLogging;
	}

	public function checkIsValidForRegister()
	{
		$errors = array();
		if (strlen($this->username) < 5) {
			$errors["username"] = "Username must be at least 5 characters length";
		}
		$username = $this->username;
		if (str_contains($username, '<') || str_contains($username, '>') || str_contains($username, '\\')) {
			$errors["username"] = "Username contains invalid characters";
		}

		$passwd = $this->passwd;
		if (strlen($passwd) < 5) {
			$errors["passwd"] = "Password must be at least 5 characters length";
		}
		if (str_contains($passwd, '<') || str_contains($passwd, '>') || str_contains($passwd, '\\')) {
			$errors["passwd"] = "password contains invalid characters";
		}

		if ($this->passwd != $this->passwdbis) {
			$errors["passwdbis"] = "Passwords must equals";
		}
		// Comprobaciones email TO-DO
		if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
			$errors["email"] = "Not valid email";
		}



		if (sizeof($errors) > 0) {
			throw new ValidationException($errors, "user is not valid");
		}
	}
}
