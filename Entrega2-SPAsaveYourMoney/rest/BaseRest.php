<?php
require_once(__DIR__ . "/../model/User.php");
require_once(__DIR__ . "/../model/UserMapper.php");

/**
 * Class BaseRest
 *
 * Superclass for Rest endpoints
 *
 * It simply contains a method to authenticate users via HTTP Basic Auth against
 * the User database via UserMapper.
 *
 * @author lipido <lipido@gmail.com>
 */
class BaseRest
{
	public function __construct()
	{
	}

	/**
	 * Authenticates the current request. If the request does not contain
	 * auth credentials, it will generate a 401 response code and end PHP processing
	 * If the request contain credentials, it will be checked against the database.
	 * If the credentials are ok, it will return the User object just logged. If the
	 * credentials are invalid, it will generate a 401 code as well and end PHP
	 * processing.
	 * 
	 * $_SERVER['PHP_AUTH_USER'] y $_SERVER['PHP_AUTH_PW'] se reciben como el usuario/contraseña
	 * que se inserta en la cabecera de autentificación.	
	 * 
	 * @param BOOL $md5 -> si es verdadero se hace el md5 de la clave antes de mandarla a la base de datos
	 * 						si es falso no se hace el md5 (se entiende que se recibe la clave en md5)
	 *
	 * @return User the user just authenticated.
	 */
	public function authenticateUser($md5)
	{

		if (!isset($_SERVER['PHP_AUTH_USER'])) {
			header($_SERVER['SERVER_PROTOCOL'] . ' 401 Unauthorized');
			header('WWW-Authenticate: Basic realm="Rest API of MVCBLOG"');
			die('This operation requires authentication');
		} else {
			$userMapper = new UserMapper();
			$pass = $_SERVER['PHP_AUTH_PW'];
			if ($md5) {
				$pass = md5($pass);
			}
			if ($userMapper->isValidUser(
				$_SERVER['PHP_AUTH_USER'],
				//en el mysql tenemos el hash md5
				$pass
			)) {

				return new User($_SERVER['PHP_AUTH_USER']);
			} else {
				header($_SERVER['SERVER_PROTOCOL'] . ' 401 Unauthorized');
				header('WWW-Authenticate: Basic realm="Rest API of MVCBLOG"');

				die('The username/password is not valid');
			}
		}
	}
}
