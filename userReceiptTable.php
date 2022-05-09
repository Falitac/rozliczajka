<h2>Twoje paragony</h2>
<table>
  <tr>
    <th>Twoja należność</th>
    <th>Wartość</th>
    <th>???</th>
    <th>Data</th>
    <th>Zapłacono?</th>
  </tr>
  <?php
    global $pdo;
    $query = "SELECT receipts.id, date, price, users.name, `payments`.`value`, `payments`.`paid`, payer_id
      FROM payments
      INNER JOIN receipts
      ON payments.receipt_id = receipts.id
      INNER JOIN users
      ON users.id = receipts.payer_id
      WHERE user_id=:id AND payer_id=:id
      ORDER BY date DESC
    ;";

    $result = $pdo->prepare($query);
    $values = array("id" => $_SESSION['login-id']);
    $result->execute($values);
    while($row = $result->fetch(PDO::FETCH_ASSOC)) {
      $dateFormat = strtotime($row['date']);
      $dateFormat = date('d.m.Y', $dateFormat);

  ?>
    <tr data-receipt-id="<?=$row['id'];?>">
      <td class="td-money"><?=grosz2PLN($row['value']);?></td>
      <td class="td-money"><?=grosz2PLN($row['price']);?></td>
      <td><?=$row['name'];?></td>
      <td><?=$dateFormat;?></td>
      <td
      class="<?=$row['paid']?"paid":"not-paid";?>"
      onclick="onPaySwitchCellClick(this);"
      >
      </td>
    </tr>
  <?php } ?>
</table>