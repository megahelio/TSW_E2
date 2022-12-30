<?php
//file: controller/ExpenseController.php

require_once(__DIR__."/../model/Expenses.php");
require_once(__DIR__."/../model/ExpensesMapper.php");
require_once(__DIR__."/../model/User.php");

require_once(__DIR__."/../core/ViewManager.php");
require_once(__DIR__."/../controller/BaseController.php");

class ExpensesController extends BaseController {

	private $expenseMapper;

	public function __construct() {
		parent::__construct();

		$this->expenseMapper = new ExpensesMapper();
	}

	public function index() {
		if (!isset($this->currentUser)) {
			$this->view->render("layouts", "inicio");
		}else{
			$expenses = $this->expenseMapper->findAll();
	
			$this->view->setVariable("expenses", $expenses);
	
			$filtro = array();
			if(!isset($_GET["fechaInicio"]) || !isset($_GET["fechaFin"])){
				$_GET["fechaFin"] = date("Y-m-d");
				$date = new DateTime();
				$yearagoDate = $date->modify("-12 months");
				$_GET["fechaInicio"] = $date->format("Y-m-d");
				$filtro = array("COMBUSTIBLE","ALIMENTACION", "COMUNICACIONES", "SUMINISTRO", "OCIO");
			}
				$formatedData = $this->expenseMapper->getOrderedData($_GET["fechaInicio"],$_GET["fechaFin"]);
				if(isset($_GET["combustible"])) array_push($filtro,strtoupper("combustible"));
				if(isset($_GET["alimentacion"])) array_push($filtro,strtoupper("alimentacion"));
				if(isset($_GET["comunicaciones"])) array_push($filtro,strtoupper("comunicaciones"));
				if(isset($_GET["suministro"])) array_push($filtro,strtoupper("suministro"));
				if(isset($_GET["ocio"])) array_push($filtro,strtoupper("ocio"));
				$graphData = array();
				foreach($formatedData as $tograph){
					if($tograph[3] == $this->currentUser){
						if(in_array($tograph[0],$filtro)){
							array_push($graphData,$tograph);
						}
					}
				}
	
				$this->view->setVariable("filtro", $filtro);
				$this->view->setVariable("graphData", $graphData);
				$this->view->setVariable("initDate", $_GET["fechaInicio"]);
				$this->view->setVariable("finishDate", $_GET["fechaFin"]);
				$this->view->render("expenses", "index");
		}
	}

	public function inicio() {

		
		$this->view->render("layouts", "inicio");
		
	}


	public function crud(){
		if (!isset($this->currentUser)) {
			throw new Exception(i18n("No existe sesión. Ver los gastos requiere estar logeado"));
		}

		$expense = $this->expenseMapper->findAll();

		$this->view->setVariable("expenses", $expense);

		$this->view->render("expenses", "crud");

	}

	public function add() {
		if (!isset($this->currentUser)) {
			throw new Exception(i18n("No existe sesión. Añadir gastos requiere estar logeado"));
		}
		$expense = new Expenses();
		if (isset($_POST["submit"])) { 
			$expense->setTipo($_POST["type_exp"]);
			$expense->setDate($_POST["date_exp"]);
			$expense->setAmount($_POST["amount"]);
			$expense->setDescription($_POST["description_exp"]);
			$expense->setFile("");
			
			$expense->setOwner($this->currentUser);
			
			try {
				$expense->checkIsValidForCreate(); // if it fails, ValidationException
				if (is_uploaded_file($_FILES["file_exp"]["tmp_name"])) {
					if(!is_dir("uploads/".$this->currentUser->getUsername())){
						mkdir("uploads/".$this->currentUser->getUsername(), 0777);
					}
                    $ruta = "uploads/".$this->currentUser->getUsername()."/";
                    $archivo = trim($_FILES["file_exp"]["name"]);
                    $archivo = str_replace(" ", "-", $archivo);
                    $archivo = str_replace("_", "-", $archivo);
                    $idunic = uniqid();
                    $rutaCompleta = $ruta . $idunic . "_" . $archivo;

                    $expense->setFile($idunic . "_" . $archivo);
                    $expense->checkIsValidForCreate(); 
                    move_uploaded_file($_FILES["file_exp"]["tmp_name"], $rutaCompleta);
				}	
				$this->expenseMapper->save($expense);
				
				$__message = i18n("Gasto añadido exitosamente.");
				$this->view->setFlash(sprintf($__message));

			}catch(ValidationException $ex) {
				$expenses = $this->expenseMapper->findAll();

				$this->view->setVariable("expenses", $expenses);

				$errors = $ex->getErrors();
				$this->view->setVariable("errors", $errors, true);
					
			}
		}

		$this->view->setVariable("newExpense", $expense);

		$this->view->redirect("expenses", "crud");

	}

	public function edit() {
		
		if (!isset($_POST["id_exp"])) {
			throw new Exception(i18n("El id de gasto es obligatorio"));
		}

		if (!isset($this->currentUser)) {
			throw new Exception(i18n("No existe sesión. Editar gastos requiere estar logeado"));
		}


		$expenseId = $_POST["id_exp"];
		$expense = $this->expenseMapper->findById($expenseId);

		if ($expense == NULL) {
			throw new Exception(sprintf(i18n("No existe el gasto con el id: %d"),$expenseId));
		}

		if ($expense->getOwner() != $this->currentUser) {
			throw new Exception(i18n("El usuario logeado no es el propietario del gasto"));
		}

		if (isset($_POST["type_exp"])) { 
			
			$expense->setTipo($_POST["type_exp"]);
			$expense->setDate($_POST["date_exp"]);
			$expense->setAmount($_POST["amount"]);
			$expense->setDescription($_POST["description_exp"]);
			
			try {
				$expense->checkIsValidForUpdate();
				
				if (is_uploaded_file($_FILES["file_exp"]["tmp_name"])) {
					if(!is_dir("uploads/".$this->currentUser->getUsername())){
						mkdir("uploads/".$this->currentUser->getUsername(), 0777);
					}
                    $ruta = "uploads/".$this->currentUser->getUsername()."/";
                    $archivo = trim($_FILES["file_exp"]["name"]);
                    $archivo = str_replace(" ", "-", $archivo);
                    $archivo = str_replace("_", "-", $archivo);
                    $idunic = uniqid();
                    $rutaCompleta = $ruta . $idunic . "_" . $archivo;
					$ficheroAnterior = $expense->getFile();
					$expense->setFile($idunic . "_" . $archivo);
					$expense->checkIsValidForUpdate();

                    if(move_uploaded_file($_FILES["file_exp"]["tmp_name"], $rutaCompleta)){
                        $file_delete = "uploads/".$this->currentUser->getUsername()."/".$ficheroAnterior;
                        if(file_exists($file_delete)){
                            unlink($file_delete);
                        }
                    }
                }

				$this->expenseMapper->update($expense);

				$__editMessage = i18n("Gasto modificado exitosamente.");
				$this->view->setFlash(sprintf($__editMessage));

				$this->view->redirect("expenses", "crud");

			}catch(ValidationException $ex) {
				$errors = $ex->getErrors();
				$this->view->setVariable("errors", $errors,true);
			}
		}

		$this->view->setVariable("expenses", $expense);

		$this->view->redirect("expenses", "crud");
	}

	public function delete() {
		if (!isset($_POST["id"])) {
			throw new Exception(i18n("id obligatorio"));
		}
		if (!isset($this->currentUser)) {
			throw new Exception(i18n("No existe sesión. Eliminar gastos requiere estar logeado"));
		}
		
		$expenseId = $_REQUEST["id"];
		$expense = $this->expenseMapper->findById($expenseId);

		if ($expense == NULL) {
			throw new Exception(sprintf(i18n("No existe el gasto con el id: %d"),$expenseId));
		}

		if ($expense->getOwner() != $this->currentUser) {
			throw new Exception(i18n("El usuario logeado no es el propietario del gasto"));
		}

		$fichero = $expense->getFile();
		$file_delete = "uploads/".$this->currentUser->getUsername()."/".$fichero;
        if(file_exists($file_delete)){
            unlink($file_delete);
        }
		$this->expenseMapper->delete($expense);

		$this->view->setFlash(i18n("Gasto eliminado exitosamente."));

		$this->view->redirect("expenses", "crud");

	}


	public function createCSV(){
		if (!isset($this->currentUser)) {
			throw new Exception(i18n("No existe sesión. Descargar gastos requiere estar logeado"));
		}

		$fp = fopen('php://output', 'w');
		$actualDatecsv = date("Y-m-d");
		$fname = "expenses_".$actualDatecsv;
		header('Content-Type: text/csv');
		header('Content-Disposition: attachment; filename='.$fname);
		header('Pragma: no-cache');
		header('Expires: 0');

		$result = $this->expenseMapper->findAll();
		if (!$result) die("Couldn't fetch records");
		$headers = array("id","type_exp","date_exp","amount","description","file_exp");

		if ($fp && $result) {
			fputcsv($fp, $headers);
			foreach ($result as $row) {
				if($row->getOwner() == $this->currentUser){
					fputcsv($fp, array($row->getId(),$row->getTipo(),$row->getDate(),$row->getAmount(),$row->getDescription(),$row->getFile(),));
				}
			}
		}
	}

}
