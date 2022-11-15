<?php
require_once('utility.php');
if(!isSessionStarted()) {
  session_start();
}
include_once('database.php');
include_once('receipt.php');
include_once('checkIfLogged.php');
include_once('message.php');

if(isset($_GET['receiptID'])) {
  removeReceipt(intval($_GET['receiptID']));
}

function removeReceipt($receiptID) {
  global $pdo;
  $receipt = new Receipt();
  $receipt->getFromDatabase($receiptID);

  $mainMessagePresenter = unserialize($_SESSION['main-message-presenter']);

  if(!$receipt->removeFromDatabase(intval($_SESSION['login-id']), $receiptID)) {
    $mainMessagePresenter->addMessage(new Message('Nieudana operacja usunięcia paragonu', MessageType::Error));
  } else {
    $mainMessagePresenter->addMessage(new Message('Pomyślny wynik operacji usunięcia paragonu'));
  }
  $_SESSION['main-message-presenter'] = serialize($mainMessagePresenter);
}
?>