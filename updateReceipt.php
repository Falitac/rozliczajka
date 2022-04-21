<?php
require_once('receipt.php');
require_once('account.php');
session_start();

function updateReceipt() {
  if(!isset($_SESSION['login-id'])) {
    return;
  }
  if(!isset($_SESSION['new-receipt'])) {
    return;
  }

  $newReceipt = unserialize($_SESSION['new-receipt']);

  if(isset($_GET['getJSON'])) {
    getJSON($newReceipt);
    return;
  }

  if(isset($_GET['newPrice'])) {
    $newReceipt->setPrice($_GET['newPrice']);
  }

  if(isset($_GET['newDate'])) {
    $newReceipt->date = date($_GET['newDate']);
  }

  if(isset($_GET['newItem'])) {
    $itemData = explode(';', $_GET['newItem']);
    $newItem = new Item($itemData[0], $itemData[1]);

    //$newItem->addEveryoneFromReceipt($newReceipt);

    $newReceipt->addItem($newItem);
  }

  if(isset($_GET['setReceiptPayer'])) {
    $id = $_GET['setReceiptPayer'];
    $newReceipt->setPayerByID($id);
  }

  if(isset($_GET['setItemPayer'])) {
    $data = explode(';', $_GET['setItemPayer']);
    if(count($data) !== 2) {
      return;
    }

    $item = &$newReceipt->itemList[$data[0]];
    $item->addParticipant($data[1]);
  }

  if(isset($_GET['unsetItemPayer'])) {
    $data = explode(';', $_GET['unsetItemPayer']);
    if(count($data) !== 2) {
      return;
    }

    $item = &$newReceipt->itemList[$data[0]];
    $item->removeParticipant($data[1]);
  }

  if(isset($_GET['newParticipant'])) {
    $participant = new Account($_GET['newParticipant']);
    $newReceipt->addParticipant($participant);
  }

  if(isset($_GET['removeItem'])) {
    $newReceipt->removeItem($_GET['removeItem']);
  }

  if(isset($_GET['removeUser'])) {
    $newReceipt->removeUser($_GET['removeUser']);
  }

  if(isset($_GET['saveToDatabase'])) {
    $newReceipt->saveToDatabase();
  }

  $newReceipt->calculateShares();
  print_r($newReceipt);
  $_SESSION['new-receipt'] = serialize($newReceipt);
}

function getJSON($receipt) {
  $receipt->calculateShares();
  echo json_encode($receipt);
}

updateReceipt();

?>