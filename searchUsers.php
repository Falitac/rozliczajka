<?php
session_start();

require_once("database.php");
require_once("checkIfLogged.php");

print_r(json_encode(searchForPeople()));

function searchForPeople() {
  if(!isset($_GET['name'])) {
    return NULL;
  }
  if($_GET['name'] === '') {
    return [];
  }

  global $pdo;

  $query = 'SELECT id, name FROM users WHERE name LIKE :query LIMIT 10;';

  $values = array(
    'query' => $_GET['name'].'%'
  );

  $result = NULL;
  try {
    $result = $pdo->prepare($query);
    $result->execute($values);
  } catch (PDOException $e) {
    echo"problem";
    return NULL;
  }

  $personList = [];
  while($row = $result->fetch(PDO::FETCH_ASSOC)) {
    $personList[] = $row['name'];
  }
  return $personList;
}



?>