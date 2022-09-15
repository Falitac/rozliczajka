<?php

function grosz2PLN($balance) {
  return sprintf("%.2f zł", $balance / 100);
}

/**
* @return bool
*/
function isSessionStarted()
{
  if (php_sapi_name() !== 'cli' ) {
    if ( version_compare(phpversion(), '5.4.0', '>=') ) {
      return session_status() === PHP_SESSION_ACTIVE ? TRUE : FALSE;
    } else {
      return session_id() === '' ? FALSE : TRUE;
    }
  }
  return FALSE;
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