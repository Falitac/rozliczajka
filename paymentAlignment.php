<?php
require_once('database.php');
require_once('utility.php');

if(!isSessionStarted()) {
  session_start();
}
if(is_null($_SESSION['login-id'])) {
  exit;
}

if(alignPayments($_SESSION['login-id'], $_POST['person'])) {
  echo "Success";
}

function alignPayments($userID, $person) {
  global $pdo;

  $today = new DateTime();
  $today = $today->format("Y-m-d");

  $queryValues = array(
      'person' => $person,
      'userID' => $userID,
      'date' => $today
  );
    
  $credits = "UPDATE payments
    JOIN receipts
    ON receipts.id=payments.receipt_id
    JOIN users
    ON users.id=payments.user_id
    SET payments.paid=1, payments.date=:date

    WHERE payments.paid=0
    AND users.name=:person
    AND receipts.payer_id=:userID";

  $debts = "UPDATE payments
    JOIN receipts
    ON receipts.id=payments.receipt_id
    JOIN users
    ON users.id=receipts.payer_id
    SET payments.paid=1, payments.date=:date

    WHERE payments.paid=0
    AND users.name=:person
    AND payments.user_id=:userID";

  $result = null;
  try {
    $count = 0;
    $result = $pdo->prepare($debts);
    $result->execute($queryValues);
    $count += $result->rowCount();

    $result = $pdo->prepare($credits);
    $result->execute($queryValues);
    $count += $result->rowCount();

    echo "changedPayments=";
    print($count);
    echo "\n";
  } catch(\Throwable $th) {
    return false;
  }
  return true;
}
