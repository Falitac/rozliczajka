<?php
  $db_server_name = 'localhost';
  $db_name = 'rozliczajka';
  $db_username = 'root';
  $db_password = '';

  $pdo = NULL;
  try {
    $pdo = new PDO("mysql:host=$db_server_name;dbname=$db_name", $db_username, $db_password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  } catch(PDOException $exception) {
    echo "<p>Connection failed</p>";
  }
?>