<?php
require_once('database.php');
session_start();

function updateDatabaseReceipt() {
  if(!isset($_SESSION['login-id'])) {
    return FALSE;
  }
  if(isset($_GET['receiptId'])) {
    if(!isset($_GET['status'])) {
      return;
    }
    $loginId = $_SESSION['login-id'];
    $receiptId = (int)$_GET['receiptId'];
    $status = (int)$_GET['status'];

    return setReceiptPaidValue($loginId, $receiptId, $status);
  }
  return FALSE;
}

function setReceiptPaidValue($loginId, $receiptId, $status) {
  global $pdo;

  try {
    $sql = 'UPDATE `payments` SET `paid` = :status WHERE `receipt_id` = :receiptId AND `user_id` = :user_id';

    $values = [
      'status' => $status,
      'receiptId' => $receiptId,
      'user_id' => $loginId
    ];

    $result = $pdo->prepare($sql);
    $result->execute($values);

  } catch(PDOException $e) {
    echo "Database query exception $e";
    return FALSE;
  }

  return TRUE;
}

if(updateDatabaseReceipt()) {
  echo "Successfull";
} else {
  echo "Error";
}

?>