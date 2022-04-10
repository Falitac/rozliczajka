<?php
  include_once('database.php');
  include_once('utility.php');
  session_start();
  unset($_SESSION['new-receipt']);
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
      } else {
        $login_error = 'Błędne hasło';
        session_destroy();
        session_start();
        $_SESSION['logged'] = false;
      }
    } else {
      $login_error = 'Nie ma takiego użytkownika';
    }

    print_r($_SESSION);
  }

?>
<!DOCTYPE html>
<html lang="pl">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="/css/main.css" rel="stylesheet">
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
        <div class="user-info fancy-border">
          <h1>Cześć <?= $_SESSION['login-name']?>!</h1>
          <a href="./logout.php">Wyloguj</a><br>
          <a href="./addReceipt.php">Dodaj paragon</a>
          <h2>Długi</h2>
          <?php require_once('debtTable.php');?>
          <h2>Wszystkie długi:</h2>
          <?php require_once('allDebts.php');?>
        </div>

        <div class="receipt-list fancy-border" style="margin-top: 3vh;">
          <h2>Ostatnie paragony</h2>
          <?php require_once('receiptTable.php');?>
        </div>
      <?php } ?>
    </div>
  </main>
  <script>
  </script>
</body>
</html>