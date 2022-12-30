<?php

require_once(__DIR__."/../model/User.php");
require_once(__DIR__."/../model/UserMapper.php");

require_once(__DIR__."/../model/Expenses.php");
require_once(__DIR__."/../model/ExpensesMapper.php");

require_once(__DIR__."/BaseRest.php");

/**
* Class ExpenseRest
*
* It contains operations for creating, retrieving, updating, deleting and
* listing posts, as well as to create comments to posts.
*
* Methods gives responses following Restful standards. Methods of this class
* are intended to be mapped as callbacks using the URIDispatcher class.
*
*/
class ExpensesRest extends BaseRest {
	private $expenseMapper;

	public function __construct() {
		parent::__construct();

		$this->expenseMapper = new ExpensesMapper();
	}

	public function getExpenses() {
		$expenses = $this->expenseMapper->findAll();
		// json_encode Post objects.
		// since Post objects have private fields, the PHP json_encode will not
		// encode them, so we will create an intermediate array using getters and
		// encode it finally
		$expenses_array = array();
		foreach($expenses as $expense) {
			array_push($expenses_array, array(
				"id" => $expense->getId(),
				"type_exp" => $expense->getTipo(),
				"date_exp" => $expense->getDate(),
				"amount" => $expense->getAmount(),
				"description_exp" => $expense->getDescription(),
				"file_exp" => $expense->getFile(),
				"owner" => $expense->getOwner()->getUsername()
			));
		}

		header($_SERVER['SERVER_PROTOCOL'].' 200 Ok');
		header('Content-Type: application/json');
		echo(json_encode($expenses_array));
	}

	public function createExpense($data) {
		$currentUser = parent::authenticateUser();
		$expense = new Expenses();

		if (isset($data->tipo) && isset($data->date) && isset($data->amount)&& isset($data->amount)&& isset($data->description)&& isset($data->file)) {
			$expense->setTipo($data->tipo);
			$expense->setDate($data->date);
			$expense->setAmount($data->amount);
			$expense->setDescription($data->description);
			$expense->setFile($data->file);

			$expense->setOwner($currentUser);
		}

		try {
			// validate Post object
			$expense->checkIsValidForCreate(); // if it fails, ValidationException

			// save the Post object into the database
			$expenseId = $this->expenseMapper->save($expense);

			// response OK. Also send post in content
			header($_SERVER['SERVER_PROTOCOL'].' 201 Created');
			header('Location: '.$_SERVER['REQUEST_URI']."/".$expenseId);
			header('Content-Type: application/json');
			echo(json_encode(array(
				"id"=>$expenseId,
				"type_exp"=>$expense->getTipo(),
				"date_exp" => $expense->getDate(),
				"amount" => $expense->getAmount(),
				"description_exp" => $expense->getDescription(),
				"file_exp" => $expense->getFile()
			)));

		} catch (ValidationException $e) {
			header($_SERVER['SERVER_PROTOCOL'].' 400 Bad request');
			header('Content-Type: application/json');
			echo(json_encode($e->getErrors()));
		}
	}

	public function readExpense($expenseId) {
		// find the Post object in the database
		$expense = $this->expenseMapper->findById($expenseId);
		if ($expense == NULL) {
			header($_SERVER['SERVER_PROTOCOL'].' 400 Bad request');
			echo("Expenses with id ".$expenseId." not found");
			return;
		}

		$expenses_array = array(
			"id" => $expense->getId(),
			"type_exp" => $expense->getTipo(),
			"date_exp" => $expense->getDate(),
			"amount" => $expense->getAmount(),
			"description_exp" => $expense->getDescription(),
			"file_exp" => $expense->getFile(),
			"owner" => $expense->getowner()->getUsername()
		);

		header($_SERVER['SERVER_PROTOCOL'].' 200 Ok');
		header('Content-Type: application/json');
		echo(json_encode($expenses_array));

	}

	public function updateExpense($expenseId, $data) {
		$currentUser = parent::authenticateUser();

		$expense = $this->expenseMapper->findById($expenseId);
		if ($expense == NULL) {
			header($_SERVER['SERVER_PROTOCOL'].' 400 Bad request');
			echo("Expenses with id ".$expenseId." not found");
			return;
		}

		// Check if the Post author is the currentUser (in Session)
		if ($expense->getAuthor() != $currentUser) {
			header($_SERVER['SERVER_PROTOCOL'].' 403 Forbidden');
			echo("you are not the author of this expenses");
			return;
		}
		$expense->setTipo($data->tipo);
		$expense->setDate($data->date);
		$expense->setAmount($data->amount);
		$expense->setDescription($data->description);
		$expense->setFile($data->file);

		try {
			// validate Post object
			$expense->checkIsValidForUpdate(); // if it fails, ValidationException
			$this->expenseMapper->update($expense);
			header($_SERVER['SERVER_PROTOCOL'].' 200 Ok');
		}catch (ValidationException $e) {
			header($_SERVER['SERVER_PROTOCOL'].' 400 Bad request');
			header('Content-Type: application/json');
			echo(json_encode($e->getErrors()));
		}
	}

	public function deleteExpense($expenseId) {
		$currentUser = parent::authenticateUser();
		$expense = $this->expenseMapper->findById($expenseId);

		if ($expense == NULL) {
			header($_SERVER['SERVER_PROTOCOL'].' 400 Bad request');
			echo("Expenses with id ".$expenseId." not found");
			return;
		}
		// Check if the Post author is the currentUser (in Session)
		if ($expense->getOwner() != $currentUser) {
			header($_SERVER['SERVER_PROTOCOL'].' 403 Forbidden');
			echo("you are not the author of this expenses");
			return;
		}

		$this->expenseMapper->delete($expense);

		header($_SERVER['SERVER_PROTOCOL'].' 204 No Content');
	}

}

// URI-MAPPING for this Rest endpoint
$expenseRest = new ExpensesRest();
URIDispatcher::getInstance()
->map("GET", "/expense", array($expenseRest,"getExpenses"))
->map("GET", "/expense/$1", array($expenseRest,"readExpense"))
->map("POST", "/expense", array($expenseRest,"createExpense"))
->map("PUT",	"/expense/$1", array($expenseRest,"updateExpense"))
->map("DELETE", "/expense/$1", array($expenseRest,"deleteExpense"));
