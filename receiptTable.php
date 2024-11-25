<h2>
  <?php echo $receiptTableForUser ? 'Twoje paragony' : 'Paragony obce'; ?>
</h2>
<table>
  <tr>
    <th>
      <?php echo $receiptTableForUser ? 'Należność' : 'Twoja należność'; ?>
    </th>
    <th>Wartość</th>
    <?php if(!$receiptTableForUser) { ?>
      <th>Płatnik</th>
    <?php } ?>
    <th>Data</th>
    <th>Opis</th>
    <th>Spłata</th>
  </tr>
  <?php
    $operator = $receiptTableForUser ? '=' : '<>';
    global $pdo;
    $query = "SELECT receipts.id, receipts.date, price, users.name, receipts.description, `payments`.`value`, `payments`.`paid`, payer_id
      FROM payments
      INNER JOIN receipts
      ON payments.receipt_id = receipts.id
      INNER JOIN users
      ON users.id = receipts.payer_id
      WHERE user_id=:id AND payer_id$operator:id
      ORDER BY date DESC
    ;";
    $lastMonth = null;

    $result = $pdo->prepare($query);
    $values = array("id" => $_SESSION['login-id']);
    $result->execute($values);
    while($row = $result->fetch(PDO::FETCH_ASSOC)) {
      $datetime = strtotime($row['date']);
      $dateFormat = date('d.m.Y', $datetime);
      $currMonth = date('m', $datetime);
      $currYear = date('Y', $datetime);
      $monthNameFull = null;
      if(is_null($lastMonth) || $currMonth != $lastMonth) {
        $lastMonth = $currMonth;
        $monthNameFull = date('F', strtotime($dateFormat));
      }
  ?>

    <?php if(!is_null($monthNameFull)) { ?>
      <tr>
        <td colspan="6" class="td-month-break">
          <?=$monthNameFull?> <?=$currYear?>
        </td>
      </tr>
    <?php } ?>

    <tr data-receipt-id="<?=$row['id'];?>"
      onclick="onReceiptRowClick(this);"
      title="Kliknij by zobaczyć"
    >
      <td class="money td-money"><?=$row['value'] / 100;?></td>
      <td class="money td-money"><?=$row['price'] / 100;?></td>

    <?php if(!$receiptTableForUser) { ?>
      <td><?=$row['name'];?></td>
    <?php } ?>

      <td><?=$dateFormat;?></td>
      <td class="description-text"><?=$row['description'];?></td>
      <td class="<?=$row['paid']?"paid":"not-paid";?>" title="Status płatności">
      </td>
    </tr>
  <?php } ?>
</table>