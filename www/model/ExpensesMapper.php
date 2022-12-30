<?php
// file: model/ExpensesMapper.php
require_once(__DIR__."/../core/PDOConnection.php");

require_once(__DIR__."/../model/User.php");
require_once(__DIR__."/../model/Expenses.php");

/**
* Class ExpenseMapper
*
* Database interface for Expenses entities
*
*/
class ExpensesMapper {

	/**
	* Reference to the PDO connection
	* @var PDO
	*/
	private $db;

	public function __construct() {
		$this->db = PDOConnection::getInstance();
	}

	public function findAll() {
		$stmt = $this->db->query("SELECT * FROM expenses, users WHERE users.username = expenses.owner ORDER BY expenses.date_exp DESC");
		$expenses_db = $stmt->fetchAll(PDO::FETCH_ASSOC);

		$expenses = array();

		foreach ($expenses_db as $expense) {
			$owner = new User($expense["username"]);
			array_push($expenses, new Expenses($expense["id"], $expense["type_exp"], $expense["date_exp"], $expense["amount"], $expense["description_exp"], $expense["file_exp"],$owner));
		}
		return $expenses;
	}

	public function findById($expenseId){
		$stmt = $this->db->prepare("SELECT * FROM expenses WHERE id=?");
		$stmt->execute(array($expenseId));
		$expense = $stmt->fetch(PDO::FETCH_ASSOC);

		if($expense != null) {
			return new Expenses(
			$expense["id"],
			$expense["type_exp"],
			$expense["date_exp"],
			$expense["amount"],
			$expense["description_exp"],
			$expense["file_exp"],
			new User($expense["owner"]));
		} else {
			return NULL;
		}
	}


	public function save(Expenses $expense) {
		$stmt = $this->db->prepare("INSERT INTO expenses(type_exp, date_exp, amount, description_exp, file_exp, owner) values (?,?,?,?,?,?)");
		$stmt->execute(array($expense->getTipo(), $expense->getDate(), $expense->getAmount(), $expense->getDescription(), $expense->getFile(), $expense->getOwner()->getUsername()));
		return $this->db->lastInsertId();
	}

	public function update(Expenses $expense) {
		$stmt = $this->db->prepare("UPDATE expenses set type_exp=?, date_exp=?, amount=?, description_exp=?, file_exp=? where id=?");
		$stmt->execute(array($expense->getTipo(), $expense->getDate(), $expense->getAmount(), $expense->getDescription(), $expense->getFile(), $expense->getId()));
	}

	public function delete(Expenses $expense) {
		$stmt = $this->db->prepare("DELETE from expenses WHERE id=?");
		$stmt->execute(array($expense->getId()));
	}

	public function getOrderedData($fechaInicio,$fechaFin){
		$stmt = $this->db->query("SELECT expenses.type_exp, expenses.date_exp, expenses.amount, expenses.owner
		FROM expenses, users WHERE users.username = expenses.owner AND expenses.date_exp BETWEEN '$fechaInicio' and '$fechaFin'
		ORDER BY expenses.type_exp , expenses.date_exp DESC");
		$expenses_db = $stmt->fetchAll(PDO::FETCH_ASSOC);

		$expenses = array();

		foreach ($expenses_db as $expense) {
			array_push($expenses, array($expense["type_exp"],
				$expense["date_exp"], $expense["amount"],new User($expense["owner"])));
		}
		return $expenses;
		}

	}
