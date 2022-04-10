<?php
  require_once('checkIfLogged.php');
  require_once('database.php');
  require_once('utility.php');
  require_once('receipt.php');

  $newReceipt;
  if(!isset($_SESSION['new-receipt'])) {
    $newReceipt = new Receipt();
    $currentAccount = new Account();
    $currentAccount->setFromDatabaseByID($_SESSION['login-id']);
    $newReceipt->addPayer($currentAccount);
    $_SESSION['new-receipt'] = serialize($newReceipt);
  } else {
    $newReceipt = unserialize($_SESSION['new-receipt']);
  }


?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Rozliczajka</title>
  <link href="css/main.css" rel="stylesheet">
  <link rel="icon" type="image/x-icon" href="/img/favicon2.ico">
  <script type="text/javascript" src="js/qrcode.min.js"></script>
</head>
<body>
  <main>
    <header>
      <h1>Dodaj paragon</h1>
    </header>
    <div class="content">
      <a href="./" style="float: right;">Powrót do głównej</a><br>
      <div class="receipt-info">
        <div class="receipt-left-side">
          <form>
            <label>
              <h2>Kwota:</h2>
            </label>
            <input type="number" id="form-receipt-price" inputmode="decimal" value="<?=$newReceipt->getPrice()/100?>" oninput="onNumberChange(this);updateReceiptPrice(this)" onclick="this.select();"></input>
            <label>
              <h2>Data:</h2>
            </label>
            <input type="date" id="form-receipt-date" oninput="updateReceipt('newDate', this.value)"></input>
            <label><h2>Lista osób:</h2></label>
              <table id="table-person-list">
                <tr>
                  <th>Nr</th>
                  <th>Osoba</th>
                  <th>Dług</th>
                  <th>Akcja</th>
                </tr>
                <tr>
                  <td colspan="4" style="padding: 0;">
                    <input class="table-text-input" onkeydown="addPersonToList(this);" type="text" placeholder="Dodaj osobę" ></input>
                  </td>
                </tr>
              </table>
          </form>
        </div>
        <div class="receipt-right-side">
          <h2>Przedmioty</h2>
          <table id="table-item-list">
            <tr>
              <th>Przedmiot</th>
              <th>Cena</th>
              <th>Płatnicy</th>
              <th>Akcja</th>
            </tr>
            <tr>
              <td style="padding: 0; height: 3em;">
                <input type="text" id="input-item-name" placeholder="Nazwa"
                style="margin: 0; padding: 0 5%; border-radius: 0; text-align: left; height: 100%; width: 100%;"></input>
              </td>
              <td style="padding: 0; height: 3em;">
                <input type="number" placeholder="Cena" onkeydown="addItemToList(this);" oninput="onNumberChange(this);"
                style="margin: 0; padding: 0 5%; border-radius: 0; text-align: left; height: 100%; width: 90%;"></input>
              </td>
            </tr>
          </table>
        </div>
      </div>
      <input id="submit-save-receipt" type="submit" value="Zapisz w bazie" onclick="receiptSubmitToDatabase(this);">
      </input>
      <!--<div id="qrcode">-->
      <pre id="php-output">
      </pre>
    </div>
  </main>
  <script src="js/addReceiptHandler.js">
  </script>
</body>
</html>