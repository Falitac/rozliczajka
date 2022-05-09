<?php require_once('utility.php'); ?>

<h2>Wszystkie długi:</h2>
<table>
  <tr>
    <th>Osoba1</th>
    <th>Osoba2</th>
    <th>Różnica długów</th>
  </tr>
  <?php
    foreach(extractDebts() as $person1 => $persons) {
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

<?php
function extractDebts() {
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

?>
