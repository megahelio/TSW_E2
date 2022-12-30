<?php

require_once(__DIR__."/../core/ViewManager.php");
require_once(__DIR__."/../core/I18n.php");

require_once(__DIR__."/../model/User.php");
require_once(__DIR__."/../model/UserMapper.php");

require_once(__DIR__."/../controller/BaseController.php");

/**
* Class UsersController
*
* Controller to login, logout and user registration
*
* @author lipido <lipido@gmail.com>
*/
class UsersController extends BaseController {

	private $userMapper;

	public function __construct() {
		parent::__construct();

		$this->userMapper = new UserMapper();
	}

	public function login() {
		if (isset($_POST["username"])){ 
			
			if ($this->userMapper->isValidUser($_POST["username"],$_POST["password"])) {

				$_SESSION["currentuser"]=$_POST["username"];
				if(!strcmp($_POST["mantener-sesion"],"on")){
					setcookie("user",$_POST["username"],time() + (86400 * 30),"/");
				}

				$this->view->redirect("expenses", "index");

			}else{
				$errors = array();
				$errors["general"] = i18n("Usuario o contraseña incorrectos");
				$this->view->setVariable("errors", $errors, true);
				$this->view->redirect("users", "login");
			}
		}

		$this->view->render("users", "login");
	}

	public function register() {

		$user = new User();

		if (isset($_POST["username"])){ 

			$user->setUsername($_POST["username"]);
			$user->setPassword($_POST["password"]);
			$user->setEmail($_POST["email"]);

			try{
				$user->checkIsValidForRegister(); // if it fails, ValidationException

				if (!$this->userMapper->usernameExists($_POST["username"])){

					$this->userMapper->save($user);

					$this->view->setFlash(sprintf("Usuario %s añadido correctamente. Por favor, inicie sesión",$user->getUsername()));

					$this->view->redirect("users", "login");
				} else {
					$errors = array();
					$errors["username"] = i18n("Usuario ya existente");
					$this->view->setVariable("errors", $errors);

				}
			}catch(ValidationException $ex) {
				$errors = $ex->getErrors();
				$this->view->setVariable("errors", $errors,true);
				$this->view->redirect("users", "register");
			}
		}

		$this->view->setVariable("user", $user);

		$this->view->render("users", "register");

	}

	public function logout() {
		unset($_COOKIE["user"]);
		setcookie('user', null, -1, '/');
		session_destroy();
		$this->view->redirect("expenses", "inicio");
		
	}
	
	public function deleteProfile(){
		$currentUser = $this->view->getVariable("currentusername");
		if(is_dir("uploads/".$currentUser)){
			$filesDirectory = scandir("uploads/".$currentUser);
			if($filesDirectory != false){
				foreach($filesDirectory as $file){
					unlink("uploads/".$currentUser."/".$file);
				}
			}
			rmdir("uploads/".$currentUser);
		}
		unset($_COOKIE["user"]);
		setcookie('user', null, -1, '/');
		session_destroy();
		$this->userMapper->deleteUser($currentUser);
		$this->view->redirect("expenses", "inicio");
	}

}
