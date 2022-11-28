<?php
// connect to the database with PDO
$dbhost = "127.0.0.1";
$dbname = "saveyourmoney";
$dbuser = "tswuser";
$dbpass = "tswuser";

$db = new PDO(
	"mysql:host=$dbhost;dbname=$dbname;charset=utf8", // connection string
	$dbuser, 
	$dbpass, 
	array( // options
	  PDO::ATTR_EMULATE_PREPARES => false,
	  PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
	)
);

// some function to access your data...
function getData($db) {
   $stmt = $db->query("SELECT * FROM gastos");
   return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

try {
   // call the function and output the data
   $results = getData($db);   
   
} catch(PDOException $ex) {
   die("exception! ".$ex->getMessage());
}


?>