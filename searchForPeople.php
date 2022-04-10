<?php
session_start();
require_once("database.php");


function searchForPeople() {
  if(!isset($_SESSION['login-id'])) {
    return NULL;
  }
  if(!isset($_GET['name'])) {
    return NULL;
  }

  global $pdo;

  $query = 'SELECT id, name FROM users WHERE name LIKE :query; LIMIT 10';

  $values = array(
    'query' => '%'.$_GET['name'].'%'
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
  return json_encode($personList);
}

print_r(searchForPeople());


?>