<?php
require_once('utility.php');
if(!isSessionStarted()) {
  session_start();
}
include_once('database.php');
include_once('receipt.php');
include_once('checkIfLogged.php');

if(isset($_GET['receiptID'])) {
  removeReceipt(intval($_GET['receiptID']));
}

function removeReceipt($receiptID) {
  global $pdo;
  $receipt = new Receipt();
  $receipt->getFromDatabase($receiptID);

  if(!$receipt->removeFromDatabase(intval($_SESSION['login-id']), $receiptID)) {
    $_SESSION['errors'][] = 'Nieudana operacja usunięcia paragonu';
  } else {
    $_SESSION['errors'][] = 'Pomyślny wynik operacji usunięcia paragonu';
  }
}
?>