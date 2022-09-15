<?php

require_once('database.php');

function isNameValid($name) {
  $length = mb_strlen($name);
  if($length < 4 || 40 < $length) {
    return false;
  }
  return true;
}

function isPasswordValid($password) {
  $length = mb_strlen($password);
  if($length < 8 || 40 < $length) {
    return false;
  }
  return true;
}

function getUserID($name) {
  global $pdo;
  try {
    $query = "SELECT id FROM users WHERE name = :name";
    $values = array("name" => $name);

    $result = $pdo->prepare($query);
    $result->execute($values);
    $row = $result->fetch(PDO::FETCH_ASSOC);

    if(is_array($row)) {
      return $row['id'];
    }
    return NULL;

  } catch(PDOException $e) {
    throw new Exception("Database query exception");
  }
  return NULL;
}

class Account {
  private $id;
  private $name;

  public function getID() {
    return $this->id;
  }

  public function getName() {
    return $this->name;
  }

  public function exists() {
    global $pdo;
    try {
      $query = "SELECT id, name FROM users WHERE id = :id";
      $values = array(
        'id' => $this->id
      );

      $result = $pdo->prepare($query);
      $result->execute($values);
      if($result->rowCount() === 1) {
        return TRUE;
      }
    } catch(\Throwable $exception) {
      print_r($exception);
    }
    return FALSE;
  }

  public function __construct($name = NULL) {
    if(isset($name)) {
      $this->setFromDatabase($name);
      return;
    }
    $this->id = NULL;
    $this->name = NULL;
  }

  public function __destruct() {
  }

  public function addAccount(string $name, string $password) {
    global $pdo;

    if(!isNameValid($name)) {
      throw new Exception("Invalid name");
    }

    if(!isPasswordValid($password)) {
      throw new Exception("Invalid password");
    }

    if(!is_null(getUserID($name))) {
      throw new Exception("That user exists");
    }

    try {
      $query = "INSERT INTO users (name, password) VALUES (:name, :password)";
      $values = array(
        "name" => $name,
        "password" => password_hash($password, PASSWORD_DEFAULT)
      );

      $result = $pdo->prepare($query);
      $result->execute($values);
    } catch(PDOException $e) {
      throw new Exception("Database query exception $e");
    }

    return $pdo->lastInsertId();
  }

  public function setFromDatabase($name) {
    global $pdo;
    try {
      $query = "SELECT id, name FROM users WHERE name = :name";
      $values = array(
        "name" => $name
      );

      $result = $pdo->prepare($query);
      $result->execute($values);
      if($result->rowCount() === 0) {
        $this->id = NULL;
        $this->name = NULL;
        return $this;
      }

      $row = $result->fetch(PDO::FETCH_ASSOC);
      $this->id = intval($row["id"]);
      $this->name = $row["name"];
    } catch(PDOException $e) {
      throw new Exception("Database query exception $e");
    }
    return $this;
  }

  public function setFromDatabaseByID($id) {
    global $pdo;
    try {
      $query = "SELECT id, name FROM users WHERE id = :id";
      $values = array(
        "id" => $id
      );

      $result = $pdo->prepare($query);
      $result->execute($values);

      $row = $result->fetch(PDO::FETCH_ASSOC);
      $this->id = $row["id"];
      $this->name = $row["name"];
    } catch(PDOException $e) {
      throw new Exception("Database query exception $e");
    }
    return $this;
  }

}

?>