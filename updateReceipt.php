<?php
require_once('receipt.php');
require_once('account.php');
require_once('message.php');
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
    $newReceipt->date = date_create($_GET['newDate']);
  }
  if(isset($_GET['newItem'])) {
    $itemData = explode(';', $_GET['newItem']);

    $newItem = new Item($itemData[0], $itemData[1]);
    $newReceipt->addItem($newItem);
  }
  if(isset($_GET['setDescription'])) {
    $newReceipt->description = $_GET['setDescription'];
  }
  if(isset($_GET['setReceiptPayer'])) {
    $id = $_GET['setReceiptPayer'];
    $newReceipt->setPayerByID($id);
  }

  updateUserInfo($newReceipt);
  updateItemInfo($newReceipt);

  if(isset($_GET['saveToDatabase'])) {
    $savedSuccessfully = NULL;
    $messagePresenter = unserialize($_SESSION['main-message-presenter']);
    try {
      $savedSuccessfully = $newReceipt->saveToDatabase();
    } catch (\Throwable $th) {
      $messagePresenter->addMessage(new Message("Could not save to database (database error)\n", MessageType::Error));
    }
    if(!$savedSuccessfully) {
      $messagePresenter->addMessage(new Message("Nie udało się zapisać paragonu do bazy :(", MessageType::Error));
    } else { // should not be an error but we'll change it soon
      $messagePresenter->addMessage(new Message("Paragon zapisany", MessageType::Info));
    }
    $_SESSION['main-message-presenter'] = serialize($messagePresenter);
  }

  $newReceipt->calculateShares();
  print_r($newReceipt);
  $_SESSION['new-receipt'] = serialize($newReceipt);
}

function updateUserInfo($newReceipt) {
  if(isset($_GET['newParticipant'])) {
    $participant = new Account($_GET['newParticipant']);
    $newReceipt->addParticipant($participant);
  }

  if(isset($_GET['removeUser'])) {
    $newReceipt->removeUser($_GET['removeUser']);
  }
}

function updateItemInfo($newReceipt) {
  if(isset($_GET['setItemPayer'])) {
    $data = explode(';', $_GET['setItemPayer']);
    if(count($data) !== 2) {
      return;
    }

    $item = &$newReceipt->getItem($data[0]);
    $item->addParticipant($data[1]);
  }
  if(isset($_GET['unsetItemPayer'])) {
    $data = explode(';', $_GET['unsetItemPayer']);
    if(count($data) !== 2) {
      return;
    }

    $item = &$newReceipt->getItem($data[0]);
    $item->removeParticipant($data[1]);
  }
  if(isset($_GET['removeItem'])) {
    $newReceipt->removeItem($_GET['removeItem']);
  }

}

function getJSON($receipt) {
  $receipt->calculateShares();
  echo json_encode($receipt);
}

updateReceipt();

?>