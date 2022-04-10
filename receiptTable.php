<table>
  <tr>
    <th>Data</th>
    <th>Wartość</th>
    <th>Płatnik</th>
    <th>Twoja należność</th>
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
    <tr>
      <td><?=$dateFormat;?></td>
      <td><?=grosz2PLN($row['price']);?></td>
      <td><?=$row['name'];?></td>
      <td><?=grosz2PLN($row['value']);?></td>
      <td>
        <input type="checkbox"
        <?=$row['paid']?"checked":""?>
        ></input>
      </td>
    </tr>
  <?php } ?>
</table>