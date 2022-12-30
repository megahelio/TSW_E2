<?php

require_once(__DIR__ . "/../model/User.php");
require_once(__DIR__ . "/../model/UserMapper.php");
require_once(__DIR__ . "/BaseRest.php");

/**
 * Class UserRest
 *
 * It contains operations for adding and check users credentials.
 * Methods gives responses following Restful standards. Methods of this class
 * are intended to be mapped as callbacks using the URIDispatcher class.
 *
 */
class UserRest extends BaseRest
{
	private $userMapper;

	public function __construct()
	{
		parent::__construct();

		$this->userMapper = new UserMapper();
	}

	public function postUser($data)
	{
		$user = new User();
		$user->setUsername($data->username);
		$user->setEmail( $data->email);
		$user->setPassword($data->passwd);
		$user->setPasswordbis($data->passwdbis);


		print_r($data);
		print_r($user);

		try {
			$user->checkIsValidForRegister(); // if it fails, ValidationException
		} catch (ValidationException $ex) {
			// Get the errors array inside the exepction...
			$errors = $ex->getErrors();
			// And put it to the view as "errors" variable
			print_r($errors);
		}

		$user->setPassword(md5($data->passwd));
		// check if user exists in the database
		if (!$this->userMapper->usernameExists($data->username)) {

			// save the User object into the database
			$this->userMapper->save($user);
		}
	}

	public function login($data)
	{
		$currentLogged = parent::authenticateUser();
		if ($currentLogged->getUsername() != $data) {
			header($_SERVER['SERVER_PROTOCOL'] . ' 403 Forbidden');
			echo ("You are not authorized to login as anyone but you");
		} else {
			header($_SERVER['SERVER_PROTOCOL'] . ' 200 Ok');
			echo ("Hello " . $data);
		}
	}
}

// URI-MAPPING for this Rest endpoint
$userRest = new UserRest();
URIDispatcher::getInstance()
	->map("GET",	"/user/$1", array($userRest, "login"))
	->map("POST", "/user", array($userRest, "postUser"));
