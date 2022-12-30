<?php
// file: model/Expenses.php

require_once(__DIR__."/../core/ValidationException.php");

class Expenses {

	private $id;
	private $tipo;
	private $date;
	private $amount;
	private $description;
	private $file;
	private $owner;

	public function __construct($id=NULL, $tipo=NULL, $date=NULL, $amount=NULL, $description=NULL, $file=NULL, User $owner=NULL) {
		$this->id = $id;
		$this->tipo = $tipo;
		$this->date = $date;
		$this->amount = $amount;
		$this->description = $description;
		$this->file = $file;
		$this->owner = $owner;
	}

	public function getId() {
		return $this->id;
	}

	public function getTipo() {
		return $this->tipo;
	}

	public function setTipo($tipo) {
		$this->tipo = $tipo;
	}

	public function getDate() {
		return $this->date;
	}

	public function setDate($date) {
		$this->date = $date;
	}

	public function getAmount() {
		return $this->amount;
	}

	public function setAmount($amount) {
		$this->amount = $amount;
	}

	public function getDescription() {
		return $this->description;
	}

	public function setDescription($description) {
		$this->description = $description;
	}

	public function getFile() {
		return $this->file;
	}

	public function setFile($file) {
		$this->file = $file;
	}

	public function getOwner() {
		return $this->owner;
	}

	public function setOwner(User $owner) {
		$this->owner = $owner;
	}


	public function checkIsValidForCreate() {
		$errors = array();
		if (strlen(trim($this->tipo)) == 0 ) {
			$errors["type_exp"] = "El tipo es obligatorio";
		}
		if (strlen(trim($this->amount)) == 0 ) {
			$errors["amount"] = "La cantidad es obligatoria";
		}
		if($this->amount < 1){
			$errors["amount"] = "La cantidad debe ser mayor a 0";
		}
		if($this->amount > 2000000000){
			$errors["amount"] = "Cantidad demasiado elevada";
		}
		if (strlen(trim($this->date)) == 0) {
			$errors["date_exp"] = "La fecha es obligatoria";
		}
		if ($this->owner == NULL ) {
			$errors["owner"] = "El propietario del gasto es obligatorio";
		}
		if(strlen(trim($this->description)) > 200){
			$errors["description"] = "Descripción demasiado larga";
		}
		if(strlen(trim($this->file)) > 200){
			$errors["file_exp"]  = "Nombre de fichero demasiado largo";
		}
		if (strlen(trim($this->file)) != 0) {
            $archivo_split = explode(".", $this->file);
            if (!in_array(strtolower($archivo_split[count($archivo_split)-1]), array("pdf", "png", "jpg"))) {
                $errors["file_exp"] = "Sólo se acepta pdf, png and jpg";
            }
        }

		
		if (sizeof($errors) > 0){
			throw new ValidationException($errors, "Expense is not valid");
		}
	}

	public function checkIsValidForUpdate() {
		$errors = array();

		if (!isset($this->id)) {
			$errors["id"] = "id is mandatory";
		}

		try{
			$this->checkIsValidForCreate();
		}catch(ValidationException $ex) {
			foreach ($ex->getErrors() as $key=>$error) {
				$errors[$key] = $error;
			}
		}
		if (sizeof($errors) > 0) {
			throw new ValidationException($errors, "Expenses is not valid");
		}
	}
}
