<?php

require_once('utility.php');
require_once('globals.php');
require_once('receipt.php');
require_once('database.php');

if(!isSessionStarted()) {
  session_start();
}

handler();

function handler() {
  if(!isset($_SESSION['login-id'])) {
    return;
  }
  $imageName = uploadImage();
  if(!is_null($imageName)) {
    $newReceipt = unserialize($_SESSION['new-receipt']);
    $newReceipt->setImageName($imageName);
    $_SESSION['new-receipt'] = serialize($newReceipt);
    echo "Zapisałem: " . $imageName;
  }
}

function uploadImage() {
  $file = $_FILES['file'];
  if(is_null($file)) {
    return null;
  }
  $name = getHash($file['name']) . ".jpg";

  if(move_uploaded_file($file['tmp_name'], RECEIPTS_IMG_LOCATION . $name)) {
    return $name;
  }
  return null;
}

function getHash($value) {
  return hash('sha256', $value . strval(time()));
}