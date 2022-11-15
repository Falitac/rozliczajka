<?php
  include_once('database.php');
  include_once('utility.php');
  include_once('receipt.php');
  include_once('message.php');
  session_start();
  $login_error = NULL;

  function checkIfFilledLogin() {
    if(!isset($_POST['login-name'])) {
      return false;
    }
    if(!isset($_POST['login-password'])) {
      return false;
    }
    return true;
  }

  if(checkIfFilledLogin()) {
    $sql_query = 'SELECT * FROM users WHERE (name = :name)';
    $query_values = [':name' => $_POST['login-name']];

    try {
      $result = $pdo->prepare($sql_query);
      $result->execute($query_values);
    } catch(PDOException $exception) {
      echo 'Query error';
      die();
    }
    $row = $result->fetch(PDO::FETCH_ASSOC);

    if(is_array($row)) {
      if(password_verify($_POST['login-password'], $row['password'])) {
        $_SESSION['logged'] = true;
        $_SESSION['login-id'] = $row['id'];
        $_SESSION['login-name'] = $row['name'];
        $_SESSION['main-message-presenter'] = serialize(new MessagePresenter());
      } else {
        $login_error = 'Błędne hasło';
        session_destroy();
        session_start();
        $_SESSION['logged'] = false;
      }
    } else {
      $login_error = 'Nie ma takiego użytkownika';
    }
  }

?>
<!DOCTYPE html>
<html lang="pl">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <link href="/css/main.css" rel="stylesheet">
  <link href="/css/queueMessage.css" rel="stylesheet">
  <link rel="icon" type="image/x-icon" href="/img/favicon.ico">
  <title>Rozliczajka</title>
</head>
<body>
  <main>
    <header>
      <h1>Rozliczajka</h1>
    </header>
    <div class="content">
      <?php if(!isset($_SESSION['login-id'])) { ?>
        <div class="login-data">
          <h2>Logowanie</h2>
          <form action="./" method="post">
            <input type="text" name="login-name" placeholder="Login" class="round-input">
            <input type="password" name="login-password" placeholder="Hasło" class="round-input">
            <input type="submit" name="login-submit" value="Zaloguj" class="round-input">
          </form>
          <hr>
          <p>
            <a href='#'>lub zaloguj się za pomocą google</a>
          </p>

      <?php if(!is_null($login_error)) {?>
        <p style="text-shadow: 0 0 2px red;">
          <?=$login_error?>
        </p>
      <?php }?>

        </div>
      <?php } else { ?>
        <?php
          if(isset($_SESSION['main-message-presenter'])) {
            $messagePresenter = unserialize($_SESSION['main-message-presenter']);
            $messagePresenter->print();
            $_SESSION['main-message-presenter'] = serialize($messagePresenter);
          }
        ?>

        <div class="user-info fancy-border">
          <h1>Cześć <?= $_SESSION['login-name']?></h1>
          <a href="./logout.php">Wyloguj</a><br>
          <a href="./addReceipt.php">Dodaj paragon</a>
          <div class="debts-info"><?php require_once('allDebts.php');?></div>
        </div>

        <div class="receipt-list fancy-border" style="margin-top: 3vh;">
          <h1>Paragony</h1>
          <div class="container-receipt-list">
            <div>
              <?php require_once('receiptTable.php');?>
            </div>
            <div>
              <?php require_once('userReceiptTable.php');?>
            </div>
          </div>
        </div>
        
        <script src="js/mainSiteHandler.js"></script>
      <?php } ?>
      <script src="js/utility.js"></script>
    </div>
  </main>
  <footer><p>&copy; Konrad Filek</p></footer>
</body>
</html>