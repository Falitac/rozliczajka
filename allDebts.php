<?php require_once('utility.php'); ?>

<h2>Twe długi:</h2>
<table>
  <tr>
    <th>Osoba1</th>
    <th>Osoba2</th>
    <th>Różnica długów</th>
  </tr>
  <?php
    foreach(extractAllDebts() as $person1 => $persons) {
      foreach($persons as $person2 => $value) {
  ?>
  <tr>
    <td><?=$person1?></td>
    <td><?=$person2?></td>
    <td class="td-money"><?=grosz2PLN($value)?></td>
  </tr>
  <?php
      }
    }
  ?>
</table>

<h2>Różnica długu:</h2>
<table>
  <tr>
    <th>Komu</th>
    <th>Kredyt</th>
  </tr>
  <?php
    $sum = 0;
    foreach(extractUserDebtDifference($_SESSION['login-id']) as $person => $value) {
      $sum += $value;
  ?>
  <tr>
    <td><?=$person?></td>
    <td class="td-money"
    style="color:<?= $value > 0 ? '#3b8b16':'#b0451b'?>;"
    ><?=grosz2PLN($value)?></td>
  </tr>
  <?php } ?>
  <tr>
    <td><b>Podsumowanie:</b></td>
    <td class="td-money"
    style="color:<?= $sum > 0 ? '#3b8b16':'#b0451b'?>;"
    ><?=grosz2PLN($sum)?></td>
  </tr>
</table>

<?php
function extractAllDebts() {
    global $pdo;
    $query = "SELECT owner.name AS Payer, payer.name AS Debter, sum(payments.value) AS Suma
      FROM receipts
      INNER JOIN payments
      ON payments.receipt_id=receipts.id
      INNER JOIN users AS owner
      ON owner.id=receipts.payer_id
      INNER JOIN users AS payer
      ON payer.id=payments.user_id
      WHERE
        payments.user_id <> receipts.payer_id
      AND
        payments.paid = 0
      GROUP BY receipts.payer_id, payments.user_id
    ";

    try {
      $result = $pdo->prepare($query);
      $result->execute();
    } catch (\Throwable $th) {
      print_r($th);
    }

    $debts = [];
    while($row = $result->fetch(PDO::FETCH_ASSOC)) {
      $payerIndex = $row['Payer'];
      $debtIndex = $row['Debter'];
      $debts[$payerIndex][$debtIndex] = $row['Suma'];
    }

    $outcome = [];
    foreach($debts as $key1 => $value1) {
      foreach($value1 as $key2 => $value2) {
        $outcome[$key1][$key2] = $value2;
        if(isset($debts[$key2][$key1])) {
          $outcome[$key1][$key2] -= $debts[$key2][$key1];
        }
      }
    }

    return $outcome;
}


function extractUserDebtDifference($id) {
  $userCreditors = extractUserCreditors($id);
  $userDebtors   = extractUserDebtors($id);
  $result = calculateDebtDifference($userCreditors, $userDebtors);

  return $result;
}

function extractUserCreditors($id) {
  global $pdo;

  $query= "SELECT owner.name AS Creditor, sum(payments.value) AS Suma
    FROM receipts
    INNER JOIN payments
    ON payments.receipt_id=receipts.id
    INNER JOIN users AS owner
    ON owner.id=receipts.payer_id
    WHERE
      payments.user_id = :id
    AND
      payments.paid = 0
    GROUP BY receipts.payer_id, payments.user_id
  ";
  try {
    $result = $pdo->prepare($query);
    $result->execute(array('id' => $id));
  } catch (\Throwable $th) {
    print_r($th);
  }

  $userCreditors = [];
  while($row = $result->fetch(PDO::FETCH_ASSOC)) {
    $userCreditors[$row['Creditor']] = $row['Suma'];
  }
  return $userCreditors;
}

function extractUserDebtors($id) {
  global $pdo;

  $query= "SELECT payer.name AS Debtor, sum(payments.value) AS Suma
    FROM receipts
    INNER JOIN payments
    ON payments.receipt_id=receipts.id
    INNER JOIN users AS owner
    ON owner.id=receipts.payer_id
    INNER JOIN users AS payer
    ON payer.id=payments.user_id
    WHERE
      receipts.payer_id = :id
    AND
      payments.paid = 0
    GROUP BY receipts.payer_id, payments.user_id
  ";

  try {
    $result = $pdo->prepare($query);
    $result->execute(array('id' => $id));
  } catch (\Throwable $th) {
    print_r($th);
  }

  $userDebtors = [];
  while($row = $result->fetch(PDO::FETCH_ASSOC)) {
    $userDebtors[$row['Debtor']] = $row['Suma'];
  }

  return $userDebtors;
}

function calculateDebtDifference($userCreditors, $userDebtors) {
  $result = [];

  foreach($userDebtors as $debtor => $value) {
    $result[$debtor] = $value;
  }
  foreach($userCreditors as $creditor => $value) {
    if(isset($result[$creditor])) {
      $result[$creditor] = $result[$creditor] - $value;
    } else {
      $result[$creditor] = -$value;
    }
  }
  return $result;
}

extractUserDebtDifference($_SESSION['login-id']);

?>
