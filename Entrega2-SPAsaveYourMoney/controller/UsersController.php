<?php

require_once(__DIR__ . "/../core/ViewManager.php");
require_once(__DIR__ . "/../core/I18n.php");

require_once(__DIR__ . "/../model/User.php");
require_once(__DIR__ . "/../model/UserMapper.php");

require_once(__DIR__ . "/../controller/BaseController.php");

/**
 * Class UsersController
 *
 * Controller to login, logout and user registration
 *
 * @author lipido <lipido@gmail.com>
 */
class UsersController extends BaseController
{

	/**
	 * Reference to the UserMapper to interact
	 * with the database
	 *
	 * @var UserMapper
	 */
	private $userMapper;

	public function __construct()
	{
		parent::__construct();

		$this->userMapper = new UserMapper();

		// Users controller operates in a "welcome" layout
		// different to the "default" layout where the internal
		// menu is displayed
		$this->view->setLayout("welcome");
	}

	/**
	 * Action to login
	 *
	 * Logins a user checking its creedentials agains
	 * the database
	 *
	 * When called via GET, it shows the login form
	 * When called via POST, it tries to login
	 *
	 * The expected HTTP parameters are:
	 * <ul>
	 * <li>login: The username (via HTTP POST)</li>
	 * <li>passwd: The password (via HTTP POST)</li>
	 * </ul>
	 *
	 * The views are:
	 * <ul>
	 * <li>posts/login: If this action is reached via HTTP GET (via include)</li>
	 * <li>posts/index: If login succeds (via redirect)</li>
	 * <li>users/login: If validation fails (via include). Includes these view variables:</li>
	 * <ul>
	 *	<li>errors: Array including validation errors</li>
	 * </ul>
	 * </ul>
	 *
	 * @return void
	 */
	public function login()
	{

		if (isset($_POST["username"]) || (isset($_COOKIE["usr_SYM"], $_COOKIE["pass_SYM"]))) { // reaching via HTTP Post...

			//	print_r($_POST);
			$duracionCookie = time() + 60 * 60 * 24 * 30; //30 dias en segundos
			$username = isset($_POST["username"], $_POST["passwd"]) ? $_POST["username"] : $_COOKIE["usr_SYM"];
			$password = isset($_POST["username"], $_POST["passwd"]) ? md5($_POST["passwd"]) : $_COOKIE["pass_SYM"];

			//process login form
			if (
				$this->userMapper->isValidUser($username, $password) or
				(strtotime("now") >= strtotime($this->userMapper->readLastLoginDate($username) . "+1 month") and
					isset($_COOKIE["usr_SYM"], $_COOKIE["pass_SYM"])and 
					$_COOKIE["pass_SYM"]==md5($this->userMapper->getPassByUsername($_COOKIE["usr_SYM"])))
			) {

				$_SESSION["currentuser"] = $username;
				$this->userMapper->editLastLoginDate($username, date("Y-m-d", strtotime("now")));

				if (isset($_POST["remember"]) and $_POST["remember"] == "on") {
					setcookie("usr_SYM", $username, $duracionCookie);
					//la contraseña debería encriptarse pero bueno
					setcookie("pass_SYM", $password, $duracionCookie);
				}

				// send user to the restricted area (HTTP 302 code)
				$this->view->redirect("gastos", "index");
			} else {
				$errors = array();
				$errors["general"] = "Username is not valid";
				$this->view->setVariable("errors", $errors);
			}
		}

		// render the view (/view/users/login.php)
		$this->view->render("users", "login");
	}


	/**
	 * Action to register
	 *
	 * When called via GET, it shows the register form.
	 * When called via POST, it tries to add the user
	 * to the database.
	 *
	 * The expected HTTP parameters are:
	 * <ul>
	 * <li>login: The username (via HTTP POST)</li>
	 * <li>passwd: The password (via HTTP POST)</li>
	 * </ul>
	 *
	 * The views are:
	 * <ul>
	 * <li>users/register: If this action is reached via HTTP GET (via include)</li>
	 * <li>users/login: If login succeds (via redirect)</li>
	 * <li>users/register: If validation fails (via include). Includes these view variables:</li>
	 * <ul>
	 *	<li>user: The current User instance, empty or being added
	 *	(but not validated)</li>
	 *	<li>errors: Array including validation errors</li>
	 * </ul>
	 * </ul>
	 *
	 * @return void
	 */
	public function register()
	{

		$user = new User();

		if (isset($_POST["username"])) { // reaching via HTTP Post...

			// populate the User object with data form the form
			$user->setUsername($_POST["username"]);
			$user->setEmail($_POST["email"]);
			$user->setPassword($_POST["passwd"]);
			$user->setPasswordbis($_POST["passwdbis"]);

			try {
				$user->checkIsValidForRegister(); // if it fails, ValidationException
				$user->setPassword(md5($_POST["passwd"]));
				// check if user exists in the database
				if (!$this->userMapper->usernameExists($_POST["username"])) {

					// save the User object into the database
					$this->userMapper->save($user);

					// POST-REDIRECT-GET
					// Everything OK, we will redirect the user to the list of posts
					// We want to see a message after redirection, so we establish
					// a "flash" message (which is simply a Session variable) to be
					// get in the view after redirection.
					$this->view->setFlash(sprintf("Username  \"%s\" successfully added. Welcome", $user->getUsername()));

					// perform the redirection. More or less:
					// header("Location: index.php?controller=users&action=login")
					// die();
					$_SESSION["currentuser"] = $user->getUsername();
					$this->view->redirect("gastos", "index");
				} else {
					$errors = array();
					$errors["username"] = "Username already exists";
					$this->view->setVariable("errors", $errors);
				}
			} catch (ValidationException $ex) {
				// Get the errors array inside the exepction...
				$errors = $ex->getErrors();
				// And put it to the view as "errors" variable
				$this->view->setVariable("errors", $errors);
			}
		}

		// Put the User object visible to the view
		$this->view->setVariable("user", $user);

		// render the view (/view/users/register.php)
		$this->view->render("users", "register");
	}

	public function delete()
	{

		print_r($_REQUEST);

		$username = $_REQUEST["username"];
		echo ("\n  " . $username);

		if ($username != $this->currentUser->getUsername()) {
			//no puedo por lo que sea
			$this->view->setFlash(i18n("Failed to delete account"));
			$this->view->redirect("gastos", "index");
		}


		$this->userMapper->delete($username);
		//como la clave foranea de gasto es "ON DELETE CASCADE" se eliminan los gastos asociados al usuario automaticamente
		$this->view->setFlash(i18n("Account successfully deleted."));


		$this->view->redirect("users", "index");
	}

	//Funcionalidad desplazada al layout default.php

	// public function confirmUserDelete()
	// {
	// 	header('Content-type: application/javascript');
	// 	echo "function confirmUserDelete() {
    //      if (confirm(",i18n("Confirm Delete ALL account"), ")) window.location.href = \"index.php?controller=users&action=delete&username=", $this->currentUser->getUsername(), "\";}\n";
	// }
	public function index()
	{
		$this->view->render("users", "index");
	}

	/**
	 * Action to logout
	 *
	 * This action should be called via GET
	 *
	 * No HTTP parameters are needed.
	 *
	 * The views are:
	 * <ul>
	 * <li>users/login (via redirect)</li>
	 * </ul>
	 *
	 * @return void
	 */
	public function logout()
	{
		session_destroy();
		if (isset($_COOKIE["usr_SYM"], $_COOKIE["pass_SYM"])) {
			setcookie("usr_SYM", "", -1);
			setcookie("pass_SYM", "", -1);
		}

		// perform a redirection. More or less:
		// header("Location: index.php?controller=users&action=login")
		// die();
		$this->view->redirect("users", "login");
	}
}
