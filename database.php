<?php
  $db_server_name = 'localhost';
  $db_name = 'rozliczajka-test';
  $db_username = 'root';
  $db_password = '';

  $pdo = NULL;
  try {
    $pdo = new PDO("mysql:host=$db_server_name;dbname=$db_name", $db_username, $db_password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    unset($db_server_name, $db_username, $db_password);
  } catch(PDOException $exception) {
    echo "<p>Connection failed</p>";
  }
?>