<h2>Paragony obce</h2>
<table>
  <tr>
    <th>Twoja należność</th>
    <th>Wartość</th>
    <th>Płatnik</th>
    <th>Data</th>
    <th>Spłata</th>
  </tr>
  <?php
    global $pdo;
    $query = "SELECT receipts.id, date, price, users.name, `payments`.`value`, `payments`.`paid`, payer_id
      FROM payments
      INNER JOIN receipts
      ON payments.receipt_id = receipts.id
      INNER JOIN users
      ON users.id = receipts.payer_id
      WHERE user_id=:id AND payer_id<>:id
      ORDER BY date DESC
    ;";

    $result = $pdo->prepare($query);
    $values = array("id" => $_SESSION['login-id']);
    $result->execute($values);
    while($row = $result->fetch(PDO::FETCH_ASSOC)) {
      $dateFormat = strtotime($row['date']);
      $dateFormat = date('d.m.Y', $dateFormat);

  ?>
    <tr data-receipt-id="<?=$row['id'];?>"
    onclick="onReceiptRowClick(this);"
    title="Kliknij by zobaczyć"
    >
      <td class="money td-money"><?=$row['value'] / 100;?></td>
      <td class="money td-money"><?=$row['price'] / 100;?></td>
      <td><?=$row['name'];?></td>
      <td><?=$dateFormat;?></td>
      <td
      class="<?=$row['paid']?"paid":"not-paid";?>"
      onclick="event.cancelBubble=true;onPaySwitchCellClick(this);"
      title="Kliknij by zaznaczyć płatność"
      >
      </td>
    </tr>
  <?php } ?>
</table>