<?php

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
