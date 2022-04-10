<?php

function grosz2PLN($balance) {
  return sprintf("%.2fzł", $balance / 100);
}

function extractUsernamesToArray() {
  global $pdo;
  $userList = array();

  $query = "SELECT id, name FROM users;";
  $result = $pdo->prepare($query);
  $result->execute();

  while($row = $result->fetch(PDO::FETCH_ASSOC)) {
    $userList[] = $row;
  }
  return $userList;
}

?>