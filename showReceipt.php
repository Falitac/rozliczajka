<?php
  session_start();
  require_once('checkIfLogged.php');
  require_once('database.php');
  require_once('globals.php');
  require_once('utility.php');
  require_once('receipt.php');

  if(!isSessionStarted()) {
    session_start();
  }

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

  function paymentQuery($receiptID) {
    $result = array();
    $sql = "SELECT users.name as name, paid\n"
      . "FROM `payments`\n"
      . "INNER JOIN `users`\n"
      . "ON `users`.`id` = `payments`.`user_id`\n"
      . "WHERE `payments`.`receipt_id` = :receiptID;";
    
    global $pdo;
    $queryResult = null;
    try {
      $queryResult = $pdo->prepare($sql);
      $queryResult->execute(array('receiptID' => $receiptID));
    } catch(PDOException $e) {
      return null;
    }
    if(is_null($queryResult)) {
      return null;
    }
    foreach($queryResult->fetchAll(PDO::FETCH_ASSOC) as $row) {
      $result[$row['name']] = $row['paid'];
    }

    return $result;
  }

  $paymentPeopleStatus = paymentQuery($receiptID);

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
                <th>Spłacony?</th>
              </tr>
              <?php
                $number = 1;
                foreach($receipt->personList as $person) {
                  $personName = $receipt->userNames[$person];
                  $paymentStatus = $paymentPeopleStatus[$personName];
                  $paymentStatusContent =  $paymentStatus ? "✔" : "✖";
                  $paymentStatusCol = $paymentStatus ? "good-col" : "bad-col";
              ?>
                <tr>
                  <td><?=$number?></td>
                  <td><?= $person == $receipt->payerID ? $personName." (płatnik)" : $personName ?></td>
                  <td class="money td-money"><?=$receipt->shares[$person] / 100?></td>
                  <td class="<?=$paymentStatusCol?>"><?=$paymentStatusContent?></td>
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
              <th colspan="<?=count($receipt->personList)?>">Płatnicy</th>
            </tr>
            <?php
              foreach($receipt->itemList as $item) {
                $itemParticipantPrice = ceil($item->price / count($item->payers)) / 100;
            ?>
            <tr>
              <td><?=$item->name?></td>
              <td class="money td-money"><?=$item->price / 100?></td>
              <?php
                for($i = 0; $i < count($receipt->personList); $i++) {
                  $personContained = in_array($receipt->personList[$i], $item->payers);
                  $itemPriceInclude = 0;
                  $informColor = "";
                  if($personContained) {
                    $itemPriceInclude = $itemParticipantPrice;
                    $informColor = "color: var(--good-col);";
                  }
              ?>
              <td class="money td-money" style="<?=$informColor?>"><?=$itemPriceInclude?></td>
              <?php } ?>
            </tr>
            <?php } ?>
          </table>
        </div>
        <div class="receipt-image">
          <?php if(!is_null($receipt->imageName)) { ?>
            <img src="<?= RECEIPTS_IMG_LOCATION . $receipt->imageName; ?>" width="550"/>
          <?php } ?>
        </div>
      </div>
      <?php
        if($receipt->payerID === $_SESSION['login-id']) {
      ?>
      <input type="submit" value="Usuń paragon❌" style="max-width: 220px; border: 1px solid #999" onclick="makeRemoveRequest(<?= $receiptID;?>)"></input>
      <?php } ?>
    </div>
    <script src="js/utility.js"></script>
    <script>
      function makeRemoveRequest(id) {
        let userDecision = confirm("Czy napewno chcesz usunąć ten paragon? Ta zmiana jest nieodwracalna");
        if(userDecision) {
          let httpRequest = new XMLHttpRequest();
          httpRequest.onreadystatechange = () => {
            if(httpRequest.readyState == 4 && httpRequest.status == 200) {
              window.location = './';
            }
          };
          httpRequest.open("GET", `receiptRemover.php?receiptID=${id}`, true);
          httpRequest.send();
        }
      }
    </script>
  </main>
</body>
</html>