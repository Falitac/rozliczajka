<?php 
require_once('account.php');
require_once('database.php');

class Receipt {
  public $payerID;
  public $date;
  public $price;
  public $itemList;
  public $personList;

  public $userNames;
  public $shares;

  public function __construct() {
    $this->payerID = NULL;
    $this->date = date('d/m/Y');
    $this->price = 0;
    $this->itemList = array();
    $this->personList = array();
    $this->userNames = array();
    $this->share = $this->price;
  }

  public function calculateShares() {
    $personCount = count($this->personList);
    if($personCount === 0) {
      return NULL;
    }

    $result = array();
    $itemSum = 0;

    $personalItemsSum = array();
    foreach($this->itemList as $item) {
      $itemSum += $item->getPrice();
      $itemPayers = $item->getPayers();
      $itemSharedPrice = $item->getSharedPrice();

      foreach($itemPayers as $payer) {
        if(isset($personalItemsSum[$payer])) {
         $personalItemsSum[$payer] += $itemSharedPrice;
        } else {
         $personalItemsSum[$payer] = $itemSharedPrice;
        }
      }
    }

    $share = intdiv(($this->price - $itemSum), $personCount);
    for($i = 0; $i < $personCount; $i++) {
      $personalExtras = $personalItemsSum[$this->personList[$i]] ?? 0;
      $result[$this->personList[$i]] = $share + $personalExtras;
    }

    $rest = $this->price - array_sum($result);
    $result[$this->payerID] += $rest;

    $this->shares = $result;
    return $result;
  }

  public function addParticipant($account) {
    if(in_array($account->getId(), $this->personList)) {
      return;
    }
    $this->userNames[$account->getId()] = $account->getName();
    $this->personList[] = intval($account->getId());
  }

  public function addPayer($account) {
    $this->addParticipant($account);
    $this->setPayerByID($account->getID());
  }

  public function setPayerByID($id) {
    $id = intval($id);
    $index = array_search($id, $this->personList);
    if($index !== FALSE) {
      $this->payerID = $id;
    }
  }

  public function setPrice($newPrice) {
    $this->price = max(0, intval($newPrice));
  }

  public function getPrice() {
    return $this->price;
  }

  public function addItem($item) {
    $this->itemList[] = $item;
  }

  public function removeItem($itemID) {
    array_splice($this->itemList, $itemID, 1);
  }

  public function removeUser($userID) {
    $index = array_search($userID, $this->personList);
    if($index === FALSE) {
      return;
    }
    array_splice($this->personList, $index, 1);
    $this->removeUserItemsConnections($userID);
  }

  public function removeUserItemsConnections($userID) {
    foreach($this->itemList as $item) {
      $index = array_search($userID, $item->payers);
      if($index !== false) {
        array_splice($item->payers, $index);
      }
    }
  }

  public function isDataValid() {
    if($this->price < 0) {
      return FALSE;
    }
    if(count($this->personList) <= 0) {
      return FALSE;
    }
    if($this->payerID === NULL) {
      return FALSE;
    }
    if($this->shares === NULL) {
      return FALSE;
    }
    return TRUE;
  }

  public function saveToDatabase() {
    if(!$this->isDataValid()) {
      return FALSE;
    }

    global $pdo;
    try {
      $receiptID = $this->receiptQuery($pdo);
      $this->paymentsQuery($pdo, $receiptID);
      $itemPayers = $this->itemQuery($pdo, $receiptID);
      $this->itemPayersQuery($pdo, $itemPayers);

    } catch(PDOException $e) {
      throw new Exception("Database query exception $e");
    }
    return TRUE;
  }

  private function receiptQuery($pdo) {
    $query = "INSERT INTO receipts(date, price, payer_id) VALUES (:date, :price, :payer_id)";

    $sqlDateFormat = date('Y-m-d', strtotime($this->date));

    $values = array(
      'date' => $sqlDateFormat,
      'price' => $this->price,
      'payer_id' => $this->payerID,
    );
    $result = $pdo->prepare($query);
    $result->execute($values);

    return $pdo->lastInsertId();
  }

  private function paymentsQuery($pdo, $receiptID) {
    $query= "INSERT INTO payments(receipt_id, user_id, value) VALUES (:receipt_id, :user_id, :value)";

    foreach($this->shares as $userID => $price) {
      $values = array(
        "receipt_id" => $receiptID,
        "user_id" => $userID,
        "value" => $price
      );
      $result = $pdo->prepare($query);
      $result->execute($values);
    }
  }

  private function itemQuery($pdo, $receiptID) {
    $resultItemPayers = [];
    $query = "INSERT INTO items(name, value, receipt_id) VALUES (:name, :value, :receipt_id)";
    foreach($this->itemList as $item) {
      $values = array(
        "name" => $item->getName(),
        "value" => $item->getPrice(),
        "receipt_id" => $receiptID
      );
      $result = $pdo->prepare($query);
      $result->execute($values);
      $resultItemPayers[$pdo->lastInsertId()] = $item->getPayers();
    }
    return $resultItemPayers;
  }

  private function itemPayersQuery($pdo, $itemPayers) {
    $query = "INSERT INTO item_payers(item_id, person_id) VALUES (:item_id, :person_id)";
    foreach($itemPayers as $item => $payers) {
      foreach($payers as $payer) {
        $values = array(
          "item_id" => $item,
          "person_id" => $payer
        );
        $result = $pdo->prepare($query);
        $result->execute($values);
      }
    }
  }
}

class Item {
  public $name;
  public $price;
  public $payers;

  public function __construct($name = "Default", $price = 0) {
    $this->name = $name;
    $this->setPrice($price);
    $this->payers = array();
  }

  public function addEveryoneFromReceipt($receipt) {
    foreach($receipt->personList as $person) {
      $this->addParticipant($person);
    }
  }

  public function addParticipant($id) {
    $result = array_search($id, $this->payers);
    if($result === false) {
      $this->payers[] = intval($id);
    }
  }

  public function removeParticipant($id) {
    $result = array_search($id, $this->payers);
    if($result !== false) {
      array_splice($this->payers, $result, 1);
    }
  }

  public function setPrice($price) {
    $this->price = max(0, intval($price));
  }

  public function setName($name) {
    if($name === '') {
      $this->name = 'default_name';
      return;
    }
    $this->name = $name;
  }

  public function getName() {
    return $this->name;
  }

  public function getPrice() {
    return $this->price;
  }

  public function getPayers() {
    return $this->payers;
  }

  public function getSharedPrice() {
    $personCount = count($this->payers);
    if($personCount === 0) {
      return -1;
    }
    return intdiv($this->price , $personCount);
  }
}

?>