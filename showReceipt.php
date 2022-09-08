<?php
  session_start();
  require_once('checkIfLogged.php');
  require_once('database.php');
  require_once('utility.php');
  require_once('receipt.php');

  $receiptID = null;
  if(!isset($_GET['receiptID']) || $_GET['receiptID'] == '') {
    header('Location: ./');
  } else {
    $receiptID = intval($_GET['receiptID']);
  }
  $receipt = new Receipt();
  if(!$receipt->getFromDatabase($receiptID)) {
    header('Location: ./');
  }
  $personCount = count($receipt->personList);
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
      <h1>Paragon</h1>
    </header>
    <div class="content">
      <a href="./" style="float: right;">Powrót do głównej</a><br>
      <div class="receipt-info">
        <div class="receipt-left-side">
          <form>
            <div style="float: left;">
              <label>
                <h2>Kwota:</h2>
              </label>
              <input type="number" id="form-receipt-price" inputmode="decimal" value="<?=$receipt->getPrice()/100?>" readonly></input>
            </div>
            <div style="float: right;">
              <label>
                <h2>Data:</h2>
              </label>
              <input type="date" id="form-receipt-date" value="<?=$receipt->date?>" readonly></input>
            </div>
            <div style="clear: both;"></div>
            <label>
              <h2>Lista osób:</h2>
            </label>
            <table id="table-person-list">
              <tr>
                <th>Nr</th>
                <th>Osoba</th>
                <th>Dług</th>
              </tr>
              <?php
                $number = 1;
                foreach($receipt->personList as $person) {
                  $personName = $receipt->userNames[$person];
              ?>
                <tr>
                  <td><?=$number?></td>
                  <td><?= $person == $receipt->payerID ? $personName." (płatnik)" : $personName ?></td>
                  <td><?=$receipt->shares[$person] / 100?></td>
                </tr>
              <?php $number++; } ?>
            </table>
            <textarea id="receipt-description" readonly><?=$receipt->description?></textarea>
            <div id="error-informer"></div>
          </form>
        </div>
        <div class="receipt-right-side">
          <h2>Przedmioty</h2>
          <table id="table-item-list">
            <tr>
              <th>Przedmiot</th>
              <th>Cena</th>
              <th>Płatnicy</th>
            </tr>
            <?php
              foreach($receipt->itemList as $item) {
            ?>
            <tr>
              <td><?=$item->name?></td>
              <td><?=$item->price / 100?></td>
              <td>
                <?php
                  for($i = $personCount - 1; $i >= 0; $i--) {
                    $personContained = in_array($receipt->personList[$i], $item->payers);
                ?>
                <input type="checkbox" class="person-checkbox" <?= $personContained ? "checked" : ""?> onclick="return false;">
                <?php } ?>
              </td>
            </tr>
            <?php } ?>
          </table>
        </div>
      </div>
    </div>
  </main>
</body>
</html>