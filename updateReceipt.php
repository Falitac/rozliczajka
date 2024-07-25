<?php
require_once('receipt.php');
require_once('account.php');
require_once('message.php');
require_once('globals.php');
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

  if(isset($_GET['performOCR'])) {
    performOCR($newReceipt);
  }

  if(isset($_GET['saveToDatabase'])) {
    $savedSuccessfully = NULL;
    $messagePresenter = unserialize($_SESSION['main-message-presenter']);
    try {
      $savedSuccessfully = $newReceipt->saveToDatabase();
    } catch (\Throwable $th) {
      $messagePresenter->addMessage(new Message("Nie udało się zapisać paragonu do bazy :(\n", MessageType::Error));
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
  if(isset($_GET['setItemName'])) {
    $data = explode(';', $_GET['setItemName']);
    if(count($data) !== 2) {
      return;
    }

    $item = &$newReceipt->getItem($data[0]);
    $item->setName($data[1]);
  }
  if(isset($_GET['setItemPrice'])) {
    $data = explode(';', $_GET['setItemPrice']);
    if(count($data) !== 2) {
      return;
    }

    $item = &$newReceipt->getItem($data[0]);
    $item->setPrice($data[1]);
  }

}

function performOCR($receipt) {
  echo "Trying to perform OCR<br>";

  $filename = RECEIPTS_IMG_LOCATION.$receipt->imageName;
  if(!file_exists($filename)) {
    return;
  }

  echo "File exists, performing OCR<br>";
  $ocrResult = receiptOCR($filename);
  $items = $ocrResult->receipts[0]->items;
  $receipt->date = new DateTime($ocrResult->receipts[0]->date);
  echo "<pre>";
  foreach($items as $item) {
    $itemName = htmlspecialchars($item->description);
    $itemPrice = $item->amount * 100;
    if($itemName === "SUMA PLN") {
      $receipt->setPrice($itemPrice);
      continue;
    }

    $newItem = new Item($itemName, $itemPrice);
    $newItem->addEveryoneFromReceipt($receipt);
    $receipt->addItem($newItem);
  }
  echo "</pre>";
}

function receiptOCR($imageFile) {
  $receiptOcrEndpoint = 'https://ocr.asprise.com/api/v1/receipt';

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $receiptOcrEndpoint);
  curl_setopt($ch, CURLOPT_POST, true);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

  curl_setopt($ch, CURLOPT_POSTFIELDS, array(
   'api_key' => 'TEST',      // Use 'TEST' for testing purpose
    'recognizer' => 'auto',     // can be 'US', 'CA', 'JP', 'SG' or 'auto'
    'ref_no' => 'ocr_php_123',  // optional caller provided ref code
    'file' => curl_file_create($imageFile) // the image file
  ));

  $result = curl_exec($ch);
  if(curl_errno($ch)){
      throw new Exception(curl_error($ch));
  }
  echo "<pre>";
  echo $result;
  echo "</pre>";

  return json_decode($result);
}

function mockupReceiptOCR($imageFile) {
  return json_decode(file_get_contents("13.02.json"));
}

function getJSON($receipt) {
  $receipt->calculateShares();
  echo json_encode($receipt);
}

updateReceipt();

?>