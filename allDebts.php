<table>
  <tr>
    <th>Osoba1</th>
    <th>Osoba2</th>
    <th>Wartość</th>
  </tr>
      <tr>
        <td></td>
        <td></td>
        <td></td>
      </tr>
</table>

<?php
function extractDebts() {
    global $pdo;
    $query = "SELECT receipts.payer_id, payments.user_id, sum(payments.value) AS Suma
      FROM receipts
      INNER JOIN payments
      ON payments.receipt_id=receipts.id
      WHERE
      payments.user_id <> receipts.payer_id
      GROUP BY receipts.payer_id, payments.user_id
    ";

    $result = $pdo->prepare($query);
    $result->execute();

    $debts = [];
    while($row = $result->fetch(PDO::FETCH_ASSOC)) {
      $payerIndex = $row['payer_id'];
      $debtIndex = $row['user_id'];
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
    echo "<pre>";
    print_r($debts);
    print_r($outcome);
    echo "</pre>";
}
extractDebts();
?>