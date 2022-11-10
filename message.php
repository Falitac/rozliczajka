<?php

enum MessageType : int {
  case Info = 0;
  case Error = 1;
  case Warning = 2;
};

class Message implements JsonSerializable {
  private string $text;
  private MessageType $messageType;
  private bool $isRead;

  public function __construct($text, $messageType = MessageType::Info) {
    $this->text = $text;
    $this->messageType = $messageType;
    $this->isRead = false;
  }

  public function setAsRead() {
    $this->isRead = true;
  }

  public function isRead() {
     return $this->isRead;
  }

  public function getText() {
    return $this->text;
  }

  public function typeAsString() {
    switch($this->messageType) {
      case MessageType::Info: return "message-info";
      case MessageType::Error: return "message-error";
      case MessageType::Warning: return "message-warning";
    }
    return "message-other";
  }

  public function print() {
      $type = $this->typeAsString();
      echo "<div class=\"messages-queue-item $type\">";
      echo $this->getText();
      echo '</div>';
  }

  public function jsonSerialize() {
    return get_object_vars($this);
  }
}

class MessagePresenter {
  private array $messageQueue;

  public function __construct() {
    $this->messageQueue = [];
  }

  public function addMessage(Message $message) {
    $this->messageQueue[] = $message;
  }

  public function isEmpty() {
    return count($this->messageQueue) === 0;
  }

  public function print() {
    if($this->isEmpty()) {
      return;
    }

    echo '<div class="messages-queue">';
    echo '<h2>Nowe notyfikacje</h2>';
    foreach($this->messageQueue as &$message ) {
      $message->print();
      $message->setAsRead();
    }
    echo '</div>';
    $this->cleanUp();
  }

  private function cleanUp() {
    $this->messageQueue = array_filter($this->messageQueue, static function($message) {
      return !$message->isRead();
    });
  }

}

// Unit tests? What are those?
// echo '<link href="/css/main.css" rel="stylesheet">';
// echo '<link href="/css/queueMessage.css" rel="stylesheet">';

// $t = new Message("Saved successfully");
// $e = new Message("Database error", MessageType::Error);
// $w = new Message("You should pay your debts, that was the agreement", MessageType::Warning);
// #$o = new Message("Okaeri", 5);

// // message processor test:
// $mp = new MessagePresenter();
// $mp->addMessage($t);
// $mp->addMessage($e);
// $mp->addMessage($w);
// $mp->addMessage($e);
// $mp->addMessage($t);
// $mp->printMessageQueue();
// $mp->printMessageQueue();