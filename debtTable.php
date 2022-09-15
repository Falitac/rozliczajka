<h2>Długi:</h2>
<table>
  <tr>
    <th>Osoba</th>
    <th>Wartość</th>
  </tr>
  <?php
    global $pdo;
    $query = "SELECT users.name, SUM(`payments`.`value`) as suma
      FROM payments
      INNER JOIN receipts
      ON payments.receipt_id = receipts.id
      INNER JOIN users
      ON users.id = receipts.payer_id
      WHERE user_id=:id AND payer_id<>:id
      AND
        payments.paid = 0
      GROUP BY users.name
    ;";

    $result = $pdo->prepare($query);
    $values = array("id" => $_SESSION['login-id']);
    $result->execute($values);
    while($row = $result->fetch(PDO::FETCH_ASSOC)) {

  ?>
    <tr>
      <td><?=$row['name'];?></td>
      <td class="money"><?=grosz2PLN($row['suma']);?></td>
    </tr>
  <?php } ?>
</table>

<?php
$sql = "SELECT receipts.payer_id, payments.user_id, sum(payments.value) AS Suma FROM receipts INNER JOIN payments ON payments.receipt_id=receipts.id WHERE payments.user_id <> receipts.payer_id GROUP BY receipts.payer_id, payments.user_id";
?>