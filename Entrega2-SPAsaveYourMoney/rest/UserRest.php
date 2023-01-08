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

	/**
	 * Guarda el usuario que se pasa como parametro.
	 * 
	 * @throws  400 Bad request -> Error en la validación del usuario
	 * @throws  400 Bad request -> No incluye algún campo obligatorio
	 * @throws  500 Internal Server Error -> UserMapper no confirma creación
	 * 
	 * @param $data usuario a guardar
	 * 
	 * @return 200 OK -> se crea el usuario correctamente
	 */
	public function createUser($data)
	{
		$user = new User();

		/*En caso de que no se incluya algún elemento obligatorio lo registraremos
        y enviaremos una bad request con el feedback*/
		$incluyeCamposObligatorios = true;
		$incluyeCamposObligatoriosErrorLog = "[";

		//Campo obligatorio
		if (isset($data->username)) {
			$user->setUsername($data->username);
		} else {
			//Activamos flag de que un elemento obligatorio no existe
			$incluyeCamposObligatorios = false;
			//Incluimos el aviso en el log
			$incluyeCamposObligatoriosErrorLog = $incluyeCamposObligatoriosErrorLog . "username ";
		}
		if (isset($data->email)) {
			$user->setEmail($data->email);
		} else {
			//Activamos flag de que un elemento obligatorio no existe
			$incluyeCamposObligatorios = false;
			//Incluimos el aviso en el log
			$incluyeCamposObligatoriosErrorLog = $incluyeCamposObligatoriosErrorLog . "email ";
		}
		if (isset($data->passwd)) {
			$user->setPassword($data->passwd);
		} else {
			//Activamos flag de que un elemento obligatorio no existe
			$incluyeCamposObligatorios = false;
			//Incluimos el aviso en el log
			$incluyeCamposObligatoriosErrorLog = $incluyeCamposObligatoriosErrorLog . "passwd ";
		}
		if (isset($data->passwdbis)) {
			$user->setPasswordbis($data->passwdbis);
		} else {
			//Activamos flag de que un elemento obligatorio no existe
			$incluyeCamposObligatorios = false;
			//Incluimos el aviso en el log
			$incluyeCamposObligatoriosErrorLog = $incluyeCamposObligatoriosErrorLog . "passwdbis ";
		}
		//Respondemos Bad request al no encontrar elemntos obligatorios
		if (!$incluyeCamposObligatorios) {
			header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad request');
			die($incluyeCamposObligatoriosErrorLog . "] not found");
		}

		try {
			$user->checkIsValidForRegister(); // if it fails, ValidationException

			/*No podemos validar las condiciones de contraseña valida si nos llega una contraseña hasheada por lo que debemos hashearla despues de validar en back.*/
			$user->setPassword(md5($data->passwd));

			// check if user exists in the database
			if (!$this->userMapper->usernameExists($data->username)) {

				// save the User object into the database
				$this->userMapper->save($user);
				//Comprobamos que el usuario fué creado
				$userSaved = $this->userMapper->getUserByUsername($user->getUsername());
				if (isset($userSaved)) {
					header($_SERVER['SERVER_PROTOCOL'] . ' 200 Ok');
					header('Content-Type: application/json');
					echo (json_encode(array(
						"username" => $userSaved->getUsername(),
						"email" => $userSaved->getEmail(),
						"passwd" => $userSaved->getPassword(),
						"lastLogging" => $userSaved->getLastLogging()
					)));
				} else {
					header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error');
					die("User might be not created by a server error.");
				}
			} else {
				$errors["username"] = "Username already exists";
				throw new ValidationException($errors, "user is not valid");
			}
		} catch (ValidationException $e) {
			header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad request');
			header('Content-Type: application/json');
			die(json_encode($e->getErrors()));
		}
	}
	/**
	 * Modifica la información del usuario logeado
	 * 
	 * @throws  401 Unauthorized -> No hay usuario logeado
	 * @throws  500 Internal Server Error -> UserMapper no devuelve el usuario actualizado
	 * @throws 400 Bad request -> La solicitud no contiene campos obligatorios o son invalidos
	 * 
	 * @return 200 OK + json información del gasto -> se actualiza el usuario correctamente
	 */
	public function updateUser($data)
	{
		$currentUser = parent::authenticateUser(true)->getUsername();
		$userUpdate = new User();

		$user = $this->userMapper->getUserByUsername($currentUser);
		$userUpdate->setUsername($currentUser);

		//Campo obligatorio
		if (isset($data->email)) {
			$userUpdate->setEmail($data->email);
		} else {
			$userUpdate->setEmail($user->getEmail());
		}
		$needMD5 = false;
		if (isset($data->passwd) && isset($data->passwdbis)) {
			$userUpdate->setPassword($data->passwd);
			$userUpdate->setPasswordbis($data->passwdbis);
			$needMD5 = true;
		} else {
			$userUpdate->setPassword($user->getPassword());
			$userUpdate->setPasswordbis($user->getPassword());
		}

		//El formato de la fecha 1999-12-31 funciona
		if (isset($data->lastLogging)) {
			$userUpdate->setLastLogging($data->lastLogging);
		} else {
			$userUpdate->setLastLogging($user->getLastLogging());
		}
		try {
			$userUpdate->checkIsValidForRegister(); // if it fails, ValidationException
			if ($needMD5) {
				$userUpdate->setPassword(md5($data->passwd));
			}
			$this->userMapper->update($userUpdate);
			$userSaved = $this->userMapper->getUserByUsername($currentUser);
			if (isset($userSaved)) {
				header($_SERVER['SERVER_PROTOCOL'] . ' 200 Ok');
				header('Content-Type: application/json');
				echo (json_encode(array(
					"username" => $userSaved->getUsername(),
					"email" => $userSaved->getEmail(),
					"passwd" => $userSaved->getPassword(),
					"lastLogging" => $userSaved->getLastLogging()
				)));
				//si el gasto no se actualiza correctamente...
			} else {
				header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error');
				die("User might be not updated by a server error.");
			}
		} catch (ValidationException $e) {
			header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad request');
			header('Content-Type: application/json');
			die(json_encode($e->getErrors()));
		}
	}

	/**
	 * Elimina el usuario con el username que se pasa como parámetro
	 * En la base de datos existe una restricción de clave foranea entre el usuario y su gasto definida "ON DELETE CASCADE" por lo que al eliminar un usuario de la base de datos borramos todos sus gastos
	 * 
	 * @throws  401 Unauthorized -> no hay usuario logeado
	 * @throws 500 Internal Server Error -> UserMapper encontró un error en la eliminación
	 * 
	 * @param string  username
	 * 
	 * @return 200 OK -> Se elimina correctamente el usuario
	 */

	public function deleteUser()
	{
		$currentUser = parent::authenticateUser(false)->getUsername();

		$this->userMapper->delete($currentUser);

		if (!$this->userMapper->usernameExists($currentUser)) {
			header($_SERVER['SERVER_PROTOCOL'] . ' 200 OK');
			die("User: " . $currentUser . " deleted successfully");
		} else {
			header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error');
			die("User might be not deleted by a server error.");
		}
	}

	/**
	 * Verifica un par de credenciales (WWW-Authenticate: Basic) dado (Pass En MD5)
	 * 
	 * @throws 401 Unauthorized -> Validación Incorrecta
	 * 
	 * @return 200 OK -> Validación Correcta
	 * 
	 */
	public function loginMD5()
	{
		$currentUser = parent::authenticateUser(false)->getUsername();
		header($_SERVER['SERVER_PROTOCOL'] . ' 200 OK');
		die("Hello " . $currentUser);
	}



	/**
	 * Verifica un par de credenciales (WWW-Authenticate: Basic) dado
	 * y sobreescribe lastLoginDate con la fecha actual
	 * 
	 * @throws 401 Unauthorized -> Validación Incorrecta
	 * 
	 * @return 200 OK -> Validación Correcta
	 * 
	 */
	public function loginWithRemember()
	{
		$currentUser = parent::authenticateUser(false)->getUsername();
		$currentDate = getdate();
		$currentDateStringed = $currentDate["year"] . $currentDate["ymon"] . $currentDate["mday"];
		print($currentDateStringed);
		$this->userMapper->editLastLoginDate($currentUser, $currentDateStringed);
		header($_SERVER['SERVER_PROTOCOL'] . ' 200 OK');
		die("Hello " . $currentUser);
	}
}

// URI-MAPPING for this Rest endpoint
$userRest = new UserRest();
URIDispatcher::getInstance()
	->map("GET",	"/user/loginMD5",		array($userRest, "loginMD5"))
	->map("GET",	"/user/loginWithRemember",		array($userRest, "loginWithRemember"))
	->map("POST",	"/user",		array($userRest, "createUser"))
	->map("PUT",	"/user",		array($userRest, "updateUser"))
	->map("DELETE",	"/user",		array($userRest, "deleteUser"));
